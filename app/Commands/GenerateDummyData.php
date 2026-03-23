<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\CliniciansModel;
use App\Models\ShiftsModel;
use App\Models\ShiftCliniciansModel;
use App\Models\ShiftsTimekeepingModel;
use DateTime;

class GenerateDummyData extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'dummy:generate';
    protected $description = 'Generates dummy shifts and timekeeping for testing.';

    public function run(array $params)
    {
        $clinModel = new CliniciansModel();
        $shiftModel = new ShiftsModel();
        $shiftClinModel = new ShiftCliniciansModel();
        $timekeepingModel = new ShiftsTimekeepingModel();
        $unitModel = new \App\Models\FacilityUnitsModel();

        $clinicians = $clinModel->findAll();
        $clientId = 3;
        $units = $unitModel->where('client_id', $clientId)->findAll();
        $unitIds = !empty($units) ? array_column($units, 'id') : [1];

        $startDate = new DateTime('2026-03-09');
        $endDate = new DateTime('2026-03-22');

        CLI::write("Generating dummy data for " . count($clinicians) . " clinicians...");

        // foreach ($clinicians as $clin) {
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $clinID = $clinicians[array_rand($clinicians)]['id'];

                $dateStr = $currentDate->format('Y-m-d');
                $shiftTypes = [
                    ['start' => '07:00:00', 'end' => '15:00:00', 'next_day' => false],
                    ['start' => '15:00:00', 'end' => '23:00:00', 'next_day' => false],
                    ['start' => '23:00:00', 'end' => '07:00:00', 'next_day' => true],
                ];
                $randomShift = $shiftTypes[array_rand($shiftTypes)];

                $endDateShift = clone $currentDate;
                if ($randomShift['next_day']) {
                    $endDateShift->modify('+1 day');
                }
                $endDateStr = $endDateShift->format('Y-m-d');

                // Create Shift
                $shiftData = [
                    'facility_id'      => $clientId, // facility_id seems to be used as client_id in some places
                    'client_id'        => $clientId,
                    'unit_id'          => $unitIds[array_rand($unitIds)],
                    'start_date'       => $dateStr,
                    'end_date'         => $endDateStr,
                    'shift_start_time' => $randomShift['start'],
                    'shift_end_time'   => $randomShift['end'],
                    'rate'             => rand(25, 70),
                    'status'           => 10, // Active
                ];
                $shiftId = $shiftModel->insert($shiftData);

                if ($shiftId) {
                    // Assign Clinician
                    $shiftClinModel->insert([
                        'shift_id'     => $shiftId,
                        'clinician_id' => $clinID,
                        'client_id'    => $clientId,
                        'status'       => 10, // Accepted
                        'shift_status' => 0, // Not finished
                    ]);

                    // Punch In
                    $timekeepingModel->insert([
                        'shift_id'       => $shiftId,
                        'clinician_id'   => $clinID,
                        'punch_datetime' => $dateStr . ' ' . $randomShift['start'],
                        'punch_type'     => 10,
                        'ip_address'     => '127.0.0.1',
                    ]);
                    
                    // Punch Out
                    $timekeepingModel->insert([
                        'shift_id'       => $shiftId,
                        'clinician_id'   => $clinID,
                        'punch_datetime' => $endDateStr . ' ' . $randomShift['end'],
                        'punch_type'     => 20,
                        'reference'      => 'DUMMY_DATA',
                        'ip_address'     => '127.0.0.1',
                    ]);
                }

                CLI::write("Done for date: " . $dateStr);
                $currentDate->modify('+1 day');
            }
        // }

        CLI::write("Dummy data generation complete!", 'green');
    }
}
