<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAverageRateAndBonusToOnboarding extends Migration
{
    public function up()
    {
        $fields = [
            'average_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'average_census'
            ],
            'bonus' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'after'      => 'average_rate'
            ],
        ];
        $this->forge->addColumn('tbl_client_onboarding_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_client_onboarding_settings', 'average_rate');
        $this->forge->dropColumn('tbl_client_onboarding_settings', 'bonus');
    }
}
