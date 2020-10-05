<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_companies_custaccountid extends CI_Migration
{
    public function up()
    {
        ## Alter Table companies
        $fields = [
            'custaccountid' => [
                'type' => 'varchar',
                'constraint' => '255',
                'null' => true,
                'default' => '',
            ],
        ];
        $columns = Company::table()->columns;
        if (!array_key_exists("custaccountid", $columns)) {
            $this->dbforge->add_column('companies', $fields);
        }
    }

    public function down()
    { }
}
