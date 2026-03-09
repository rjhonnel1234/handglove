<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ShiftsTimekeepingModel;
use App\Models\InvoicesModel;
use App\Models\ShiftsModel;
use App\Models\SettingsModel;
use DateTime;

class GenerateInvoices extends BaseCommand
{
    protected $group       = 'Invoice';
    protected $name        = 'invoice:generate';
    protected $description = 'Generates invoices for all completed shifts that do not have one.';

    public function run(array $params)
    {
        CLI::write('Checking for completed shifts without invoices...', 'yellow');

        $objTimekeeping = new ShiftsTimekeepingModel();
        $objInvoices = new InvoicesModel();
        $objShifts = new ShiftsModel();

        $db = \Config\Database::connect();
        
        // Find pairs of punch-in (10) and punch-out (20)
        $query = $db->query("
            SELECT t1.shift_id, t1.clinician_id, t1.punch_datetime as punch_in, t2.punch_datetime as punch_out
            FROM tbl_shift_timekeeping t1
            JOIN tbl_shift_timekeeping t2 ON t1.shift_id = t2.shift_id AND t1.clinician_id = t2.clinician_id
            WHERE t1.punch_type = 10 AND t2.punch_type = 20
        ");

        $results = $query->getResultArray();
        
        if (empty($results)) {
            CLI::write('No completed shifts found.', 'red');
            return;
        }

        //apply billing rate
        $objSettings = new SettingsModel();
        $billingRate = $objSettings->getSetting('billing_rate', 1.00);

        $count = 0;
        foreach ($results as $row) {
            $exists = $objInvoices->where('shift_id', $row['shift_id'])
                                 ->where('clinician_id', $row['clinician_id'])
                                 ->first();
            
            if (empty($exists)) {
                $shiftDetails = $objShifts->find($row['shift_id']);
                
                if (!empty($shiftDetails)) {
                    $start = new DateTime($row['punch_in']);
                    $end = new DateTime($row['punch_out']);
                    $interval = $start->diff($end);
                    
                    $totalHours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
                    $rate = $shiftDetails['rate'];
                    $totalAmount = $totalHours * $rate * $billingRate;

                    $invoiceItem = [
                        'shift_id'     => $row['shift_id'],
                        'clinician_id' => $row['clinician_id'],
                        'client_id'    => $shiftDetails['client_id'],
                        'total_hours'  => $totalHours,
                        'rate'         => $rate,
                        'billing_rate' => $billingRate,
                        'total_amount' => $totalAmount,
                        'status'       => 10 // Pending
                    ];

                    if ($objInvoices->save($invoiceItem)) {
                        CLI::write("Generated invoice for shift ID: {$row['shift_id']}, Clinician: {$row['clinician_id']}, Amount: \${$totalAmount}", 'green');
                        $count++;
                    } else {
                        CLI::error("Failed to save invoice for shift ID: {$row['shift_id']}");
                    }
                }
            }
        }

        CLI::write("Invoice generation complete. Total generated: {$count}", 'cyan');
    }
}
