
  <div class="col-sm-12  col-md-12 main">  
 
     <div class="row">
      <a href="<?=base_url()?>calendar/create" class="btn btn-primary" data-toggle="mainmodal"><?=$this->lang->line('application_create_event');?></a>
      <a href="<?=base_url()?>calendar/integrate" class="btn btn-primary pull-right" data-toggle="mainmodal"><?=$this->lang->line('application_integrate');?></a>
    </div>  
      <div class="row">
        <div class="box-shadow">
            <div class="table-head"><?=$this->lang->line('application_calendar');?></div>
            <div class="table-div">
        
              <div id='fullcalendar'></div>

            </div>
        </div>
      </div>
<?php 
if ($this->input->cookie('fc2language') != '') {
    $systemlanguage = $this->input->cookie('fc2language');
} else {
    $systemlanguage = $core_settings->language;
}
switch ($systemlanguage) {
      case 'english': $lang = 'en'; break;
      case 'dutch': $lang = 'nl'; break;
      case 'french': $lang = 'fr'; break;
      case 'german': $lang = 'de'; break;
      case 'italian': $lang = 'it'; break;
      case 'norwegian': $lang = 'no'; break;
      case 'polish': $lang = 'pl'; break;
      case 'portuguese': $lang = 'pt'; break;
      case 'russian': $lang = 'ru'; break;
      case 'spanish': $lang = 'es'; break;
      default: $lang = 'en'; break;
}
  ?>
           <script type="text/javascript">
//fullcalendar

$(document).ready(function() {

    // page is now ready, initialize the calendar...

    $('#fullcalendar').fullCalendar({
        lang: '<?=$lang;?>',
        header:{
            left:   'month,agendaWeek,agendaDay',
            center: 'title',
            right:  'today prev,next'
          },
          <?php if ($core_settings->calendar_google_api_key != '' && $core_settings->calendar_google_event_address != '') {
      ?>
          googleCalendarApiKey: '<?=$core_settings->calendar_google_api_key; ?>',

          eventSources: [
             
            {
                googleCalendarId: '<?=$core_settings->calendar_google_event_address; ?>',
                className: 'google-event',

            }

        ], <?php
  } ?>
          
          events: [

                    <?php if (isset($project_events)) {
      echo $project_events;
  } ?>
                    <?php if (isset($events_list)) {
      echo $events_list;
  } ?>  
                      
                  ],
            
              eventRender: function(event, element) {
                  element.attr('title', event.description);
                  
                  if(event.source.className[0] == "google-event"){
                    element.attr('target', "_blank");
                  }
                  if(event.modal == 'true'){
                    element.attr('data-toggle', "mainmodal");
                  } 
                  if(event.description != ''){
                    element.attr('title', event.description);

                    var tooltip = event.description;
                  $(element).attr("data-original-title", tooltip)
                  $(element).tooltip({ container: "body", trigger: 'hover', delay: { "show": 300, "hide": 50 }})
                  } 
                  

                 
                  
              },
              eventClick: function(event) {
                if (event.url && event.modal == 'true') {
                  NProgress.start();
                  var url = event.url;
 
                  if (url.indexOf('#') === 0) {
                    $('#mainModal').modal('open');
                  } else {
                    $.get(url, function(data) { 
                        $('#mainModal').modal();
                        $('#mainModal').html(data);
                      }).done(function() { NProgress.done();  });
                  }
                  return false;
                }
              }

    });




});
</script>



