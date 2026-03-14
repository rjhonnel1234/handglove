<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFromCalloutToShiftRequests extends Migration
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
        $this->forge->addColumn('tbl_client_shift_requests', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_client_shift_requests', 'from_callout');
    }
}
