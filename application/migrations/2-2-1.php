<?php

$fields = array(
                        'province' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
);

  $columns = Company::table()->columns;
  if(!array_key_exists("province", $columns)){ 
$this->dbforge->add_column('companies', $fields);
}
$fields = array(
                        'stripe_key' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'stripe' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                )
);

$columns = Setting::table()->columns;
  if(!array_key_exists("stripe_key", $columns) && !array_key_exists("stripe", $columns)){ 
$this->dbforge->add_column('core', $fields);
}

$fields = array(
                        'priority' => array(
                                                 'type' => 'smallint',
                                                 'constraint' => '6',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'time' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                            
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'value' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                               
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'description' => array(
                                                 'type' => 'TEXT',
                                                 
                                                 'null' => TRUE,
                                                ),
                        'datetime' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'due_date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
);

$columns = ProjectHasTask::table()->columns;
  if(!array_key_exists("priority", $columns) && !array_key_exists("due_date", $columns)){ 
$this->dbforge->add_column('project_has_tasks', $fields);
}

$fields = array(
                        'tax' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
);

      $columns = Invoice::table()->columns;
  if(!array_key_exists("tax", $columns)){ 
$this->dbforge->add_column('invoices', $fields);
}
$fields = array(
                        'tax' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                
                                                 'null' => TRUE,
                                                 'default' => '',
                                                )
);


$columns = Subscription::table()->columns;
  if(!array_key_exists("tax", $columns)){ 
$this->dbforge->add_column('subscriptions', $fields);
}
$fields = array(
                        'queue' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                )
);

$columns = User::table()->columns;
  if(!array_key_exists("queue", $columns)){ 
$this->dbforge->add_column('users', $fields);
}

$fields = array(
                        'project_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                )
);

$columns = Invoice::table()->columns;
  if(!array_key_exists("project_id", $columns)){ 
$this->dbforge->add_column('invoices', $fields);
}


$fields = array(
                'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'type' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'name' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'text' => array(
                                                 'type' => 'LONGTEXT'
                                                ),
                  'updated' => array(
                                                 'type' => 'TIMESTAMP',
                                                 'null' => TRUE,
                                                 'default' => '0000-00-00 00:00:00'
                                          )
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('templates', TRUE); 


$fields = array(
                'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'project_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'invoice_id' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('project_has_invoices', TRUE); 


$fields = array(
                'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'project_id' => array(
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
                  'client_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'datetime' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'subject' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),

                  'message' => array(
                                                 'type' => 'LONGTEXT'
                                                ),
                  'type' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                          )
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('project_has_activities', TRUE); 


$setting = Setting::first();
$_POST["template"] = "blueline";
$setting = $setting->update_attributes($_POST);
