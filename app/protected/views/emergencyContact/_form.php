<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'emergency-contact-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($emergencyContact); ?>

	<div class="row">
		<?php echo $form->labelEx($emergencyContact,'first_name'); ?>
		<?php echo $form->textField($emergencyContact,'first_name',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($emergencyContact,'first_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($emergencyContact,'middle_initial'); ?>
		<?php echo $form->textField($emergencyContact,'middle_initial',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($emergencyContact,'middle_initial'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($emergencyContact,'last_name'); ?>
		<?php echo $form->textField($emergencyContact,'last_name',array('size'=>40,'maxlength'=>40)); ?>
		<?php echo $form->error($emergencyContact,'last_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($emergencyContact,'phone'); ?>
		<?php echo $form->textField($emergencyContact,'phone',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($emergencyContact,'phone'); ?>
	</div>

	<div class="row">
		<?php 
                $val='';
                if(isset($project_fk)) {
                    $val=array('value'=>$project_fk);
                }
                echo $form->hiddenField($emergencyContact,'project_fk',$val); ?>
		<?php echo $form->error($emergencyContact,'project_fk'); ?>
	</div>

	<div class="row buttons">
		<?php
                if($emergencyContact->isNewRecord) {
                    echo CHtml::link('Add Contact',
                    '',array(
                    'onclick'=>CHtml::ajax(array
                    (
                    'url'=>array('/emergencyContact/create'),
                    'beforeSend'=>'function() { $("#ajaxAddContactResponse").fadeOut(); }',
                    'type'=>'POST',
                    'dataType'=>'json',
                    'error'=>'function (xhr, ajaxOptions, thrownError){
                        alert(xhr.statusText);
                        alert(thrownError);
                    }',
                    'success'=>'function(data) {
                        if(data.status=="f") {
                            $("#ajaxAddContactResponse").html(data.response).fadeIn();
                        } else {
                            $("#addContactForm").slideToggle();
                        }
                        $.fn.yiiGridView.update("emergency-contact-grid");
                     }',
                    )),
                    'style'=>"cursor:pointer;",
                    'class'=>'i_checkmark buttonLink'
                    ));
                } else {
                    echo CHtml::submitButton('Save'); 
                }
                ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<div id="ajaxAddContactResponse"></div>
