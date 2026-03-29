<?php

namespace App\Models;
use CodeIgniter\Model;
 
class FacilityModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['leads_id', 'provider_id', 'company_logo', 'company_name', 'company_address', 'zip_code', 'company_email', 'company_number', 'agencies', 'created_datetime', 'status', 'timezone', 'census', 'country_id', 'state_id'];

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
        0 => 'Inactive',
        10 => 'Active',
    ];

    public $status_badge_color = [
        0 => 'bg-danger',
        10 => 'bg-success',
    ];
}
