<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($tabNote,'tab_note_oid'); ?>
		<?php echo $form->textField($tabNote,'tab_note_oid',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($tabNote,'tab_fk'); ?>
		<?php echo $form->textField($tabNote,'tab_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($tabNote,'project_fk'); ?>
		<?php echo $form->textField($tabNote,'project_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($tabNote,'tab_note'); ?>
		<?php echo $form->textArea($tabNote,'tab_note',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->