<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class InitialPersonnelAndClinicians extends Migration
{
    public function up()
    {
        // 1. tbl_clinician_types
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
            'grouping' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clinician_types');

        // 2. tbl_client_personnel
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
            'type' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'date_of_birth' => [
                'type' => 'DATE',
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
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'clinician_type' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'signature' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_personnel');

        // 3. tbl_clinicians
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
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'profile_pic_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'tier' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
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
            'birthday' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'type' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'rate' => [
                'type'       => 'DOUBLE',
                'constraint' => '11,2',
            ],
            'connector' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'agencies' => [
                'type'       => 'VARCHAR',
                'constraint' => 11,
            ],
            'company_worked' => [
                'type' => 'TEXT',
            ],
            'cv' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'is_supervisor' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'pw' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'pcc_username' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'pcc_password' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clinicians');

        // 4. tbl_clinician_credentials
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
            ],
            'credential_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'upload_datetime' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clinician_credentials');

        // 5. tbl_clinician_referrals
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
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'supervisor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clinician_referrals');

        // 6. tbl_credential_types
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
        $this->forge->createTable('tbl_credential_types');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_credential_types');
        $this->forge->dropTable('tbl_clinician_referrals');
        $this->forge->dropTable('tbl_clinician_credentials');
        $this->forge->dropTable('tbl_clinicians');
        $this->forge->dropTable('tbl_client_personnel');
        $this->forge->dropTable('tbl_clinician_types');
    }
}
