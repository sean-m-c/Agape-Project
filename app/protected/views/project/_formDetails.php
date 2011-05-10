<?php //Yii::app()->clientScript->registerScriptFile('http://maps.google.com/maps/api/js?sensor=false',CClientScript::POS_HEAD);      ?>
<fieldset>
    <div class="row">
        <?php echo $form->label($model, 'project_description', array('required'=>true)); ?>
        <?php echo $form->textArea($model, 'project_description', array('cols' => 70, 'rows' => 6, 'maxlength' => 1000)); ?>
        <?php echo $form->error($model, 'project_description'); ?>
    </div>

</fieldset>
<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, 'prep_work'); ?>
        <?php echo $form->checkBox($model, 'prep_work'); ?>
        <?php echo $form->error($model, 'prep_work'); ?>
    </div>


    <div class="row" id="prepWorkHelpContainer" style="display:none;">
        <?php echo $form->labelEx($model, 'prep_work_help'); ?>
        <?php echo $form->checkBox($model, 'prep_work_help'); ?>
        <?php echo $form->error($model, 'prep_work_help'); ?>
    </div>

    <div id="prepWorkHelpNotice" class="row flash flash-notice" style="display:none;margin-top:3em;">
                Please don't forget to specify your specific prep work needs in the "description" textbox above.
            </div>
</fieldset>
<fieldset>
    <div class="row">
        <?php echo CHtml::label('Does this project have any cost per person?', 'isCost'); ?>
        <?php
        $htmlOptions=array('id' => 'isCost');
        if(isset($model->person_cost) && !empty($model->person_cost)) {
            $htmlOptions['checked'] = 'checked';
        }
        echo CHtml::checkBox('isCost', false, $htmlOptions); ?>
    </div>

    <div id="costs" class="hide">
        <div class='row'>
            <?php echo $form->labelEx($model, 'person_cost'); ?>
            $<?php echo $form->textField($model, 'person_cost'); ?>
            <?php echo $form->error($model, 'person_cost'); ?>
        </div>

        <div class='row'>
            <?php echo $form->labelEx($model, 'cost_description'); ?>
            <?php echo $form->textArea($model, 'cost_description', array('cols' => 70, 'rows' => 6, 'maxlength' => 1000)); ?>
            <?php echo $form->error($model, 'cost_description'); ?>
        </div>
    </div>

</fieldset>
<!-- Project locations -->
<fieldset>
    <legend>Locations</legend>
    <p>
        <?php
            echo CHtml::link('Add New Location', '',
                    array('class' => 'i_add buttonLink noLoader', 'id' => 'addLocationLink',
                        'onclick' => '$("#addLocationContainer").slideToggle(); return false;'));
        ?>
        </p>

        <div id="addLocationContainer" style="display:none;">
        <?php $this->renderPartial('/location/_form', array('location' => new Location('project'), 'projectOID' => $model->id), false, true); ?>
        </div>

        <div id="locationsList">
        <?php $this->renderPartial('/location/ajaxWrapper', array('model' => $model)); ?>
        </div>

    </fieldset>
<?php echo CHtml::script('
var isCostCheckbox = $("form div.row :checkbox#isCost");
var projectPrepWorkCheckbox = $("form div.row :checkbox#Project_prep_work");
var projectPrepWorkHelpCheckbox = $("form div.row :checkbox#Project_prep_work_help");

var costForm = $("div#costs");
var prepForm = $("div#prepWorkHelpContainer");
var notice = $("div#prepWorkHelpNotice");

if(!costForm.find("#Project_person_cost").val().length==0) {
    isCostCheckbox.attr("checked",true);
    costForm.show();
}

if(projectPrepWorkCheckbox.attr("checked")) {
    prepForm.show();
}

if(projectPrepWorkHelpCheckbox.attr("checked")) {
    notice.show();
}

isCostCheckbox.change(function() {
    if(!$(this).attr("checked")) {
        costForm.fadeOut().find(":input").val("");
    } else {
        costForm.fadeIn();
    }
});

projectPrepWorkCheckbox.change(function() {
    if(!$(this).attr("checked")) {
        prepForm.fadeOut();
        notice.fadeOut();
        projectPrepWorkHelpCheckbox.attr("checked",false);
    } else {
        prepForm.fadeIn();
    }
});

projectPrepWorkHelpCheckbox.change(function() {
    if(!$(this).attr("checked")) {
        notice.fadeOut();
    } else {
        notice.fadeIn();
    }
});
'); ?>