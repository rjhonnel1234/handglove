<?php

namespace App\Models;

use CodeIgniter\Model;

class PayStubDetailsModel extends Model
{
    protected $table            = 'tbl_pay_stub_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['pay_stub_id', 'shift_id'];

    public function getStubShifts($pay_stub_id)
    {
        return $this->select('tbl_pay_stub_details.*, tbl_shifts.start_date, tbl_shifts.shift_start_time, tbl_shifts.shift_end_time, tbl_shifts.rate')
                    ->join('tbl_shifts', 'tbl_shifts.id = tbl_pay_stub_details.shift_id')
                    ->where('pay_stub_id', $pay_stub_id)
                    ->findAll();
    }
}
