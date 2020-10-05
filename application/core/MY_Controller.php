<?php
class My_Controller extends CI_Controller
{
    public $user = false;
    public $client = false;
    public $core_settings = false;
    // Theme functionality
    protected $theme_view = 'application';
    protected $content_view = '';
    protected $view_data = array();
    
    public function __construct()
    {
        parent::__construct();

        /* XSS Filtering */
        if (!empty($_POST)) {
            $fieldList = array("description","message", "terms", "note", "invoice_terms", "estimate_terms", "bank_transfer_text", "smtp_pass", "password", "ticket_config_pass", "css-area");
            $ignoreXSS = array("mail_body");
            function remove_bad_tags_from($field)
            {
                $_POST[$field] = preg_replace('/(&lt;|<)\?php(.*)(\?(&gt;|>))/imx', '[php] $2 [php]', $_POST[$field]);
                $_POST[$field] = preg_replace('/((&lt;|<)(\s*|\/)script(.*?)(&gt;|>))/imx', ' [script] ', $_POST[$field]);
                $_POST[$field] = preg_replace('/((&lt;|<)(\s*)link(.*?)\/?(&gt;|>))/imx', '[link $4 ]', $_POST[$field]);
                $_POST[$field] = preg_replace('/((&lt;|<)(\s*)(\/*)(\s*)style(.*?)(&gt;|>))/imx', ' [style] ', $_POST[$field]);
                $_POST[$field] = preg_replace('/((&lt;|<)(\s*)(\/*)(\s*)input(.*?)(&gt;|>))/imx', ' [input] ', $_POST[$field]);
                $_POST[$field] = preg_replace('/((\s*)(\/*)(\s*)javascript:(.*?))/imx', ' [javascript] ', $_POST[$field]);
                $_POST[$field] = preg_replace('/((\s*)(\/*)(\s*)(alert|confirm|console.log)(\s*?\()(.*?))/imx', ' [blocked] ', $_POST[$field]);
                $_POST[$field] = preg_replace('/((\s *)(\/*)(\s*)(onclick|onfocus|ondblclick|onmouseover|onmousemove|onmouseenter)(\s*?)(\=))/imx', ' [blocked] ', $_POST[$field]);
        
            }

            foreach ($_POST as $key => $value) {
                if (in_array($key, $fieldList)) {
                    remove_bad_tags_from($key);
                } elseif (!in_array($key, $ignoreXSS)) {
                    $_POST[$key] = $this->security->xss_clean($_POST[$key]);
                }
            }
        }

        
        $this->view_data['core_settings'] = Setting::first();

        //Timezone
        if ($this->view_data['core_settings']->timezone != "") {
            date_default_timezone_set($this->view_data['core_settings']->timezone);
        }
        
        $this->view_data['datetime'] = date('Y-m-d H:i', time());
        $date = date('Y-m-d', time());

        //Languages
        if ($this->input->cookie('fc2language') != "") {
            $language = $this->input->cookie('fc2language');
        } else {
            if (isset($this->view_data['language'])) {
                $language = $this->view_data['language'];
            } else {
                if (!empty($this->view_data['core_settings']->language)) {
                    $language = $this->view_data['core_settings']->language;
                } else {
                    $language = "english";
                }
            }
        }
        $this->view_data['time24hours'] = "true";
        switch ($language) {

              case "english": $this->view_data['langshort'] = "en"; $this->view_data['timeformat'] = "h:i K"; $this->view_data['dateformat'] = "F j, Y"; $this->view_data['time24hours'] = "false"; break;
              case "dutch": $this->view_data['langshort'] = "nl"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d-m-Y"; break;
              case "french": $this->view_data['langshort'] = "fr"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d-m-Y"; break;
              case "german": $this->view_data['langshort'] = "de"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d.m.Y"; break;
              case "italian": $this->view_data['langshort'] = "it"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d/m/Y"; break;
              case "norwegian": $this->view_data['langshort'] = "no"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d.m.Y"; break;
              case "polish": $this->view_data['langshort'] = "pl"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d.m.Y"; break;
              case "portuguese": $this->view_data['langshort'] = "pt"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d/m/Y"; break;
              case "portuguese_pt": $this->view_data['langshort'] = "pt"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d/m/Y"; break;
              case "russian": $this->view_data['langshort'] = "ru"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d.m.Y"; break;
              case "spanish": $this->view_data['langshort'] = "es"; $this->view_data['timeformat'] = "H:i"; $this->view_data['dateformat'] = "d/m/Y"; break;
              default: $this->view_data['langshort'] = "en"; $this->view_data['timeformat'] = "h:i K"; $this->view_data['dateformat'] = "F j, Y"; $this->view_data['time24hours'] = "false"; break;

        }

        //fetch installed languages
        $installed_languages = array();
        if ($handle = opendir('application/language/')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != ".DS_Store") {
                    array_push($installed_languages, $entry);
                }
            }
            closedir($handle);
        }

        $this->lang->load('application', $language);
        if (file_exists("./application/language/".$language."/custom_lang.php")) {
            $this->lang->load('custom', $language);
        }
        $this->lang->load('messages', $language);
        $this->lang->load('event', $language);
        $this->view_data['current_language'] = $language;
        $this->view_data['installed_languages'] = $installed_languages;


        //userdata
        $this->user = (($this->session->userdata('user_id') != null) ? User::find_by_id($this->session->userdata('user_id')) : false);
        $this->client = (($this->session->userdata('client_id') != null) ? Client::find_by_id($this->session->userdata('client_id')) : false);
        
        //Check for login link
        if (isset($_GET['loginhash'])) {
            $hash = $_GET['loginhash'];
            
            $entry = Loginsession::find('first', array('conditions' => array('hash = \'' . $hash . '\' AND expires > \'' . date('Y-m-d H:i:s') . '\'')));
            if ($entry) {
                $this->user = User::find_by_id($entry->agent);
            }
        }

        if ($this->user || $this->client) {

            //check if user or client
            if ($this->user) {
                $access            = explode(",", $this->user->access);
                $update            = $this->user;
                $email                = 'u'.$this->user->id;
                $userIsSuperAdmin    = ($this->user->admin == '1') ? true : false;
                $comp_array            = false;
                //Create client access list if active user is not super admin
                if (!$userIsSuperAdmin) {
                    $comp_array = array();
                    $thisUserHasCompanies = (array) $this->user->companies;
                    if (!empty($thisUserHasCompanies)) {
                        foreach ($this->user->companies as $value) {
                            array_push($comp_array, $value->id);
                        }
                        $comp_array = "'".implode(',', $comp_array)."'";
                    } else {
                        $comp_array = 0;
                    }
                }
                    

                $this->view_data['menu'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('id in (?) AND type = ?', $access, 'main')));
                $this->view_data['module_permissions'] = array();
                $notification_list = array();
                foreach ($this->view_data['menu'] as $key => $value) {
                    array_push($this->view_data['module_permissions'], $value->link);
                }
                
                $this->view_data['widgets']        = Module::find('all', array('conditions' => array('id in (?) AND type = ?', $access, 'widget')));
                $this->view_data['user_online']    = User::all(array('conditions' => array('last_active+(30 * 60) > ? AND status = ?', time(), "active")));
                $this->view_data['client_online']    = Client::all(array('conditions' => array('last_active+(30 * 60) > ? AND inactive = ?', time(), "0")));
                $this->view_data['quotations_new']    = Quote::count(array('conditions' => array('status = ?', "New")));
                $this->view_data['quotations_new']    = $this->view_data['quotations_new']+Quoterequest::count(array('conditions' => array('status = ?', "New")));

                $this->view_data['sticky']            = Project::find_by_sql("select distinct(projects.name), projects.id, projects.tracking, projects.progress from projects, project_has_workers where projects.sticky = 1 AND projects.id = project_has_workers.project_id AND project_has_workers.user_id=".$this->user->id);
                
                $this->view_data['tickets_access'] = false;
                if (in_array("tickets", $this->view_data['module_permissions'])) {
                    $this->view_data['tickets_access'] = true;
                    $this->view_data['tickets_new'] = Ticket::newTicketCount($this->user->id, $comp_array);
                }

                
                if (in_array("invoices", $this->view_data['module_permissions'])) {
                    $overdueInvoices = Invoice::overdueByDate($comp_array, $date);
                    foreach ($overdueInvoices as $key => $value) {
                        $line = str_replace("{invoice_number}", '<a href="'.base_url().'invoices/view/'.$value->id.'">#'.$this->view_data['core_settings']->invoice_prefix.$value->reference.'</a>', $this->lang->line('event_invoice_overdue'));
                        $notification_list[$value->due_date.".".$value->id] = $line;
                    }
                }
                if (in_array("subscriptions", $this->view_data['module_permissions'])) {
                    $outstandingInvoices = Subscription::newInvoiceOutstanding($comp_array, $date);
                    foreach ($outstandingInvoices as $key2 => $value2) {
                        $eventline = str_replace("{subscription_number}", '<a href="'.base_url().'subscriptions/view/'.$value2->id.'">#'.$this->view_data['core_settings']->subscription_prefix.$value2->reference.'</a>', $this->lang->line('event_subscription_new_invoice'));
                        $notification_list[$value2->next_payment.".".$value2->id] = $eventline;
                    }
                }

                if (in_array("projects", $this->view_data['module_permissions'])) {
                    $overdueProjects = Project::overdueByDate($this->user->id, $comp_array, $date);
                    //task notification
                    $this->view_data['projects_icon'] = true;
                    $this->view_data['task_notifications'] = ProjectHasTask::find('all', array('conditions' => array('user_id = ? AND tracking != ?', $this->user->id, 0)));
                    foreach ($overdueProjects as $key2 => $value2) {
                        if ($this->user->admin == 0) {
                            $sql = "SELECT id FROM `project_has_workers` WHERE project_id = ".$value->id." AND user_id = ".$this->user->id;
                            $res = Project::find_by_sql($sql);
                            //$res = $query;
                            if ($res) {
                                $eventline = str_replace("{project_number}", '<a href="'.base_url().'projects/view/'.$value2->id.'">#'.$this->view_data['core_settings']->project_prefix.$value2->reference.'</a>', $this->lang->line('event_project_overdue'));
                                $notification_list[$value2->end.".".$value2->id] = $eventline;
                            }
                        } else {
                            $eventline = str_replace("{project_number}", '<a href="'.base_url().'projects/view/'.$value2->id.'">#'.$this->view_data['core_settings']->project_prefix.$value2->reference.'</a>', $this->lang->line('event_project_overdue'));
                            $notification_list[$value2->end.".".$value2->id] = $eventline;
                        }
                    }
                }

                krsort($notification_list);
                $this->view_data["notification_list"] = $notification_list;
                $this->view_data["notification_count"] = count($notification_list);
            } else {
                $this->theme_view = 'application_client';
                $access = $this->client->access;
                $access = explode(",", $access);
                $email = 'c'.$this->client->id;
                $this->view_data['menu'] = Module::find('all', array('order' => 'sort asc', 'conditions' => array('id in (?) AND type = ?', $access, 'client')));
                $update = Client::find($this->client->id);
                $this->view_data['estimates_new'] = Invoice::find_by_sql("select count(id) as amount from invoices where (`estimate_status`='Sent' or `estimate_status`='Revised') and company_id =".$this->client->company_id);
            }

            //Update user last active
            $update->last_active = time();
            $update->save();

            $this->view_data['messages_new'] = Privatemessage::find_by_sql("select count(id) as amount from privatemessages where `status`='New' AND recipient = '".$email."'");
        }

        /*$this->load->database();
        $sql = "select * FROM templates WHERE type='notes'";
        $query = $this->db->query($sql); */
        $this->view_data["note_templates"] = "";//$query->result();

        /* save current url */
        $url = explode('/', $this->uri->uri_string());
        $no_link = array('login', 'register', 'logout', 'language', 'forgotpass', 'postmaster', 'cronjob', 'agent', 'api');
        if (!in_array($this->uri->uri_string(), $no_link) && empty($_POST) && (!isset($url[1]) || $url[1] == "view")) {
            $link = '/'.$this->uri->uri_string();
            $cookie = array(
                       'name'   => 'fc2_link',
                       'value'  => $link,
                       'expire' => '500',
                   );

            $this->input->set_cookie($cookie);
        }
    }
    
    public function _output($output)
    {
        // set the default content view
        if ($this->content_view !== false && empty($this->content_view)) {
            $this->content_view = $this->router->class . '/' . $this->router->method;
        }
        //render the content view
        $yield = file_exists(APPPATH . 'views/' . $this->view_data['core_settings']->template . '/' . $this->content_view .  '.php') ? $this->load->view($this->view_data['core_settings']->template . '/' . $this->content_view, $this->view_data, true) : false;

        //render the theme
        if ($this->theme_view) {
            echo $this->load->view($this->view_data['core_settings']->template . '/' .'theme/' . $this->theme_view, array('yield' => $yield), true);
        } else {
            echo $yield;
        }
        
        echo $output;
    }
}
