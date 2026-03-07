<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class InitialClientsAndOnboarding extends Migration
{
    public function up()
    {
        // 1. tbl_clients
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'leads_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'company_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'company_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'zip_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'company_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'company_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'agencies' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'created_datetime' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'timezone' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'census' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'country_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'state_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clients');

        // 2. tbl_clients_management
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'is_approver' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clients_management');

        // 3. tbl_client_onboarding_settings
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'timezone' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'total_beds' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'average_census' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'clock_in' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'access' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'clock_out_approval' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'task_delay' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'back_up_approval' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'phone' => [
                'type' => 'TEXT',
            ],
            'allow_overtime' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'accepted_per_diem_network' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_when' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_onboarding_settings');

        // 4. tbl_client_onboarding_files
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'youtube_link' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'command' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'created_datetime' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_onboarding_files');

        // 5. tbl_agencies
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_agencies');

        // 6. tbl_donors
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'logo' => [
                'type' => 'TEXT',
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_donors');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_donors');
        $this->forge->dropTable('tbl_agencies');
        $this->forge->dropTable('tbl_client_onboarding_files');
        $this->forge->dropTable('tbl_client_onboarding_settings');
        $this->forge->dropTable('tbl_clients_management');
        $this->forge->dropTable('tbl_clients');
    }
}
