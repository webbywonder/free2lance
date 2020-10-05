<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->client) {
        } elseif ($this->user) {
        } else {
            redirect('login');
        }
    }

    public function show($id = false)
    {
        $this->theme_view = 'modal';
        $this->view_data['title'] = $this->lang->line('application_view_user');
        $this->view_data['form_action'] = 'users/null';
        $this->view_data['user'] = User::find_by_id($id);
        $this->content_view = 'users/show';
    }
}