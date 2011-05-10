<?php if(Yii::app()->controller->action->id == 'update') : ?>
<div class="form">
    <fieldset>
        <?php echo CHtml::link('Change Email',array('#'),array('id'=>'changeEmail','class'=>'i_arrow_down icon_pad')); ?>
        <div id="changeEmailContainer" style="display:none;">
                <?php $form=$this->beginWidget('CActiveForm', array(
                        'id'=>'user-form',
                        'enableAjaxValidation'=>true,
                )); ?>
            <div class="row">
                    <?php echo $form->labelEx($model,'email'); ?>
                    <?php echo $form->textField($model,'email',array('size'=>32,'maxlength'=>32)); ?>
                    <?php echo $form->error($model,'email'); ?>
            </div>

            <div class="row">
                    <?php echo $form->labelEx($model,'emailConfirm'); ?>
                    <?php echo $form->textField($model,'emailConfirm',array('size'=>32,'maxlength'=>32)); ?>
                    <?php echo $form->error($model,'emailConfirm'); ?>
            </div>

            <div class="row buttons">
                    <?php echo CHtml::submitButton('Change',array('class'=>'i_checkmark')); ?>
            </div>

                <?php $this->endWidget(); ?>
        </div>
    </fieldset>
    <fieldset>
        <?php echo CHtml::link('Change Password',array('#'),array('id'=>'changePassword','class'=>'i_arrow_down icon_pad')); ?>
        <div id="changePasswordContainer" style="display:none;">
                <?php $form=$this->beginWidget('CActiveForm', array(
                        'id'=>'user-form',
                        'enableAjaxValidation'=>true,
                )); ?>
            <div class="row">
                    <?php echo $form->labelEx($model,'password'); ?>
                    <?php echo $form->passwordField($model,'password',array('value'=>'','size'=>32,'maxlength'=>32)); ?>
                    <?php echo $form->error($model,'password'); ?>
            </div>

            <div class="row">
                    <?php echo $form->labelEx($model,'passwordConfirm'); ?>
                    <?php echo $form->passwordField($model,'passwordConfirm',array('value'=>'','size'=>32,'maxlength'=>32)); ?>
                    <?php echo $form->error($model,'passwordConfirm'); ?>
            </div>

            <div class="row buttons">
                    <?php echo CHtml::submitButton('Change',array('class'=>'i_checkmark')); ?>
            </div>
                <?php $this->endWidget(); ?>
        </div>
    </fieldset>
</div>
<?php endif; ?>

