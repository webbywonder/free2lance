<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_settings_maxtablerow extends CI_Migration
{
    public function up()
    {
        ## Alter Table core
        $fields = [
            'max_table_row' => [
                'type' => 'INT',
                'constraint' => '4',
                'null' => false,
                'default' => '100',
                'unsigned' => true
            ],
        ];
        $columns = Setting::table()->columns;
        if (!array_key_exists("max_table_row", $columns)) {
            $this->dbforge->add_column('core', $fields);
        }
    }

    public function down()
    { }
}
