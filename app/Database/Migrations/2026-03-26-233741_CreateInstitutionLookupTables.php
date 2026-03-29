<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstitutionLookupTables extends Migration
{
    public function up()
    {
        // 1. Create tbl_provider_institution_type
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_provider_institution_type');

        // 2. Create tbl_provider_institution_ownership_type
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_provider_institution_ownership_type');

        // 3. Populate lookup tables with unique values from existing data
        $db = \Config\Database::connect();
        
        $types = $db->table('tbl_provider_institution')->select('provider_type')->distinct()->get()->getResultArray();
        foreach ($types as $type) {
            if (!empty($type['provider_type'])) {
                $db->table('tbl_provider_institution_type')->insert(['name' => $type['provider_type']]);
            }
        }

        $owners = $db->table('tbl_provider_institution')->select('ownership_type')->distinct()->get()->getResultArray();
        foreach ($owners as $owner) {
            if (!empty($owner['ownership_type'])) {
                $db->table('tbl_provider_institution_ownership_type')->insert(['name' => $owner['ownership_type']]);
            }
        }

        // 4. Add ID columns to tbl_provider_institution
        $this->forge->addColumn('tbl_provider_institution', [
            'provider_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'provider_type'
            ],
            'ownership_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'ownership_type'
            ],
        ]);

        // 5. Update tbl_provider_institution with matching IDs
        $typeLookup = $db->table('tbl_provider_institution_type')->get()->getResultArray();
        foreach ($typeLookup as $t) {
            $db->table('tbl_provider_institution')
               ->where('provider_type', $t['name'])
               ->update(['provider_type_id' => $t['id']]);
        }

        $ownerLookup = $db->table('tbl_provider_institution_ownership_type')->get()->getResultArray();
        foreach ($ownerLookup as $o) {
            $db->table('tbl_provider_institution')
               ->where('ownership_type', $o['name'])
               ->update(['ownership_type_id' => $o['id']]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_provider_institution', ['provider_type_id', 'ownership_type_id']);
        $this->forge->dropTable('tbl_provider_institution_ownership_type');
        $this->forge->dropTable('tbl_provider_institution_type');
    }
}
