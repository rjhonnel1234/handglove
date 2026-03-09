<?php

namespace App\Models;

use CodeIgniter\Model;

class AccessMatrixModel extends Model
{
    protected $table            = 'tbl_access_matrix';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['access', 'roleId', 'isDeleted', 'createdBy', 'updatedBy'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdDtm';
    protected $updatedField  = 'updatedDtm';

    // Validation
    protected $validationRules      = [
        'roleId' => 'required|is_unique[tbl_access_matrix.roleId,id,{id}]',
        'access' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
}
