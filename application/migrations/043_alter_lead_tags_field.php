<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_lead_tags_field extends CI_Migration
{
    public function up()
    {
        ## Alter Table companies
        $fields = [
            'tags' => [
                'name' => 'tags',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => ''
            ]
        ];
        $this->dbforge->modify_column('leads', $fields);
    }

    public function down()
    {
    }
}
