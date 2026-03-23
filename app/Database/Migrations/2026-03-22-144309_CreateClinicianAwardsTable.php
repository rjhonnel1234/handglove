<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClinicianAwardsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'voting_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'award_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default'    => 'Employee of the Week',
            ],
            'award_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clinician_awards');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_clinician_awards');
    }
}
