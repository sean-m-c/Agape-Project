<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'community_partner_oid'); ?>
		<?php echo $form->textField($model,'community_partner_oid',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'agency_name'); ?>
		<?php echo $form->textField($model,'agency_name',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pc_first_name'); ?>
		<?php echo $form->textField($model,'pc_first_name',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pc_last_name'); ?>
		<?php echo $form->textField($model,'pc_last_name',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pc_email'); ?>
		<?php echo $form->textField($model,'pc_email',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pc_phone_number'); ?>
		<?php echo $form->textField($model,'pc_phone_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pc_url'); ?>
		<?php echo $form->textField($model,'pc_url',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pending'); ?>
		<?php echo $form->textField($model,'pending'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->