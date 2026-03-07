<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastActiveAtToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'last_active_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'online_status'
            ],
        ];
        $this->forge->addColumn('tbl_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_users', 'last_active_at');
    }
}
