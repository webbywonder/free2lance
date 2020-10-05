<?php 
$fields = array(
                  'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'attachment' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'attachment_link' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'datetime' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'message' => array(
                                                 'type' => 'TEXT',
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
                   'task_id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
);

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', TRUE);
      $this->dbforge->create_table('task_has_comments', TRUE); 

$fields = array(


                        'reference_lenght' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'unsigned' => TRUE,
                                                 'default' => 0,
                                                ),             
                  
);

$columns = Setting::table()->columns;
  if(!array_key_exists("reference_lenght", $columns)){ 
$this->dbforge->add_column('core', $fields);
}
