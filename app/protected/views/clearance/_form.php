<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'clearance-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'is_default'); ?>
		<?php echo $form->checkbox($model,'is_default'); ?>
		<?php echo $form->error($model,'is_default'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'url'); ?>
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>250)); ?>
		<?php echo $form->error($model,'url'); ?>
	</div>

	<div class="row buttons">
		<?php
                $class = 'i_checkmark';
                if($model->isNewRecord) {
                    $class='i_add';
                }
                echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'buttonLink '.$class)); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->