<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFromCalloutToShiftClinicians extends Migration
{
    public function up()
    {
        $fields = [
            'from_callout' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'status'
            ],
        ];
        $this->forge->addColumn('tbl_shift_clinicians', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_shift_clinicians', 'from_callout');
    }
}
