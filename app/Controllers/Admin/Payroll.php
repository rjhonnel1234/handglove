<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\PayrollPeriodsModel;
use App\Models\InvoicesModel;
use App\Models\PayStubsModel;
use App\Models\PayStubDetailsModel;
use App\Models\PayStubDeductionsModel;
use App\Models\CliniciansModel;
use App\Models\TaxTypeModel;
use DateTime;

class Payroll extends BaseController
{
    public function setup()
    {
        $settingsModel = new SettingsModel();
        $periodsModel = new PayrollPeriodsModel();

        $data = [
            'page_title' => 'Payroll Setup',
            'session' => session(),
            'frequency' => $settingsModel->getSetting('payroll_frequency', 'weekly'),
            'start_day' => $settingsModel->getSetting('payroll_start_day', 'Monday'),
            'pay_date_offset' => $settingsModel->getSetting('payroll_pay_date_offset', '5'),
            'periods' => $periodsModel->orderBy('start_date', 'DESC')->findAll(),
        ];

        return view('admin/payroll/setup', $data);
    }

    public function saveSettings()
    {
        $settingsModel = new SettingsModel();
        
        $frequency = $this->request->getPost('frequency');
        $startDay = $this->request->getPost('start_day');
        $payDateOffset = $this->request->getPost('pay_date_offset');

        $settingsModel->updateSetting('payroll_frequency', $frequency);
        $settingsModel->updateSetting('payroll_start_day', $startDay);
        $settingsModel->updateSetting('payroll_pay_date_offset', $payDateOffset);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message' => 'Payroll settings updated successfully.'
        ]);
    }

    public function generatePeriod()
    {
        $periodsModel = new PayrollPeriodsModel();
        $settingsModel = new SettingsModel();

        $frequency = $settingsModel->getSetting('payroll_frequency', 'weekly');
        $latest = $periodsModel->getLatestPeriod();

        if ($latest) {
            $startDate = new DateTime($latest['end_date']);
            $startDate->modify('+1 day');
        } else {
            // Default to start of current month if no periods exist
            $startDate = new DateTime('first day of this month');
        }

        $endDate = clone $startDate;
        switch ($frequency) {
            case 'weekly':
                $endDate->modify('+6 days');
                break;
            case 'bi-weekly':
                $endDate->modify('+13 days');
                break;
            case 'monthly':
                $endDate->modify('last day of this month');
                break;
            default:
                $endDate->modify('+6 days');
        }

        $item = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
            'pay_date'   => clone $endDate,
            'status'     => 10, // Open
        ];
        
        $payDateOffset = $settingsModel->getSetting('payroll_pay_date_offset', '5');
        $item['pay_date']->modify('+' . $payDateOffset . ' days');
        $item['pay_date'] = $item['pay_date']->format('Y-m-d');

        $periodsModel->save($item);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message' => 'New payroll period generated.'
        ]);
    }

    public function deletePeriod($id)
    {
        $periodsModel = new PayrollPeriodsModel();
        $periodsModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message' => 'Payroll period deleted.'
        ]);
    }

    public function viewPeriod($id)
    {
        $periodsModel = new PayrollPeriodsModel();
        $invoicesModel = new InvoicesModel();
        $stubsModel = new PayStubsModel();

        $period = $periodsModel->find($id);
        if (!$period) {
            return redirect()->to(base_url('admin/payroll/setup'))->with('error', 'Period not found.');
        }

        // Get completed shifts for this period (those with both punch-in and punch-out)
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_shift_timekeeping t1');
        $builder->select('t1.shift_id, t1.clinician_id, t1.punch_datetime as punch_in, t2.punch_datetime as punch_out, tbl_clinicians.name as clinician_name, tbl_shifts.start_date, tbl_shifts.rate')
                ->join('tbl_shift_timekeeping t2', 't1.shift_id = t2.shift_id AND t1.clinician_id = t2.clinician_id')
                ->join('tbl_clinicians', 'tbl_clinicians.id = t1.clinician_id')
                ->join('tbl_shifts', 'tbl_shifts.id = t1.shift_id')
                ->where('t1.punch_type', 10)
                ->where('t2.punch_type', 20)
                ->where('tbl_shifts.start_date >=', $period['start_date'])
                ->where('tbl_shifts.start_date <=', $period['end_date']);
        $completedShifts = $builder->get()->getResultArray();

        // Calculate hours for each shift
        foreach ($completedShifts as &$shift) {
            $start = new DateTime($shift['punch_in']);
            $end = new DateTime($shift['punch_out']);
            $interval = $start->diff($end);
            $shift['total_hours'] = round($interval->h + ($interval->days * 24) + ($interval->i / 60), 2);
            $shift['total_amount'] = round($shift['total_hours'] * $shift['rate'], 2);
        }

        // Get existing stubs
        $stubs = $stubsModel->select('tbl_pay_stubs.*, tbl_clinicians.name as clinician_name')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_pay_stubs.clinician_id')
            ->where('period_id', $id)
            ->findAll();

        $data = [
            'page_title' => 'Manage Pay Period',
            'session' => session(),
            'period' => $period,
            'completedShifts' => $completedShifts,
            'stubs' => $stubs,
        ];

        return view('admin/payroll/view_period', $data);
    }

    public function generateStubs($period_id)
    {
        $periodsModel = new PayrollPeriodsModel();
        $invoicesModel = new InvoicesModel();
        $stubsModel = new PayStubsModel();
        $stubDetailsModel = new PayStubDetailsModel();

        $period = $periodsModel->find($period_id);
        if (!$period) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Period not found.']);
        }
        //do not include invoices, generate pay stubs based on shifts and hours
        // Find all completed shifts in this period
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_shift_timekeeping t1');
        $builder->select('t1.shift_id, t1.clinician_id, t1.punch_datetime as punch_in, t2.punch_datetime as punch_out, tbl_shifts.rate')
                ->join('tbl_shift_timekeeping t2', 't1.shift_id = t2.shift_id AND t1.clinician_id = t2.clinician_id')
                ->join('tbl_shifts', 'tbl_shifts.id = t1.shift_id')
                ->where('t1.punch_type', 10)
                ->where('t2.punch_type', 20)
                ->where('tbl_shifts.start_date >=', $period['start_date'])
                ->where('tbl_shifts.start_date <=', $period['end_date']);
        $shifts = $builder->get()->getResultArray();

        if (empty($shifts)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No completed shifts found for this period.']);
        }

        // Group shifts by clinician and calculate totals
        $grouped = [];
        foreach ($shifts as $s) {
            $start = new DateTime($s['punch_in']);
            $end = new DateTime($s['punch_out']);
            $interval = $start->diff($end);
            $hours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
            
            $s['total_hours'] = $hours;
            $s['total_amount'] = $hours * $s['rate'];
            
            $grouped[$s['clinician_id']][] = $s;
        }

        $count = 0;
        //implement tax types for deductions
        $taxTypeModel = new TaxTypeModel();
        $payStubDeductionsModel = new PayStubDeductionsModel();
        $activeTaxes = $taxTypeModel->where('status', 1)->findAll();

        foreach ($grouped as $clinicianId => $clShifts) {
            // Check if stub already exists
            $exists = $stubsModel->where('clinician_id', $clinicianId)->where('period_id', $period_id)->first();
            if ($exists) continue;

            $totalHours = 0;
            $grossPay = 0;
            foreach ($clShifts as $s) {
                $totalHours += $s['total_hours'];
                $grossPay += $s['total_amount'];
            }

            // Calculate Deductions
            $totalDeductions = 0;
            $deductionsToSave = [];
            foreach ($activeTaxes as $tax) {
                $taxAmount = round($grossPay * ($tax['percentage'] / 100), 2);
                $totalDeductions += $taxAmount;
                $deductionsToSave[] = [
                    'tax_type_id' => $tax['id'],
                    'tax_name'    => $tax['name'],
                    'percentage'  => $tax['percentage'],
                    'amount'      => $taxAmount,
                ];
            }

            $stubData = [
                'clinician_id'     => $clinicianId,
                'period_id'        => $period_id,
                'total_hours'      => round($totalHours, 2),
                'gross_pay'        => round($grossPay, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_pay'          => round($grossPay - $totalDeductions, 2),
                'status'           => 10,
            ];

            $stubId = $stubsModel->insert($stubData);
            if ($stubId) {
                foreach ($clShifts as $s) {
                    $stubDetailsModel->insert([
                        'pay_stub_id' => $stubId,
                        'shift_id'    => $s['shift_id']
                    ]);
                }

                // Save individual deductions
                foreach ($deductionsToSave as $deduction) {
                    $deduction['pay_stub_id'] = $stubId;
                    $payStubDeductionsModel->insert($deduction);
                }
                $count++;
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'success' => 1,
            'message' => "Generated $count pay stubs."
        ]);
    }

    public function viewStub($id)
    {
        $stubsModel = new PayStubsModel();
        $stubDetailsModel = new PayStubDetailsModel();
        $cliniciansModel = new CliniciansModel();
        $periodsModel = new PayrollPeriodsModel();

        $stub = $stubsModel->find($id);
        if (!$stub) {
            return redirect()->to(base_url('admin/payroll/setup'))->with('error', 'Stub not found.');
        }

        $clinician = $cliniciansModel->find($stub['clinician_id']);
        $period = $periodsModel->find($stub['period_id']);
        $details = $stubDetailsModel->getStubShifts($id);

        $payStubDeductionsModel = new PayStubDeductionsModel();
        $deductions = $payStubDeductionsModel->getStubDeductions($id);

        // Calculate hours for detail rows if not stored
        foreach ($details as &$d) {
            $start = new DateTime($d['shift_start_time']); // Note: this might need adjustment if shifts cross midnight, but using punch data is better if available
            // Actually, we should probably fetch the specific timekeeping for this clinician/shift for accuracy
            $objTimekeeping = new \App\Models\ShiftsTimekeepingModel();
            $punchIn = $objTimekeeping->where('shift_id', $d['shift_id'])->where('clinician_id', $stub['clinician_id'])->where('punch_type', 10)->first();
            $punchOut = $objTimekeeping->where('shift_id', $d['shift_id'])->where('clinician_id', $stub['clinician_id'])->where('punch_type', 20)->first();
            
            if ($punchIn && $punchOut) {
                $pIn = new DateTime($punchIn['punch_datetime']);
                $pOut = new DateTime($punchOut['punch_datetime']);
                $interval = $pIn->diff($pOut);
                $d['actual_hours'] = $interval->h + ($interval->days * 24) + ($interval->i / 60);
                $d['actual_amount'] = $d['actual_hours'] * $d['rate'];
            } else {
                $d['actual_hours'] = 0;
                $d['actual_amount'] = 0;
            }
        }

        $data = [
            'page_title' => 'Pay Stub',
            'stub' => $stub,
            'clinician' => $clinician,
            'period' => $period,
            'details' => $details,
            'deductions' => $deductions,
        ];

        return view('admin/payroll/pay_stub', $data);
    }

    public function markAsPaid($id)
    {
        $stubsModel = new PayStubsModel();
        $stub = $stubsModel->find($id);
        if (!$stub) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Stub not found.']);
            }
            return redirect()->back()->with('error', 'Stub not found.');
        }

        $stubsModel->update($id, ['status' => 20]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Stub marked as paid.']);
        }

        return redirect()->back()->with('success', 'Stub marked as paid.');
    }
}
