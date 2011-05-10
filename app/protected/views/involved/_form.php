<div class="form">
    <fieldset>
        <?php /* echo CHtml::link('What is this?',array('#'),
        array('class'=>'i_question showTooltip buttonLink noLoader',
        'onclick'=>'return false;',
        'title'=>'Here you can affiliate yourself with a community partner. You can
            apply to be connected with any of the partners in the list, or you
            can create a new community partner.')); */?>

        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'involved-form',
                'enableAjaxValidation'=>true,
        )); ?>

        <?php echo $form->errorSummary($model); ?>

        <div class="row">
            <?php echo $form->hiddenField($model,'user_fk',array('value'=>Yii::app()->user->id)); ?>
            <?php echo $form->error($model,'user_fk'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'community_partner_fk'); ?>
            <?php echo $form->dropdownList($model,'community_partner_fk',
            CHtml::listdata(CommunityPartner::model()->findAll(),
            'community_partner_oid','agency_name')); ?>
            <?php echo $form->error($model,'community_partner_fk'); ?>
        </div>
        <div class="row">
            <?php
            echo CHtml::link($model->isNewRecord ? 'Connect To Partner' : 'Save',
            '',array(
            'onclick'=>CHtml::ajax(array(
            'url'=>array('/involved/create'),
            'type'=>'POST',
            'success'=>'function(data) {
                    if(data.substr(0,1)=="f") {
                        $("#ajaxResponse").html("<p>There was a problem connecting to this community partner.</p>").fadeIn();
                    } else {
                        $.fn.yiiGridView.update("myPartners-grid");
                        $("#connectPartnerDialog").dialog("close");
                    }
                }'
            )),
            'style'=>"cursor:pointer;",
            'class'=>'i_checkmark buttonLink',
            'id'=>'joinPartnerSubmitLink'
            ));?>
        </div>

        <div id="ajaxResponse"></div>
        
        <?php $this->endWidget(); ?>
    </fieldset>

    <?php if($this->route!='involved/myPartners') : ?>
    <p>
            <?php echo CHtml::beginForm(array('communityPartner/create')); ?>
            <?php echo CHtml::hiddenField('Hidden[userid]',$userid); ?>
            <?php echo CHtml::submitButton('I want to create a new community partner.',array('class'=>'i_add buttonLink')); ?>
    </p>
    <?php endif; ?>

</div><!-- form -->