<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusDatetimesToLeads extends Migration
{
    public function up()
    {
        $fields = [
            'presentation_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ],
            'awaiting_contract_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'presentation_datetime',
            ],
            'on_contract_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'awaiting_contract_datetime',
            ],
            'cancelled_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'on_contract_datetime',
            ],
        ];
        $this->forge->addColumn('tbl_leads', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_leads', 'presentation_datetime');
        $this->forge->dropColumn('tbl_leads', 'awaiting_contract_datetime');
        $this->forge->dropColumn('tbl_leads', 'on_contract_datetime');
        $this->forge->dropColumn('tbl_leads', 'cancelled_datetime');
    }
}
