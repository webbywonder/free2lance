<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_company_social_fields extends CI_Migration
{
    public function up()
    {
        ## Alter Table companies
        $fields = [
            'twitter' => [
                'name' => 'twitter',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'skype' => [
                'name' => 'skype',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'linkedin' => [
                'name' => 'linkedin',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'facebook' => [
                'name' => 'facebook',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'instagram' => [
                'name' => 'instagram',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'googleplus' => [
                'name' => 'googleplus',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'youtube' => [
                'name' => 'youtube',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],

            'pinterest' => [
                'name' => 'pinterest',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ];
        $this->dbforge->modify_column('companies', $fields);
    }

    public function down()
    {
    }
}
