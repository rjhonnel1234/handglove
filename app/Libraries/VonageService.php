<?php

namespace App\Libraries;

use App\Models\MessageModel;

class VonageService
{
    protected $model;

    public function __construct()
    {
        $this->model = new MessageModel();
    }

    public function sendMessage($to, $text, $channel = 'sms')
    {
        $from = getenv('vonage.from');
        $mode = getenv('vonage.mode') ?? 'sandbox';

        $isSandbox = $mode === 'sandbox';
        $isWhatsApp = $channel === 'whatsapp';

        if ($isWhatsApp && $isSandbox) {
            $url = 'https://messages-sandbox.nexmo.com/v1/messages';
        } else {
            $url = 'https://api.nexmo.com/v1/messages';
        }


        if ($isSandbox) {
            $authHeader = base64_encode(getenv('vonage.api_key') . ':' . getenv('vonage.api_secret'));
            $headers = [
                "Authorization: Basic $authHeader",
                "Content-Type: application/json",
                "Accept: application/json"
            ];
        } else {
            $jwt = VonageJWT::generate();
            $headers = [
                "Authorization: Bearer $jwt",
                "Content-Type: application/json",
                "Accept: application/json"
            ];
        }

        if ($isSandbox) {
            $payload = [
                "from" => $from,
                "to" => $to,
                "channel" => $isWhatsApp ? "whatsapp" : "sms",
                "message_type" => "text",
                "text" => $text
            ];
        } else {

            $payload = [
                "from" => [
                    "type" => $isWhatsApp ? "whatsapp" : "sms",
                    "number" => $from
                ],
                "to" => [
                    "type" => $isWhatsApp ? "whatsapp" : "sms",
                    "number" => $to
                ],
                "message" => [
                    "content" => [
                        "type" => "text",
                        "text" => $text
                    ]
                ]
            ];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', 'Vonage Error: ' . $error);
            return ['error' => $error];
        }

        $responseData = json_decode($response, true);
        $uuid = $responseData['message_uuid'] ?? ($responseData['messages'][0]['message_uuid'] ?? null);

        // Save message to DB
        $this->model->save([
            'message_uuid' => $uuid,
            'from_number' => $from,
            'to_number' => $to,
            'message' => $text,
            'channel' => $channel,
            'direction' => 'outbound',
            'status' => 'sent',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $responseData;
    }
}
