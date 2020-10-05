<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_companies_individual extends CI_Migration
{
    public function up()
    {
        ## Alter Table companies
        $fields = [
            'individual' => [
                'type' => 'int',
                'constraint' => '1',
                'null' => false,
                'default' => '0',
                'unsigned' => true
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => '128',
                'null' => true,
                'default' => ''
            ]
        ];
        $columns = Company::table()->columns;
        if (!array_key_exists("individual", $columns)) {
            $this->dbforge->add_column('companies', $fields);
        }
    }

    public function down()
    { }
}
