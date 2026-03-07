<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class InitialSchedulingAndShifts extends Migration
{
    public function up()
    {
        // 1. tbl_client_units
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
                'constraint' => 256,
            ],
            'unit_manager_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'census' => [
                'type'       => 'VARCHAR',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_units');

        // 2. tbl_shift_types
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
        $this->forge->createTable('tbl_shift_types');

        // 3. tbl_shifts
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
            'start_date' => [
                'type' => 'DATE',
            ],
            'shift_start_time' => [
                'type' => 'TIME',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'shift_end_time' => [
                'type' => 'TIME',
            ],
            'shift_type' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'slots' => [
                'type'       => 'INT',
                'constraint' => 11,
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
            'rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,2',
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_shifts');

        // 4. tbl_shift_clinicians
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
            'connection' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => '10 = Active, 20 = Inactive',
            ],
            'shift_status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => '30 = Calledout, 40 = DNR',
            ],
            'pcc_status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => '10 = Off, 20 = On',
            ],
            'dnr_remarks' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_shift_clinicians');

        // 5. tbl_shift_timekeeping
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'shift_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'clinician_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'punch_datetime' => [
                'type' => 'DATETIME',
            ],
            'punch_type' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => '10 = Punch In, 20 = Punch out',
            ],
            'reference' => [
                'type' => 'TEXT',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_shift_timekeeping');

        // 6. tbl_client_shift_requests
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
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_shift_requests');

        // 7. tbl_client_schedules_upload
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
            'schedule_date' => [
                'type' => 'DATE',
            ],
            'filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 512,
            ],
            'uploaded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => '10 = uploaded, 30 posted',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_client_schedules_upload');

        // 8. tbl_client_schedule_details
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
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'personnel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'schedule_date' => [
                'type' => 'DATE',
            ],
            'shift_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'shift_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['client_id', 'schedule_date'], false, false, 'idx_client_date');
        $this->forge->addKey('personnel_id', false, false, 'idx_personnel');
        $this->forge->createTable('tbl_client_schedule_details');

        // 9. tbl_clinician_temp_shift
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'clinician_Id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'shift_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_clinician_temp_shift');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_clinician_temp_shift');
        $this->forge->dropTable('tbl_client_schedule_details');
        $this->forge->dropTable('tbl_client_schedules_upload');
        $this->forge->dropTable('tbl_client_shift_requests');
        $this->forge->dropTable('tbl_shift_timekeeping');
        $this->forge->dropTable('tbl_shift_clinicians');
        $this->forge->dropTable('tbl_shifts');
        $this->forge->dropTable('tbl_shift_types');
        $this->forge->dropTable('tbl_client_units');
    }
}
