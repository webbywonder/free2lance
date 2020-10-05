<?php

$fields = array(
                        'stripe_p_key' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'stripe_currency' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 'USD',
                                                ),
                        'bank_transfer' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'bank_transfer_text' => array(
                                                 'type' => 'LONGTEXT'
                                                )
);

$columns = Setting::table()->columns;
  if(!array_key_exists("stripe_p_key", $columns)){ 
$this->dbforge->add_column('core', $fields);
}
