<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPostedToShifts extends Migration
{
    public function up()
    {
        $fields = [
            'posted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'status'
            ],
        ];
        $this->forge->addColumn('tbl_shifts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_shifts', 'posted');
    }
}
