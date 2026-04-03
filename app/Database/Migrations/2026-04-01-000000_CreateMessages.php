<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'message_uuid' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'from_number' => ['type' => 'VARCHAR', 'constraint' => 20],
            'to_number' => ['type' => 'VARCHAR', 'constraint' => 20],
            'message' => ['type' => 'TEXT'],
            'channel' => ['type' => 'VARCHAR', 'constraint' => 20],
            'direction' => ['type' => 'VARCHAR', 'constraint' => 10],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_messages');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_messages');
    }
}