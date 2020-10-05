<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_invoices_category extends CI_Migration
{
    public function up()
    {
        ## Alter Table invoices
        $fields = [
            'category' => [
                'type' => 'varchar',
                'constraint' => '128',
                'null' => true,
                'default' => ''
            ]
        ];
        $columns = Invoice::table()->columns;
        if (!array_key_exists("category", $columns)) {
            $this->dbforge->add_column('invoices', $fields);
        }
    }

    public function down()
    { }
}
