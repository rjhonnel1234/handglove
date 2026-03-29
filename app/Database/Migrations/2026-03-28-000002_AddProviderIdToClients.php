<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProviderIdToClients extends Migration
{
    public function up()
    {
        $fields = [
            'provider_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'leads_id',
            ],
        ];
        $this->forge->addColumn('tbl_clients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_clients', 'provider_id');
    }
}
