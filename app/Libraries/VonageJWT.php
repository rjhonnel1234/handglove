<?php

namespace App\Libraries;

use Firebase\JWT\JWT;

class VonageJWT
{
    public static function generate()
    {
        $privateKey = file_get_contents(ROOTPATH . 'private.key');
        $payload = [
            "application_id" => getenv('vonage.application_id'),
            "iat" => time(),
            "exp" => time() + 3600 // 1 hour
        ];
        return JWT::encode($payload, $privateKey, 'RS256');
    }
}
