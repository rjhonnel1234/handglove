<?php

namespace App\Models;
use CodeIgniter\Model;
 
class ShiftRequestsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_client_shift_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['client_id', 'shift_id', 'clinician_id', 'status', 'from_callout', 'bonus', 'replacing_clinician_id'];

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


    public $status_mapping_facility_pov = [
        10 => '<div class="alert alert-secondary text-center" role="alert">Awaiting response</div>',
        15 => 'Applied',
        20 => '<div class="alert alert-success text-center" role="alert">Accepted</div>',
        100 => '<div class="alert alert-danger text-center" role="alert">Declined</div>',
    ];
    public $status_mapping_clinician_pov = [
        10 => 'Requested',
        15 => '<div class="alert alert-secondary text-center" role="alert">Awating response</div>',
        20 => '<div class="alert alert-success text-center" role="alert">Accepted</div>',
        100 => '<div class="alert alert-danger text-center" role="alert">Declined</div>'
    ];

    function getRequests($clinician_id){
        $db = db_connect();
        $db      = \Config\Database::connect();
        $requestArr = $db->table('tbl_client_shift_requests')
                        ->where('tbl_client_shift_requests.clinician_id', $clinician_id)
                        ->orderBy('tbl_client_shift_requests.shift_id', 'DESC')
                        ->get()
                        ->getResult();


        $result = [];
        foreach($requestArr as $request){
            $builder = $db->table('tbl_shifts');
            $query = $builder->select('tbl_shifts.*, tbl_shift_types.name as type_name, tbl_client_units.name as unit_name, tbl_client_units.census as unit_census')
                            ->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner')
                            ->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner')
                            ->where('tbl_shifts.id', $request->shift_id)
                            ->get();

            $arr = $query->getResultArray();
            $arr[0]['request_id'] = $request->id;
            $arr[0]['request_status'] = $request->status;
            $result[] = $arr[0];
        }

        return $result;
    }

}
