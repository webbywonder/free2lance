<?php 
$fields = array(
                   'expense_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                   'next_payment' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => "",
                                                ),
                   'status' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => "",
                                                ),
);

$columns = Expense::table()->columns;
  if(!array_key_exists("expense_id", $columns)){ 
$this->dbforge->add_column('expenses', $fields);
}

$fields = array(
                   'task_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => FALSE,
                                                 'default' => 0,
                                                ),
);

$columns = InvoiceHasItem::table()->columns;
  if(!array_key_exists("task_id", $columns)){ 
$this->dbforge->add_column('invoice_has_items', $fields);
}

$fields = array(
                   'twitter' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'skype' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'linkedin' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'facebook' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'instagram' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'googleplus' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'youtube' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
                   'pinterest' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => FALSE,
                                                 'default' => "",
                                                ),
);

$columns = Client::table()->columns;
  if(!array_key_exists("twitter", $columns)){ 
      $this->dbforge->add_column('clients', $fields);
      $this->dbforge->add_column('companies', $fields);
  }

$fields = array(


                        'stripe_ideal' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'unsigned' => TRUE,
                                                 'default' => 0,
                                                ),     
                        'zip_position' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '10',
                                                 'null' => TRUE,
                                                 'default' => "left",
                                                ), 
                        'timezone' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => "",
                                                ),           
                  
);

$columns = Setting::table()->columns;
  if(!array_key_exists("stripe_ideal", $columns)){ 
$this->dbforge->add_column('core', $fields);
}
