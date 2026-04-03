<?php

namespace App\Models;

use CodeIgniter\Model;

class FacilityIpModel extends Model
{
    protected $table            = 'tbl_facility_ips';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'ip_address',
        'facility_name',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'ip_address' => 'required|valid_ip|is_unique[facility_ips.ip_address,id,{id}]',
        'facility_name' => 'permit_empty|string|max_length[100]',
    ];

    protected $validationMessages = [
        'ip_address' => [
            'required'  => 'IP address is required',
            'valid_ip'  => 'Invalid IP address',
            'is_unique' => 'IP address already exists',
        ],
    ];

    public function getByIp($ip)
    {
        return $this->where('ip_address', $ip)->first();
    }

    public function isWhitelisted($ip)
    {
        return $this->where('ip_address', $ip)->countAllResults() > 0;
    }
}
