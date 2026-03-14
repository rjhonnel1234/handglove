<?php

namespace App\Models;
use CodeIgniter\Model;
 
class FacilityOnboardingSettingsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_client_onboarding_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['client_id', 'timezone', 'clock_in', 'access', 'clock_out_approval', 'task_delay', 'back_up_approval', 'phone', 'allow_overtime', 'accepted_per_diem_network', 'total_beds', 'average_census', 'average_rate', 'bonus'];

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


}
