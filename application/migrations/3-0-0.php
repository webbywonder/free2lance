<?php $modules = Module::all();
            foreach ($modules as $module) {
            	switch($module->icon){
                    case "icon-th":
                        $icon = "fa-dashboard";
                    break;
                    case "icon-inbox":
                        $icon = "fa-inbox";
                    break;
                    case "icon-briefcase":
                        $icon = "fa-lightbulb-o";
                    break;
                    case "icon-user":
                        $icon = "fa-users";
                    break;
                    case "icon-list-alt":
                        $icon = "fa-file-text-o";
                    break;
                    case "icon-calendar":
                        $icon = "fa-calendar";
                    break;
                    case "icon-file":
                        $icon = "fa-archive";
                    break;
                    case "icon-tag":
                        $icon = "fa-tag";
                    break;
                    case "icon-cog":
                        $icon = "fa-cog";
                    break;
                    default:
                    $icon = $module->icon;
                    break;
               }
               $module->icon = $icon;
               $module->save();
            }


    $fields = array(

                        'login_background' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => 'blur.jpg',
                                                ),
                        'login_logo' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'login_style' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
                                                 'null' => TRUE,
                                                 'default' => 'left',
                                                ),
                        'custom_colors' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => TRUE,
                                                 'default' => 1,
                                                ),
                        'top_bar_background' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '60',
                                                 'null' => TRUE,
                                                 'default' => '#FFFFFF',
                                                ),
                        'top_bar_color' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '60',
                                                 'null' => TRUE,
                                                 'default' => '#333333',
                                                ),
                        'body_background' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '60',
                                                 'null' => TRUE,
                                                 'default' => '#D8DCE3',
                                                ),
                        'menu_background' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '60',
                                                 'null' => TRUE,
                                                 'default' => '#2c3e4d',
                                                ),
                        'menu_color' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '60',
                                                 'null' => TRUE,
                                                 'default' => '#FFFFFF',
                                                ),
                        'primary_color' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '60',
                                                 'null' => TRUE,
                                                 'default' => '#28a9f1',
                                                ),
                        'twocheckout_publishable_key' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'twocheckout_private_key' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'twocheckout_seller_id' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                        'twocheckout' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '10',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'twocheckout_currency' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 'USD',
                                                ),

                        
                   

    );

$columns = Setting::table()->columns;
  if(!array_key_exists("login_background", $columns)){ 
$this->dbforge->add_column('core', $fields);
}

    $fields = array(

                        'client_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '30',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'created_by_client' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '30',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'tracking' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'time_spent' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'milestone_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '100',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'invoice_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '100',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'milestone_order' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '100',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'task_order' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '100',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'progress' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '100',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'start_date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                   

    );

$columns = ProjectHasTask::table()->columns;
  if(!array_key_exists("client_id", $columns)){ 
$this->dbforge->add_column('project_has_tasks', $fields);
}

    $fields = array(
                        'hide_tasks' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                        'enable_client_tasks' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '1',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                   

    );

$columns = Project::table()->columns;
  if(!array_key_exists("hide_tasks", $columns)){ 
$this->dbforge->add_column('projects', $fields);
}

$fields = array(
                   'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '40',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'project_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'name' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'description' => array(
                                                 'type' => 'LONGTEXT',
                                                 'null' => TRUE,
                                                ),
                  'due_date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'start_date' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => '',
                                                ),
                  'orderindex' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '20',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('project_has_milestones', TRUE); 

$fields = array(
                   'id' => array(
                                                 'type' => 'BIGINT',
                                                 'constraint' => '40',
                                                 'unsigned' => TRUE,
                                                 'auto_increment' => TRUE
                                                ),
                  'project_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'user_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'client_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'task_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'time' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'start' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'end' => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '250',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'invoice_id' => array(
                                                 'type' => 'INT',
                                                 'constraint' => '11',
                                                 'null' => TRUE,
                                                 'default' => 0,
                                                ),
                  'description' => array(
                                                 'type' => 'LONGTEXT',
                                                 'null' => TRUE,
                                                ),
                  
                   
);

$this->dbforge->add_field($fields);
$this->dbforge->add_key('id', TRUE);
$this->dbforge->create_table('project_has_timesheets', TRUE); 

$fields = array(
                        'value' => array(
                                                         'type' => 'FLOAT',
                                                         'constraint' => '20',
                                                         'null' => TRUE,
                                                         'default' => 0,
                                                ),
);
$this->dbforge->modify_column('project_has_tasks', $fields);

$attributes = array();
$attributes["id"] = "33";
$attributes["name"] = "Reports";
$attributes["link"] = "reports";
$attributes["type"] = "main";
$attributes["icon"] = "fa-area-chart";
$attributes["sort"] = "8.2";
$setting = Module::create($attributes);
$attributes = array();
$thisuser = User::find_by_id($this->user->id);
$thisuser->access = $thisuser->access.",33";
$thisuser->save();
