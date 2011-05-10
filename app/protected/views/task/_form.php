<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'task-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

    <!--
	<div class="row">
		<?php //echo $form->labelEx($model,'parent_fk'); ?>
		<?php //echo $form->textField($model,'parent_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php //echo $form->error($model,'parent_fk'); ?>
	</div>
    -->

	<div class="row">
		<?php //echo $form->labelEx($model,'task_description'); ?>
		<?php echo $form->textArea($model,'task_description',array('maxlength'=>500)); ?>
		<?php echo $form->error($model,'task_description'); ?>
	</div>

    <!--
	<div class="row">
		<?php //echo $form->labelEx($model,'completed'); ?>
		<?php //echo $form->textField($model,'completed'); ?>
		<?php //echo $form->error($model,'completed'); ?>
	</div>
    -->

	<div class="row">
        <?php echo CHtml::link('Save',
        '',array('id'=>'saveTask',
        'onclick'=>CHtml::ajax(array(
            'url'=>array('/task/update','id'=>$model->task_oid),
            'type'=>'POST',
            'dataType'=>'json',
            'beforeSend'=>'function() { $("div#ajaxResponse").fadeOut().empty(); }',
            'success'=>'function(data) {
                if(data.status=="t") {
                    $.fn.yiiGridView.update("task-grid");
                    $("#addDialog").dialog("close");
                } else if(data.status=="f") {
                    $("div#ajaxResponse").html(data.response).fadeIn();
                } else {
                    $("div#ajaxResponse").html("<div class=\"flash flash-error\">There was a problem submitting this form.</div>").fadeIn();
                }
            }'
        )),
        'style'=>"cursor:pointer;",
        'class'=>'i_checkmark buttonLink'
        )
        ); ?>
    </div>
    <div id="ajaxResponse"></div>

<?php $this->endWidget(); ?>

</div><!-- form -->