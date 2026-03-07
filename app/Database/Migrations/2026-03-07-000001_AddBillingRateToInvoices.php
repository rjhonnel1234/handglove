<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddBillingRateToInvoices extends Migration
{
    public function up()
    {
        // 1. Create tbl_settings
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'value' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('tbl_settings');

        // Insert default billing rate
        $this->db->table('tbl_settings')->insert([
            'key' => 'billing_rate',
            'value' => '1.00',
        ]);

        // 2. Add billing_rate to tbl_invoices
        $fields = [
            'billing_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 1.00,
                'after'      => 'rate',
            ],
        ];
        $this->forge->addColumn('tbl_invoices', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_invoices', 'billing_rate');
        $this->forge->dropTable('tbl_settings');
    }
}
