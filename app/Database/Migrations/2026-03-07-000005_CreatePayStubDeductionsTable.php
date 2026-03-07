<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayStubDeductionsTable extends Migration
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
            'pay_stub_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tax_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tax_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pay_stub_id');
        $this->forge->createTable('tbl_pay_stub_deductions');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pay_stub_deductions');
    }
}
