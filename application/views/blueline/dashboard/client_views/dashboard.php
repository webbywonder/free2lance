<style>
  @media (max-width: 767px){
  .content-area {
      padding: 0;
  }
  .row.mainnavbar {
    margin-bottom: 0px;
    margin-right: 0px;
  }
}

</style>

<div class="grid">
  <div class="grid__col-md-7 dashboard-header">
        <h1><?=sprintf($this->lang->line('application_welcome_back'), $this->client->firstname);?></h1>
<?php if (isset($hasmessages)) { ?><small><?=sprintf($this->lang->line('application_welcome_subline'), $messages_new[0]->amount, $event_count_for_today);?></small><?php } ?>
      </div>
      <div class="grid__col-md-5 dashboard-header hidden-xs">
          <div class="grid grid--bleed grid--justify-end">
          <?php if (isset($messages)) { ?>
              <div class="grid__col-4 grid__col-lg-3 grid--align-self-center">  
                <h6><?=$this->lang->line('application_tasks');?></h6>
                  <h2><?=count($messages);?></h2>
              </div>
              <?php } ?>
              <?php if(isset($ticketcounter)){ ?>
              <div class="grid__col-4 grid__col-lg-3 grid--align-self-center">
                  <h6><?=$this->lang->line('application_tickets');?></h6>
                  <h2><?=$ticketcounter;?></h2>            
              </div>
              <?php } ?>
              <?php if (isset($invoicecounter)) { ?>
              <div class="grid__col-4 grid__col-lg-3 grid--align-self-center">
                <h6><?=$this->lang->line('application_clients');?></h6>
                <h2><?=$invoicecounter;?></h2>
              </div>
              <?php } ?>
          </div>
      </div>


    <div class="grid__col-sm-12 grid__col-md-8 grid__col-lg-9 grid__col--bleed">
      <div class="grid grid--align-content-start">
        <?php if (isset($hasprojects)) { ?>
        <div class="grid__col-6 grid__col-xs-6 grid__col-sm-6  grid__col-md-6 grid__col-lg-3">
            <a href="<?=base_url();?>projects/filter/open" class="tile-base box-shadow"> 
            <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-lightbulb-outline"></i></div>
            <div class="tile-small-header"><?=$this->lang->line('application_open_projects');?></div>
              <div class="tile-body">
                  <?=$projects_open;?><small> / <?=$projects_all;?></small>
              </div>
              <div class="tile-bottom">
                      <div class="progress tile-progress tile-progress--green" >
                      <div class="progress-bar" role="progressbar" aria-valuenow="<?=$openProjectsPercent?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$openProjectsPercent?>%"></div>
                      </div>
                  </div> 
            </a>
        </div>
        <?php } ?>
        <?php if (isset($hasinvoices)) { ?>
        <div class="grid__col-6 grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3">
            <a href="<?=base_url();?>invoices/filter/open" class="tile-base box-shadow">
            <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-paper-outline"></i></div>
            <div class="tile-small-header"><?=$this->lang->line('application_open_invoices');?></div>
              <div class="tile-body">
                  <?=$invoices_open;?><small> / <?=$invoices_all;?></small>
              </div>
              <div class="tile-bottom">
                      <div class="progress tile-progress tile-progress--orange">
                      <div class="progress-bar" role="progressbar" aria-valuenow="<?=$openInvoicePercent?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$openInvoicePercent?>%"></div>
                      </div>
                  </div> 
            </a>
        </div>
        <?php } ?>
