<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'issue-form',
	'enableAjaxValidation'=>false,
)); ?>


	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php //echo $form->labelEx($model,'issue_type_fk'); ?>
		<?php echo $form->dropdownList($model,'issue_type_fk',
                        CHtml::listdata(IssueType::model()->findAll(),'issue_type_oid','type')); ?>
		<?php echo $form->error($model,'issue_type_fk'); ?>
	</div>

		<div class="row">
        <?php echo CHtml::link('Save',
        '',array('id'=>'saveIssue',
        'onclick'=>CHtml::ajax(array(
            'url'=>array('/issue/update','id'=>$model->issue_oid),
            'type'=>'POST',
            'dataType'=>'json',
            'beforeSend'=>'function() { $("div#ajaxResponse").fadeOut().empty(); }',
            'success'=>'function(data) {
                if(data.status=="t") {
                    $.fn.yiiGridView.update("issue-grid");
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