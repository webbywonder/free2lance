<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cDashboard extends MY_Controller {
               
	function __construct()
	{
		parent::__construct();

		if($this->client){	
		} elseif ($this->user){
			redirect('dashboard');
		} else {
			redirect('login');
		}
    }
    	
	function index()
	{
        $this->view_data['event_count_for_today'] = 0;

        if (in_array('101', explode(',', $this->client->access))) {
            $this->view_data['hasprojects'] = true;

            $sql = 'SELECT * FROM project_has_tasks WHERE status != "done" AND user_id = "' . $this->client->id . '" ORDER BY project_id';
            $taskquery = ProjectHasTask::find('all', ['conditions' => ['status != ? and user_id = ?', 'done', $this->client->id], 'order' => 'project_id desc', 'limit' => '6']);
            $this->view_data['tasks'] = $taskquery;

            $this->view_data['projects_open'] = Project::count(['conditions' => ['progress < 100 AND company_id = ' . $this->client->company_id]]);
            $this->view_data['projects_all'] = Project::count(['conditions' => ['company_id = ' . $this->client->company_id]]);
            $this->view_data['recent_activities'] = ProjectHasActivity::find('all', ['conditions' => 'client_id = ' . $this->client->id, 'order' => 'datetime desc', 'limit' => 10]);
            
            $this->view_data['openProjectsPercent'] = ($this->view_data['projects_open'] == 0) ? 0 : @round($this->view_data['projects_open'] / $this->view_data['projects_all'] * 100);
        }

        if (in_array('102', explode(',', $this->client->access))) {
            $this->view_data['hasinvoices'] = true;

            $this->view_data['invoices_open'] = Invoice::count(['conditions' => ['company_id = ? AND status != ? AND status != ? AND estimate != ?', $this->client->company_id, 'Paid', 'Canceled', 1]]); // Get all but canceled and Paid invoices
            $this->view_data['invoices_all'] = Invoice::count(['conditions' => ['company_id = ? AND status != ? AND estimate != ?', $this->client->company_id, 'Canceled', 1]]); // Get all but canceled invoices
            
            $this->view_data['openInvoicePercent'] = ($this->view_data['invoices_open'] == 0) ? 0 : @round($this->view_data['invoices_open'] / $this->view_data['invoices_all'] * 100);
        }

        if (in_array('103', explode(',', $this->client->access))) {
            $this->view_data['hasmessages'] = true;

            $sql = 'SELECT pm.id, pm.`status`, pm.subject, pm.message, pm.`time`, pm.`recipient`, cl.`userpic` as userpic_c, us.`userpic` as userpic_u, us.`email` as email_u , cl.`email` as email_c , CONCAT(us.firstname," ", us.lastname) as sender_u, CONCAT(cl.firstname," ", cl.lastname) as sender_c
							FROM privatemessages pm
							LEFT JOIN clients cl ON CONCAT("c",cl.id) = pm.sender
							LEFT JOIN users us ON CONCAT("u",us.id) = pm.sender 
							WHERE pm.recipient = "c' . $this->client->id . '"AND pm.status != "deleted" 
							ORDER BY pm.`time` DESC LIMIT 6';
            $query = Privatemessage::find_by_sql($sql);
            $this->view_data['message'] = array_filter($query);
        }

        if (in_array('104', explode(',', $this->client->access))) {
            $this->view_data['hassubs'] = true;
        }

        if (in_array('106', explode(',', $this->client->access))) {
            $this->view_data['hastickets'] = true;

            $this->view_data['ticket'] = Ticket::find('all', array('conditions' => 'client_id = ' . $this->client->id, 'limit' => '6', 'order' => 'created desc'));
        }

        if (in_array('107', explode(',', $this->client->access))) {
            $this->view_data['hasestimates'] = true;
        }

        $this->content_view = "dashboard/client_views/dashboard";
    }
}