</div>

        <div class="grid__col-12">
                  <?php if(isset($tasks))
                  { ?> 
                    <div class="stdpad box-shadow">
                        <div class="table-head"><?=$this->lang->line('application_my_open_tasks');?></div>
                        <div id="main-nano-wrapper" class="nano">
                            <div class="nano-content">
                                <ul id="jp-container" class="todo jp-container">
                                    <?php  
                                    $count = 0;
                                    $projectname = "";
                                    foreach ($tasks as $value): 
                                        if (!$value->public) continue; 
                                        $count = $count+1; 
                                        if(is_object($value->project) && $projectname != $value->project->name)
                                        {
                                            $projectname = $value->project->name;
                                            echo "<h5>".$projectname."</h5>";
                                        }
                                    ?>
                                    <li class="<?=$value->status;?> priority<?=$value->priority;?> list-item">
                                        <span class="lbl-"> 
                                            <p class="truncate"><input name="form-field-checkbox" type="checkbox" class="checkbox-nolabel task-check" data-link="<?=base_url()?>projects/tasks/<?=$value->project_id;?>/check/<?=$value->id;?>" <?=$value->status;?>/>
                                                <a href="<?=base_url()?>cprojects/view/<?=$value->project_id;?>"><?=$value->name;?></a>
                                            </p>
                                        </span> 
                                        <span class="pull-right hidden-xs" style="margin-top: -43px;">
                                              <?php if ($value->public != 0) {  ?><span class="list-button"><i class="icon dripicons-preview tt" title="" data-original-title="<?=$this->lang->line('application_task_public');?>"></i></span><?php } ?>
                                              <a href="<?=base_url()?>cprojects/tasks/<?=$value->project_id;?>/update/<?=$value->id;?>" class="edit-button" data-toggle="mainmodal"><i class="icon dripicons-gear"></i></a>
                                        </span>
                                    </li>
                                    <?php 
                                    endforeach;
                                    if($count == 0) 
                                    { ?>
                                        <div class="empty">
                                          <i class="ion-checkmark-round"></i><br> 
                                          <?=$this->lang->line('application_no_tasks_yet');?>
                                        </div>
                              <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
            <?php } ?>
        </div>
        <div class="grid__col-12">
                  <?php if (isset($hastickets)) { ?>
                    <div class="stdpad stdpad--auto-height box-shadow"><div class="table-head"><?=$this->lang->line('application_new_tickets');?></div>
                        <div class="table-div">
                            <table class="table" id="tickets" rel="<?=base_url()?>" cellspacing="0" cellpadding="0">
                            <thead>
                              <th class="hidden-xs no_sort" style="width:5px; padding-right: 5px;"><i class="icon dripicons-star"></i></th>
                              <th><?=$this->lang->line('application_subject');?></th>
                              
                              <th class="hidden-xs hidden-md"><?=$this->lang->line('application_queue');?></th>
                              <th class="hidden-xs"><?=$this->lang->line('application_client');?></th>
                              <th class="hidden-xs hidden-md"><?=$this->lang->line('application_owner');?></th>
                            </thead>
                            <?php foreach ($ticket as $value):?>
                            <tr id="<?=$value->id;?>" >
                              <?php if(is_object($value->user)){$client_id = $value->client->id; }else{ $client_id = FALSE; }?>
                              <td  class="hidden-xs" style="width:15px"><?php if($value->updated == "1" && $client_id == $this->client->id){?><i class="icon dripicons-star" style="color: #d48b2a;"></i><?php }else{?> <i class="icon dripicons-star" style="opacity: 0.2;"></i><?php } ?></td>
                              <td><?=$value->subject;?></td>
                              <td class="hidden-xs hidden-md"><span><?php if(is_object($value->queue)){ echo $value->queue->name;}?></span></td>
                              <td class="hidden-xs"><?php if(!is_object($value->company)){echo '<span class="label">'.$this->lang->line('application_no_client_assigned').'</span>'; }else{ echo '<span class="label label-info">'.$value->company->name.'</span>'; }?></td>
                              <td class="hidden-xs hidden-md"><?php if(!is_object($value->user)){echo '<span class="label">'.$this->lang->line('application_not_assigned').'</span>'; }else{ echo '<span class="label label-info">'.$value->user->firstname.' '.$value->user->lastname.'</span>'; }?></td>

                            </tr>

                            <?php endforeach;?>

                            </table>
                            <?php if(empty($ticket)) { ?>
                                      <div class="empty">
                                          <i class="ion-ios-pricetags"></i><br> 
                                          <?=$this->lang->line('application_no_new_tickets');?>
                                      </div>
                            <?php } ?>
                            
                            </div>
                    </div>      
          <?php } ?>
        </div>


      </div>
    </div>


        <div class="grid__col-sm-12 grid__col-md-4 grid__col-lg-3 grid__col--bleed">
           <div class="grid grid--align-content-start">

           <div class="grid__col-12 ">
                      <div class="stdpad box-shadow"><div class="table-head"><?=$this->lang->line('application_project_activities');?></div>
                        <div id="main-nano-wrapper" class="nano">
                          <div class="nano-content">
                            <ul class="activity__list">
                                <?php foreach ($recent_activities as $value) { ?>
                                    <li>
                                        <h3 class="activity__list--header">
                                            <?php echo time_ago($value->datetime); ?>
                                        </h3>
                                        <p class="activity__list--sub truncate">
                                            <?php if(is_object($value->user))
                                                  { 
                                                        echo $value->user->firstname." ".$value->user->lastname;
                                                       echo (is_object($value->project)) ? ' <a href="'.base_url().'projects/view/'.$value->project->id.'">'.$value->project->name."</a>" : ""; 
                                               
                                                  } ?>
                                        </p>
                                        <div class="activity__list--body">
                                            <?=character_limiter(str_replace(array("\r\n", "\r", "\n",), "",strip_tags($value->message)), 260); ?>
                                        </div>
                                    </li>
                                <?php } ?>
                                <?php if(empty($recent_activities)) { ?>
                                          <div class="empty">
                                              <i class="ion-ios-people"></i><br> 
                                              <?=$this->lang->line('application_no_recent_activities');?>
                                          </div>
                                <?php } ?>
                            </ul>
                          </div>
                        </div>
                  </div>      
                </div>

                <div class="grid__col-12">
                  <?php if(isset($message)){ ?> 
                      <div class="stdpad box-shadow">
                          <div class="table-head"><?=$this->lang->line('application_recent_messages');?></div>

                              <ul class="dash-messages">
                                  <?php foreach ($message as $value):?>          
                                      <li>
                                          <a href="<?=base_url()?>messages">
                                            <img class="userpic img-circle" src="
                                              <?php 
                                                if($value->userpic_u){
                                                  echo get_user_pic($value->userpic_u, $value->email_u);
                                                }else{
                                                  echo get_user_pic($value->userpic_c, $value->email_c);
                                                }
                                                ?>
                                              "/>
                                              <div class="pull-left" style="width: 78%;">
                                                  <p class="dash-messages__header truncate">
                                                      <?php if($value->status == "New"){ echo '<span class="new"><i class="icon dripicons-mail"></i></span>';}?> 
                                                      <?=$value->subject;?>
                                                  </p>
                                                  <p class="dash-messages__name">
                                                  <?php if($value->sender_u){echo $value->sender_u;}else{ echo $value->sender_c; } ?>
                                                  </p>    
                                              </div>
                                              <br clear="all">
                                              <!-- <small><?php echo time_ago($value->time); ?></small> -->
                                              <p class="dash-messages__body">
                                                  <?=character_limiter(str_replace(array("\r\n", "\r", "\n",), "",strip_tags($value->message)), 70); ?>
                                              </p>
                                          </a>
                                      </li>
                                  <?php endforeach;?>
                                  <?php if(empty($message)) { ?>
                                      <div class="empty">
                                          <i class="ion-ios-chatbubble"></i><br> 
                                          <?=$this->lang->line('application_no_messages');?>
                                      </div>
                                  <?php } ?>
                              </ul><br/>
                             </div>
                  <?php } ?>

                </div>
        </div>
    </div>
</div>

 