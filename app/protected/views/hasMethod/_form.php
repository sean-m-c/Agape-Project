<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'has-method-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'strategy_fk'); ?>
		<?php echo $form->textField($model,'strategy_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'strategy_fk'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'method_fk'); ?>
		<?php echo $form->textField($model,'method_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'method_fk'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->