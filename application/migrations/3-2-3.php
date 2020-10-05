<?php 

$fields = [
                   'terms' => [
                                                 'type' => 'TEXT',
                                                 'null' => true,
                                                ],
];

$columns = Company::table()->columns;
if (array_key_exists('terms', $columns)) {
    $this->dbforge->modify_column('companies', $fields);
}

$fields = [
                   'raw' => [
                                                 'type' => 'LONGTEXT',
                                                 'null' => true,
                   ],
];

$columns = Ticket::table()->columns;
if (array_key_exists('raw', $columns)) {
    $this->dbforge->modify_column('tickets', $fields);
}

$columns = TicketHasArticle::table()->columns;
if (array_key_exists('raw', $columns)) {
    $this->dbforge->modify_column('ticket_has_articles', $fields);
}

$fields = [
                   'signature' => [
                                                 'type' => 'TEXT',
                                                 'null' => true,
                                                ],
];

$columns = User::table()->columns;
if (array_key_exists('signature', $columns)) {
    $this->dbforge->modify_column('users', $fields);
}
$columns = Client::table()->columns;
if (array_key_exists('signature', $columns)) {
    $this->dbforge->modify_column('clients', $fields);
}

$fields = [
                   'description' => [
                                                 'type' => 'TEXT',
                                                 'null' => true,
                                                ],
];

$columns = Lead::table()->columns;
if (array_key_exists('description', $columns)) {
    $this->dbforge->modify_column('leads', $fields);
}

$columns = LeadStatus::table()->columns;
if (array_key_exists('description', $columns)) {
    $this->dbforge->modify_column('lead_status', $fields);
}

$fields = [
                   'body' => [
                                                 'type' => 'TEXT',
                                                 'null' => true,
                                                ],
];

$columns = Reminder::table()->columns;
if (array_key_exists('body', $columns)) {
    $this->dbforge->modify_column('reminders', $fields);
}
