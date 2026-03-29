<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderInstitutionTypeModel extends Model
{
    protected $table            = 'tbl_provider_institution_type';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name'];
}
