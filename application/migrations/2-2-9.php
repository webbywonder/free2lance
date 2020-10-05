<?php
$fields = array(
                        'estimate' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'estimate_status' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '0',
                                                ),
                        'estimate_accepted_date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '0',
                                                ),
                        'estimate_sent' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '0',
                                                ),
                        'sum' => array(
                                                 'type' => 'float',           
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'second_tax' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '5',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),


);

$columns = Invoice::table()->columns;
  if(!array_key_exists("estimate", $columns)){ 
$this->dbforge->add_column('invoices', $fields);
$attributes = array();
$attributes["id"] = "18";
$attributes["name"] = "Estimates";
$attributes["link"] = "estimates";
$attributes["type"] = "main";
$attributes["icon"] = "fa-files-o";
$attributes["sort"] = "5.1";
$setting = Module::create($attributes);

$attributes = array();
$attributes["id"] = "19";
$attributes["name"] = "Expenses";
$attributes["link"] = "expenses";
$attributes["type"] = "main";
$attributes["icon"] = "fa-money";
$attributes["sort"] = "5.2";
$setting = Module::create($attributes);

$attributes = array();
$attributes["id"] = "107";
$attributes["name"] = "Estimates";
$attributes["link"] = "cestimates";
$attributes["type"] = "client";
$attributes["icon"] = "fa-files-o";
$attributes["sort"] = "3.1";
$setting = Module::create($attributes);
$attributes = array();
$thisuser = User::find_by_id($this->user->id);
$thisuser->access = $thisuser->access.",18,19";
$thisuser->save();

}

$fields = array(
                'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'description' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'type' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'category' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'currency' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'value' => array(
                                                 'type' => 'FLOAT',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'vat' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'reference' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'project_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'default' => 0,
                                                ),
                  'rebill' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'default' => 0,
                                                ),
                  'invoice_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'default' => 0,
                                                ),
                  'attachment' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',

                                                ),
                  'attachment_description' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',

                                                ),
                  'recurring' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',

                                                ),
                  'recurring_until' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',

                                                ),
                  'user_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,

                                                ),
                  
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('expenses', TRUE); 


$fields = array(

                        'estimate_terms' => array(
                                                 'type' => 'LONGTEXT'
                                                ),
                        'estimate_prefix' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 'EST',
                                                ),
                        'estimate_pdf_template' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 'templates/estimate/default',
                                                ),
                        'invoice_pdf_template' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 'invoices/preview',
                                                ),
                        'second_tax' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '5',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'estimate_mail_subject' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => 'New Estimate #{estimate_id}',
                                                ),
                        

);

$columns = Setting::table()->columns;
  if(!array_key_exists("estimate_terms", $columns)){ 
$this->dbforge->add_column('core', $fields);
}




