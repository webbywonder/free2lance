<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Projects extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $access = false;
        if ($this->client) {
            if ($this->input->cookie('fc2_link') != "") {
                $link = $this->input->cookie('fc2_link');
                $link = str_replace("/tickets/", "/ctickets/", $link);
                redirect($link);
            } else {
                redirect('cprojects');
            }
        } elseif ($this->user) {
            $this->view_data['invoice_access'] = false;
            foreach ($this->view_data['menu'] as $key => $value) {
                if ($value->link == "invoices") {
                    $this->view_data['invoice_access'] = true;
                }
                if ($value->link == "projects") {
                    $access = true;
                }
            }
            if (!$access) {
                redirect('login');
            }
        } else {
            redirect('login');
        }
        $this->view_data['submenu'] = array(
            $this->lang->line('application_all') => 'projects/filter/all',
            $this->lang->line('application_open') => 'projects/filter/open',
            $this->lang->line('application_closed') => 'projects/filter/closed'
        );
    }

    public function index()
    {
        $settings = Setting::first();
        $options = array('conditions' => 'progress < 100', 'order' => 'id DESC', 'include' => array('company', 'project_has_workers'));
        if ($this->user->admin == 0) {
            $comp_array = array();
            $thisUserHasNoCompanies = (array) $this->user->companies;
            if (!empty($thisUserHasNoCompanies)) {
                foreach ($this->user->companies as $value) {
                    array_push($comp_array, $value->id);
                }
                $projects_by_client_admin = Project::find('all', array('conditions' => array('progress < ? AND company_id in (?)', '100', $comp_array), 'order' => 'id DESC', 'include' => array('company', 'project_has_workers')));

                //merge projects by client admin and assigned to projects
                $result = array_merge($projects_by_client_admin, array_diff($projects_by_client_admin, $this->user->projects));
                //duplicate objects will be removed
                $result = array_map("unserialize", array_unique(array_map("serialize", $result), SORT_STRING));
                //array is sorted on the bases of id
                sort($result);

                $this->view_data['project'] = $result;
            } else {
                $this->view_data['project'] = $this->user->projects;
            }
        } else {
            $this->view_data['project'] = Project::all($options);
        }
        $this->content_view = 'projects/all';

        // $this->view_data['projects_min'] = $min;
        // $this->view_data['projects_max'] = $count;
        // $this->view_data['projects_step'] = 100;

        $this->view_data['projects_assigned_to_me'] = ProjectHasWorker::find_by_sql('select count(distinct(projects.id)) AS "amount" FROM projects, project_has_workers WHERE projects.progress != "100" AND (projects.id = project_has_workers.project_id AND project_has_workers.user_id = "' . $this->user->id . '") ');
        $this->view_data['tasks_assigned_to_me'] = ProjectHasTask::count(array('conditions' => 'user_id = ' . $this->user->id . ' and status = "open"'));

        $now = time();
        $beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
        $end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
        $this->view_data['projects_opened_this_week'] = Project::find_by_sql('select count(id) AS "amount", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%w") AS "date_day", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%Y-%m-%d") AS "date_formatted" from projects where datetime >= "' . $beginning_of_week . '" AND datetime <= "' . $end_of_week . '" Group By date_formatted, `date_day`');
    }
    public function filter($condition)
    {
        $options = array('conditions' => 'progress < 100');
        if ($this->user->admin == 0) {
            switch ($condition) {
                case 'open':
                    $options = 'progress < 100';
                    break;
                case 'closed':
                    $options = 'progress = 100';
                    break;
                case 'all':
                    $options = '(progress = 100 OR progress < 100)';
                    break;
            }

            $project_array = array();
            if ($this->user->projects) {
                foreach ($this->user->projects as $value) {
                    array_push($project_array, $value->id);
                }
            }

            $thisUserHasNoCompanies = (array) $this->user->companies;
            if (!empty($thisUserHasNoCompanies)) {
                $comp_array = array();
                foreach ($this->user->companies as $value) {
                    array_push($comp_array, $value->id);
                }


                $projects_by_client_admin = Project::find('all', array('conditions' => array($options . ' AND company_id in (?)', $comp_array), 'include' => array('company', 'project_has_workers')));

                //merge projects by client admin and assigned to projects
                $result = array_merge($projects_by_client_admin, array_diff($projects_by_client_admin, $this->user->projects));
                //duplicate objects will be removed
                $result = array_map("unserialize", array_unique(array_map("serialize", $result), SORT_STRING));
                //array is sorted on the bases of id
                sort($result);

                $this->view_data['project'] = $result;
            } else {
                $this->view_data['project'] = Project::find('all', array('conditions' => array($options . ' AND id in (?)', $project_array), 'include' => array('company', 'project_has_workers')));
            }
        } else {
            switch ($condition) {
                case 'open':
                    $options = array('conditions' => 'progress < 100');
                    break;
                case 'closed':
                    $options = array('conditions' => 'progress = 100');
                    break;
                case 'all':
                    $options = array('conditions' => 'progress = 100 OR progress < 100');
                    break;
            }
            $this->view_data['project'] = Project::all($options);
        }


        $this->content_view = 'projects/all';

        $this->view_data['projects_assigned_to_me'] = ProjectHasWorker::find_by_sql('select count(distinct(projects.id)) AS "amount" FROM projects, project_has_workers WHERE projects.progress != "100" AND (projects.id = project_has_workers.project_id AND project_has_workers.user_id = "' . $this->user->id . '") ');
        $this->view_data['tasks_assigned_to_me'] = ProjectHasTask::count(array('conditions' => 'user_id = ' . $this->user->id . ' and status = "open"'));

        $now = time();
        $beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
        $end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
        $this->view_data['projects_opened_this_week'] = Project::find_by_sql('select count(id) AS "amount", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%w") AS "date_day", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%Y-%m-%d") AS "date_formatted" from projects where datetime >= "' . $beginning_of_week . '" AND datetime <= "' . $end_of_week . '" Group By date_formatted, `date_day`');
    }
    // public function filter($condition)
    // {
    //     $startfrom = (isset($_GET['start']) ? $_GET['start'] : 0);
    //     $count = Project::find_by_sql('SELECT MAX(id) AS `maxid` FROM `projects` WHERE progress < 100')->maxid;
    //     $min = Project::find_by_sql('SELECT MIN(id) AS `minid` FROM `projects` WHERE progress < 100');
    //     var_dump($min['minid']);
    //     die();
    //     $settings = Setting::first();
    //     $options = array('conditions' => 'progress < 100 AND id <= ' . (($startfrom == 0) ? Project::count(array('conditions' => 'progress < 100')) : $startfrom), 'limit' => $settings->max_table_row);
    //     if ($this->user->admin == 0) {
    //         switch ($condition) {
    //             case 'open':
    //                 $options = 'progress < 100';
    //                 break;
    //             case 'closed':
    //                 $options = 'progress = 100';
    //                 break;
    //             case 'all':
    //                 $options = '(progress = 100 OR progress < 100)';
    //                 break;
    //         }

    //         $project_array = array();
    //         if ($this->user->projects) {
    //             foreach ($this->user->projects as $value) {
    //                 array_push($project_array, $value->id);
    //             }
    //         }

    //         $thisUserHasNoCompanies = (array) $this->user->companies;
    //         if (!empty($thisUserHasNoCompanies)) {
    //             $comp_array = array();
    //             foreach ($this->user->companies as $value) {
    //                 array_push($comp_array, $value->id);
    //             }


    //             $count = Project::find_by_sql('SELECT MAX(id) AS `maxid` FROM `projects` WHERE ' . $options)->maxid;
    //             $min = Project::find_by_sql('SELECT MIN(id) AS `minid` FROM `projects` WHERE ' . $options)->minid;

    //             $projects_by_client_admin = Project::find('all', array('conditions' => array($options . ' AND company_id in (?) AND id <= ?', $comp_array, (($startfrom == 0) ? $count : $startfrom)), 'limit' => $settings->max_table_row, 'include' => array('company', 'project_has_workers')));

    //             //merge projects by client admin and assigned to projects
    //             $result = array_merge($projects_by_client_admin, array_diff($projects_by_client_admin, $this->user->projects));
    //             //duplicate objects will be removed
    //             $result = array_map("unserialize", array_unique(array_map("serialize", $result), SORT_STRING));
    //             //array is sorted on the bases of id
    //             sort($result);

    //             $this->view_data['project'] = $result;
    //         } else {
    //             $count = Project::find_by_sql('SELECT MAX(id) AS `maxid` FROM `projects` WHERE ' . $options)->maxid;
    //             $min = Project::find_by_sql('SELECT MIN(id) AS `minid` FROM `projects` WHERE ' . $options)->minid;
    //             $this->view_data['project'] = Project::find('all', array('conditions' => array($options . ' AND id in (?) AND id <= ?', $project_array, (($startfrom == 0) ? $count : $startfrom)), 'limit' => $settings->max_table_row, 'include' => array('company', 'project_has_workers')));
    //         }
    //     } else {
    //         switch ($condition) {
    //             case 'open':
    //                 $count = Project::find_by_sql('SELECT MAX(id) AS `maxid` FROM `projects` WHERE progress < 100')->maxid;
    //                 $min = Project::find_by_sql('SELECT MIN(id) AS `minid` FROM `projects` WHERE progress < 100')->minid;
    //                 $options = array('conditions' => 'progress < 100 AND id <=' . (($startfrom == 0) ? $count : $startfrom), 'limit' => $settings->max_table_row);
    //                 break;
    //             case 'closed':
    //                 $count = Project::find_by_sql('SELECT MAX(id) AS `maxid` FROM `projects` WHERE progress = 100')->maxid;
    //                 $min = Project::find_by_sql('SELECT MIN(id) AS `minid` FROM `projects` WHERE progress = 100')->minid;
    //                 $options = array('conditions' => 'progress = 100 AND id <= ' . (($startfrom == 0) ? $count : $startfrom), 'limit' => $settings->max_table_row);
    //                 break;
    //             case 'all':
    //                 $count = Project::find_by_sql('SELECT MAX(id) AS `maxid` FROM `projects` WHERE progress = 100 OR progress < 100')->maxid;
    //                 $min = Project::find_by_sql('SELECT MIN(id) AS `minid` FROM `projects` WHERE progress = 100 OR progress < 100')->minid;
    //                 $options = array('conditions' => '(progress = 100 OR progress < 100) AND id <= ' . (($startfrom == 0) ? $count : $startfrom), 'limit' => $settings->max_table_row);
    //                 break;
    //         }
    //         $this->view_data['project'] = Project::all($options);
    //     }


    //     $this->content_view = 'projects/all';

    //     $this->view_data['filterurl'] = $condition;
    //     $this->view_data['projects_max'] = $count;
    //     $this->view_data['projects_min'] = $min;
    //     $this->view_data['projects_step'] = $settings->max_table_row;

    //     $this->view_data['projects_assigned_to_me'] = ProjectHasWorker::find_by_sql('select count(distinct(projects.id)) AS "amount" FROM projects, project_has_workers WHERE projects.progress != "100" AND (projects.id = project_has_workers.project_id AND project_has_workers.user_id = "' . $this->user->id . '") ');
    //     $this->view_data['tasks_assigned_to_me'] = ProjectHasTask::count(array('conditions' => 'user_id = ' . $this->user->id . ' and status = "open"'));

    //     $now = time();
    //     $beginning_of_week = strtotime('last Monday', $now); // BEGINNING of the week
    //     $end_of_week = strtotime('next Sunday', $now) + 86400; // END of the last day of the week
    //     $this->view_data['projects_opened_this_week'] = Project::find_by_sql('select count(id) AS "amount", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%w") AS "date_day", DATE_FORMAT(FROM_UNIXTIME(`datetime`), "%Y-%m-%d") AS "date_formatted" from projects where datetime >= "' . $beginning_of_week . '" AND datetime <= "' . $end_of_week . '" Group By date_formatted, `date_day`');
    // }
    public function create()
    {
        if ($_POST) {
            unset($_POST['send']);
            $_POST['datetime'] = time();
            $_POST = array_map('htmlspecialchars', $_POST);
            unset($_POST['files']);

            $project = Project::create($_POST);
            $new_project_reference = $_POST['reference'] + 1;
            $project_reference = Setting::first();
            $project_reference->update_attributes(array('project_reference' => $new_project_reference));
            if (!$project) {
                $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_project_error'));
            } else {
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_project_success'));
                $attributes = array('project_id' => $project->id, 'user_id' => $this->user->id);
                ProjectHasWorker::create($attributes);
            }
            redirect('projects');
        } else {
            if ($this->user->admin == 0) {
                $this->view_data['companies'] = $this->user->companies;
            } else {
                $this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', '0')));
            }

            $this->view_data['status_options'] = array(
                'notstarted' => $this->lang->line('application_project_status_notstarted'),
                'started' => $this->lang->line('application_project_status_started'),
                'onhold' => $this->lang->line('application_project_status_onhold'),
                'finished' => $this->lang->line('application_project_status_finished'),
                'canceled' => $this->lang->line('application_project_status_canceled')
            );

            $this->view_data['next_reference'] = Project::last();
            $this->view_data['category_list'] = Project::get_categories();
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_create_project');
            $this->view_data['form_action'] = 'projects/create';
            $this->content_view = 'projects/_project';
        }
    }
    public function update($id = false)
    {
        if ($_POST) {
            unset($_POST['send']);
            $id = $_POST['id'];
            unset($_POST['files']);
            $_POST = array_map('htmlspecialchars', $_POST);
            if (!isset($_POST["progress_calc"])) {
                $_POST["progress_calc"] = 0;
            }
            if ($this->user->admin == 1) {
                if (!isset($_POST["hide_tasks"])) {
                    $_POST["hide_tasks"] = 0;
                }
            }
            if (!isset($_POST["enable_client_tasks"])) {
                $_POST["enable_client_tasks"] = 0;
            }

            if ($_POST['progress'] == '100') {
                $_POST['status'] = 'finished';
            }

            $project = Project::find($id);
            $project->update_attributes($_POST);
            if (!$project) {
                $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_project_error'));
            } else {
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_project_success'));
            }
            redirect('projects/view/' . $id);
        } else {
            if ($this->user->admin == 0) {
                $this->view_data['companies'] = $this->user->companies;
            } else {
                $this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', '0')));
            }

            $this->view_data['status_options'] = array(
                'notstarted' => $this->lang->line('application_project_status_notstarted'),
                'started' => $this->lang->line('application_project_status_started'),
                'onhold' => $this->lang->line('application_project_status_onhold'),
                'finished' => $this->lang->line('application_project_status_finished'),
                'canceled' => $this->lang->line('application_project_status_canceled')
            );

            $this->view_data['project'] = Project::find($id);
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_edit_project');
            $this->view_data['form_action'] = 'projects/update';
            $this->content_view = 'projects/_project';
        }
    }
    public function sortlist($sort = false, $list = false)
    {
        if ($sort) {
            $sort = explode("-", $sort);
            $sortnumber = 1;
            foreach ($sort as $value) {
                $task = ProjectHasTask::find_by_id($value);
                if ($list != "task-list") {
                    $task->milestone_order = $sortnumber;
                } else {
                    $task->task_order = $sortnumber;
                }
                $task->save();
                $sortnumber = $sortnumber + 1;
            }
        }
        $this->theme_view = 'blank';
    }
    public function sort_milestone_list($sort = false, $list = false)
    {
        if ($sort) {
            $sort = explode("-", $sort);
            $sortnumber = 1;
            foreach ($sort as $value) {
                $task = ProjectHasMilestone::find_by_id($value);
                $task->orderindex = $sortnumber;
                $task->save();
                $sortnumber = $sortnumber + 1;
            }
        }
        $this->theme_view = 'blank';
    }
    public function move_task_to_milestone($taskId = false, $listId = false)
    {
        if ($listId && $taskId) {
            $task = ProjectHasTask::find_by_id($taskId);
            $task->milestone_id = $listId;
            $task->save();
        }
        $this->theme_view = 'blank';
    }
    public function task_change_attribute()
    {
        if ($_POST) {
            $name = $_POST["name"];
            $taskId = $_POST["pk"];
            $value = $_POST["value"];
            $task = ProjectHasTask::find_by_id($taskId);
            $task->{$name} = $value;
            $task->save();
        }
        $this->theme_view = 'blank';
    }
    public function task_start_stop_timer($taskId)
    {
        $task = ProjectHasTask::find_by_id($taskId);
        if ($task->tracking != 0) {
            $now = time();
            $diff = $now - $task->tracking;
            $timer_start = $task->tracking;
            $task->time_spent = $task->time_spent + $diff;
            $task->tracking = "";
            //add time to timesheet
            $attributes = array(
                'task_id' => $task->id,
                'user_id' => $task->user_id,
                'project_id' => $task->project_id,
                'client_id' => 0,
                'time' => $diff,
                'start' => $timer_start,
                'end' => $now
            );
            $timesheet = ProjectHasTimesheet::create($attributes);
        } else {
            $task->tracking = time();
        }
        $task->save();
        $this->theme_view = 'blank';
    }
    public function get_milestone_list($projectId)
    {
        $milestone_list = "";
        $project = Project::find_by_id($projectId);
        foreach ($project->project_has_milestones as $value) {
            $milestone_list .= '{value:' . $value->id . ', text: "' . $value->name . '"},';
        }
        echo $milestone_list;
        $this->theme_view = 'blank';
    }
    public function copy($id = false)
    {
        if ($_POST) {
            unset($_POST['send']);
            $id = $_POST['id'];
            unset($_POST['id']);
            $_POST['datetime'] = time();
            $_POST = array_map('htmlspecialchars', $_POST);
            unset($_POST['files']);
            if (isset($_POST['tasks'])) {
                unset($_POST['tasks']);
                $tasks = true;
            }

            $project = Project::create($_POST);
            $new_project_reference = $_POST['reference'] + 1;
            $project_reference = Setting::first();
            $project_reference->update_attributes(array('project_reference' => $new_project_reference));

            if ($tasks) {
                unset($_POST['tasks']);
                $source_project    = Project::find_by_id($id);
                foreach ($source_project->project_has_tasks as $row) {
                    $attributes = array(
                        'project_id' => $project->id,
                        'name' => $row->name,
                        'user_id' => '',
                        'status' => 'open',
                        'public' => $row->public,
                        'datetime' => $project->start,
                        'due_date' => $project->end,
                        'description' => $row->description,
                        'value' => $row->value,
                        'priority' => $row->priority,

                    );
                    ProjectHasTask::create($attributes);
                }
            }

            if (!$project) {
                $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_project_error'));
            } else {
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_project_success'));
                $attributes = array('project_id' => $project->id, 'user_id' => $this->user->id);
                ProjectHasWorker::create($attributes);
            }
            redirect('projects/view/' . $id);
        } else {
            $this->view_data['companies'] = Company::find('all', array('conditions' => array('inactive=?', '0')));
            $this->view_data['project'] = Project::find($id);
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_copy_project');
            $this->view_data['form_action'] = 'projects/copy';
            $this->content_view = 'projects/_copy';
        }
    }
    public function assign($id = false)
    {
        $this->load->helper('notification');
        if ($_POST) {
            unset($_POST['send']);
            $id = addslashes($_POST['id']);
            $project = Project::find_by_id($id);
            if (!isset($_POST["user_id"])) {
                $_POST["user_id"] = array();
            }
            $query = array();
            foreach ($project->project_has_workers as $key => $value) {
                array_push($query, $value->user_id);
            }

            $added = array_diff($_POST["user_id"], $query);
            $removed = array_diff($query, $_POST["user_id"]);

            foreach ($added as $value) {
                $atributes = array('project_id' => $id, 'user_id' => $value);
                $worker = ProjectHasWorker::create($atributes);
                send_notification($worker->user->email, $this->lang->line('application_notification_project_assign_subject'), $this->lang->line('application_notification_project_assign') . '<br><strong>' . $project->name . '</strong>');
            }

            foreach ($removed as $value) {
                $atributes = array('project_id' => $id, 'user_id' => $value);
                $worker = ProjectHasWorker::find($atributes);
                $worker->delete();
            }

            $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_project_success'));
            redirect('projects/view/' . $id);
        } else {
            $this->view_data['users'] = User::find('all', array('conditions' => array('status=?', 'active')));
            $this->view_data['project'] = Project::find($id);
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_assign_to_agents');
            $this->view_data['form_action'] = 'projects/assign';
            $this->content_view = 'projects/_assign';
        }
    }
    public function delete($id = false)
    {
        if ($this->user->admin == 0) {
            $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_project_error'));
            redirect('projects');
        }
        $project = Project::find($id);
        $project->delete();
        $tasks = ProjectHasTask::find('all', array('conditions' => array('project_id=?', $id)));
        $toDelete = array();
        foreach ($tasks as $value) {
            array_push($toDelete, $value->id);
        }
        ProjectHasTask::table()->delete(array('id' => $toDelete));
        $this->content_view = 'projects/all';
        if (!$project) {
            $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_project_error'));
        } else {
            $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_project_success'));
        }
        if (isset($view)) {
            redirect('projects/view/' . $id);
        } else {
            redirect('projects');
        }
    }
    public function timer_reset($id = false)
    {
        $project = Project::find($id);
        $attr = array('time_spent' => '0');
        $project->update_attributes($attr);
        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_timer_reset'));
        redirect('projects/view/' . $id);
    }
    public function timer_set($id = false)
    {
        if ($_POST) {
            $project = Project::find_by_id($_POST['id']);
            $hours = $_POST['hours'];
            $minutes = $_POST['minutes'];
            $timespent = ($hours * 60 * 60) + ($minutes * 60);
            $attr = array('time_spent' => $timespent);
            $project->update_attributes($attr);
            $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_timer_set'));
            redirect('projects/view/' . $_POST['id']);
        } else {
            $this->view_data['project'] = Project::find($id);
            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_timer_set');
            $this->view_data['form_action'] = 'projects/timer_set';
            $this->content_view = 'projects/_timer';
        }
    }
    public function view($id = false, $taskId = false)
    {
        $this->load->helper('file');
        $this->view_data['submenu'] = array();
        $this->view_data['project'] = Project::find($id);
        $this->view_data['go_to_taskID'] = $taskId;
        $this->view_data['first_project'] = Project::first();
        $this->view_data['last_project'] = Project::last();
        $this->view_data['project_has_invoices'] = Invoice::all(array('conditions' => array('project_id = ? AND estimate != ?', $id, 1)));
        if (!isset($this->view_data['project_has_invoices'])) {
            $this->view_data['project_has_invoices'] = array();
        }
        $tasks = ProjectHasTask::count(array('conditions' => 'project_id = ' . $id));
        $this->view_data['alltasks'] = $tasks;
        $this->view_data['opentasks'] = ProjectHasTask::count(array('conditions' => array('status != ? AND project_id = ?', 'done', $id)));
        $this->view_data['usercountall'] = User::count(array('conditions' => array('status = ?', 'active')));
        $this->view_data['usersassigned'] = ProjectHasWorker::count(array('conditions' => array('project_id = ?', $id)));

        $this->view_data['assigneduserspercent'] = round($this->view_data['usersassigned'] / $this->view_data['usercountall'] * 100);


        //Format statistic labels and values
        $this->view_data["labels"] = "";
        $this->view_data["line1"] = "";
        $this->view_data["line2"] = "";

        $daysOfWeek = getDatesOfWeek();
        $this->view_data['dueTasksStats'] = ProjectHasTask::getDueTaskStats($id, $daysOfWeek[0], $daysOfWeek[6]);
        $this->view_data['startTasksStats'] = ProjectHasTask::getStartTaskStats($id, $daysOfWeek[0], $daysOfWeek[6]);


        foreach ($daysOfWeek as $day) {
            $counter = "0";
            $counter2 = "0";
            foreach ($this->view_data['dueTasksStats'] as $value) :
                if ($value->due_date == $day) {
                    $counter = $value->tasksdue;
                }
            endforeach;
            foreach ($this->view_data['startTasksStats'] as $value) :
                if ($value->start_date == $day) {
                    $counter2 = $value->tasksdue;
                }
            endforeach;
            $this->view_data["labels"] .= '"' . $day . '"';
            $this->view_data["labels"] .= ',';
            $this->view_data["line1"] .= $counter . ",";
            $this->view_data["line2"] .= $counter2 . ",";
        }




        $this->view_data['time_days'] = round((human_to_unix($this->view_data['project']->end . ' 00:00') - human_to_unix($this->view_data['project']->start . ' 00:00')) / 3600 / 24);
        $this->view_data['time_left'] = $this->view_data['time_days'];
        $this->view_data['timeleftpercent'] = 100;

        if (human_to_unix($this->view_data['project']->start . ' 00:00') < time() && human_to_unix($this->view_data['project']->end . ' 00:00') > time()) {
            $this->view_data['time_left'] = round((human_to_unix($this->view_data['project']->end . ' 00:00') - time()) / 3600 / 24);
            $this->view_data['timeleftpercent'] = $this->view_data['time_left'] / $this->view_data['time_days'] * 100;
        }
        if (human_to_unix($this->view_data['project']->end . ' 00:00') < time()) {
            $this->view_data['time_left'] = 0;
            $this->view_data['timeleftpercent'] = 0;
        }
        $this->view_data['allmytasks'] = ProjectHasTask::all(array('conditions' => array('project_id = ? AND user_id = ?', $id, $this->user->id)));
        $this->view_data['mytasks'] = ProjectHasTask::count(array('conditions' => array('status != ? AND project_id = ? AND user_id = ?', 'done', $id, $this->user->id)));
        $this->view_data['tasksWithoutMilestone'] = ProjectHasTask::find('all', array('conditions' => array('milestone_id = ? AND project_id = ? ', '0', $id)));

        $tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
        $this->view_data['progress'] = $this->view_data['project']->progress;
        if ($this->view_data['project']->progress_calc == 1) {
            if ($tasks) {
                @$this->view_data['progress'] = round($tasks_done / $tasks * 100);
            }
            $attr = array('progress' => $this->view_data['progress']);
            $this->view_data['project']->update_attributes($attr);
        }
        @$this->view_data['opentaskspercent'] = ($tasks == 0 ? 0 : $tasks_done / $tasks * 100);
        $projecthasworker = ProjectHasWorker::all(array('conditions' => array('user_id = ? AND project_id = ?', $this->user->id, $id)));
        @$this->view_data['worker_is_client_admin'] = CompanyHasAdmin::all(array('conditions' => array(
            'user_id = ? AND
		 company_id = ?',
            $this->user->id,
            $this->view_data['project']->company_id
        )));
        if (!$projecthasworker && $this->user->admin != 1 && !$this->view_data['worker_is_client_admin']) {
            $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_no_access_error'));
            redirect('projects');
        }
        $tracking = $this->view_data['project']->time_spent;
        if (!empty($this->view_data['project']->tracking)) {
            $tracking = (time() - $this->view_data['project']->tracking) + $this->view_data['project']->time_spent;
        }
        $this->view_data['timertime'] = $tracking;
        $this->view_data['time_spent_from_today'] = time() - $this->view_data['project']->time_spent;
        $tracking = floor($tracking / 60);
        $tracking_hours = floor($tracking / 60);
        $tracking_minutes = $tracking - ($tracking_hours * 60);



        $this->view_data['time_spent'] = $tracking_hours . " " . $this->lang->line('application_hours') . " " . $tracking_minutes . " " . $this->lang->line('application_minutes');
        $this->view_data['time_spent_counter'] = sprintf("%02s", $tracking_hours) . ":" . sprintf("%02s", $tracking_minutes);

        $this->view_data['tickets'] = Ticket::find('all', array('conditions' => array('project_id = ?', $id)));



        $this->view_data['project_has_expenses'] = Expense::all(array('conditions' => array('project_id = ?', $id)));
        if (!isset($this->view_data['project_has_expenses'])) {
            $this->view_data['project_has_expenses'] = array();
        }

        $this->content_view = 'projects/view';
    }
    public function ganttChart($id)
    {
        $gantt_data = "[";
        $project = Project::find_by_id($id);
        foreach ($project->project_has_milestones as $milestone) :

            $counter = 0;
            foreach ($milestone->project_has_tasks as $value) :
                $milestone_Name = ($counter == 0) ? $milestone->name : "";
                $counter++;
                $start = ($value->start_date) ? $value->start_date : $milestone->start_date;
                $end = ($value->due_date) ? $value->due_date : $milestone->due_date;

                $gantt_data .= '{ name: "' . $milestone_Name . '", desc: "' . $value->name . '", values: [';

                $gantt_data .= '{ label: "' . $value->name . '", from: "' . $start . '", to: "' . $end . '" }';
                $gantt_data .= ']},';
            endforeach;

        endforeach;
        $gantt_data .= "]";
        $this->theme_view = 'blank';


        echo $gantt_data;
    }
    public function quicktask()
    {
        if ($_POST) {
            $_POST = array_map('htmlspecialchars', $_POST);
            unset($_POST['send']);
            unset($_POST['files']);
            $task = ProjectHasTask::create($_POST);
            echo $task->id;
        }

        $this->theme_view = 'blank';
    }
    public function generate_thumbs($id = false)
    {
        if ($id) {
            $medias = Project::find_by_id($id)->project_has_files;
            //check image processor extension
            if (extension_loaded('gd2')) {
                $lib = 'gd2';
            } else {
                $lib = 'gd';
            }
            foreach ($medias as $value) {
                if (!file_exists('./files/media/thumb_' . $value->savename)) {
                    $config['image_library'] = $lib;
                    $config['source_image']    = './files/media/' . $value->savename;
                    $config['new_image']    = './files/media/thumb_' . $value->savename;
                    $config['create_thumb'] = true;
                    $config['thumb_marker'] = "";
                    $config['maintain_ratio'] = true;
                    $config['width']    = 170;
                    $config['height']    = 170;
                    $config['master_dim']    = "height";
                    $config['quality']    = "100%";
                    $this->load->library('image_lib');
                    $this->image_lib->initialize($config);
                    $this->image_lib->resize();
                    $this->image_lib->clear();
                }
            }
            redirect('projects/view/' . $id);
        }
    }
    public function dropzone($id = false)
    {
        $attr = array();
        $config['upload_path'] = './files/media/';
        $config['encrypt_name'] = true;
        $config['allowed_types'] = '*';

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path']);
        }

        $this->load->library('upload', $config);


        if ($this->upload->do_upload("file")) {
            $data = array('upload_data' => $this->upload->data());

            $attr['name'] = $data['upload_data']['orig_name'];
            $attr['filename'] = $data['upload_data']['orig_name'];
            $attr['savename'] = $data['upload_data']['file_name'];
            $attr['type'] = $data['upload_data']['file_type'];
            $attr['date'] = date("Y-m-d H:i", time());
            $attr['phase'] = "";

            $attr['project_id'] = $id;
            $attr['user_id'] = $this->user->id;
            $media = ProjectHasFile::create($attr);
            echo $media->id;

            //check image processor extension
            if (extension_loaded('gd2')) {
                $lib = 'gd2';
            } else {
                $lib = 'gd';
            }
            $config['image_library'] = $lib;
            $config['source_image']    = './files/media/' . $attr['savename'];
            $config['new_image']    = './files/media/thumb_' . $attr['savename'];
            $config['create_thumb'] = true;
            $config['thumb_marker'] = "";
            $config['maintain_ratio'] = true;
            $config['width']    = 170;
            $config['height']    = 170;
            $config['master_dim']    = "height";
            $config['quality']    = "100%";




            $this->load->library('image_lib');
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();

            $attributes = array('subject' => $this->lang->line('application_new_media_subject'), 'message' => '<b>' . $this->user->firstname . ' ' . $this->user->lastname . '</b> ' . $this->lang->line('application_uploaded') . ' ' . $attr['filename'], 'datetime' => time(), 'project_id' => $id, 'type' => 'media', 'user_id' => $this->user->id);
            $activity = ProjectHasActivity::create($attributes);
        } else {
            echo "Upload faild";
            $error = $this->upload->display_errors('', ' ');
            $this->session->set_flashdata('message', 'error:' . $error);
            echo $error;
        }





        $this->theme_view = 'blank';
    }
    public function timesheets($taskid)
    {
        $this->view_data['timesheets'] = ProjectHasTimesheet::find("all", array("conditions" => array("task_id = ?", $taskid)));
        $this->view_data['task'] = ProjectHasTask::find_by_id($taskid);

        $this->theme_view = 'modal';
        $this->view_data['title'] = $this->lang->line('application_timesheet');
        $this->view_data['form_action'] = 'projects/timesheet_add';
        $this->content_view = 'projects/_timesheets';
    }
    public function timesheet_add()
    {
        if ($_POST) {
            $time = ($_POST["hours"] * 3600) + ($_POST["minutes"] * 60);
            $attr = array(
                "project_id" => $_POST["project_id"],
                "user_id" => $_POST["user_id"],
                "time" => $time,
                "client_id" => 0,
                "task_id" => $_POST["task_id"],
                "start" => $_POST["start"],
                "end" => $_POST["end"],
                "invoice_id" => 0,
                "description" => "",
            );
            $timesheet = ProjectHasTimesheet::create($attr);
            $task = ProjectHasTask::find_by_id($timesheet->task_id);
            $task->time_spent =    $task->time_spent + $time;
            $task->save();
            echo $timesheet->id;
        }
        $this->theme_view = 'blank';
    }
    public function timesheet_delete($timesheet_id)
    {
        $timesheet = ProjectHasTimesheet::find_by_id($timesheet_id);
        $task = ProjectHasTask::find_by_id($timesheet->task_id);
        $task->time_spent = $task->time_spent - $timesheet->time;
        $task->save();
        $timesheet->delete();
        $this->theme_view = 'blank';
    }
    public function privnote($id = false, $value = false)
    {
        $this->theme_view = 'blank';
        $project = Project::find($id);
        if ($project) {
            $project->privnote = ($value == '0') ? false : true;
            $project->save();
            echo ($project->privnote) ? '1' : '0';
        }
    }
    public function tasks($id = false, $condition = false, $task_id = false)
    {
        $this->view_data['submenu'] = array(
            $this->lang->line('application_back') => 'projects',
            $this->lang->line('application_overview') => 'projects/view/' . $id,
        );
        switch ($condition) {
            case 'add':
                $this->content_view = 'projects/_tasks';
                if ($_POST) {
                    unset($_POST['send']);
                    unset($_POST['files']);
                    $description = $_POST['description'];
                    $_POST = array_map('htmlspecialchars', $_POST);
                    $_POST['description'] = $description;
                    $_POST['value'] = str_replace(',', '', $_POST['value']);
                    $_POST['project_id'] = $id;
                    $task = ProjectHasTask::create($_POST);

                    if ($task) {
                        $data['core_settings'] = Setting::first();
                        if ($data['core_settings']->sendmail_on_taskassign) {
                            $agent = Users::find('first', array('id' => $_POST['user_id']));
                            if ($agent) {
                                $this->load->library('parser');
                                $this->load->helper('file');

                                $this->email->from($data['core_settings']->email, $data['core_settings']->company);
                                $this->email->to($agent->email);

                                $this->email->subject($data['core_settings']->task_assign_mail_subject);
                                $parse_data = [
                                    'task_link' => base_url() . 'projects/view/' . $id . '#task-tab',
                                    'company' => $data['core_settings']->company,
                                    'client_contact' => $agent->firstname . ' ' . $agent->lastname,
                                    'assigned' => $agent->firstname . ' ' . $agent->lastname,
                                    'task_id' => $task->id,
                                    'project_id' => $id,
                                    'logo' => '<img src="' . base_url() . '' . $data['core_settings']->logo . '" alt="' . $data['core_settings']->company . '"/>',
                                    'invoice_logo' => '<img src="' . base_url() . '' . $data['core_settings']->invoice_logo . '" alt="' . $data['core_settings']->company . '"/>'
                                ];
                                $email = file_get_contents('./application/views/' . $data['core_settings']->template . '/templates/email_taskassign.html');
                                $message = $this->parser->parse_string($email, $parse_data);
                                $this->email->message($message);
                                $this->email->send();
                            }
                        }
                    }

                    if (!$task) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_task_success'));
                    }
                    redirect('projects/view/' . $id);
                } else {
                    $this->theme_view = 'modal';
                    $this->view_data['project'] = Project::find($id);
                    $this->view_data['title'] = $this->lang->line('application_add_task');
                    $this->view_data['form_action'] = 'projects/tasks/' . $id . '/add';
                    $this->content_view = 'projects/_tasks';
                }
                break;
            case 'update':
                $this->content_view = 'projects/_tasks';
                $this->view_data['task'] = ProjectHasTask::find($task_id);
                if ($_POST) {
                    unset($_POST['send']);
                    unset($_POST['files']);
                    if (!isset($_POST['public'])) {
                        $_POST['public'] = 0;
                    }
                    $description = $_POST['description'];
                    $_POST = array_map('htmlspecialchars', $_POST);
                    $_POST['description'] = $description;
                    $_POST['value'] = str_replace(',', '', $_POST['value']);
                    $task_id = $_POST['id'];
                    $task = ProjectHasTask::find($task_id);

                    if ($task->user_id != $_POST['user_id']) {
                        //stop timer and add time to timesheet
                        if ($task->tracking != 0) {
                            $now = time();
                            $diff = $now - $task->tracking;
                            $timer_start = $task->tracking;
                            $task->time_spent = $task->time_spent + $diff;
                            $task->tracking = "";
                            $attributes = array(
                                'task_id' => $task->id,
                                'user_id' => $task->user_id,
                                'project_id' => $task->project_id,
                                'client_id' => 0,
                                'time' => $diff,
                                'start' => $timer_start,
                                'end' => $now
                            );
                            $timesheet = ProjectHasTimesheet::create($attributes);
                        }
                    }

                    $oldagent = User::find('first', array('id' => $task->user_id));

                    $task->update_attributes($_POST);

                    if ($task) {
                        $data['core_settings'] = Setting::first();
                        if ($data['core_settings']->sendmail_on_taskassign) {
                            $agent = Users::find('first', array('id' => $_POST['user_id']));
                            if ($agent && $agent != $oldagent) {
                                $this->load->library('parser');
                                $this->load->helper('file');

                                $this->email->from($data['core_settings']->email, $data['core_settings']->company);
                                $this->email->to($agent->email);

                                $this->email->subject($data['core_settings']->task_assign_mail_subject);
                                $parse_data = [
                                    'task_link' => base_url() . 'projects/view/' . $id . '#task-tab',
                                    'company' => $data['core_settings']->company,
                                    'client_contact' => $oldagent->firstname . ' ' . $oldagent->lastname,
                                    'assigned' => $agent->firstname . ' ' . $agent->lastname,
                                    'task_id' => $task->id,
                                    'project_id' => $id,
                                    'logo' => '<img src="' . base_url() . '' . $data['core_settings']->logo . '" alt="' . $data['core_settings']->company . '"/>',
                                    'invoice_logo' => '<img src="' . base_url() . '' . $data['core_settings']->invoice_logo . '" alt="' . $data['core_settings']->company . '"/>'
                                ];
                                $email = file_get_contents('./application/views/' . $data['core_settings']->template . '/templates/email_taskassign.html');
                                $message = $this->parser->parse_string($email, $parse_data);
                                $this->email->message($message);
                                $this->email->send();

                                $this->email->from($data['core_settings']->email, $data['core_settings']->company);
                                $this->email->to($agent->email);

                                $this->email->subject($data['core_settings']->task_assign_mail_subject);
                                $parse_data = [
                                    'task_link' => base_url() . 'projects/view/' . $id . '#task-tab',
                                    'company' => $data['core_settings']->company,
                                    'client_contact' => $agent->firstname . ' ' . $agent->lastname,
                                    'assigned' => $agent->firstname . ' ' . $agent->lastname,
                                    'task_id' => $task->id,
                                    'project_id' => $id,
                                    'logo' => '<img src="' . base_url() . '' . $data['core_settings']->logo . '" alt="' . $data['core_settings']->company . '"/>',
                                    'invoice_logo' => '<img src="' . base_url() . '' . $data['core_settings']->invoice_logo . '" alt="' . $data['core_settings']->company . '"/>'
                                ];
                                $email = file_get_contents('./application/views/' . $data['core_settings']->template . '/templates/email_taskassign.html');
                                $message = $this->parser->parse_string($email, $parse_data);
                                $this->email->message($message);
                                $this->email->send();
                            }
                        }
                    }

                    if (!$task) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_task_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_task_success'));
                    }
                    redirect('projects/view/' . $id);
                } else {
                    $this->theme_view = 'modal';
                    $this->view_data['project'] = Project::find($id);
                    $this->view_data['title'] = $this->lang->line('application_edit_task');
                    $this->view_data['form_action'] = 'projects/tasks/' . $id . '/update/' . $task_id;
                    $this->content_view = 'projects/_tasks';
                }
                break;
            case 'check':
                $this->theme_view = 'blank';
                $task = ProjectHasTask::find($task_id);
                if ($task->status == 'done') {
                    $task->status = 'open';
                } else {
                    if ($task->tracking > 0) {
                        json_response("error", htmlspecialchars($this->lang->line('application_task_timer_must_be_stopped_first')));
                    }
                    $task->status = 'done';

                    $data['core_settings'] = Setting::first();

                    if ($data['core_settings']->sendmail_on_taskcomplete && $task->public && $task->project->company_id) {

                        if ($clients = Client::all(array('conditions' => array('company_id = ?', $task->project->company_id)))) {

                            foreach ($clients as $client) {
                                $this->load->library('parser');
                                $this->load->helper('file');

                                $this->email->from($data['core_settings']->email, $data['core_settings']->company);
                                $this->email->to($client->email);

                                $this->email->subject($data['core_settings']->task_complete_mail_subject);
                                $parse_data = [
                                    'task_link' => base_url() . 'projects/view/' . $id . '#task-tab',
                                    'company' => $data['core_settings']->company,
                                    'client_contact' => $client->firstname . ' ' . $client->lastname,
                                    'task_id' => $task->id,
                                    'project_id' => $id,
                                    'logo' => '<img src="' . base_url() . '' . $data['core_settings']->logo . '" alt="' . $data['core_settings']->company . '"/>',
                                    'invoice_logo' => '<img src="' . base_url() . '' . $data['core_settings']->invoice_logo . '" alt="' . $data['core_settings']->company . '"/>'
                                ];
                                $email = file_get_contents('./application/views/' . $data['core_settings']->template . '/templates/email_taskcomplete.html');
                                $message = $this->parser->parse_string($email, $parse_data);
                                $this->email->message($message);
                                $this->email->send();
                            }
                        }
                    }
                }

                $task->save();
                $project = Project::find($id);
                $tasks = ProjectHasTask::count(array('conditions' => 'project_id = ' . $id));
                $tasks_done = ProjectHasTask::count(array('conditions' => array('status = ? AND project_id = ?', 'done', $id)));
                if ($project->progress_calc == 1) {
                    if ($tasks) {
                        $progress = round($tasks_done / $tasks * 100);
                    }
                    $attr = array('progress' => $progress);
                    if ($progress == 100) {
                        array_push($attr, ['status' => 'finished']);
                    }
                    $project->update_attributes($attr);
                }

                if (!$task) {
                    json_response("error", "Error while task toggle!");
                }
                json_response("success", "task_checked");
                break;
            case 'unlock':
                $this->theme_view = 'blank';
                $task = ProjectHasTask::find($task_id);
                $task->invoice_id = '0';
                $task->save();
                if ($task) {
                    json_response("success", htmlspecialchars($this->lang->line('application_task_has_been_unlocked')));
                } else {
                    json_response("error", htmlspecialchars($this->lang->line('application_task_has_not_been_unlocked')));
                }
                break;
            case 'delete':
                $task = ProjectHasTask::find($task_id);
                $task->delete();
                if (!$task) {
                    $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_task_error'));
                } else {
                    $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_task_success'));
                }
                redirect('projects/view/' . $id);
                break;
            default:
                $this->view_data['project'] = Project::find($id);
                $this->content_view = 'projects/tasks';
                break;
        }
    }
    public function milestones($id = false, $condition = false, $milestone_id = false)
    {
        $this->view_data['submenu'] = array(
            $this->lang->line('application_back') => 'projects',
            $this->lang->line('application_overview') => 'projects/view/' . $id,
        );
        switch ($condition) {
            case 'add':
                $this->content_view = 'projects/_milestones';
                if ($_POST) {
                    unset($_POST['send']);
                    unset($_POST['files']);
                    $description = $_POST['description'];
                    $_POST = array_map('htmlspecialchars', $_POST);
                    $_POST['description'] = $description;
                    $_POST['project_id'] = $id;
                    $milestone = ProjectHasMilestone::create($_POST);
                    if (!$milestone) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_milestone_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_milestone_success'));
                    }
                    redirect('projects/view/' . $id);
                } else {
                    $this->theme_view = 'modal';
                    $this->view_data['project'] = Project::find($id);
                    $this->view_data['title'] = $this->lang->line('application_add_milestone');
                    $this->view_data['form_action'] = 'projects/milestones/' . $id . '/add';
                    $this->content_view = 'projects/_milestones';
                }
                break;
            case 'update':
                $this->content_view = 'projects/_milestones';
                $this->view_data['milestone'] = ProjectHasMilestone::find($milestone_id);
                if ($_POST) {
                    unset($_POST['send']);
                    unset($_POST['files']);
                    $description = $_POST['description'];
                    $_POST = array_map('htmlspecialchars', $_POST);
                    $_POST['description'] = $description;
                    $milestone_id = $_POST['id'];
                    $milestone = ProjectHasMilestone::find($milestone_id);
                    $milestone->update_attributes($_POST);
                    if (!$milestone) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_milestone_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_milestone_success'));
                    }
                    redirect('projects/view/' . $id);
                } else {
                    $this->theme_view = 'modal';
                    $this->view_data['project'] = Project::find($id);
                    $this->view_data['title'] = $this->lang->line('application_edit_milestone');
                    $this->view_data['form_action'] = 'projects/milestones/' . $id . '/update/' . $milestone_id;
                    $this->content_view = 'projects/_milestones';
                }
                break;
            case 'delete':
                $milestone = ProjectHasMilestone::find($milestone_id);

                foreach ($milestone->project_has_tasks as $value) {
                    $value->milestone_id = "";
                    $value->save();
                }
                $milestone->delete();
                if (!$milestone) {
                    $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_milestone_error'));
                } else {
                    $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_milestone_success'));
                }
                redirect('projects/view/' . $id);
                break;
            default:
                $this->view_data['project'] = Project::find($id);
                $this->content_view = 'projects/milestones';
                break;
        }
    }
    public function notes($id = false)
    {
        if ($_POST) {
            unset($_POST['send']);
            $_POST = array_map('htmlspecialchars', $_POST);
            $_POST['note'] = strip_tags($_POST['note']);
            $project = Project::find($id);
            $project->update_attributes($_POST);
        }
        $this->theme_view = 'ajax';
    }
    public function media($id = false, $condition = false, $media_id = false)
    {
        $projecthasworker = ProjectHasWorker::all(array('conditions' => array('user_id = ? AND project_id = ?', $this->user->id, $id)));

        if (!$projecthasworker && $this->user->admin != 1 && !$this->view_data['worker_is_client_admin']) {
            $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_no_access_error'));
            redirect('projects');
        }

        $this->load->helper('notification');
        $this->view_data['submenu'] = array(
            $this->lang->line('application_back') => 'projects',
            $this->lang->line('application_overview') => 'projects/view/' . $id,
            $this->lang->line('application_tasks') => 'projects/tasks/' . $id,
            $this->lang->line('application_media') => 'projects/media/' . $id,
        );
        switch ($condition) {
            case 'view':

                if ($_POST) {
                    unset($_POST['send']);
                    unset($_POST['_wysihtml5_mode']);
                    unset($_POST['files']);
                    //$_POST = array_map('htmlspecialchars', $_POST);
                    $_POST['text'] = $_POST['message'];
                    unset($_POST['message']);
                    $_POST['project_id'] = $id;
                    $_POST['media_id'] = $media_id;
                    $_POST['from'] = $this->user->firstname . ' ' . $this->user->lastname;
                    $this->view_data['project'] = Project::find_by_id($id);
                    $this->view_data['media'] = ProjectHasFile::find($media_id);
                    $message = Message::create($_POST);
                    if (!$message) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_message_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_message_success'));

                        foreach ($this->view_data['project']->project_has_workers as $workers) {
                            send_notification($workers->user->email, "[" . $this->view_data['project']->name . "] New comment", 'New comment on media file: ' . $this->view_data['media']->name . '<br><strong>' . $this->view_data['project']->name . '</strong>');
                        }
                        if (is_object($this->view_data['project']->company->client)) {
                            $access = explode(',', $this->view_data['project']->company->client->access);
                            if (in_array('12', $access)) {
                                send_notification($this->view_data['project']->company->client->email, "[" . $this->view_data['project']->name . "] New comment", 'New comment on media file: ' . $this->view_data['media']->name . '<br><strong>' . $this->view_data['project']->name . '</strong>');
                            }
                        }
                    }
                    redirect('projects/media/' . $id . '/view/' . $media_id);
                }
                $this->content_view = 'projects/view_media';
                $this->view_data['media'] = ProjectHasFile::find($media_id);
                $this->view_data['form_action'] = 'projects/media/' . $id . '/view/' . $media_id;
                $this->view_data['filetype'] = explode('.', $this->view_data['media']->filename);
                $this->view_data['filetype'] = $this->view_data['filetype'][1];
                $this->view_data['backlink'] = 'projects/view/' . $id;
                break;
            case 'add':
                $this->content_view = 'projects/_media';
                $this->view_data['project'] = Project::find($id);
                if ($_POST) {
                    $config['upload_path'] = './files/media/';
                    $config['encrypt_name'] = true;
                    $config['allowed_types'] = '*';

                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path']);
                    }

                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors('', ' ');
                        $this->session->set_flashdata('message', 'error:' . $error);
                        redirect('projects/media/' . $id);
                    } else {
                        $data = array('upload_data' => $this->upload->data());

                        $_POST['filename'] = $data['upload_data']['orig_name'];
                        $_POST['savename'] = $data['upload_data']['file_name'];
                        $_POST['type'] = $data['upload_data']['file_type'];

                        //check image processor extension
                        if (extension_loaded('gd2')) {
                            $lib = 'gd2';
                        } else {
                            $lib = 'gd';
                        }
                        $config['image_library'] = $lib;
                        $config['source_image']    = './files/media/' . $_POST['savename'];
                        $config['new_image']    = './files/media/thumb_' . $_POST['savename'];
                        $config['create_thumb'] = true;
                        $config['thumb_marker'] = "";
                        $config['maintain_ratio'] = true;
                        $config['width']    = 170;
                        $config['height']    = 170;
                        $config['master_dim']    = "height";
                        $config['quality']    = "100%";

                        $this->load->library('image_lib');
                        $this->image_lib->initialize($config);
                        $this->image_lib->resize();
                        $this->image_lib->clear();
                    }

                    unset($_POST['send']);
                    unset($_POST['userfile']);
                    unset($_POST['file-name']);
                    unset($_POST['files']);
                    $_POST = array_map('htmlspecialchars', $_POST);
                    $_POST['project_id'] = $id;
                    $_POST['user_id'] = $this->user->id;
                    $media = ProjectHasFile::create($_POST);
                    if (!$media) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_media_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_media_success'));

                        $attributes = array('subject' => $this->lang->line('application_new_media_subject'), 'message' => '<b>' . $this->user->firstname . ' ' . $this->user->lastname . '</b> ' . $this->lang->line('application_uploaded') . ' ' . $_POST['name'], 'datetime' => time(), 'project_id' => $id, 'type' => 'media', 'user_id' => $this->user->id);
                        $activity = ProjectHasActivity::create($attributes);

                        foreach ($this->view_data['project']->project_has_workers as $workers) {
                            send_notification($workers->user->email, "[" . $this->view_data['project']->name . "] " . $this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added') . ' <strong>' . $this->view_data['project']->name . '</strong>');
                        }
                        if (is_object($this->view_data['project']->company->client)) {
                            $access = explode(',', $this->view_data['project']->company->client->access);
                            if (in_array('12', $access)) {
                                send_notification($this->view_data['project']->company->client->email, "[" . $this->view_data['project']->name . "] " . $this->lang->line('application_new_media_subject'), $this->lang->line('application_new_media_file_was_added') . ' <strong>' . $this->view_data['project']->name . '</strong>');
                            }
                        }
                    }
                    redirect('projects/view/' . $id);
                } else {
                    $this->theme_view = 'modal';
                    $this->view_data['title'] = $this->lang->line('application_add_media');
                    $this->view_data['form_action'] = 'projects/media/' . $id . '/add';
                    $this->content_view = 'projects/_media';
                }
                break;
            case 'update':
                $this->content_view = 'projects/_media';
                $this->view_data['media'] = ProjectHasFile::find($media_id);
                $this->view_data['project'] = Project::find($id);
                if ($_POST) {
                    unset($_POST['send']);
                    unset($_POST['_wysihtml5_mode']);
                    unset($_POST['files']);
                    $_POST = array_map('htmlspecialchars', $_POST);
                    $media_id = $_POST['id'];
                    $media = ProjectHasFile::find($media_id);
                    $media->update_attributes($_POST);
                    if (!$media) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_media_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_media_success'));
                    }
                    redirect('projects/view/' . $id);
                } else {
                    $this->theme_view = 'modal';
                    $this->view_data['title'] = $this->lang->line('application_edit_media');
                    $this->view_data['form_action'] = 'projects/media/' . $id . '/update/' . $media_id;
                    $this->content_view = 'projects/_media';
                }
                break;
            case 'delete':
                $media = ProjectHasFile::find($media_id);
                $media->delete();
                ProjectHasFile::find_by_sql("DELETE FROM messages WHERE media_id = $media_id");

                if (!$media) {
                    $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_media_error'));
                } else {
                    unlink('./files/media/' . $media->savename);
                    $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_media_success'));
                }
                redirect('projects/view/' . $id);
                break;
            default:
                $this->view_data['project'] = Project::find($id);
                $this->content_view = 'projects/view/' . $id;
                break;
        }
    }
    public function deletemessage($project_id, $media_id, $id)
    {
        $message = Message::find($id);
        if ($message->from == $this->user->firstname . " " . $this->user->lastname || $this->user->admin == "1") {
            $message->delete();
        }
        if (!$message) {
            $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_delete_message_error'));
        } else {
            $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_delete_message_success'));
        }
        redirect('projects/media/' . $project_id . '/view/' . $media_id);
    }
    public function tracking($id = false)
    {
        $project = Project::find($id);
        if (empty($project->tracking)) {
            $project->update_attributes(array('tracking' => time()));
        } else {
            $timeDiff = time() - $project->tracking;
            $project->update_attributes(array('tracking' => '', 'time_spent' => $project->time_spent + $timeDiff));
        }
        redirect('projects/view/' . $id);
    }
    public function sticky($id = false)
    {
        $project = Project::find($id);
        if ($project->sticky == 0) {
            $project->update_attributes(array('sticky' => '1'));
            $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_make_sticky_success'));
        } else {
            $project->update_attributes(array('sticky' => '0'));
            $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_remove_sticky_success'));
        }
        redirect('projects/view/' . $id);
    }
    public function download($media_id = false, $comment_file = false)
    {
        $this->load->helper('download');
        $this->load->helper('file');
        if ($media_id && $media_id != "false") {
            $media = ProjectHasFile::find($media_id);
            $media->download_counter = $media->download_counter + 1;
            $media->save();
            $file = './files/media/' . $media->savename;
        }
        if ($comment_file && $comment_file != "false") {
            $file = './files/media/' . $comment_file;
        }

        $mime = get_mime_by_extension($file);
        if (file_exists($file)) {
            if ($mime != "") {
                header('Content-Type: ' . $mime);
            } else {
                header("Content-type: application/octet-stream");
            }
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . basename($media->filename));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            header('Content-Length: ' . filesize($file));

            readfile_chunked($file);
            @ob_end_flush();
            exit;
        } else {
            show_error("File could not be found!", 400, "File download error");
        }
    }
    public function task_comment($id, $condition)
    {
        $this->load->helper('notification');
        $task = ProjectHasTask::find_by_id($id);
        switch ($condition) {
            case 'create':
                if ($_POST) {
                    $config['upload_path'] = './files/media/';
                    $config['encrypt_name'] = true;
                    $config['allowed_types'] = '*';

                    if (!is_dir($config['upload_path'])) {
                        mkdir($config['upload_path']);
                    }

                    $this->load->library('upload', $config);

                    unset($_POST['send']);
                    $_POST['message'] = htmlspecialchars(strip_tags($_POST['message'], '<br><br/><p></p><a></a><b></b><i></i><u></u><span></span>'));
                    $_POST['task_id'] = $id;
                    $_POST['user_id'] = $this->user->id;
                    $_POST['datetime'] = time();

                    $attachment = false;
                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors('', ' ');
                        if ($error != 'You did not select a file to upload.') {
                            //$this->session->set_flashdata('message', 'error:'.$error);
                        }
                    } else {
                        $data = array('upload_data' => $this->upload->data());
                        $_POST['attachment'] = $data['upload_data']['orig_name'];
                        $_POST['attachment_link'] = $data['upload_data']['file_name'];
                        $attachment = $data['upload_data']['file_name'];
                    }
                    unset($_POST['userfile']);

                    $comment = TaskHasComment::create($_POST);
                    if (!$comment) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_success'));

                        send_notification($task->user->email, "[" . $task->project->name . "] " . $task->name, $_POST['message']);

                        if (is_object($task->client)) {
                            send_notification($task->client->email, "[" . $task->project->name . "] " . $task->name, $_POST['message']);
                        }
                        // if(isset($project->company->client->email)){
                        // 	$access = explode(',', $project->company->client->access);
                        // 	if(in_array('12', $access)){
                        // 		send_notification($project->company->client->email, "[".$project->name."] ".$_POST['subject'], $_POST['message'].'<br><strong>'.$project->name.'</strong>');
                        // 	}
                        // }
                    }
                    echo "success";
                    exit;
                }
                break;
        }
    }

    public function invoice($id = false)
    {
        if ($_POST) {
            unset($_POST['send']);
            unset($_POST['_wysihtml5_mode']);
            unset($_POST['files']);
            $project = Project::find_by_id($id);
            $values = array(
                "project_id" => $id,
                "company_id" => $project->company_id,
                "status" => "Open",
                "reference" => $_POST["reference"],
                "issue_date" => $_POST["issue_date"],
                "due_date" => $_POST["due_date"],
                "terms" => $_POST["terms"],
                "currency" => $_POST["currency"],
                "discount" => $_POST["discount"],
                "tax" => $_POST["tax"],
                "second_tax" => $_POST["second_tax"]
            );
            $invoice = Invoice::create($values);
            $new_invoice_reference = $_POST['reference'] + 1;
            if (is_array($_POST["tasks"])) {
                foreach ($_POST["tasks"] as $value) {
                    $task = ProjectHasTask::find_by_id($value);
                    $task->invoice_id = $invoice->id;
                    $task->save();
                    $seconds = $task->time_spent;
                    $H = floor($seconds / 3600);
                    $i = ($seconds / 60) % 60;
                    $s = $seconds % 60;
                    $hours = sprintf('%0.2f', $H + ($i / 60));
                    $item_values = array(
                        "invoice_id" => $invoice->id,
                        "item_id" => 0,
                        "amount" => $hours,
                        "value" => $task->value,
                        "name" => $task->name,
                        "description" => $task->description,
                        "type" => "task",
                        "task_id" => $task->id
                    );
                    $newItem = InvoiceHasItem::create($item_values);
                }
            }


            $invoice_reference = Setting::first();
            $invoice_reference->update_attributes(array('invoice_reference' => $new_invoice_reference));
            if (!$invoice) {
                $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_invoice_error'));
            } else {
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_invoice_success'));
            }
            redirect('invoices/view/' . $invoice->id);
        } else {
            $this->view_data['invoices'] = Invoice::all();
            $this->view_data['next_reference'] = Invoice::last();
            $this->view_data['project'] = Project::find_by_id($id);
            $this->view_data['done_tasks'] = ProjectHasTask::getDoneTasks($id);


            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_create_invoice');
            $this->view_data['form_action'] = 'projects/invoice/' . $id;
            $this->content_view = 'projects/_invoice';
        }
    }

    public function tickets($id = false)
    {
        if ($_POST) {
            $config['upload_path'] = './files/media/';
            $config['encrypt_name'] = true;
            $config['allowed_types'] = '*';

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path']);
            }

            $this->load->library('upload', $config);
            $this->load->helper('notification');

            $ticket_reference = Setting::first();
            $_POST['reference'] = $ticket_reference->ticket_reference;
            $_POST['status'] = $ticket_reference->ticket_default_status;
            $new_ticket_reference = $_POST['reference'] + 1;
            $ticket_reference->update_attributes(['ticket_reference' => $new_ticket_reference]);

            $client = Client::find_by_id($_POST['client_id']);
            $user = User::find_by_id($_POST['user_id']);
            if (isset($client->email)) {
                $_POST['from'] = $client->firstname . ' ' . $client->lastname . ' - ' . $client->email;
            } else {
                $_POST['from'] = $this->user->firstname . ' ' . $this->user->lastname . ' - ' . $this->user->email;
            }

            if (isset($_POST['notify_agent']) && $user) {
                $notify_agent = 'true';
            }
            if (isset($_POST['notify_client'])) {
                $notify_client = 'true';
            }

            if (is_object($client) && !empty($client->company_id)) {
                $_POST['company_id'] = $client->company->id;
            }

            $project = Project::find_by_id($id);
            $values = array(
                "project_id" => $id,
                "company_id" => $_POST['company_id'],
                "reference" => $_POST["reference"],
                "subject" => htmlspecialchars($_POST["subject"]),
                "text" => htmlspecialchars($_POST["text"]),
                "created" => time(),
                "updated" => "1",
                "from" => $_POST['from'],
                "type_id" => $_POST['type_id'],
                "status" => $_POST['status'],
                "client_id" => $_POST['client_id'],
                "queue_id" => $_POST['queue_id'],
                "status" => $_POST['status']
            );
            $ticket = Ticket::create($values);

            if (!$this->upload->do_upload()) {
                $error = $this->upload->display_errors('', ' ');
                $this->session->set_flashdata('message', 'error:' . $error);
                $email_attachment = '';
            } else {
                $data = ['upload_data' => $this->upload->data()];

                $attributes = ['ticket_id' => $ticket->id, 'filename' => $data['upload_data']['orig_name'], 'savename' => $data['upload_data']['file_name']];
                $attachment = TicketHasAttachment::create($attributes);
                $email_attachment = array($data['upload_data']['orig_name'] => $data['upload_data']['file_name']);
            }

            if (!$ticket) {
                $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_create_ticket_error'));
            } else {
                $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_create_ticket_success'));

                if (isset($notify_agent)) {
                    send_ticket_notification($user->email, '[Ticket#' . $ticket->reference . '] - ' . $_POST['subject'], $_POST['text'], $ticket->id, $email_attachment);
                }
                if (isset($notify_client)) {
                    send_ticket_notification($client->email, '[Ticket#' . $ticket->reference . '] - ' . $_POST['subject'], $_POST['text'], $ticket->id, $email_attachment);
                }
            }

            redirect('tickets/view/' . $ticket->id);
        } else {
            $this->view_data['tickets'] = Ticket::all();
            $this->view_data['next_reference'] = Ticket::last();
            $this->view_data['project'] = Project::find_by_id($id);
            $this->view_data['settings'] = Setting::first();
            $this->view_data['types'] = Type::find('all', ['conditions' => ['inactive=?', '0']]);
            $this->view_data['queues'] = Queue::find('all', ['conditions' => ['inactive=?', '0']]);

            if ($this->user->admin != 1) {
                $comp_array = [];
                $thisUserHasNoCompanies = (array) $this->user->companies;
                if (!empty($thisUserHasNoCompanies)) {
                    foreach ($this->user->companies as $value) {
                        array_push($comp_array, $value->id);
                    }
                    $this->view_data['clients'] = Client::find('all', ['conditions' => ['inactive=? AND company_id in (?)', '0', $comp_array]]);
                } else {
                    $this->view_data['clients'] = (object) [];
                }
            } else {
                $this->view_data['clients'] = Client::find('all', ['conditions' => ['inactive=?', '0']]);
            }

            $this->view_data['users'] = User::find('all', ['conditions' => ['status=?', 'active']]);

            $this->theme_view = 'modal';
            $this->view_data['title'] = $this->lang->line('application_create_ticket');
            $this->view_data['form_action'] = 'projects/tickets/' . $id;
            $this->content_view = 'projects/_ticket';
        }
    }

    public function activity($id = false, $condition = false, $activityID = false)
    {
        $this->load->helper('notification');
        $project = Project::find_by_id($id);
        //$activity = ProjectHasAktivity::find_by_id($activityID);
        switch ($condition) {
            case 'add':
                if ($_POST) {
                    unset($_POST['send']);
                    $_POST['subject'] = htmlspecialchars($_POST['subject']);

                    $_POST['message'] = strip_tags($_POST['message'], '<iframe></iframe><img><ul></ul><li></li><ol></ol><br><br/><p></p><a></a><b></b><i></i><u></u><span></span>');
                    $_POST['project_id'] = $id;
                    $_POST['user_id'] = $this->user->id;
                    $_POST['type'] = "comment";
                    unset($_POST['files']);
                    $_POST['datetime'] = time();
                    $activity = ProjectHasActivity::create($_POST);
                    if (!$activity) {
                        $this->session->set_flashdata('message', 'error:' . $this->lang->line('messages_save_error'));
                    } else {
                        $this->session->set_flashdata('message', 'success:' . $this->lang->line('messages_save_success'));
                        foreach ($project->project_has_workers as $workers) {
                            send_notification($workers->user->email, "[" . $project->name . "] " . $_POST['subject'], $_POST['message'] . '<br><strong>' . $project->name . '</strong>');
                        }
                        if (is_object($project->company->client)) {
                            $access = explode(',', $project->company->client->access);
                            if (in_array('12', $access)) {
                                send_notification($project->company->client->email, "[" . $project->name . "] " . $_POST['subject'], $_POST['message'] . '<br><strong>' . $project->name . '</strong>');
                            }
                        }
                    }
                    //redirect('projects/view/'.$id);
                }
                break;
            case 'update':

                break;
            case 'delete':
                $activity = ProjectHasActivity::find_by_id($activityID);
                if ($activity->user_id == $this->user->id) {
                    $activity->delete();
                }

                break;
        }
    }
}
