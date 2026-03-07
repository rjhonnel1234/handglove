<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPersonnelToShifts extends Migration
{
    public function up()
    {
        $fields = [
            'personnel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'clinician_id'
            ],
        ];
        $this->forge->addColumn('tbl_shift_clinicians', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_shift_clinicians', 'personnel_id');
    }
}
