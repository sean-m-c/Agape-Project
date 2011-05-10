<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'review_oid'); ?>
		<?php echo $form->textField($model,'review_oid',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'comment'); ?>
		<?php echo $form->textArea($model,'comment',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tab_fk'); ?>
		<?php echo $form->textField($model,'tab_fk'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'makes_review_fk'); ?>
		<?php echo $form->textField($model,'makes_review_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->