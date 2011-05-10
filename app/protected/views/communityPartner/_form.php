    <div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'community-partner-form',
            'enableAjaxValidation'=>true,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <?php if(!$model->isNewRecord) : ?>
    <fieldset><legend>Partner Information</legend>
    <?php endif; ?>

        <div class="row">
            <?php echo $form->labelEx($model,'agency_name'); ?>
            <?php echo $form->textField($model,'agency_name',array('size'=>45,'maxlength'=>45)); ?>
            <?php echo $form->error($model,'agency_name'); ?>
        </div>

     <?php if(!$model->isNewRecord) : ?>
        <div class="row">
            <?php echo $form->labelEx($model,'pc_url'); ?>
            <?php echo $form->textField($model,'pc_url',array('size'=>45,'maxlength'=>45)); ?>
            <?php echo $form->error($model,'pc_url'); ?>
        </div>
    </fieldset>
    <fieldset>
        <legend>Primary Contact</legend>
        <div class="row">
            <?php echo $form->labelEx($model,'pc_first_name'); ?>
            <?php echo $form->textField($model,'pc_first_name',array('size'=>45,'maxlength'=>20)); ?>
            <?php echo $form->error($model,'pc_first_name'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'pc_last_name'); ?>
            <?php echo $form->textField($model,'pc_last_name',array('size'=>45,'maxlength'=>30)); ?>
            <?php echo $form->error($model,'pc_last_name'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'pc_email'); ?>
            <?php echo $form->textField($model,'pc_email',array('size'=>45,'maxlength'=>45)); ?>
            <?php echo $form->error($model,'pc_email'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'pc_phone_number'); ?>
            <?php echo $form->textField($model,'pc_phone_number'); ?>
            <?php echo $form->error($model,'pc_phone_number'); ?>
        </div>
    </fieldset>

    <div class="row">
        <?php //echo $form->labelEx($model,'pending'); ?>
        <?php //echo $form->textField($model,'pending'); ?>
        <?php //echo $form->error($model,'pending'); ?>
    </div>

    <fieldset>
    <legend>Locations</legend>
    <div class="buttonContainer">
    <?php
        echo CHtml::link('Add New Location', '',
                array('class' => 'i_add buttonLink noLoader', 'id' => 'addLocationLink',
                    'onclick' => '$("#addLocationContainer").slideToggle(); return false;'));
    ?>
    </div>

    <div id="addLocationContainer" style="display:none;">
    <?php $this->renderPartial('/location/_form', array('location' => new Location(), 'communityPartnerOID' => $model->community_partner_oid),false,true); ?>
    </div>

    <div id="locationsList">
    <?php $this->renderPartial('/location/ajaxWrapper', array('model' => $model)); ?>    
    </div>
    </fieldset>
    <?php endif; // end create community partner ?>

    <div class="row buttons">
        <?php
        if($model->isNewRecord) {
            echo CHtml::link('Create Partner',
            '',array(
            'onclick'=>CHtml::ajax(array(
            'url'=>array('/communityPartner/create'),
            'type'=>'POST',
            'dataType'=>'json',
            'success'=>'function(data) {
                    if(data.status=="f") {
                        $("#ajaxResponse").html(data.response).fadeIn();
                        $(".buttonLink").removeClass("ajaxLoaderSmall");
                    } else if(data.status=="t") {
                        window.location = data.response;
                    }
                }'
            )),
            'style'=>"cursor:pointer;",
            'class'=>'i_checkmark buttonLink',
            'id'=>'createPartnerSubmitLink'
            ));
        } else {
            echo CHtml::link('Save',array('communityPartner/update'),array(
                'class'=>'i_checkmark buttonLink geocodeAddress',
                'onclick' => 'loadAPI(); return false;',
                ));
        }

    ?>
    </div>

    <div id="ajaxResponse"></div>

    <?php $this->endWidget(); ?>
    
</div><!-- form -->