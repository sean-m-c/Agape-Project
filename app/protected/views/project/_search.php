<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'project_name'); ?>
		<?php echo $form->textField($model,'project_name',array('size'=>45,'maxlength'=>45)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'project_description'); ?>
		<?php echo $form->textField($model,'project_description',array('size'=>60,'maxlength'=>1000)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'start_date'); ?>
		<?php echo $form->textField($model,'start_date',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'end_date'); ?>
		<?php echo $form->textField($model,'end_date',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'geographic'); ?>
		<?php echo $form->textField($model,'geographic'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'volunteer_lead_name'); ?>
		<?php echo $form->textField($model,'volunteer_lead_name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'volunteer_lead_email'); ?>
		<?php echo $form->textField($model,'volunteer_lead_email',array('size'=>35,'maxlength'=>35)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'volunteer_lead_phone'); ?>
		<?php echo $form->textField($model,'volunteer_lead_phone',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'credit_bearing'); ?>
		<?php echo $form->textField($model,'credit_bearing'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'prep_work'); ?>
		<?php echo $form->textField($model,'prep_work'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'prep_work_help'); ?>
		<?php echo $form->textField($model,'prep_work_help'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'measure_results'); ?>
		<?php echo $form->textField($model,'measure_results'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'indoor_outdoor'); ?>
		<?php echo $form->textField($model,'indoor_outdoor'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'contingency_description'); ?>
		<?php echo $form->textField($model,'contingency_description',array('size'=>60,'maxlength'=>500)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'rmp'); ?>
		<?php echo $form->textField($model,'rmp'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'volunteer_count'); ?>
		<?php echo $form->textField($model,'volunteer_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'minimum_age'); ?>
		<?php echo $form->textField($model,'minimum_age'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'apparel'); ?>
		<?php echo $form->textField($model,'apparel',array('size'=>60,'maxlength'=>250)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'food_provided'); ?>
		<?php echo $form->textField($model,'food_provided'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'food_provider'); ?>
		<?php echo $form->textField($model,'food_provider',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'restroom'); ?>
		<?php echo $form->textField($model,'restroom'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'handicap_friendly'); ?>
		<?php echo $form->textField($model,'handicap_friendly'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'arrival_time'); ?>
		<?php echo $form->textField($model,'arrival_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'parking_instructions'); ?>
		<?php echo $form->textField($model,'parking_instructions',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'community_partner_fk'); ?>
		<?php echo $form->textField($model,'community_partner_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'user_fk'); ?>
		<?php echo $form->textField($model,'user_fk',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'overall_comment'); ?>
		<?php echo $form->textArea($model,'overall_comment',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->