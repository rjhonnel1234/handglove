<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBonusToShiftRequests extends Migration
{
    public function up()
    {
        $fields = [
            'bonus' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'from_callout'
            ],
        ];
        $this->forge->addColumn('tbl_client_shift_requests', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_client_shift_requests', 'bonus');
    }
}
