<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class InitialMiscAndCMS extends Migration
{
    public function up()
    {
        // 1. notifications
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'info',
            ],
            'is_read' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('is_read');
        $this->forge->createTable('notifications');

        // 2. tbl_board_of_directors
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'ceo_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'picture' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'company_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'created_datetime' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'company_website' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_board_of_directors');

        // 3. tbl_client_ratings
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
            'shift_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'cleanliness' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'work_environment' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tools_needed' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'average' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'comment' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'datetime_added' => [
                'type' => 'DATETIME',
            ],
            'insert_datetime' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_ratings');

        // 4. tbl_client_voting
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
            'voting_start_date' => [
                'type' => 'DATE',
            ],
            'voting_end_date' => [
                'type' => 'DATE',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'voting_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_voting');

        // 5. tbl_client_voting_details
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
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'votes' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_voting_details');

        // 6. tbl_client_voting_votes
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
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'profile_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_voting_votes');

        // 7. tbl_lead_types
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
                'constraint' => 512,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_lead_types');

        // 8. tbl_leads
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'company_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'zip_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'agencies' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'reference' => [
                'type' => 'TEXT',
            ],
            'supervisor' => [
                'type' => 'TEXT',
            ],
            'created_datetime' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'origin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'shift_type' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'shift_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'shift_time_start' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'shift_time_end' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
            ],
            'ref_clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'census' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'features' => [
                'type' => 'TEXT',
            ],
            'provider_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'country' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'state' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_leads');

        // 9. tbl_leads_management
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
            'user_type' => [
                'type'       => 'INT',
                'constraint' => 12,
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
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_leads_management');

        // 10. tbl_provider_institution
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ccn' => [
                'type'       => 'VARCHAR',
                'constraint' => 11,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'zip' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'county' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'ownership_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'provider_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'country' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_provider_institution');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_provider_institution');
        $this->forge->dropTable('tbl_leads_management');
        $this->forge->dropTable('tbl_leads');
        $this->forge->dropTable('tbl_lead_types');
        $this->forge->dropTable('tbl_client_voting_votes');
        $this->forge->dropTable('tbl_client_voting_details');
        $this->forge->dropTable('tbl_client_voting');
        $this->forge->dropTable('tbl_client_ratings');
        $this->forge->dropTable('tbl_board_of_directors');
        $this->forge->dropTable('notifications');
    }
}
