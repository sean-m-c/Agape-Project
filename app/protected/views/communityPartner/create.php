<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'community-partner-form',
            'enableAjaxValidation'=>true,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <fieldset><legend>Partner Information</legend>
        <div class="row">
            <?php echo $form->labelEx($model,'agency_name'); ?>
            <?php echo $form->textField($model,'agency_name',array('size'=>45,'maxlength'=>45)); ?>
            <?php echo $form->error($model,'agency_name'); ?>
        </div>

     <?php // If they're creating it, take the creator's ID so we can create
     // connection in the involved table and make them a CPAdmin
     if($model->isNewRecord)
        echo $form->hiddenField('CommunityPartner[userid]',Yii::app()->user->id); ?>

        <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'i_checkmark')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->