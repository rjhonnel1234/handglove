<?php

namespace App\Models;
use CodeIgniter\Model;
 
class ShiftsModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tbl_shifts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['client_id', 'start_date', 'end_date', 'shift_type', 'shift_start_time', 'shift_end_time', 'slots', 'rate', 'unit_id', 'status', 'posted'];

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

    public $shift_time_range = [
        '07:00 AM - 3:00 PM',
        '03:00 PM - 11:00 PM',
        '11:00 PM - 7:00 AM',
        '7:00 AM - 7:00 PM',
        '7:00 PM - 7:00 AM'
    ];

    function get(){
        $db = db_connect();
        $db      = \Config\Database::connect();
        $builder = $db->table('tbl_shifts');
        $cols = [
            'tbl_shifts.start_date',
            'tbl_shifts.shift_start_time',
            'tbl_shifts.shift_end_time',
            'tbl_shifts.shift_type',
            'tbl_shifts.slots',
            'tbl_shifts.rate',
            'tbl_shifts.unit',
            'tbl_clients.company_name',
            'tbl_shift_types.name as shift_type_name',
            '(SELECT count(id) FROM tbl_shift_clinicians WHERE tbl_shift_clinicians.shift_id = tbl_shifts.id) as slots_taken'
        ];

        $builder->select(implode(",", $cols));
        $builder->join('tbl_clients', 'tbl_clients.id = tbl_shifts.client_id', 'inner');
        $builder->join('tbl_shift_types', 'tbl_shift_types.id = tbl_shifts.shift_type', 'inner');
        $builder->where('(SELECT count(id) FROM tbl_shift_clinicians WHERE tbl_shift_clinicians.shift_id = tbl_shifts.id) < tbl_shifts.slots');
        $query = $builder->get();

    }

    function getJobHistory($clinician_id, $limit = 3)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_shifts');
        
        $cols = [
            'tbl_shifts.id',
            'tbl_shifts.start_date',
            'tbl_shifts.shift_start_time',
            'tbl_shifts.shift_end_time',
            'tbl_shifts.client_id',
            'tbl_clients.company_name',
            'tbl_client_units.name as unit_name',
        ];

        /*
            Subqueries for clock in and clock out to ensure we only get shifts with BOTH punches
            punch_type 10 = clock in
            punch_type 20 = clock out
        */
        $builder->select(implode(",", $cols));
        $builder->join('tbl_clients', 'tbl_clients.id = tbl_shifts.client_id', 'inner');
        $builder->join('tbl_client_units', 'tbl_client_units.id = tbl_shifts.unit_id', 'inner');
        $builder->join('tbl_shift_clinicians', 'tbl_shift_clinicians.shift_id = tbl_shifts.id');
        $builder->where('tbl_shift_clinicians.clinician_id', $clinician_id);
        $builder->orderBy('tbl_shifts.start_date', 'DESC');
        $builder->orderBy('tbl_shifts.shift_start_time', 'DESC');
        $builder->groupBy('tbl_shifts.id');
        $builder->limit($limit);
        
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function hasActiveShift($clientId)
    {
        return $this->where('client_id', $clientId)
            ->where('start_date', date("Y-m-d"))
            ->where('status', 1)
            ->countAllResults() > 0;
    }

    public function hasActiveShiftThisWeek($clientId)
    {
        $startOfWeek = date("Y-m-d", strtotime('monday this week'));
        
        for ($i = 0; $i < 7; $i++) {
            $currentDate = date("Y-m-d", strtotime("$startOfWeek +$i days"));
            $count = $this->where('client_id', $clientId)
                ->where('start_date', $currentDate)
                ->where('status', 1)
                ->countAllResults();
            
            if ($count === 0) {
                return false;
            }
        }

        return true;
    }
}
