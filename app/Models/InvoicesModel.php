<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoicesModel extends Model
{
    protected $table            = 'tbl_invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['shift_id', 'clinician_id', 'client_id', 'total_hours', 'rate', 'billing_rate', 'total_amount', 'status'];
    protected $useTimestamps    = false; // We use created_at manually via current_timestamp in DB
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
}
