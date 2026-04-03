<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Libraries\VonageService;
use App\Libraries\PinService;
use App\Models\UserModel;
use App\Models\NotificationsModel;
use App\Models\MessageModel;
use App\Models\CliniciansModel;
use App\Models\ShiftsTimekeepingModel;
use App\Models\ShiftsModel;
use App\Models\InvoicesModel;
use App\Models\SettingsModel;
use App\Models\ShiftCliniciansModel;
use App\Models\ShiftClinicianUpdatesModel;

class Vonage extends BaseController
{
    use ResponseTrait;

    protected $vonage;

    public function __construct()
    {
        $this->vonage       = new VonageService();
        $this->pinService          = new PinService();
        $this->messageModel = new MessageModel();
    }

    /**
     * Send SMS or WhatsApp message (API endpoint)
     * POST: /vonage/send
     */
    public function send()
    {
        $to = $this->request->getPost('to');
        $text = $this->request->getPost('text');
        $channel = $this->request->getPost('channel') ?? 'sms';

        if (empty($to) || empty($text)) {
            return $this->fail('Recipient number and text are required', 400);
        }

        $responseData = $this->vonage->sendMessage($to, $text, $channel);

        return $this->respond($responseData);
    }

    /**
     * Handle inbound messages from Vonage
     * POST: /vonage/inbound
     */
    public function inbound()
    {
        $data = $this->request->getJSON(true);
        if (!$data) return $this->fail('No data received', 400);

        $from = $data['from'] ?? '';
        $text = trim($data['text'] ?? '');
        $channel = $data['channel'] ?? 'sms';

        if (empty($text)) {
            return $this->fail('No text received', 400);
        }

        $parts = explode(' ', $text);
        $command = strtoupper(array_shift($parts));
        $parameters = $parts;

        $responseMessage = '';
        $success = 0;

        switch ($command) {

            case 'ON':
            case 'OFF':
                [$responseMessage, $success] = $this->handleOnlineStatus($from, $command);
                break;
            case 'IN':
            case 'OUT':
                [$responseMessage, $success] = $this->handleClockInOut($from, $command, $parameters);
                break;
            case 'GO':
                [$responseMessage, $success] = $this->handleShiftClinicianUpdates($from, $command, $parameters);
                break;

            default:
                $responseMessage = 'Unknown command';
        }

        // // Reply to sender
        // $this->vonage->sendMessage($from, $responseMessage, $channel);

        // // Save inbound message
        // $this->messageModel->save([
        //     'message_uuid' => $data['message_uuid'] ?? null,
        //     'from_number' => $from,
        //     'to_number' => $data['to'] ?? '',
        //     'message' => $text,
        //     'channel' => $channel,
        //     'direction' => 'inbound',
        //     'status' => 'received'
        // ]);

        return $this->respond([
            'status' => 'received',
            'success' => $success,
            'message' => $responseMessage
        ]);
    }

    /**
     * Status callback
     */
    public function status()
    {
        $data = $this->request->getJSON(true);
        if (!$data) return $this->fail('No data received', 400);

        $uuid = $data['message_uuid'] ?? null;
        $status = $data['status'] ?? null;

        if ($uuid && $status) {
            $message = $this->messageModel->where('message_uuid', $uuid)->first();
            if ($message) {
                $this->messageModel->update($message['id'], ['status' => $status]);
            }
        }

        return $this->respond(['status' => 'updated']);
    }

    private function handleOnlineStatus($from, $command)
    {
        if (empty($from)) {
            return ['Invalid sender', 0];
        }

        $status = ($command === 'ON') ? 1 : 0;

        $userModel = new UserModel();
        $user = $userModel
            ->where('contact_number', $from)
            ->first();

        if (!$user) {
            return ['Number not registered', 0];
        }

        if ((int)$user['online_status'] === (int)$status) {
            return [
                $status ? 'Already ONLINE' : 'Already OFFLINE',
                1
            ];
        }

        $updated = $userModel->update($user['id'], [
            'online_status' => $status
        ]);

        if (!$updated) {
            return ['Update failed', 0];
        }

        if ($status) {
            $notificationsModel = new NotificationsModel();
            $notificationsModel
                ->where('user_id', $user['id'])
                ->set(['is_read' => 1])
                ->update();
        }

        return [
            $status ? 'You are now ONLINE' : 'You are now OFFLINE',
            1
        ];
    }

