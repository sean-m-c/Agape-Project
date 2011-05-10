<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'goal-form',
	'enableAjaxValidation'=>true,
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
		<?php //echo $form->labelEx($model,'goal_description'); ?>
		<?php echo $form->textArea($model,'goal_description',array('rows'=>3,'cols'=>40,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'goal_description'); ?>
	</div>

	<div class="row">
        <?php echo CHtml::link('Save',
        '',array('id'=>'saveGoal',
        'onclick'=>CHtml::ajax(array(
            'url'=>array('/goal/update','id'=>$model->goal_oid),
            'type'=>'POST',
            'dataType'=>'json',
            'beforeSend'=>'function() { $("div#ajaxResponse").fadeOut().empty(); }',
            'success'=>'function(data) {
                if(data.status=="t") {
                    $.fn.yiiGridView.update("goal-grid");
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