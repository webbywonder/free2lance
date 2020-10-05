<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_settings_sendmail_overdue extends CI_Migration
{
    public function up()
    {
        ## Alter Table core
        $fields = [
            'sendmail_on_overdue' => [
                'type' => 'int',
                'constraint' => '1',
                'null' => false,
                'default' => '0',
            ],
            'sendmail_on_overduexperiod' => [
                'type' => 'int',
                'null' => false,
                'default' => '0',
            ]
        ];
        $columns = Setting::table()->columns;
        if (!array_key_exists("sendmail_on_overdue", $columns)) {
            $this->dbforge->add_column('core', $fields);
        }
    }

    public function down()
    { }
}
