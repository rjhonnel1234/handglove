<?php

namespace App\Libraries;

use App\Models\FacilityIpModel;

class PinService
{
    protected $facilityModel;

    public function __construct()
    {
        $this->facilityModel = new FacilityIpModel();
    }

    /**
     * Generate 6-digit PIN for a given IP
     */
    public function generatePin($ip, $offset = 0)
    {
        $interval = 600; // 10 minutes
        $timeWindow = floor(time() / $interval) + $offset;

        $data = $ip . '|' . $timeWindow . '|' . env('pin.secret_key');
        $hash = hash('sha256', $data);
        $pin = substr(preg_replace('/\D/', '', $hash), 0, 6);

        return str_pad($pin, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify PIN for a facility IP
     */
    public function verifyPin($ip, $inputPin)
    {
        $current = $this->generatePin($ip);
        $previous = $this->generatePin($ip, -1);

        return in_array($inputPin, [$current, $previous]);
    }

    /**
     * Check if an IP is whitelisted
     */
    public function isWhitelisted($ip)
    {
        return (bool) $this->facilityModel->where('ip_address', $ip)->first();
    }

    /**
     * Get all whitelisted facilities
     */
    public function getAllFacilities()
    {
        return $this->facilityModel->findAll();
    }

    /**
     * Get current PIN for a request IP
     * Returns null if IP not whitelisted
     */
    public function getPinForRequest($ip)
    {
        // normalize localhost for testing
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }

        if (!$this->isWhitelisted($ip)) {
            return null;
        }

        return $this->generatePin($ip);
    }
}