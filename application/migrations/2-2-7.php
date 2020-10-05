<?php

$fields = array(
                        'deleted' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'attachment' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'attachment_link' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),

);

$columns = Privatemessage::table()->columns;
  if(!array_key_exists("deleted", $columns)){ 
$this->dbforge->add_column('privatemessages', $fields);
}