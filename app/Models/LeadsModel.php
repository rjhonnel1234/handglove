<?php

namespace App\Models;
use CodeIgniter\Model;
 
class LeadsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_leads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'company_name', 'address', 'zip_code', 'email', 'contact_number', 
        'date', 'time', 'agencies', 'reference', 'supervisor', 
        'created_datetime', 'status', 'origin', 'shift_type', 'shift_date', 
        'shift_time_start', 'shift_time_end', 'notes', 'ref_clinician_id', 
        'census', 'features', 'provider_id', 'country', 'state', 
        'booking_date', 'booking_time',
        'presentation_datetime', 'awaiting_contract_datetime', 'on_contract_datetime', 'cancelled_datetime'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];


    public $status_mapping = [
        0 => 'Cold',
        10 => 'Presentation',
        20 => 'Awaiting Contract',
        50 => 'On Contract',
        100 => 'Cancelled',
    ];

    public $origin_mapping = [
        1 => 'Manual',
        2 => 'SMS',
        3 => 'Referral',
        4 => 'Demo Request',
        5 => 'Claim',
    ];
    

    public $status_badge_color = [
        0 => 'bg-secondary',
        10 => 'bg-warning',
        20 => 'bg-primary',
        30 => 'bg-warning',
        40 => 'bg-warning',
        50 => 'bg-success',
        100 => 'bg-danger',
    ];

}
