<?php

namespace App\Models;

use CodeIgniter\Model;

class PayStubDeductionsModel extends Model
{
    protected $table            = 'tbl_pay_stub_deductions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'pay_stub_id', 
        'tax_type_id', 
        'tax_name', 
        'percentage', 
        'amount'
    ];

    public function getStubDeductions($pay_stub_id)
    {
        return $this->where('pay_stub_id', $pay_stub_id)->findAll();
    }
}
