<div class="form">
<fieldset>

    <?php if($refer=='sendEmail') : ?>
    <legend>Your Email</legend>
    <?php elseif($refer=='resetPassword'): ?>
    <legend>Reset Password</legend>
    <?php endif; ?>

<?php if(!$error) : ?>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reset-password-form-resetPassword-form',
	'enableAjaxValidation'=>false,
)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

        <?php if($refer=='sendEmail') : ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
        <?php echo $form->hiddenField($model,'formScenario',array('value'=>'getEmailAddress')); ?>
        
        <div class="row buttons">
		<?php echo CHtml::submitButton('Send Email'); ?>
	</div>

        <?php endif; ?>

        <?php if($refer=='resetPassword') : ?>

        <?php echo $form->hiddenField("ResetPasswordForm['formScenario']",'resetPassword'); ?>
        <?php echo $form->hiddenField("ResetPasswordForm['email']",$_GET['email']); ?>
        <?php echo $form->hiddenField("ResetPasswordForm['passwordReset']",'true'); ?>

        <div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

        <?php endif; ?>
        
	

<?php $this->endWidget(); ?>

<?php else: // Their link doesn't add up, might be a hacker or a system goof ?>
The link does not appear to be valid. Click <?php echo CHtml::link('here',array('/site/forgotPassword')); ?> to resend the email.
<?php endif; ?>
        </fieldset>
</div><!-- form -->
