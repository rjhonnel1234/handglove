<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftClinicianUpdatesModel extends Model
{
    protected $table            = 'tbl_shift_clinician_updates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['shift_clinician_id', 'status_text', 'location_text', 'minutes_away', 'is_arrival', 'created_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at needed for history log
}
