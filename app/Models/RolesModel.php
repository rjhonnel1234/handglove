<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $table            = 'tbl_roles';
    protected $primaryKey       = 'roleId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['role', 'status', 'isDeleted', 'createdBy', 'updatedBy'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdDtm';
    protected $updatedField  = 'updatedDtm';

    // Validation
    protected $validationRules      = [
        'role' => 'required|min_length[3]|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}
