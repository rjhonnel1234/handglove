<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReplacingClinicianToShiftRequests extends Migration
{
    public function up()
    {
        $fields = [
            'replacing_clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'clinician_id'
            ],
        ];
        $this->forge->addColumn('tbl_client_shift_requests', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_client_shift_requests', 'replacing_clinician_id');
    }
}
