<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_projects_status extends CI_Migration
{
    public function up()
    {
        ## Alter Table projects
        $fields = [
            'status' => [
                'type' => 'varchar',
                'constraint' => '128',
                'null' => true,
                'default' => 'notstarted',
            ],
        ];
        $columns = Project::table()->columns;
        if (!array_key_exists("status", $columns)) {
            $this->dbforge->add_column('projects', $fields);
        }
    }

    public function down()
    { }
}
