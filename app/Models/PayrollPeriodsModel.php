<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollPeriodsModel extends Model
{
    protected $table            = 'tbl_payroll_periods';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['start_date', 'end_date', 'pay_date', 'status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLatestPeriod()
    {
        return $this->orderBy('end_date', 'DESC')->first();
    }
}
