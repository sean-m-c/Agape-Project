<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, 'rmp'); ?>
        <div class="listRow">
            <?php echo $form->radioButtonList($model, 'rmp', array('1' => 'Yes', '0' => 'No', '2'=>'Other'),
                    array('template' => '{input}{label}', 'separator' => '</div><div class="listRow">','required'=>true)); ?>
        </div>
        <?php echo $form->error($model, 'rmp'); ?>
    </div>
    
    <div class="row" id="rmpDescription" style="display:none;">
        <?php echo $form->labelEx($model, 'rmp_description'); ?>
        <?php echo $form->textArea($model, 'rmp_description', array('cols' => 70, 'rows' => 6, 'maxlength' => 1000)); ?>
        <?php echo $form->error($model, 'rmp_description'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'permission_slip'); ?>
        <?php echo $form->checkbox($model, 'permission_slip'); ?>
        <?php echo $form->error($model, 'permission_slip'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'disclaimer_accepted'); ?>
        <?php echo $form->checkbox($model, 'disclaimer_accepted'); ?>
        <?php echo $form->error($model, 'disclaimer_accepted'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'photo_release_permission'); ?>
        <?php echo $form->checkbox($model, 'photo_release_permission'); ?>
        <?php echo $form->error($model, 'photo_release_permission'); ?>
    </div>

    <?php
            $defaults = Clearance::model()->findAll('is_default=1');
            $i = 0;
// Loop through default checkboxes, and echo a checkbox for each
            foreach ($defaults as $default) {

                // Get the entry for this checkbox, if there isn't one, create a new model
                $count = NeedClearance::model()->find(array(
                            'condition' => 'clearance_fk=:clearanceFk',
                            'params' => array(':clearanceFk' => $default->clearance_oid)));


                // If there's a row for this checkbox, show it as checked
                $checked = false;
                $status = 'unchecked';
                if ($count > 0) {
                    $checked = true;
                    $status = 'checked';
                }

                echo '<div class="row checkboxToggle">' . "\n" .
                CHtml::label($default->name, 'NeedClearance_project_fk') . "\n" .
                CHtml::checkbox('NeedClearance[' . $i . ']', $checked) . "\n" .
                CHtml::hiddenField('NeedClearance[' . $i . '][status]', $status, array('class' => 'status')) . "\n" .
                CHtml::hiddenField('NeedClearance[' . $i . '][key]', $default->clearance_oid) . "\n" .
                '</div>' . "\n";

                $i++;
            }

            // Anything other than the defaults
            $others = NeedClearance::model()->findAll(array(
                        'condition' => 't.project_fk=:projectOid AND clearance.is_default=0',
                        'with' => 'clearance',
                        'params' => array(':projectOid' => $model->id)));

            if (!empty($others)) {
                foreach ($others as $other) {
                    echo '<div class="row checkboxToggle">' . "\n" .
                    CHtml::label($other->clearance->name, 'NeedClearance[' . $i . ']') . "\n" .
                    CHtml::checkbox('NeedClearance[' . $i . ']', true) . "\n" .
                    CHtml::hiddenField('NeedClearance[' . $i . '][status]', 'checked', array('class' => 'status')) . "\n" .
                    CHtml::hiddenField('NeedClearance[' . $i . '][key]', $other->clearance_fk) . "\n" .
                    '</div>';
                    $i++;
                }
            }
    ?>


    <?php
            // Dropdown box suggestion list
            $userClearances = Clearance::model()->findAll('is_default=0');
            $autoList = array();
            foreach ($userClearances as $userClearance) {
                $autoList[] = array('label' => $userClearance->name, 'value' => $userClearance->clearance_oid);
            }
    ?>

            <div class="newClearance row">
        <?php
            echo CHtml::label('Other', 'NeedClearance_project_fk') .
            CHtml::checkbox('toggleField', '');
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name' => 'NeedClearance[' . $i . '][text]',
                'value' => '',
                'source' => $autoList,
                // additional javascript options for the autocomplete plugin
                'options' => array(
                    'showAnim' => 'fold',
                ),
                'htmlOptions' => array('style' => 'display:none;'),
            ));
            echo CHtml::hiddenField('NeedClearance[' . $i . '][key]', null) .
            CHtml::hiddenField("Counter", $i, array('class' => 'count')) .
            CHtml::link('Add More', '', array('style' => 'display:none;margin-left:1em;',
                'class' => 'buttonLink noLoader i_add addLink'));
        ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>Emergency Contacts</legend>
        <?php echo CHtml::link('Add new contact',array('#'),
        array('class'=>'i_add buttonLink noLoader',
        'onclick'=>'$("#addContactForm").toggle("fast");
        return false;'));
        ?>
        <div id="addContactForm" style="display:none;">
            <?php $this->renderPartial('/emergencyContact/_form',
                    array('emergencyContact'=>new EmergencyContact,'project_fk'=>$model->id)); ?>
        </div>

        <?php
        $dataProvider=new CActiveDataProvider('EmergencyContact',array(
            'criteria'=>array(
                'condition'=>'project_fk='.$model->id
        )));
        $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'emergency-contact-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                    'fullName',
                    'phone',
                    array(
                            'class'=>'CButtonColumn',
                            'htmlOptions'=>array('width'=>'100'),
                            'deleteButtonLabel'=>'Delete this contact.',
                            'deleteConfirmation'=>'Are you sure you want to delete this contact?',
                            'deleteButtonOptions'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                            'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                            'deleteButtonUrl'=>'Yii::app()->createUrl("/emergencyContact/delete",
                                array("id"=>$data->emergency_contact_oid))',
                            'updateButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_edit.png',
                            'updateButtonOptions'=>array('height'=>'25','width'=>'25'),
                            'updateButtonUrl'=>'Yii::app()->createUrl("/emergencyContact/update",
                                array("id"=>$data->emergency_contact_oid,
                                "returnUrl"=>Yii::app()->request->urlReferrer))',
                            'updateButtonLabel'=>'Edit contact.',
                            'template'=>'{delete} {update}',
                            'htmlOptions'=>array('width'=>'90')
                    ),
            ),
    )); ?>
        
    </fieldset>

