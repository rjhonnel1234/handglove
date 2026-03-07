<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // 1. tbl_roles
        $roles = [
            ['roleId' => 1, 'role' => 'System Administrator', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 0, 'createdDtm' => '2021-01-21 00:00:00', 'updatedBy' => 1, 'updatedDtm' => '2024-02-22 06:13:16'],
            ['roleId' => 2, 'role' => 'Staff', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 1, 'createdDtm' => '2025-07-05 09:19:49', 'updatedBy' => null, 'updatedDtm' => null],
            ['roleId' => 3, 'role' => 'Facility Admin', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 1, 'createdDtm' => '2026-02-10 01:08:40', 'updatedBy' => null, 'updatedDtm' => null],
            ['roleId' => 4, 'role' => 'Facility HR', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 1, 'createdDtm' => '2026-02-10 01:08:40', 'updatedBy' => null, 'updatedDtm' => null],
            ['roleId' => 5, 'role' => 'Facility DON', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 1, 'createdDtm' => '2026-02-10 01:08:40', 'updatedBy' => null, 'updatedDtm' => null],
            ['roleId' => 6, 'role' => 'Facility Unit Manager', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 1, 'createdDtm' => '2026-02-10 01:08:40', 'updatedBy' => null, 'updatedDtm' => null],
            ['roleId' => 7, 'role' => 'Facility Supervisor', 'status' => 1, 'isDeleted' => 0, 'createdBy' => 1, 'createdDtm' => '2026-02-10 01:08:40', 'updatedBy' => null, 'updatedDtm' => null],
        ];
        $this->db->table('tbl_roles')->insertBatch($roles);

        // 2. tbl_user_types
        $userTypes = [
            ['id' => 1, 'name' => 'Supervisor', 'description' => ''],
            ['id' => 2, 'name' => 'Admin', 'description' => ''],
            ['id' => 3, 'name' => 'DON', 'description' => ''],
            ['id' => 4, 'name' => 'HR', 'description' => ''],
            ['id' => 5, 'name' => 'Scheduler', 'description' => ''],
            ['id' => 6, 'name' => 'Unit Manager', 'description' => ''],
            ['id' => 7, 'name' => 'Staff', 'description' => ''],
        ];
        $this->db->table('tbl_user_types')->insertBatch($userTypes);

        // 3. tbl_clinician_types
        $clinicianTypes = [
            ['id' => 1, 'name' => 'GNA', 'description' => '', 'grouping' => 'gna', 'status' => 1],
            ['id' => 2, 'name' => 'LPN/LVN', 'description' => '', 'grouping' => 'nurse', 'status' => 1],
            ['id' => 3, 'name' => 'RN', 'description' => '', 'grouping' => 'nurse', 'status' => 1],
            ['id' => 4, 'name' => 'CNA', 'description' => '', 'grouping' => 'gna', 'status' => 1],
        ];
        $this->db->table('tbl_clinician_types')->insertBatch($clinicianTypes);

        // 4. tbl_shift_types
        $shiftTypes = [
            ['id' => 1, 'name' => 'GNA', 'description' => '', 'status' => 1],
            ['id' => 2, 'name' => 'LPN', 'description' => 'qwr qr qrwqwq', 'status' => 1],
            ['id' => 3, 'name' => 'RN', 'description' => '', 'status' => 1],
            ['id' => 4, 'name' => 'CNA', 'description' => '', 'status' => 1],
        ];
        $this->db->table('tbl_shift_types')->insertBatch($shiftTypes);

        // 5. tbl_credential_types
        $credentialTypes = [
            ['id' => 1, 'name' => 'COVID', 'description' => '', 'status' => 1],
            ['id' => 2, 'name' => 'CRIMI', 'description' => '', 'status' => 1],
            ['id' => 3, 'name' => 'DEM', 'description' => '', 'status' => 1],
            ['id' => 4, 'name' => 'DRUG', 'description' => '', 'status' => 1],
            ['id' => 5, 'name' => 'EABUS', 'description' => '', 'status' => 1],
            ['id' => 6, 'name' => 'HIPAA', 'description' => '', 'status' => 1],
            ['id' => 7, 'name' => 'ID', 'description' => '', 'status' => 1],
            ['id' => 8, 'name' => 'LIC2', 'description' => '', 'status' => 1],
            ['id' => 9, 'name' => 'MMES', 'description' => '', 'status' => 1],
            ['id' => 10, 'name' => 'PHYSC', 'description' => '', 'status' => 1],
            ['id' => 11, 'name' => 'PPD', 'description' => '', 'status' => 1],
            ['id' => 12, 'name' => 'RES', 'description' => '', 'status' => 1],
            ['id' => 13, 'name' => 'STATE', 'description' => '', 'status' => 1],
            ['id' => 14, 'name' => 'CV', 'description' => '', 'status' => 1],
        ];
        $this->db->table('tbl_credential_types')->insertBatch($credentialTypes);

        // 6. tbl_lead_types
        $leadTypes = [
            ['id' => 1, 'name' => 'Patient', 'description' => 'qwr qr qrwqwq', 'status' => 1],
            ['id' => 3, 'name' => 'Provider', 'description' => '', 'status' => 1],
            ['id' => 4, 'name' => 'Caregiver', 'description' => '', 'status' => 1],
        ];
        $this->db->table('tbl_lead_types')->insertBatch($leadTypes);
    }
}
