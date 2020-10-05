<?php 

$this->dbforge->drop_table('expenses', TRUE);
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