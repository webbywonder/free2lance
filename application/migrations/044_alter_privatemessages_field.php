<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_privatemessages_field extends CI_Migration
{
    public function up()
    {
        ## Alter Table companies
        $fields = [
            'conversation' => [
                'name' => 'conversation',
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => ''
            ]
        ];
        $this->dbforge->modify_column('privatemessages', $fields);
    }

    public function down()
    {
    }
}