<?php echo CHtml::script('
var autoComplete = $("input:text");
var otherCheckbox = $("div.newClearance input:checkbox");
var addLink = $("a.addLink");

autoComplete.bind( "autocompleteselect", function(event, ui) {
    autoComplete.next("input:hidden").val(ui.item.value);
    autoComplete.val(ui.item.label);
    return false;
});

autoComplete.keyup(function() {
    if($(this).val().length > 0) {
        $(this).nextAll("a").fadeIn();
    } else {
        $(this).nextAll("a").hide();
    }
});

otherCheckbox.click(function() {
    if ($(this).is(":checked")) {
        $(this).next("input:text").fadeIn();
    } else {
        var parent = $(this).parents("div.dynamic").eq(0);
        
        if(parent.hasClass("dynamic")) {

            var prevparent = parent.prev("div.row");

            if(parent.hasClass("newClearance")) {
                prevparent.addClass("newClearance");
            }
           
            if(parent.find("a").length > 0) {
                parent.find("a").appendTo(prevparent);
            }

            if(prevparent.find("input:text").val().length > 0) {
                prevparent.find("a").fadeIn();
            }
                   
            parent.remove();

        } else {
            $(this).nextAll("input:text").hide().val("");
            $(this).nextAll("a").hide();
        }
    }
});

addLink.click(function() {
    var oldClearance = $("div.newClearance");
    var newClearance = oldClearance.clone(true);

    if(!newClearance.hasClass("dynamic")) {
        newClearance.addClass("dynamic");
    }

    var textField = newClearance.find("input:text");
    newClearance.find("a").hide();
    var countField = newClearance.find(".count");
    countField.val(parseInt(countField.val()) + 1);
    textField.val("");
    textField.attr("id", "NeedClearance_" + (parseInt(countField.val()) + 1) + "_status");
    textField.attr("name", "NeedClearance[" + (parseInt(countField.val()) + 1) + "][status]");
    oldClearance.removeClass("newClearance").find("a").remove();

    oldClearance.after(newClearance).fadeIn();

});

$("input:checkbox").click(function() {
    if($(this).is(":checked")) {
        $(this).nextAll(".status").val("checked");
    } else {
        $(this).nextAll(".status").val("unchecked");
    }
});

var radioOtherRmp = $("#Project_rmp_2");

if(radioOtherRmp.val()=="2") {
    $("div#rmpDescription").show();
}

$("input:radio").change(function() {

    if($("#Project_rmp_2:checked").val()=="2") {
        $("div#rmpDescription").slideDown();
    } else {
        $("div#rmpDescription").slideUp();
    }
    
});

'); ?>