<?php
namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\ClientSchedulesUploadModel;
use App\Models\ClientScheduleDetailsModel;
use App\Models\FacilityUnitsModel;
use App\Models\ClientPersonnelModel;
use App\Models\FacilityModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Schedules extends BaseController
{
    protected $clientScheduleUploadModel;
    protected $clientScheduleDetailsModel;
    protected $facilityUnitsModel;
    protected $clientPersonnelModel;
    protected $facilityModel;
    protected $shiftsModel;
    protected $shiftCliniciansModel;
    protected $facilityOnboardingSettingsModel;
    protected $clinicianTypesModel;
    protected $session;

    public function __construct()
    {
        $this->clientScheduleUploadModel = new ClientSchedulesUploadModel();
        $this->clientScheduleDetailsModel = new ClientScheduleDetailsModel();
        $this->facilityUnitsModel = new FacilityUnitsModel();
        $this->clientPersonnelModel = new ClientPersonnelModel();
        $this->facilityModel = new FacilityModel();
        $this->shiftsModel = new \App\Models\ShiftsModel();
        $this->shiftCliniciansModel = new \App\Models\ShiftCliniciansModel();
        $this->facilityOnboardingSettingsModel = new \App\Models\FacilityOnboardingSettingsModel();
        $this->clinicianTypesModel = new \App\Models\ClinicianTypesModel();
        $this->session = session();
    }



    //create a view page for schedules
    public function view()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return redirect()->to('/login');
        }

        $facilityId = $this->session->get('facility_id');
        $date = $this->request->getGet('date') ?: date('Y-m-d');

        $facility = $this->facilityModel->find($facilityId);
        $units = $this->facilityUnitsModel->where('client_id', $facilityId)->findAll();

        $scheduleUpload = $this->clientScheduleUploadModel->where([
            'client_id' => $facilityId,
            'schedule_date' => $date
        ])->first();

        $data = [
            'facility' => $facility,
            'units' => $units,
            'selectedDate' => $date,
            'scheduleUpload' => $scheduleUpload,
            'session' => $this->session,
            'page' => 'schedules'
        ];

        return view('components/header', [
            'title' => 'View Schedule',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => [
                'title' => 'View Schedule',
                'description' => '',
                'image' => IMG_URL . ''
            ],
            'styles' => [
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_scheduler_profile',
            ],
            'session' => $this->session
        ])
            . view('facility/scheduler/view', $data)
            . view('components/scripts_render', [
                'scripts' => [
                    'https://code.jquery.com/jquery-3.5.1.min.js',
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/components/notifications.min.js',
                    ASSETS_URL . 'js/pages/facility/scheduler/facility_scheduler_form.min.js',
                ]
            ]);
    }

    public function add()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return redirect()->to('/login');
        }

        $facilityId = $this->session->get('facility_id');
        $date = $this->request->getGet('date') ?: date('Y-m-d');

        $facility = $this->facilityModel->find($facilityId);
        $units = $this->facilityUnitsModel->where('client_id', $facilityId)->findAll();

        // Fetch staff with type 7
        $personnel = $this->clientPersonnelModel
            ->select('tbl_client_personnel.*, tbl_clinician_types.name as clinician_type_name')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_client_personnel.clinician_type', 'left')
            ->where('tbl_client_personnel.client_id', $facilityId)
            ->where('tbl_client_personnel.type', 7)
            ->where('tbl_client_personnel.status', 1)
            ->findAll();

        $clinicianTypes = ($this->clinicianTypesModel ?? new \App\Models\ClinicianTypesModel())->where('status', 1)->findAll();

        // Check for existing manual schedule metadata
        $scheduleUpload = $this->clientScheduleUploadModel
            ->select('tbl_client_schedules_upload.*, tbl_users.first_name as uploader_first, tbl_users.last_name as uploader_last')
            ->join('tbl_users', 'tbl_users.id = tbl_client_schedules_upload.uploaded_by', 'left')
            ->where([
                'client_id' => $facilityId,
                'schedule_date' => $date
            ])->first();

        if ($scheduleUpload && $scheduleUpload['status'] == 20) {
            return redirect()->to('/facility/schedules/view?date=' . $date);
        }

        $data = [
            'facility' => $facility,
            'units' => $units,
            'personnel' => $personnel,
            'clinicianTypes' => $clinicianTypes,
            'selectedDate' => $date,
            'scheduleUpload' => $scheduleUpload,
            'session' => $this->session,
            'page' => 'schedules'
        ];

        return view('components/header', [
            'title' => 'Manual Schedule',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => [
                'title' => 'Manual Schedule',
                'description' => '',
                'image' => IMG_URL . ''
            ],
            'styles' => [
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/toastr',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_scheduler_profile',
            ],
            'session' => $this->session
        ])
            . view('facility/scheduler/create', $data)
            . view('components/scripts_render', [
                'scripts' => [
                    'https://code.jquery.com/jquery-3.5.1.min.js',
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/plugins/toastr.min.js',
                    ASSETS_URL . 'js/pages/facility/scheduler/facility_scheduler_form.min.js',
                ]
            ])
            . view('components/footer');
    }

    public function save_manual()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $facilityId = $this->session->get('facility_id');
        $date = $this->request->getPost('date') ?: $this->request->getPost('schedule_date');
        $uploadId = $this->request->getPost('upload_id');
        $isConversion = $this->request->getPost('is_conversion');
        $convertedData = $this->request->getPost('converted_data');
        $assignments = $this->request->getPost('assignments'); // From preview page (old way)
        $unitsSchedule = $this->request->getPost('units_schedule'); // From manual page

        if (!$date) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing required date']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Handle Conversion Logic (Automated PDF to Schedule)
        if ($isConversion && $convertedData) {
            $unitsData = json_decode($convertedData, true);
            if (!is_array($unitsData)) {
                return $this->response->setJSON(['success' => 0, 'message' => 'Invalid conversion data format.']);
            }

            // Clear existing schedule details for this date to ensure 1:1 sync with PDF
            $this->clientScheduleDetailsModel->where(['client_id' => $facilityId, 'schedule_date' => $date])->delete();
            foreach ($unitsData as $uData) {
                $unitName = trim($uData['name']);
                if (empty($unitName))
                    continue;

                // Find or Create Unit
                $unit = $this->facilityUnitsModel->where(['client_id' => $facilityId, 'name' => $unitName])->first();
                if (!$unit) {
                    $unitId = $this->facilityUnitsModel->insert([
                        'client_id' => $facilityId,
                        'name' => $unitName,
                        'description' => 'Automatically created from PDF upload'
                    ]);
                } else {
                    $unitId = $unit['id'];
                }

                foreach ($uData['shifts'] as $shiftName => $shiftInfo) {
                    $staffArray = (isset($shiftInfo['staff'])) ? $shiftInfo['staff'] : $shiftInfo;
                    $slots = (isset($shiftInfo['slots'])) ? $shiftInfo['slots'] : 0;

                    foreach ($staffArray as $staff) {
                        $name = trim($staff['name'] ?? '');
                        $position = trim($staff['position'] ?? '');
                        if (empty($name))
                            continue;

                        // Find or Create Clinician Type
                        $cType = $this->clinicianTypesModel->where('name', $position)->first();
                        if (!$cType && !empty($position)) {
                            $cTypeId = $this->clinicianTypesModel->insert([
                                'name' => $position,
                                'status' => 1,
                                'grouping' => ''
                            ]);
                        } else {
                            $cTypeId = $cType ? $cType['id'] : 0;
                        }

                        // Split Name
                        $nameParts = explode(' ', $name);
                        $fName = $nameParts[0] ?? 'Unknown';
                        $lName = count($nameParts) > 1 ? trim(implode(' ', array_slice($nameParts, 1))) : '';

                        // Find or Create Personnel
                        $personnel = $this->clientPersonnelModel->where([
                            'client_id' => $facilityId,
                            'first_name' => $fName,
                            'last_name' => $lName
                        ])->first();

                        if (!$personnel) {
                            $personnelId = $this->clientPersonnelModel->insert([
                                'client_id' => $facilityId,
                                'type' => 7, // Facility staff
                                'first_name' => $fName,
                                'last_name' => $lName,
                                'status' => 1,
                                'clinician_type' => $cTypeId,
                                'email' => '',
                                'contact_number' => ''
                            ]);
                        } else {
                            $personnelId = $personnel['id'];
                        }

                        // Create Schedule Detail
                        $this->clientScheduleDetailsModel->insert([
                            'client_id' => $facilityId,
                            'unit_id' => $unitId,
                            'personnel_id' => $personnelId,
                            'schedule_date' => $date,
                            'shift_name' => $shiftName,
                            'shift_time' => $this->getShiftTimeRange($shiftName),
                            'slots' => $slots
                        ]);
                    }
                }
            }

            // Sync original upload record status to 10 (Pending)
            if ($uploadId) {
                $this->clientScheduleUploadModel->update($uploadId, ['status' => 10]);
            }
        } else {
            // ORIGINAL MANUAL SAVE LOGIC (Retained for backwards compatibility)
            if ($uploadId) {
                // Finalizing a PDF upload (old way)
                $uploadExists = $this->clientScheduleUploadModel->find($uploadId);
                if ($uploadExists && $uploadExists['client_id'] == $facilityId) {
                    $this->clientScheduleUploadModel->update($uploadId, ['status' => 20]);
                }
            }

            // Handle Assignments (from Preview - old way)
            if (is_array($assignments)) {
                foreach ($assignments as $unitId => $shifts) {
                    foreach ($shifts as $shiftName => $staffArray) {
                        foreach ($staffArray as $staffJson) {
                            $staff = json_decode($staffJson, true);
                            if (!$staff)
                                continue;

                            $name = $staff['name'];
                            $nameParts = explode(' ', $name);
                            $fName = $nameParts[0] ?? 'Unknown';
                            $lName = count($nameParts) > 1 ? trim(implode(' ', array_slice($nameParts, 1))) : '';

                            $personnel = $this->clientPersonnelModel->where(['client_id' => $facilityId, 'first_name' => $fName, 'last_name' => $lName])->first();
                            if (!$personnel) {
                                $pType = $this->clinicianTypesModel->where('name', $staff['position'])->first();
                                $pTypeId = $pType ? $pType['id'] : 0;
                                $personnelId = $this->clientPersonnelModel->insert([
                                    'client_id' => $facilityId,
                                    'type' => 7,
                                    'first_name' => $fName,
                                    'last_name' => $lName,
                                    'status' => 1,
                                    'clinician_type' => $pTypeId
                                ]);
                            } else {
                                $personnelId = $personnel['id'];
                            }

                            $exists = $this->clientScheduleDetailsModel->where(['client_id' => $facilityId, 'unit_id' => $unitId, 'personnel_id' => $personnelId, 'schedule_date' => $date, 'shift_name' => $shiftName])->first();
                            if (!$exists) {
                                $this->clientScheduleDetailsModel->insert([
                                    'client_id' => $facilityId,
                                    'unit_id' => $unitId,
                                    'personnel_id' => $personnelId,
                                    'schedule_date' => $date,
                                    'shift_name' => $shiftName,
                                    'shift_time' => $this->getShiftTimeRange($shiftName)
                                ]);
                            }
                        }
                    }
                }
            }

            // Handle units_schedule (from Manual Add)
            if (is_array($unitsSchedule)) {
                foreach ($unitsSchedule as $unitId => $shifts) {
                    foreach ($shifts as $shiftName => $staffIds) {
                        foreach ($staffIds as $personnelId) {
                            $this->clientScheduleDetailsModel->insert([
                                'client_id' => $facilityId,
                                'unit_id' => $unitId,
                                'personnel_id' => $personnelId,
                                'schedule_date' => $date,
                                'shift_name' => $shiftName,
                                'shift_time' => $this->getShiftTimeRange($shiftName),
                            ]);
                        }
                    }
                }
            }
            $unitsSlots = $this->request->getPost('units_slots');
            if (is_array($unitsSlots)) {
                foreach ($unitsSlots as $unitId => $shifts) {
                    foreach ($shifts as $shiftName => $slots) {
                        if ($slots > 0) {
                            $staffs = $this->clientScheduleDetailsModel->where(['client_id' => $facilityId, 'unit_id' => $unitId, 'schedule_date' => $date, 'shift_name' => $shiftName])->findAll();
                            foreach ($staffs as $staff) {
                                $this->clientScheduleDetailsModel->update($staff['id'], ['slots' => $slots]);
                            }
                        }
                    }
                }
            }
        }
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Failed to save schedule']);
        }

        $msg = ($isConversion) ? 'Schedule converted and saved successfully' : 'Schedule saved successfully';
        return $this->response->setJSON(['success' => 1, 'message' => $msg]);
    }

    public function save_as_shifts()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $facilityId = $this->session->get('facility_id');
        $date = $this->request->getPost('schedule_date');
        $confirmedUnderstaffed = $this->request->getPost('confirmed_understaffed'); // Data from modal

        if (!$date) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing date']);
        }

        // 1. Fetch all assignments for this date to analyze staffing levels
        $assignments = $this->clientScheduleDetailsModel
            ->select('tbl_client_schedule_details.*, tbl_client_personnel.clinician_type, tbl_client_units.name as unit_name')
            ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_client_schedule_details.personnel_id')
            ->join('tbl_client_units', 'tbl_client_units.id = tbl_client_schedule_details.unit_id')
            ->where([
                'tbl_client_schedule_details.client_id' => $facilityId,
                'tbl_client_schedule_details.schedule_date' => $date
            ])->findAll();

        if (empty($assignments)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'No schedule assignments found for this date. Please save the schedule first.']);
        }

        // 2. Group assignments by Unit and Shift to identify understaffed ones
        $grouped = [];
        foreach ($assignments as $a) {
            $key = $a['unit_id'] . '_' . strtolower($a['shift_name']);
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'unit_id' => $a['unit_id'],
                    'unit_name' => $a['unit_name'],
                    'shift_name' => strtolower($a['shift_name']),
                    'slots' => $a['slots'],
                    'assigned_count' => 0,
                    'staff' => []
                ];
            }
            $grouped[$key]['assigned_count']++;
            $grouped[$key]['staff'][] = $a;
        }

        // 3. Check for Understaffed Shifts (unless already confirmed)
        if (!$confirmedUnderstaffed) {
            $understaffedShifts = [];
            $onboarding = $this->facilityOnboardingSettingsModel->where('client_id', $facilityId)->first();
            $averageRate = ($onboarding && !empty($onboarding['average_rate'])) ? $onboarding['average_rate'] : 0;
            $shiftTypes = ($this->shiftTypesModel ?? new \App\Models\ShiftTypesModel())->where('status', 1)->findAll();

            foreach ($grouped as $g) {
                if ($g['assigned_count'] < $g['slots']) {
                    $understaffedShifts[] = [
                        'unit_id' => $g['unit_id'],
                        'unit_name' => $g['unit_name'],
                        'shift_name' => $g['shift_name'],
                        'shift_time_display' => $this->getShiftTimeRange($g['shift_name']),
                        'slots_total' => $g['slots'],
                        'slots_taken' => $g['assigned_count'],
                        'slots_remaining' => $g['slots'] - $g['assigned_count'],
                        'average_rate' => $averageRate,
                        'date' => $date
                    ];
                }
            }

            if (!empty($understaffedShifts)) {
                return $this->response->setJSON([
                    'success' => 1,
                    'understaffed' => true,
                    'shifts' => $understaffedShifts,
                    'shift_types' => $shiftTypes
                ]);
            }
        }

        // 4. Proceed with Synchronization
        $db = \Config\Database::connect();
        $db->transStart();

        // Process understaffed overrides if provided
        $overrides = [];
        if ($confirmedUnderstaffed) {
            foreach ($confirmedUnderstaffed as $ov) {
                $overrides[$ov['unit_id'] . '_' . strtolower($ov['shift_name'])] = $ov;
            }
        }

        foreach ($grouped as $key => $g) {
            $unitId = $g['unit_id'];
            $shiftName = $g['shift_name'];
            $override = $overrides[$key] ?? null;

            $slots = ($override) ? $override['slots'] : $g['slots'];
            $rate = ($override) ? $override['rate'] : null; // Will fetch average if still null
            $shiftType = ($override) ? $override['shift_type'] : 1;
            $posted = ($override && isset($override['posted']) && $override['posted'] == 'true') ? 1 : 0;

            $startTime = '';
            $endTime = '';
            $endDate = $date;

            switch ($shiftName) {
                case 'day':
                    $startTime = '07:00:00';
                    $endTime = '15:00:00';
                    break;
                case 'mid':
                case 'evening':
                    $startTime = '15:00:00';
                    $endTime = '23:00:00';
                    break;
                case 'night':
                    $startTime = '23:00:00';
                    $endTime = '07:00:00';
                    $endDate = date('Y-m-d', strtotime($date . ' + 1 day'));
                    break;
            }

            if (!$rate) {
                $onboarding = $this->facilityOnboardingSettingsModel->where('client_id', $facilityId)->first();
                $rate = ($onboarding && !empty($onboarding['average_rate'])) ? $onboarding['average_rate'] : 0;
            }

            // Create or Find Shift
            $shift = $this->shiftsModel->where([
                'client_id' => $facilityId,
                'unit_id' => $unitId,
                'start_date' => $date,
                'shift_start_time' => $startTime
            ])->first();

            if (!$shift) {
                $shiftId = $this->shiftsModel->insert([
                    'client_id' => $facilityId,
                    'unit_id' => $unitId,
                    'start_date' => $date,
                    'end_date' => $endDate,
                    'shift_start_time' => $startTime,
                    'shift_end_time' => $endTime,
                    'shift_type' => $shiftType,
                    'rate' => $rate,
                    'slots' => $slots,
                    'status' => 1,
                    'posted' => $posted
                ]);
            } else {
                $shiftId = $shift['id'];
                // Update slots/posted if override provided?
                if ($override) {
                    $this->shiftsModel->update($shiftId, [
                        'slots' => $slots,
                        'posted' => $posted,
                        'rate' => $rate,
                        'shift_type' => $shiftType
                    ]);
                }
            }

            // Link personnel
            foreach ($g['staff'] as $staff) {
                $exists = $this->shiftCliniciansModel->where([
                    'shift_id' => $shiftId,
                    'personnel_id' => $staff['personnel_id']
                ])->first();

                if (!$exists) {
                    $this->shiftCliniciansModel->insert([
                        'client_id' => $facilityId,
                        'shift_id' => $shiftId,
                        'clinician_id' => 0,
                        'personnel_id' => $staff['personnel_id'],
                        'status' => 10,
                        'shift_status' => 0,
                        'pcc_status' => 10
                    ]);
                }
            }
        }

        // Update schedule upload status
        $this->clientScheduleUploadModel->where([
            'client_id' => $facilityId,
            'schedule_date' => $date
        ])->set(['status' => 20])->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Failed to synchronize schedules as shifts']);
        }

        return $this->response->setJSON(['success' => 1, 'message' => 'Schedule successfully synchronized as shifts']);
    }

    private function getShiftTimeRange($shiftName)
    {
        switch (strtolower($shiftName)) {
            case 'day':
                return '07:00 AM - 03:00 PM';
            case 'mid':
            case 'evening':
                return '03:00 PM - 11:00 PM';
            case 'night':
                return '11:00 PM - 07:00 AM';
            default:
                return '';
        }
    }

    public function get_personnel()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $facilityId = $this->session->get('facility_id');
        $date = $this->request->getPost('date');
        $isPast = $date < date('Y-m-d');

        // Check upload status to determine source
        $upload = $this->clientScheduleUploadModel->where(['client_id' => $facilityId, 'schedule_date' => $date])->first();
        $status = $upload ? $upload['status'] : 10;

        if ($status == 20) {
            // For past dates, we get personnel from actual shifts (could be internal or external)
            $schedules = $this->shiftCliniciansModel
                ->select('tbl_shift_clinicians.personnel_id, tbl_shift_clinicians.clinician_id, 
                         tbl_client_personnel.first_name as staff_first, tbl_client_personnel.last_name as staff_last, 
                         tbl_clinicians.name as clinician_name, 
                         tbl_clinician_types.name as staff_type, staff_clinician_type.name as ext_clinician_type,
                         tbl_clinician_types.grouping as staff_group, staff_clinician_type.grouping as ext_group,
                         tbl_shifts.unit_id, tbl_shifts.shift_start_time')
                ->join('tbl_shifts', 'tbl_shifts.id = tbl_shift_clinicians.shift_id')
                ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_shift_clinicians.personnel_id', 'left')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_clinicians.clinician_id', 'left')
                ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_client_personnel.clinician_type', 'left')
                ->join('tbl_clinician_types as staff_clinician_type', 'staff_clinician_type.id = tbl_clinicians.type', 'left')
                ->where([
                    'tbl_shift_clinicians.client_id' => $facilityId,
                    'tbl_shifts.start_date' => $date
                ])
                ->findAll();

            // Map the data for consistent frontend usage
            foreach ($schedules as &$s) {
                $s['id'] = null; // No record ID for deletion since these are from shifts

                // Name resolution
                if ($s['clinician_id'] > 0) {
                    $nameParts = explode(' ', $s['clinician_name']);
                    $s['first_name'] = $nameParts[0] ?? '';
                    $s['last_name'] = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
                    $s['clinician_type_name'] = $s['ext_clinician_type'];
                    $s['clinician_type_group'] = $s['ext_group'];
                } else {
                    $s['first_name'] = $s['staff_first'];
                    $s['last_name'] = $s['staff_last'];
                    $s['clinician_type_name'] = $s['staff_type'];
                    $s['clinician_type_group'] = $s['staff_group'];
                }

                $startTime = $s['shift_start_time'];
                if ($startTime == '07:00:00') {
                    $s['shift_name'] = 'day';
                } elseif ($startTime == '15:00:00') {
                    $s['shift_name'] = 'mid';
                } elseif ($startTime == '23:00:00') {
                    $s['shift_name'] = 'night';
                } else {
                    $s['shift_name'] = 'day'; // Default
                }
            }

            // Sort by clinician type grouping (nurse first and then gna), then clinician type name
            usort($schedules, function ($a, $b) {
                if (($a['clinician_type_group'] ?? '') !== ($b['clinician_type_group'] ?? '')) {
                    return strcmp($b['clinician_type_group'] ?? '', $a['clinician_type_group'] ?? ''); // DESC
                }
                return strcmp($a['clinician_type_name'] ?? '', $b['clinician_type_name'] ?? ''); // ASC
            });
        } else {
            // Sort by clinician type grouping (nurse first and then gna), then clinician type name
            $schedules = $this->clientScheduleDetailsModel
                ->select('tbl_client_schedule_details.*, tbl_client_personnel.first_name, tbl_client_personnel.last_name, tbl_clinician_types.name as clinician_type_name, tbl_clinician_types.grouping as clinician_type_group')
                ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_client_schedule_details.personnel_id')
                ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_client_personnel.clinician_type')
                ->where([
                    'tbl_client_schedule_details.client_id' => $facilityId,
                    'tbl_client_schedule_details.schedule_date' => $date
                ])
                ->orderBy('tbl_clinician_types.grouping', 'DESC')
                ->orderBy('tbl_clinician_types.name', 'ASC')
                ->findAll();
        }

        // Check if locked
        $isLocked = ($isPast || $status == 20);
        return $this->response->setJSON([
            'success' => 1,
            'data' => $schedules,
            'is_locked' => $isLocked
        ]);
    }

    public function delete_personnel()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $id = $this->request->getPost('id');
        $facilityId = $this->session->get('facility_id');

        $record = $this->clientScheduleDetailsModel->find($id);
        if ($record && $record['client_id'] == $facilityId) {
            $upload = $this->clientScheduleUploadModel->where(['client_id' => $facilityId, 'schedule_date' => $record['schedule_date']])->first();
            if ($upload && $upload['status'] == 20) {
                return $this->response->setJSON(['success' => 0, 'message' => 'This schedule is synchronized as shifts and is now locked for editing.']);
            }
            $this->clientScheduleDetailsModel->delete($id);
            return $this->response->setJSON(['success' => 1, 'message' => 'Staff removed from schedule']);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Record not found']);
    }

    public function get_all_personnel()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $facilityId = $this->session->get('facility_id');

        $personnel = $this->clientPersonnelModel
            ->select('tbl_client_personnel.*, tbl_clinician_types.name as clinician_type_name, tbl_clinician_types.grouping as clinician_type_group')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_client_personnel.clinician_type', 'left')
            ->where('tbl_client_personnel.client_id', $facilityId)
            ->where('tbl_client_personnel.type', 7)
            ->where('tbl_client_personnel.status', 1)
            ->findAll();

        return $this->response->setJSON(['success' => 1, 'data' => $personnel]);
    }

    public function upload()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $validationRule = [
            'file' => [
                'label' => 'PDF File',
                'rules' => [
                    'uploaded[file]',
                    'mime_in[file,application/pdf]',
                    'max_size[file,10240]', // 10MB
                ],
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['success' => 0, 'message' => $this->validator->getErrors()]);
        }

        $file = $this->request->getFile('file');
        $shiftDate = $this->request->getPost('shift_date');
        $facilityId = $this->session->get('facility_id');

        if ($file->isValid() && !$file->hasMoved()) {
            if (!is_dir(FCPATH . 'uploads/schedules')) {
                mkdir(FCPATH . 'uploads/schedules', 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/schedules', $newName);
            $filePath = 'uploads/schedules/' . $newName;

            // Create draft upload record
            $uploadId = $this->clientScheduleUploadModel->insert([
                'client_id' => $facilityId,
                'schedule_date' => $shiftDate,
                'uploaded_by' => $this->session->get('id'),
                'file_path' => $filePath,
                'filename' => $file->getClientName(),
                'status' => 5 // For Review
            ]);

            return $this->response->setJSON([
                'success' => 1,
                'message' => 'Schedule uploaded successfully',
                'redirect' => base_url('facility/schedules/preview/' . $uploadId)
            ]);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Failed to upload schedule']);
    }
    public function list()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON([]);
        }

        $schedules = $this->clientScheduleUploadModel
            ->where('client_id', $this->session->get('facility_id'))
            ->findAll();

        $events = [];
        foreach ($schedules as $schedule) {
            $bg = "#ffeb003b";
            if ($schedule['status'] == 20 || $schedule['status'] == 30) {
                $bg = "#8fdf82";
            }

            $html = '<div class="uploaded-schedule"><i class="fa fa-file-pdf"></i></div>';
            $html .= '<div class="uploaded-schedule-actions">';
            // Always show download link for official schedules or saved manual ones

            if ($schedule['status'] != 5) {
                if ($schedule['schedule_date'] < date("Y-m-d")) {
                    $html .= '<a href="' . base_url('facility/schedules/download/' . $schedule['id']) . '" data-toggle="tooltip" data-placement="top" title="Download PDF"><i class="fa fa-download"></i></a>';
                }
            }


            if ($schedule['status'] == 5) {
                if ($schedule['schedule_date'] >= date("Y-m-d")) {
                    $html .= '<a href="' . base_url('facility/schedules/preview/' . $schedule['id']) . '" target="_blank" class="pencil" data-toggle="tooltip" data-placement="top" title="Preview upload"><i class="fa fa-search"></i></a>';
                } else {
                    $html .= '<a href="' . base_url('facility/schedules/view?date=' . $schedule['schedule_date']) . '" target="_blank" class="pencil" data-toggle="tooltip" data-placement="top" title="View schedule"><i class="fa fa-eye"></i></a>';
                }
            } else if ($schedule['status'] == 10) {
                if ($schedule['schedule_date'] >= date("Y-m-d")) {
                    $html .= '<a href="' . base_url('facility/schedules/add?date=' . $schedule['schedule_date']) . '" target="_blank" class="pencil" data-toggle="tooltip" data-placement="top" title="Edit schedule"><i class="fa fa-edit"></i></a>';
                } else {
                    $html .= '<a href="' . base_url('facility/schedules/view?date=' . $schedule['schedule_date']) . '" target="_blank" class="pencil" data-toggle="tooltip" data-placement="top" title="View schedule"><i class="fa fa-eye"></i></a>';
                }
            } else {
                $html .= '<a href="' . base_url('facility/schedules/view?date=' . $schedule['schedule_date']) . '" target="_blank" class="pencil" data-toggle="tooltip" data-placement="top" title="View schedule"><i class="fa fa-eye"></i></a>';
            }
            $html .= '</div>';


            $events[] = [
                'id' => $schedule['id'],
                'title' => $schedule['filename'],
                'start' => $schedule['schedule_date'],
                'display' => 'background',
                'allDay' => true,
                'extendedProps' => [
                    'file_path' => $schedule['file_path'],
                    'html' => $html
                ],
                'backgroundColor' => $bg,
                'borderColor' => $bg,
            ];

        }

        return $this->response->setJSON($events);
    }

    // add download schedule function export it via pdf with the format
    public function download($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return redirect()->to('/login');
        }

        $facilityId = $this->session->get('facility_id');
        $schedule = $this->clientScheduleUploadModel->find($id);

        if (!$schedule || $schedule['client_id'] != $facilityId) {
            return redirect()->to('/facility');
        }

        // If it's an uploaded schedule (not status 10), serve the original file
        if ($schedule['status'] != 10 && !empty($schedule['file_path'])) {
            $filePath = FCPATH . $schedule['file_path'];
            if (file_exists($filePath)) {
                return $this->response->download($filePath, null);
            }
        }

        // Otherwise (status 10 or missing file), generate PDF from template
        $date = $schedule['schedule_date'];
        $units = $this->facilityUnitsModel->where('client_id', $facilityId)->findAll();
        $facility = $this->facilityModel->find($facilityId);

        // Fetch all personnel assignments for this date
        $isPast = $date < date('Y-m-d');
        if ($isPast) {
            // For past dates, get from actual shifts
            $assignments = $this->shiftCliniciansModel
                ->select('tbl_shift_clinicians.personnel_id, tbl_shift_clinicians.clinician_id, 
                         tbl_client_personnel.first_name as staff_first, tbl_client_personnel.last_name as staff_last, 
                         tbl_clinicians.name as clinician_name, 
                         tbl_clinician_types.name as staff_type, staff_clinician_type.name as ext_clinician_type,
                         tbl_clinician_types.grouping as staff_group, staff_clinician_type.grouping as ext_group,
                         tbl_shifts.unit_id, tbl_shifts.shift_start_time, tbl_shift_details.name as raw_shift_name')
                ->join('tbl_shifts', 'tbl_shifts.id = tbl_shift_clinicians.shift_id')
                ->join('tbl_shift_types as tbl_shift_details', 'tbl_shift_details.id = tbl_shifts.shift_type', 'left')
                ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_shift_clinicians.personnel_id', 'left')
                ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_shift_clinicians.clinician_id', 'left')
                ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_client_personnel.clinician_type', 'left')
                ->join('tbl_clinician_types as staff_clinician_type', 'staff_clinician_type.id = tbl_clinicians.type', 'left')
                ->where([
                    'tbl_shift_clinicians.client_id' => $facilityId,
                    'tbl_shifts.start_date' => $date
                ])
                ->findAll();

            foreach ($assignments as &$s) {
                // Name resolution
                if ($s['clinician_id'] > 0) {
                    $nameParts = explode(' ', $s['clinician_name']);
                    $s['first_name'] = $nameParts[0] ?? '';
                    $s['last_name'] = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
                    $s['clinician_type_name'] = $s['ext_clinician_type'];
                    $s['clinician_type_group'] = $s['ext_group'];
                } else {
                    $s['first_name'] = $s['staff_first'];
                    $s['last_name'] = $s['staff_last'];
                    $s['clinician_type_name'] = $s['staff_type'];
                    $s['clinician_type_group'] = $s['staff_group'];
                }

                $startTime = $s['shift_start_time'];
                if ($startTime == '07:00:00') {
                    $s['shift_name'] = 'day';
                } elseif ($startTime == '15:00:00') {
                    $s['shift_name'] = 'mid';
                } elseif ($startTime == '23:00:00') {
                    $s['shift_name'] = 'night';
                } else {
                    $s['shift_name'] = strtolower($s['raw_shift_name'] ?? 'day');
                }
            }

            // Sort
            usort($assignments, function ($a, $b) {
                if (($a['clinician_type_group'] ?? '') !== ($b['clinician_type_group'] ?? '')) {
                    return strcmp($b['clinician_type_group'] ?? '', $a['clinician_type_group'] ?? ''); // DESC
                }
                return strcmp($a['clinician_type_name'] ?? '', $b['clinician_type_name'] ?? ''); // ASC
            });
        } else {
            // Future/Manual
            $assignments = $this->clientScheduleDetailsModel
                ->select('tbl_client_schedule_details.*, tbl_client_personnel.first_name, tbl_client_personnel.last_name, tbl_clinician_types.name as clinician_type_name, tbl_clinician_types.grouping as clinician_type_group')
                ->join('tbl_client_personnel', 'tbl_client_personnel.id = tbl_client_schedule_details.personnel_id')
                ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_client_personnel.clinician_type', 'left')
                ->where([
                    'tbl_client_schedule_details.client_id' => $facilityId,
                    'tbl_client_schedule_details.schedule_date' => $date
                ])
                ->orderBy('tbl_clinician_types.grouping', 'DESC')
                ->orderBy('tbl_clinician_types.name', 'ASC')
                ->orderBy('tbl_client_personnel.first_name', 'ASC')
                ->findAll();
        }

        $groupedAssignments = [];
        foreach ($assignments as $assignment) {
            $unitId = $assignment['unit_id'];
            $shift = strtolower($assignment['shift_name']);
            // if ($shift == 'mid')
            //     $shift = 'evening'; // Map to consistent internal key if needed, or keep both

            if (!isset($groupedAssignments[$unitId])) {
                $groupedAssignments[$unitId] = [
                    'day' => [],
                    'mid' => [],
                    'evening' => [], // Added evening
                    'night' => []
                ];
            }
            if (!isset($groupedAssignments[$unitId][$shift])) {
                $groupedAssignments[$unitId][$shift] = [];
            }
            $groupedAssignments[$unitId][$shift][] = $assignment;
        }

        $data = [
            'facility' => $facility,
            'units' => $units,
            'date' => $date,
            'groupedAssignments' => $groupedAssignments
        ];

        $html = view('facility/scheduler/pdf_template', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Schedule_' . $date . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function preview($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return redirect()->to('/login');
        }

        $facilityId = $this->session->get('facility_id');
        $upload = $this->clientScheduleUploadModel->find($id);

        if (!$upload || $upload['client_id'] != $facilityId) {
            return redirect()->to('/facility/schedules')->with('error', 'Schedule not found or unauthorized.');
        }

        $filePath = FCPATH . $upload['file_path'];
        if (!file_exists($filePath)) {
            return redirect()->to('/facility/schedules')->with('error', 'PDF file not found on server.');
        }

        $parsedData = $this->parse_schedule_pdf($filePath);

        // Check DB existence for Units and Personnel
        if (!empty($parsedData['units'])) {
            foreach ($parsedData['units'] as $unitName => &$shiftData) {
                // Unit check
                $unitExists = $this->facilityUnitsModel->where(['client_id' => $facilityId, 'name' => $unitName])->first();
                $shiftData['db_exists'] = !empty($unitExists);

                // Personnel check
                foreach (['Day', 'Mid', 'Night'] as $sKey) {
                    if (!empty($shiftData[$sKey])) {
                        foreach ($shiftData[$sKey] as &$staff) {
                            $nameParts = explode(' ', trim($staff['name'] ?? ''));
                            $fName = $nameParts[0] ?? '';
                            $lName = (count($nameParts) > 1) ? implode(' ', array_slice($nameParts, 1)) : '';

                            $personExists = $this->clientPersonnelModel->where([
                                'client_id' => $facilityId,
                                'first_name' => $fName,
                                'last_name' => $lName
                            ])->first();
                            $staff['db_exists'] = !empty($personExists);
                        }
                    }
                }
            }
        }

        $facility = $this->facilityModel->find($facilityId);
        $clinicianTypes = ($this->clinicianTypesModel ?? new \App\Models\ClinicianTypesModel())->where('status', 1)->findAll();

        $data = [
            'facility' => $facility,
            'clinicianTypes' => $clinicianTypes,
            'selectedDate' => $upload['schedule_date'],
            'parsedData' => $parsedData,
            'raw_text' => $parsedData['raw_text'] ?? '',
            'upload_id' => $id,
            'session' => $this->session,
            'page' => 'schedules'
        ];

        return view('components/header', [
            'title' => 'PDF Preview',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => [
                'title' => 'PDF Preview',
                'description' => 'Review your uploaded PDF schedule before saving.',
                'image' => IMG_URL . ''
            ],
            'styles' => [
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_scheduler_profile',
            ],
            'session' => $this->session
        ])
            . view('facility/scheduler/preview', $data)
            . view('components/scripts_render', [
                'scripts' => [
                    'https://code.jquery.com/jquery-3.5.1.min.js',
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/pages/facility/scheduler/facility_scheduler_preview.min.js',
                ]
            ])
            . view('components/footer');
    }

    public function parse($id)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('facility_id') == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized']);
        }

        $upload = $this->clientScheduleUploadModel->find($id);
        if (!$upload || $upload['client_id'] != $this->session->get('facility_id')) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Schedule not found']);
        }

        $filePath = FCPATH . $upload['file_path'];
        if (!file_exists($filePath)) {
            return $this->response->setJSON(['success' => 0, 'message' => 'File not found on server']);
        }

        $parsedData = $this->parse_schedule_pdf($filePath);

        return $this->response->setJSON([
            'success' => 1,
            'data' => $parsedData,
            'message' => count($parsedData) > 0 ? 'Schedule parsed successfully' : 'No data could be extracted from this image. Ensure the image is clear and contains legible text.'
        ]);
    }

    //do not use OCR, try to use php pdf parser
    private function parse_schedule_pdf($filePath)
    {
        $extractedData = [
            'date' => null,
            'units' => [],
            'raw_text' => ''
        ];

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $extractedData['raw_text'] = $pdf->getText();

            $currentUnit = 'Default';
            $currentShifts = ['Day', 'Mid', 'Night'];
            $currentShiftIndex = -1;

            // Known positions and roles to help identify staff rows
            $positions = ['RN', 'LPN', 'GNA', 'UM', 'DON', 'ADON', 'Supervisor', 'Orientee', 'CNA', 'CMA', 'Wound Nurse'];
            $positionRegex = '/\b(' . implode('|', $positions) . ')\b/i';

            foreach ($pdf->getPages() as $pageIndex => $page) {
                $text = $page->getText();
                $lines = explode("\n", $text);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line))
                        continue;
                    // 1. Detect Date (e.g., 2026-02-18 or 02/18/2026)
                    if (!$extractedData['date']) {
                        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $line, $matches)) {
                            $extractedData['date'] = $matches[1];
                        } elseif (preg_match('/(\d{2}\/\d{2}\/\d{4})/', $line, $matches)) {
                            $extractedData['date'] = date('Y-m-d', strtotime($matches[1]));
                        }
                    }

                    // 2. Detect Unit Headers
                    if (preg_match('/(Unit\s*\d+|Memory\s*Care|Other\s*Staff)/i', $line, $matches)) {
                        $currentUnit = trim(ucwords(strtolower($matches[0])));
                        $currentShiftIndex = -1; // Reset on unit change
                        if (!isset($extractedData['units'][$currentUnit])) {
                            $extractedData['units'][$currentUnit] = [
                                'Day' => [],
                                'Mid' => [],
                                'Night' => []
                            ];
                        }
                        continue;
                    }

                    // 3. Detect Shift Header (The trigger to increment shift index)
                    // Every "Name Other Position" signifies a new shift block in this sequential layout
                    if (stripos($line, 'Name') !== false && stripos($line, 'Position') !== false) {
                        $currentShiftIndex++;
                        continue;
                    }

                    // 4. Extract Staff Rows (Only if we've found a shift block)
                    if ($currentShiftIndex >= 0 && $currentShiftIndex < 3) {
                        $shiftName = $currentShifts[$currentShiftIndex];

                        // Skip lines that are just headers we already handled
                        if (preg_match('/(Day|Mid|Evening)\s*Shift/i', $line))
                            continue;

                        // Check if it's a staff row by looking for positions
                        if (preg_match($positionRegex, $line, $posMatches)) {
                            $pos = strtoupper($posMatches[1]);

                            // Capture extra markers like "UM" if it's appended (e.g. LPN UM)
                            $fullPos = $pos;
                            if (stripos($line, $pos . ' UM') !== false)
                                $fullPos = $pos . ' UM';
                            if (stripos($line, $pos . ' Supervisor') !== false)
                                $fullPos = $pos . ' Supervisor';

                            // Extract Name: Text before the position usually.
                            // Since it's sequential, the name is likely at the start of the line.
                            // We remove position and common markers from the line to get the name.
                            $name = trim(str_ireplace([$fullPos, 'Other', 'Name', 'Position', 'Orientee', 'ShiftMed'], '', $line));
                            $name = preg_replace('/[^a-zA-Z\s,]/', '', $name);

                            $other = '';
                            if (stripos($line, 'Orientee') !== false)
                                $other = 'Orientee';
                            elseif (stripos($line, 'ShiftMed') !== false)
                                $other = 'ShiftMed';
                            elseif (stripos($line, 'UM') !== false && stripos($fullPos, 'UM') === false)
                                $other = 'UM';

                            if (!empty($name)) {
                                $extractedData['units'][$currentUnit][$shiftName][] = [
                                    'name' => trim($name),
                                    'position' => $fullPos,
                                    'other' => $other
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'PDF Parsing Error: ' . $e->getMessage());
        }

        return $extractedData;
    }

}
