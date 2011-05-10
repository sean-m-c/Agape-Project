<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'need-clearance-form',
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
		<?php echo $form->labelEx($model,'clearance_fk'); ?>
		<?php echo $form->textField($model,'clearance_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'clearance_fk'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->