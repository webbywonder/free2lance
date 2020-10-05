<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_project_has_tasks_warned extends CI_Migration
{
    public function up()
    {
        ## Alter Table project_has_tasks
        $fields = [
            'warned' => [
                'type' => 'int',
                'constraint' => '1',
                'null' => false,
                'default' => '0',
                'unsigned' => true
            ]
        ];
        $columns = ProjectHasTask::table()->columns;
        if (!array_key_exists("warned", $columns)) {
            $this->dbforge->add_column('project_has_tasks', $fields);
        }
    }

    public function down()
    { }
}