<div class="form">
    <fieldset>
        <legend>General Information</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'user-form',
                'enableAjaxValidation'=>true,
        )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>        

        <?php if(Yii::app()->user->isGuest || Yii::app()->controller->action->id == 'create') : ?>
        <div class="row">
                <?php echo $form->labelEx($model,'email'); ?>
                <?php echo $form->textField($model,'email',array('size'=>32,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'email'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'password'); ?>
                <?php echo $form->passwordField($model,'password',array('size'=>32,'maxlength'=>32)); ?>
                <?php echo $form->error($model,'password'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'passwordConfirm'); ?>
                <?php echo $form->passwordField($model,'passwordConfirm',array('size'=>32,'maxlength'=>32)); ?>
                <?php echo $form->error($model,'passwordConfirm'); ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <?php echo $form->labelEx($model,'organization_name'); ?>
            <?php echo $form->textField($model,'organization_name',array('size'=>40,'maxlength'=>100)); ?>
            <?php echo $form->error($model,'organization_name'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'first_name'); ?>
            <?php echo $form->textField($model,'first_name',array('size'=>32,'maxlength'=>30)); ?>
            <?php echo $form->error($model,'first_name'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'middle_initial'); ?>
            <?php echo $form->textField($model,'middle_initial',array('size'=>1,'maxlength'=>1)); ?>
            <?php echo $form->error($model,'middle_initial'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'last_name'); ?>
            <?php echo $form->textField($model,'last_name',array('size'=>32,'maxlength'=>40)); ?>
            <?php echo $form->error($model,'last_name'); ?>
        </div>

        <?php
        // Hide this information when they're registering (if they're not signed in), they won't want to be bothered with it
        if(!Yii::app()->user->isGuest) : ?>

        <div class="row">
                <?php echo $form->labelEx($model,'address_line_1'); ?>
                <?php echo $form->textField($model,'address_line_1',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'address_line_1'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'address_line_2'); ?>
                <?php echo $form->textField($model,'address_line_2',array('size'=>50,'maxlength'=>50)); ?>
                <?php echo $form->error($model,'address_line_2'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'city'); ?>
                <?php echo $form->textField($model,'city',array('size'=>40,'maxlength'=>40)); ?>
                <?php echo $form->error($model,'city'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'state'); ?>
                <?php echo $form->textField($model,'state',array('size'=>2,'maxlength'=>2)); ?>
                <?php echo $form->error($model,'state'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'zip'); ?>
                <?php echo $form->textField($model,'zip',array('size'=>5,'maxlength'=>5)); ?>
                <?php echo $form->error($model,'zip'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'phone'); ?>
                <?php echo $form->textField($model,'phone',array('size'=>14,'maxlength'=>14)); ?>
                <?php echo $form->error($model,'phone'); ?>
        </div>

        <?php endif; // End profile information ?>


        <?php
        // First, make sure the user is logged in so we don't throw errors
        if(!Yii::app()->user->isGuest) : ?>

            <?php
            // We want application admins to be able to disable 'unruly' users
            if(Yii::app()->user->checkAccess('aidadmin') || Yii::app()->user->checkAccess('adminhead')) : ?>
        <div class="row">
                    <?php echo $form->labelEx($model,'login_enabled'); ?>
                    <?php echo $form->checkbox($model,'login_enabled'); ?>
                    <?php echo $form->error($model,'login_enabled'); ?>
        </div>
            <?php endif; ?>

            <?php
            // We only want adminheads to be able to modify this part
            if(Yii::app()->user->checkAccess('adminhead')) : ?>
        <div class="row">
                    <?php echo $form->labelEx($model,'is_adminhead'); ?>
                    <?php echo $form->checkbox($model,'is_adminhead'); ?>
                    <?php echo $form->error($model,'is_adminhead'); ?>
        </div>
            <?php endif; ?>

        <?php
        // We only want adminheads and aidadmins to be able to edit this
        if(Yii::app()->user->checkAccess('aidadmin') || Yii::app()->user->checkAccess('adminhead')) : ?>
        <div class="row">
                    <?php echo $form->labelEx($model,'is_aidadmin'); ?>
                    <?php echo $form->checkbox($model,'is_aidadmin'); ?>
                    <?php echo $form->error($model,'is_aidadmin'); ?>
        </div>
         <?php endif; ?>

        <div class="row">
                <?php echo $form->labelEx($model,'is_volunteer'); ?>
                <?php echo $form->checkbox($model,'is_volunteer'); ?>
                <?php echo $form->error($model,'is_volunteer'); ?>
        </div>

        <?php

        if(Yii::app()->user->checkAccess('aidadmin') ||
                Yii::app()->user->checkAccess('adminhead')) {
            // This variable gets checked to disable them being automatically logged in after account creation
            echo CHtml::hiddenField('User[autoLogin]',false);
        }
        ?>

        <?php endif; // End checking if user is logged in ?>

        <?php if(Yii::app()->user->isGuest) {
        // We want people registering to be registered as volunteers automatically.
        // If someone is logged in and creating users, they get a choice.
            echo CHtml::activeHiddenField($model,'is_volunteer',array('value'=>'1'));
            echo CHtml::hiddenField('User[volunteerRegister]','1');
            echo CHtml::hiddenField('User[autoLogin]',true);
        }
        ?>

        <div class="row buttons">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Register' : 'Save',array('class'=>'i_checkmark i_button')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </fieldset>
</div><!-- form -->

<?php echo CHtml::script("
jQuery(document).ready(function() {
    jQuery('a#changeEmail').click(function() {
        jQuery('#changeEmailContainer').slideToggle();
        return false;
    });
    jQuery('a#changePassword').click(function() {
        jQuery('#changePasswordContainer').slideToggle();
        return false;
    });
});
"); ?>
