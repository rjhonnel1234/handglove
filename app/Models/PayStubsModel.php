<?php

namespace App\Models;

use CodeIgniter\Model;

class PayStubsModel extends Model
{
    protected $table            = 'tbl_pay_stubs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'clinician_id', 
        'period_id', 
        'total_hours', 
        'gross_pay', 
        'total_deductions', 
        'net_pay', 
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getClinicianStubs($clinician_id)
    {
        return $this->where('clinician_id', $clinician_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
