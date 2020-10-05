<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_settings_zipcode extends CI_Migration
{
    public function up()
    {
        ## Alter Table core
        $fields = [
            'zipcode' => [
                'type' => 'varchar',
                'constraint' => '128',
                'null' => true,
                'default' => '',
            ],
        ];
        $columns = Setting::table()->columns;
        if (!array_key_exists("zipcode", $columns)) {
            $this->dbforge->add_column('core', $fields);
        }
    }

    public function down()
    { }
}