    private function handleShiftClinicianUpdates($from, $command, $parameters)
    {
        // Validate required parameters
        if (count($parameters) < 2) {
            return ['Missing required parameters.', 0];
        }

        [$shiftId, $status] = $parameters;

        $statusMap = [
            1 => 'Getting Ready',
            2 => 'On the Way',
            3 => 'Arrived'
        ];

        if (!isset($statusMap[$status])) {
            return ['Invalid status value.', 0];
        }

        $minutesAway = 0;
        $isArrival = ($status == 3) ? 1 : 0;

        // Handle "On the Way" case
        if ($status == 2) {
            if (count($parameters) < 3) {
                return ['Missing minutes away parameter.', 0];
            }
            $minutesAway = (int) $parameters[2];
        }

        $shiftCliniciansModel = new ShiftCliniciansModel();

        $shiftClinician = $shiftCliniciansModel
            ->select('id')
            ->where('shift_id', $shiftId)
            ->first();

        if (!$shiftClinician) {
            return ['Shift clinician not found.', 0];
        }

        $shiftClinicianId = $shiftClinician['id'];

        $shiftClinicianUpdatesModel = new ShiftClinicianUpdatesModel();

        $shiftClinicianUpdatesModel->insert([
            'shift_clinician_id' => $shiftClinicianId,
            'status_text'        => $statusMap[$status],
            'location_text'      => '',
            'minutes_away'       => $minutesAway,
            'is_arrival'         => $isArrival
        ]);

        if ($isArrival) {
            $shiftCliniciansModel->update($shiftClinicianId, [
                'status' => 10
            ]);
        }

        return ['Update successfully.', 1];
    }

    private function handleClockInOut($from, $command, $parameters)
    {
        $punch_type = ($command === 'IN') ? 10 : 20;
        $shiftId = $parameters[0];
        $pin = $parameters[1];

        if (empty($pin)) {
            return['No PIN received', 0];
        }

        $facilities = $this->pinService->getAllFacilities();
        $clinModel = new CliniciansModel;
        $profileData = $clinModel
                        ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                        ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                        ->where('tbl_clinicians.contact_number', $from)
                        ->first();

        if(!empty($profileData)){
            foreach ($facilities as $facility) {
                if ($this->pinService->verifyPin($facility['ip_address'], $pin)) {
                    $shiftTimekeepingModel = new ShiftsTimekeepingModel;
                    $punch_datetime = date("Y-m-d H:i:s");
                    $item = [
                        'shift_id' => $shiftId,
                        'clinician_id' => $profileData['id'],
                        'punch_datetime' => $punch_datetime,
                        'punch_type' => $punch_type,
                        'ip_address' => $pin
                    ];
                    
                    $save = $shiftTimekeepingModel->save($item);

                    if ($save) {
                        //generate invoice
                        $punchIn = $shiftTimekeepingModel
                                            ->where('shift_id', $shiftId)
                                            ->where('clinician_id', $profileData['id'])
                                            ->where('punch_type', 10)
                                            ->orderBy('punch_datetime', 'asc')
                                            ->first();

                        if(!empty($punchIn) && $punch_type === 20){
                            $shiftsModel = new ShiftsModel();
                            $shiftDetails = $shiftsModel->find($shiftId);

                            if(!empty($shiftDetails)){
                                $start = new  \DateTime($punchIn['punch_datetime']);
                                $end = new  \DateTime($punch_datetime);
                                $interval = $start->diff($end);
                                
                                $totalHours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
                                $rate = $shiftDetails['rate'];
                                $totalAmount = $totalHours * $rate;

                                $invoiceModel = new InvoicesModel();
                                $settingsModel = new SettingsModel();
                                $billingRate = $settingsModel->getSetting('billing_rate', 1.00);

                                $invoiceItem = [
                                    'shift_id'     => $shiftId,
                                    'clinician_id' => $profileData['id'],
                                    'client_id'    => $shiftDetails['client_id'],
                                    'total_hours'  => $totalHours,
                                    'rate'         => $rate,
                                    'billing_rate' => $billingRate,
                                    'total_amount' => $totalHours * $rate * $billingRate,
                                    'status'       => 10 // Pending
                                ];
                                $invoiceModel->save($invoiceItem);

                                return ['Punch Out successful.', 1];
                            }
                        } else {
                            return ['Punch In successful.', 1];
                        }
                    }
                }
            }
        } else {
            return ['Clinician not found', 0];
        }

        return ['PIN invalid or expired', 0];
    }
}