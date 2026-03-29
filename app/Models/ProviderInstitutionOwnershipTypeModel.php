<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderInstitutionOwnershipTypeModel extends Model
{
    protected $table            = 'tbl_provider_institution_ownership_type';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name'];
}
