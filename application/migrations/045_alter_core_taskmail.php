<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_core_taskmail extends CI_Migration
{
    public function up()
    {
        ## Alter Table core
        $fields = [
            'task_complete_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
                'default' => 'Task completed',
            ],

            'sendmail_on_taskcomplete' => [
                'type' => 'int',
                'constraint' => '1',
                'null' => false,
                'default' => '0',
            ],

            'sendmail_on_taskassign' => [
                'type' => 'int',
                'constraint' => '1',
                'null' => false,
                'default' => '0',
            ],

            'task_assign_mail_subject' => [
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
                'default' => 'Task assigned',
            ]
        ];
        $columns = Setting::table()->columns;
        if (!array_key_exists("task_complete_mail_subject", $columns)) {
            $this->dbforge->add_column('core', $fields);
        }
    }

    public function down()
    { }
}
