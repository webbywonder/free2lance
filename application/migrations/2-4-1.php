<?php 
                        $modules = Module::find('all', array('order' => 'sort asc', 'conditions' => array('type = ?', 'client')));
                        $client_default_access = "";
                        foreach ($modules as $value) {
                              if($value->name == "Projects" || $value->name == "Messages" || $value->name == "Tickets" || $value->name == "Invoices"){
                                    $client_default_access .= $value->id.",";
                              }
                        }
$fields = array(

                        'calendar_google_api_key' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),

                        'calendar_google_event_address' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'default_client_modules' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => $client_default_access,
                                                ),
                        'estimate_reference' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '10',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),


                        
                        

);

$columns = Setting::table()->columns;
  if(!array_key_exists("default_client_modules", $columns)){ 
$this->dbforge->add_column('core', $fields);
}

$fields = array(

                        'receiver_delete' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '10',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                   

);

$columns = Privatemessage::table()->columns;
  if(!array_key_exists("receiver_delete", $columns)){ 
$this->dbforge->add_column('privatemessages', $fields);
}

$fields = array(

                        'estimate_reference' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '10',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'estimate_decline_message' => array(
                                                 'type' => 'TEXT',
                                                 'null' => TRUE,
                                                ),
                   

);

$columns = Invoice::table()->columns;
  if(!array_key_exists("estimate_reference", $columns)){ 
$this->dbforge->add_column('invoices', $fields);
$estimate_ref = Setting::first();
$estimate_ref->estimate_reference = $estimate_ref->invoice_reference;
$estimate_ref->save();

$invoices = Invoice::find('all', array('conditions' => array('estimate = ?', 1)));
            foreach ($invoices as $invoice) {
                  $invoice->estimate_reference = $invoice->reference;
                  $invoice->save();
            }

}

$fields = array(
                'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                 'company_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'user_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'access' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('company_has_admins', TRUE); 

$fields = array(
                  'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                 'title' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'description' => array(
                                                 'type' => 'text',
                                                ),
                  'allday' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '30',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'url' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'classname' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'start' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'end' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'user_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'access' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'reminder' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('events', TRUE); 


$attributes = array();
$attributes["id"] = "20";
$attributes["name"] = "Calendar";
$attributes["link"] = "calendar";
$attributes["type"] = "main";
$attributes["icon"] = "fa-calendar";
$attributes["sort"] = "8.1";
$setting = Module::create($attributes);
$attributes = array();
$thisuser = User::find_by_id($this->user->id);
$thisuser->access = $thisuser->access.",20";
$thisuser->save();
