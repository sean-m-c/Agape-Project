<?php
//$dbiCal = new EDbiCal();
?>

<fieldset>
    <div class="buttonContainer">
        <?php echo CHtml::label('Add event on click',false); ?>
        <?php echo CHtml::checkbox('addOnClick',true); ?>
        <?php echo CHtml::link('New Event',array('event/create',
            'calData'=>array('projectOID'=>$model->id)),
            array('id'=>'newEventLink', 'class'=>'buttonLink', 'rel'=>'facebox',
            'onclick'=>'
                alert($(this).attr("href"));
                $("#eventForm").load($(this).attr("href")).hide().fadeIn();
                return false;')); ?>
    </div>
    <div id="eventForm" class="overlay"></div>
<?php

$this->widget('application.extensions.fullcalendar.FullcalendarGraphWidget',
    array(
    'data'=>Generic::parseCalendarEvents($model->event),
    'options' => array(
        'editable' => true,
        'header' => array(
            'left' => 'prev,next today',
            'right' => 'month,agendaWeek,agendaDay',
            'center' => 'title',
        ),
        'eventClick'=> 'js: function(event, jsEvent, view) {
               $.ajax({
                url: "'.CController::createUrl('/event/update').'",
                data: "id=" + event.id,
                dataType: "html",
                error: function(a,b,c) {
                    alert("Error retrieving data, XMLHttpRequest: "+ a + "; textStatus: "+b+" errorThrown: "+c);
                },
                success: function(data) {
                    $("#eventDialog").html(data);
                }

            });
        }',
        'selectable' => true,
        'selectHelper' => true,
        'select' => 'js:function(start, end, allDay) {

            var start = $.fullCalendar.formatDate(start,"M/d/yyyy");
            var end = $.fullCalendar.formatDate(end,"M/d/yyyy");
            var projectOID = "'.$model->id.'";

            var request = "GET";
            var data = "calData[start]=" + start + "&calData[projectOID]=" + projectOID;

            if($("input#addOnClick").is(":checked")) {
                var request = "POST";
                var data = "Event[start]=" + start + "& Event[end]=" + end + "&Event[project_fk]=" + projectOID;
            }

            $.ajax({
                url: "'.CController::createUrl('/event/create').'",
                data: data,
                dataType: "html",
                type: request,
                error: function(a,b,c) {
                    alert("Error retrieving data, XMLHttpRequest: "+ a + "; textStatus: "+b+" errorThrown: "+c);
                },
                success: function(data) {
                    alert(data);
                    calendar.fullCalendar(\'renderEvent\',data);
                }

            });

            calendar.fullCalendar("unselect");
            }',
        ),
        )
    ); ?>
        <div id="eventDialog"></div>
</fieldset>