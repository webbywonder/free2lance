<?php 
$fields = [
                  'id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => true,
                                                 'auto_increment' => true
                                                ],
                  'attachment' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'attachment_link' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'datetime' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'message' => [
                                                 'type' => 'TEXT',
                                                ],
                  'user_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                   'lead_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
];

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', true);
      $this->dbforge->create_table('lead_has_comments', true);
$fields = [
                   'token' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                   'language' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                   'signature' => [
                                                 'type' => 'TEXT',
                                                ],
];

$columns = User::table()->columns;
if (!array_key_exists('token', $columns)) {
    $this->dbforge->add_column('users', $fields);
}
$columns = Client::table()->columns;
if (!array_key_exists('token', $columns)) {
    $this->dbforge->add_column('clients', $fields);
}

$fields = [
                   'terms' => [
                                                 'type' => 'TEXT',
                                                ],
];

$columns = Company::table()->columns;
if (!array_key_exists('terms', $columns)) {
    $this->dbforge->add_column('companies', $fields);
}

$fields = [
                   'project_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                   'raw' => [
                                                 'type' => 'LONGTEXT',
                   ],
];

$columns = Ticket::table()->columns;
if (!array_key_exists('project_id', $columns)) {
    $this->dbforge->add_column('tickets', $fields);
}

$fields = [
                   'user_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                   ],
                   'note' => [
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => true,
                                                 'default' => 0,
                   ],
                   'raw' => [
                                                 'type' => 'LONGTEXT',
                   ],
];

$columns = TicketHasArticle::table()->columns;
if (!array_key_exists('user_id', $columns)) {
    $this->dbforge->add_column('ticket_has_articles', $fields);
}

$fields = [
                   'po_number' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
];

$columns = Invoice::table()->columns;
if (!array_key_exists('po_number', $columns)) {
    $this->dbforge->add_column('invoices', $fields);
}

$fields = [
                  'id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => true,
                                                 'auto_increment' => true
                                                ],
                  'status_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'source' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'name' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'position' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'address' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'city' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'state' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'country' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'zipcode' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'address' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'language' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'email' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'website' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'phone' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'mobile' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'company' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'tags' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'description' => [
                                                 'type' => 'TEXT',
                                                ],
                  'first_contact' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'last_contact' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'valid_until' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'created' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'modified' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => '',
                                                ],

                  'private' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => '0',
                                                ],
                  'custom' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'user_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'icon' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'order' => [
                                                 'type' => 'FLOAT',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
];

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', true);
      $this->dbforge->create_table('leads', true);

$fields = [
                  'id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => true,
                                                 'auto_increment' => true
                                                ],
                  'name' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'description' => [
                                                 'type' => 'TEXT',
                                                ],
                  'order' => [
                                                 'type' => 'FLOAT',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'offset' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '200',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'limit' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '200',
                                                 'null' => true,
                                                 'default' => 50,
                                                ],
                  'color' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '100',
                                                 'null' => true,
                                                 'default' => '#5071ab',
                                                ],
];

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', true);
      $this->dbforge->create_table('lead_status', true);

$fields = [
                  'id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'unsigned' => true,
                                                 'auto_increment' => true
                                                ],
                  'module' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'source_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'user_id' => [
                                                 'type' => 'BIGINT',
                                                 'constraint' => '20',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'title' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'body' => [
                                                 'type' => 'TEXT',
                                                ],
                  'email_notification' => [
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'done' => [
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                  'datetime' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '50',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
                  'sent_at' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '50',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
];

      $this->dbforge->add_field($fields);
      $this->dbforge->add_key('id', true);
      $this->dbforge->create_table('reminders', true);

$fields = [
                        'receipt_mail_subject' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '200',
                                                 'null' => true,
                                                 'default' => 'Receipt {payment_reference}',
                                                ],
                        'notifications' => [
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'unsigned' => true,
                                                 'default' => 0,
                                                ],
                        'last_notification' => [
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '100',
                                                 'null' => true,
                                                 'default' => '',
                                                ],
];

$columns = Setting::table()->columns;
if (!array_key_exists('notifications', $columns)) {
    $this->dbforge->add_column('core', $fields);
}

$fields = [
                        'marked' => [
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => true,
                                                 'default' => 0,
                                                ],
                        'read' => [
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => true,
                                                 'default' => 0,
                                                ]
];

$columns = Privatemessage::table()->columns;
if (!array_key_exists('marked', $columns)) {
    $this->dbforge->add_column('privatemessages', $fields);
}

$moduleExists = Module::find_by_name('Leads');
if (!$moduleExists) {
    $attributes = [];
    $attributes['name'] = 'Leads';
    $attributes['link'] = 'leads';
    $attributes['type'] = 'main';
    $attributes['icon'] = 'icon dripicons-phone';
    $attributes['sort'] = '4';
    $module = Module::create($attributes);
    $thisModule = Module::find_by_name('Leads');

    $attributes = [];
    $thisuser = User::find_by_id($this->user->id);
    $thisuser->access = $thisuser->access . ',' . $thisModule->id;
    $thisuser->save();
}
