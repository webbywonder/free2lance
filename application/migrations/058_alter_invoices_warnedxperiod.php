<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_invoices_warnedxperiod extends CI_Migration
{
    public function up()
    {
        ## Alter Table invoices
        $fields = [
            'warnedxperiod' => [
                'type' => 'int',
                'constraint' => '1',
                'null' => false,
                'default' => '0',
                'unsigned' => true
            ]
        ];
        $columns = Invoice::table()->columns;
        if (!array_key_exists("warnedxperiod", $columns)) {
            $this->dbforge->add_column('invoices', $fields);
        }
    }

    public function down()
    { }
}
