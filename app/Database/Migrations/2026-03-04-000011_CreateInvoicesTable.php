<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateInvoicesTable extends Migration
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
            'shift_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'total_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
            ],
            'total_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10, // 10 = Pending
                'comment'    => '10 = Pending, 20 = Paid, 30 = Cancelled',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('shift_id');
        $this->forge->addKey('clinician_id');
        $this->forge->addKey('client_id');
        $this->forge->createTable('tbl_invoices');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_invoices');
    }
}
