<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'involved_oid'); ?>
		<?php echo $form->textField($model,'involved_oid',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'user_fk'); ?>
		<?php echo $form->textField($model,'user_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'community_partner_fk'); ?>
		<?php echo $form->textField($model,'community_partner_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pending'); ?>
		<?php echo $form->textField($model,'pending'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'is_cpadmin'); ?>
		<?php echo $form->textField($model,'is_cpadmin'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->