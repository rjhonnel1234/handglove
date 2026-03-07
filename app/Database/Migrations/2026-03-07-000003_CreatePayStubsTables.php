<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePayStubsTables extends Migration
{
    public function up()
    {
        // 1. tbl_pay_stubs
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'period_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'total_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'gross_pay' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
            ],
            'total_deductions' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
                'default'    => 0.00,
            ],
            'net_pay' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10, // 10 = Generated/Pending, 20 = Paid
                'comment'    => '10 = Pending, 20 = Paid',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['clinician_id', 'period_id']);
        $this->forge->createTable('tbl_pay_stubs');

        // 2. tbl_pay_stub_details (links invoices to stubs)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pay_stub_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'invoice_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pay_stub_id');
        $this->forge->addKey('invoice_id');
        $this->forge->createTable('tbl_pay_stub_details');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pay_stub_details');
        $this->forge->dropTable('tbl_pay_stubs');
    }
}
