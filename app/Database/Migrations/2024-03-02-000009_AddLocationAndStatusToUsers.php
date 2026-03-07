<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocationAndStatusToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
                'after'      => 'token_datetime'
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
                'after'      => 'latitude'
            ],
            'online_status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '0: Offline, 1: Online',
                'after'      => 'longitude'
            ],
        ];
        $this->forge->addColumn('tbl_users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_users', ['latitude', 'longitude', 'online_status']);
    }
}
