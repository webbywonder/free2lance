<?php 
$fields = array(
                  'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'invoice_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'reference' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'amount' => array(
                                                 'type' => 'FLOAT',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),

                  'type' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',

                                                ),
                  'notes' => array(
                                                 'type' => 'TEXT',

                                                ),
                  'user_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  
                  
                   
);

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', TRUE);
      $this->dbforge->create_table('invoice_has_payments', TRUE); 



$fields = array(
                        'paid' => array(
                                                 'type' => 'FLOAT',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'outstanding' => array(
                                                 'type' => 'FLOAT',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),

);
 $columns = Invoice::table()->columns;
  if(!array_key_exists("paid", $columns)){ 
$this->dbforge->add_column('invoices', $fields);
}
$fields = array(

                        'authorize_net' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => '0',
                                                ),

                        'authorize_api_login_id' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'authorize_api_transaction_key' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'authorize_currency' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '30',
                                                 'null' => TRUE,
                                                 'default' => 'USD',
                                                ),
                        'invoice_prefix' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'company_prefix' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'quotation_prefix' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'project_prefix' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'subscription_prefix' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),

                        
                        

);

$columns = Setting::table()->columns;
  if(!array_key_exists("authorize_net", $columns)){ 
$this->dbforge->add_column('core', $fields);
}
