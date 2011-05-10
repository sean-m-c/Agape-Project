<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'project-event-location-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'project_fk'); ?>
		<?php echo $form->textField($model,'project_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'project_fk'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'event_fk'); ?>
		<?php echo $form->textField($model,'event_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'event_fk'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'location_fk'); ?>
		<?php echo $form->textField($model,'location_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'location_fk'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->