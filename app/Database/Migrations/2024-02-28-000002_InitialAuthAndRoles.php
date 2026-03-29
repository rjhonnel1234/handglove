<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class InitialAuthAndRoles extends Migration
{
    public function up()
    {
        // 1. tbl_roles
        $this->forge->addField([
            'roleId' => [
                'type'           => 'TINYINT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'role id',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'role text',
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 1,
            ],
            'isDeleted' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            'createdBy' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'createdDtm' => [
                'type' => 'DATETIME',
            ],
            'updatedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'updatedDtm' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('roleId', true);
        $this->forge->createTable('tbl_roles');

        // 2. tbl_user_types
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
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'is_management' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_user_types');

        // 3. tbl_users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 30,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'facility_id' => [
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
                'constraint' => 128,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
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
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
            'token_active' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'token_datetime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_users');

        // 4. tbl_admin_users
        $this->forge->addField([
            'userId' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'account_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ],
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'roleId' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
            ],
            'designation' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'isAdmin' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 2,
            ],
            'isDeleted' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            'createdBy' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'createdDtm' => [
                'type' => 'DATETIME',
            ],
            'updatedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'updatedDtm' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('userId', true);
        $this->forge->createTable('tbl_admin_users');

        // 5. tbl_access_matrix
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'access' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'roleId' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'isDeleted' => [
                'type'       => 'TINYINT',
                'constraint' => 4,
                'default'    => 0,
            ],
            'createdBy' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'createdDtm' => [
                'type' => 'DATETIME',
            ],
            'updatedBy' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'updatedDtm' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_access_matrix');

        // 6. tbl_last_login
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'auto_increment' => true,
            ],
            'userId' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
            ],
            'sessionData' => [
                'type'       => 'VARCHAR',
                'constraint' => 2048,
            ],
            'machineIp' => [
                'type'       => 'VARCHAR',
                'constraint' => 1024,
            ],
            'userAgent' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'agentString' => [
                'type'       => 'VARCHAR',
                'constraint' => 1024,
            ],
            'platform' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
            ],
            'createdDtm' => [
                'type'    => 'DATETIME',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_last_login');

        // 7. tbl_email_otp
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 12,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 12,
            ],
            'datetime_generated' => [
                'type' => 'DATETIME',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
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
        $this->forge->createTable('tbl_email_otp');

        // 8. tbl_sms_otp
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 12,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 12,
            ],
            'datetime_generated' => [
                'type' => 'DATETIME',
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
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
        $this->forge->createTable('tbl_sms_otp');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_sms_otp');
        $this->forge->dropTable('tbl_email_otp');
        $this->forge->dropTable('tbl_last_login');
        $this->forge->dropTable('tbl_access_matrix');
        $this->forge->dropTable('tbl_admin_users');
        $this->forge->dropTable('tbl_users');
        $this->forge->dropTable('tbl_user_types');
        $this->forge->dropTable('tbl_roles');
    }
}
