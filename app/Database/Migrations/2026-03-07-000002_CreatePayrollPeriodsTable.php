<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePayrollPeriodsTable extends Migration
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
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
            ],
            'pay_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10, // 10 = Open/Pending, 20 = Processing, 30 = Paid/Closed
                'comment'    => '10 = Open, 20 = Processing, 30 = Paid',
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
        $this->forge->createTable('tbl_payroll_periods');

        // Add some default settings for payroll
        $db = \Config\Database::connect();
        $db->table('tbl_settings')->insertBatch([
            [
                'key' => 'payroll_frequency',
                'value' => 'weekly', // weekly, bi-weekly, semi-monthly, monthly
            ],
            [
                'key' => 'payroll_start_day',
                'value' => 'Monday',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_payroll_periods');
        
        $db = \Config\Database::connect();
        $db->table('tbl_settings')->whereIn('key', ['payroll_frequency', 'payroll_start_day'])->delete();
    }
}
