<?php

namespace App\Models;
use CodeIgniter\Model;
 
class FacilityVoteDetailsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_client_voting_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['voting_id', 'clinician_id', 'votes'];

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
    public function getTopCliniciansLastWeek($grouping, $limit = 2)
    {
        $lastWeekStart = date('Y-m-d', strtotime('last monday -7 days'));
        $lastWeekEnd = date('Y-m-d', strtotime('last sunday'));

        return $this->select('tbl_clinicians.name, tbl_clients.company_name, SUM(tbl_client_voting_details.votes) as total_votes')
            ->join('tbl_client_voting', 'tbl_client_voting.id = tbl_client_voting_details.voting_id')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_voting_details.clinician_id')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type')
            ->join('tbl_clients', 'tbl_clients.id = tbl_client_voting.client_id')
            ->where('tbl_clinician_types.grouping', $grouping)
            ->where('tbl_client_voting.voting_start_date >=', $lastWeekStart)
            ->where('tbl_client_voting.voting_end_date <=', $lastWeekEnd)
            ->groupBy('tbl_clinicians.id, tbl_clients.id')
            ->orderBy('total_votes', 'DESC')
            ->findAll($limit);
    }
}
