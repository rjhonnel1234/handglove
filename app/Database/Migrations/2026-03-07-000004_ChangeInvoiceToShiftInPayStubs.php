<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeInvoiceToShiftInPayStubs extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('tbl_pay_stub_details', 'invoice_id');
        $this->forge->addColumn('tbl_pay_stub_details', [
            'shift_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'pay_stub_id',
            ],
        ]);
        $this->forge->addKey('shift_id');
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_pay_stub_details', 'shift_id');
        $this->forge->addColumn('tbl_pay_stub_details', [
            'invoice_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'pay_stub_id',
            ],
        ]);
        $this->forge->addKey('invoice_id');
    }
}
