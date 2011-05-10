
<?php
// For hour picker
$hours = null;
$minutes = null;
;
$iH = 1;
while ($iH <= 12) {
    $hours[$iH] = $iH;
    $iH++;
}
$iM = 0;
while ($iM <= 60) {
    if (strlen($iM) === 1) {
        $minutes["0" . $iM] = "0" . $iM;
    } else {
        $minutes[$iM] = $iM;
    }
    $iM+=5;
}
?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
                'id' => 'event-form',
                'enableAjaxValidation' => false,
            ));
    ?>
    <?php if($model->event_oid != null) : ?>
        <p style="margin-bottom:1em;">
            <?php
            echo CHtml::link('Delete',
                    array('/event/delete',
                        'id' => $model->event_oid,
                        'ajax' => true),
                    array(
                        'class' => 'i_logout buttonLink noBGPad deleteEvent',
                        'id' => 'deleteEvent_' . $model->event_oid,
            ));
            ?>
        </p>
        <?php 
        echo CHtml::script('
        $(".deleteEvent").live("click",function() {

            if(confirm("Are you sure you want to delete this event?")) {
                var link = $(this);
                var url = link.attr("href");
                
                var urlParams = getUrlVars(url);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: { id : urlParams["id"] },
                    dataType: "HTML",
                    error: function(a,b,c) {
                        alert("Error retrieving data, XMLHttpRequest: "+ a.statusText + "; textStatus: " + b + " errorThrown: " + c);
                    },
                    success : function(data) {
                        $("#calendar").fadeOut().html(data).fadeIn();
                        $("#createEventDialog").dialog("close");
                    }

                });
            }
            return false;
        });'); ?>
    <?php endif; ?>
    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('size' => 50, 'maxlength' => 50)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>

    <div class="row">
        <?php
        echo CHtml::link('Click here to view Messiah\'s academic calendar.',
                'http://www.messiah.edu/academics/calendar.html',
                array('target' => '_blank', 'class' => 'buttonLink i_forward noLoader'));
        ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'start', array('required' => true)); ?>
        <?php echo $form->textField($model, 'start', array('value'=>$calData['start'], 'disabled'=>'disabled')); ?>
        <?php echo $form->hiddenField($model, 'start', array('value'=>$calData['start'])); ?>

        <?php echo $form->dropdownList($model, "start_hour", array($hours)); ?>
        <?php echo $form->dropdownList($model, "start_minute", array($minutes)); ?>
        <?php echo $form->dropdownList($model, "start_meridian", array("AM" => "AM", "PM" => "PM")); ?>
        <?php echo $form->error($model, 'start'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'end', array('required' => true)); ?>
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'id' => 'end',
            'name' => 'Event[end]',
            // additional javascript options for the date picker plugin
            'options' => array(
                'showAnim' => 'fold',
            ),
            'htmlOptions' => array(
                'value' => $model->end,
            //'style'=>'height:20px;'
            ),
        ));
        ?>
        <?php echo $form->dropdownList($model, "end_hour", array($hours)); ?>
        <?php echo $form->dropdownList($model, "end_minute", array($minutes)); ?>
        <?php echo $form->dropdownList($model, "end_meridian", array("AM" => "AM", "PM" => "PM")); ?>
        <?php echo $form->error($model, 'end'); ?>
    </div>
        <div class="row">
            <?php //echo $form->labelEx($model,'project_fk'); ?>
            <?php echo $form->hiddenField($model, 'project_fk', array('value' => $calData['projectOID'])); ?>
            <?php echo $form->error($model, 'project_fk'); ?>
            <?php var_dump($calData['projectOID']);
            var_dump($model->event_oid); ?>
        </div>

        <div class="row">
        <?php
        $action = 'create';
        $url = array('/event/create');
        
        if($model->event_oid != null) {
            $action = 'update';
            $url = array('/event/update', 'id'=>$model->event_oid);
        }
        
        echo CHtml::link(ucwords($action),
        '',array('id'=>'createEventSubmit',
        'onclick'=>CHtml::ajax(array(
            'url'=>$url,
            'type'=>'POST',
            'dataType'=>'html',
            'error'=>'function(a,b,c) {
                    alert("Error retrieving data, XMLHttpRequest: "+ a + "; textStatus: "+b+" errorThrown: "+c);
             }',
            'success'=>'function(data) {
                $("#calendar").fadeOut().html(data).fadeIn();
                $("#createEventDialog").dialog("close");
                //$("#ajaxResponse").html(data.response).fadeIn();
            }'
        )),
        'style'=>"cursor:pointer;",
        'class'=>'i_checkmark buttonLink'
        )
        ); ?>
    </div>
        <?php $this->endWidget(); ?>
        <div id="ajaxResponse"></div>
    </div><!-- form -->

    <?php
    echo $model->start;
    
    //print_r(getdate($model->start));
    //$d = mktime($model->start_hour,$model->start_minute,null,$date[0],$date[1],$date[2]);
    echo date("Y-m-d H:i:s",strtotime($model->start));
    $date = date("Y-m-d",strtotime($model->start));
    $time = $model->start_hour . ':' . $model->start_minute . ':00 ' . $model->start_meridian;

    $newDate = date("Y-m-d H:i:s", strtotime($date.' '.$time));
    echo $newDate;
    /*echo date("Y-m-d H:i:s", strtotime($model->start.' '.$model->start_hour . ':' .
                                            $model->start_minute . ':00 ' . $model->start_meridian));*/