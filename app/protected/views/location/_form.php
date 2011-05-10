
<div id="locationForm" class="form">
    <fieldset>
    <?php
    $locationForm = $this->beginWidget('CActiveForm', array(
                'id' => 'location-form',
                'enableAjaxValidation' => true
            ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $locationForm->errorSummary($location); ?>

    <div class="row">
        <?php echo $locationForm->labelEx($location, 'country'); ?>
        <?php echo $locationForm->dropDownList($location, 'country',
                CHtml::listData(Country::model()->findAll(array('order' => 'name ASC')),
                        'name', 'name'),array('options'=>array('United States'=>array('selected'=>'selected')))); ?>
        <?php echo $locationForm->error($location, 'country'); ?>
    </div>

    <div class="nationalFields">
        <div class="row">
            <?php echo $locationForm->labelEx($location, 'address_line_1'); ?>
            <?php echo $locationForm->textField($location, 'address_line_1', array('size' => 60, 'maxlength' => 80)); ?>
            <?php echo $locationForm->error($location, 'address_line_1'); ?>
        </div>
    </div>

    <div class="row">
        <?php echo $locationForm->labelEx($location, 'address_line_2'); ?>
        <?php echo $locationForm->textField($location, 'address_line_2', array('size' => 60, 'maxlength' => 80)); ?>
        <?php echo $locationForm->error($location, 'address_line_2'); ?>
        </div>


        <div class="row">
        <?php echo $locationForm->labelEx($location, 'city'); ?>
        <?php echo $locationForm->textField($location, 'city', array('size' => 50, 'maxlength' => 50)); ?>
        <?php echo $locationForm->error($location, 'city'); ?>
        </div>


        <div class="nationalFields">
            <div class="row">
            <?php echo $locationForm->labelEx($location, 'state'); ?>
            <?php echo $locationForm->textField($location, 'state', array('size' => 2, 'maxlength' => 2)); ?>
            <?php echo $locationForm->error($location, 'state'); ?>
        </div>

        <div class="row">
            <?php echo $locationForm->labelEx($location, 'zip'); ?>
            <?php echo $locationForm->textField($location, 'zip', array('size' => 5, 'maxlength' => 5)); ?>
            <?php echo $locationForm->error($location, 'zip'); ?>
        </div>
    </div>

    <div class="row">
        <?php
            //echo CHtml::hiddenField('communityPartnerID', $communityPartnerOID);
            echo CHtml::hiddenField('scenario', $location->scenario);
            echo CHtml::hiddenField('geographicScenario', 'national');
            echo $locationForm->hiddenField($location, 'latlng');
            echo $locationForm->error($location, 'latlng'); ?>
        </div>

        <div class="row">
        <?php
            echo CHtml::link('Add Location', array('/location/create'),
                    array('onclick' => 'loadAPI(); return false;',
                        'class' => 'i_checkmark buttonLink',
                        'id' => 'addLocationSubmit',
            ));
        ?>
        </div>

        <div id="ajaxResponse"></div>

    <?php $this->endWidget(); ?>
    </fieldset>
        </div><!-- form -->

<?php echo CHtml::script('
var country = $(":input#Location_country");

setLocationRequired(country);

country.change(function() {
    setLocationRequired(country);
});

function setLocationRequired(country) {
    if(country.val()!="United States") {
        $(".nationalFields label").find("span.required").remove();
        $("#geographicScenario").val("international");
    } else if (country.val()=="United States") {
        $(".nationalFields label").append(" <span class=\'required\'>*</span>").fadeIn();
        $("#geographicScenario").val("national");
    }
}

'); ?>