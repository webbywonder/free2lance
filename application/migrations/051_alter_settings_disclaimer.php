<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_settings_disclaimer extends CI_Migration
{
    public function up()
    {
        ## Alter Table core
        $fields = [
            'disclaimer' => [
                'type' => 'mediumtext',
                'null' => true,
                'default' => '',
            ],
        ];
        $columns = Setting::table()->columns;
        if (!array_key_exists("disclaimer", $columns)) {
            $this->dbforge->add_column('core', $fields);
        }
    }

    public function down()
    { }
}
