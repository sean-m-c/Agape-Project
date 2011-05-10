<div class="form">

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'tab-note-form',
	'enableAjaxValidation'=>false,
)); ?>

	<!--<p class="note">Fields with <span class="required">*</span> are required.</p>-->
    <fieldset>
	<?php echo $form->errorSummary($tabNote); ?>

	<div class="row">
		<?php //echo $form->labelEx($tabNote,'tab_fk'); ?>
		<?php echo $form->hiddenField($tabNote,'tab_fk',array('size'=>20,'maxlength'=>20,'value'=>$tab_fk)); ?>
		<?php echo $form->error($tabNote,'tab_fk'); ?>
	</div>

	<div class="row">
		<?php //echo $form->labelEx($tabNote,'project_fk'); ?>
		<?php echo $form->hiddenField($tabNote,'project_fk',array('size'=>20,'maxlength'=>20,'value'=>$project_fk)); ?>
		<?php echo $form->error($tabNote,'project_fk'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($tabNote,'tab_note'); ?>
		<?php echo $form->textArea($tabNote,'tab_note',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($tabNote,'tab_note'); ?>
	</div>
<!--
	<div class="row buttons">
		<?php //echo CHtml::submitButton($tabNote->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
-->
    </fieldset>
<?php $this->endWidget(); ?>

</div><!-- form -->