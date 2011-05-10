<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'strategy-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

    <fieldset><legend>Evaluation Methods</legend>
    <?php 
    // Get all the available methods from the method table
    $methods = Method::model()->findAll();

    // Function to list all available methods depending on type
    // Qualitative marked with flag "0"
    // Quantitative marked with flag "1"
    function listMethod($type) {
        $retval=null;
        if(isset($type)) {
            $i=0;
            foreach($methods as $method) {
                if($method->type==$type) {

                    $retval .= '<div class="row checkboxToggle">' . "\n" .
                        CHtml::label($default->name, 'NeedClearance_project_fk') . "\n" .
                        CHtml::checkbox('NeedClearance[' . $i . ']', '', array('value' => $method->method_oid)) . "\n" .
                        CHtml::hiddenField('NeedClearance[' . $i . '][action]', $action, array('class' => 'action')) . "\n" .
                        CHtml::hiddenField('NeedClearance[' . $i . '][key]', $method->method_oid) . "\n" .
                        '</div>' . "\n";
                }
                
                $i++;
            }
        }
        return $retval;
    }
    ?>
    <?php echo CHtml::checkbox('quantitative',null,array('id'=>'checkbox_quantitative')); ?>
        <div id="quantitativeMethodContainer" style="display:none;">
            <?php listMethod('1'); ?>
        </div>
    <?php echo CHtml::checkbox('qualitative',null,array('id'=>'checkbox_qualitative')); ?>
        <div id="qualitativeMethodContainer" style="display:none;">
            <?php listMethod('0'); ?>
        </div>
    </fieldset>
    <!--
	<div class="row">
		<?php //echo $form->labelEx($model,'parent_fk'); ?>
		<?php //echo $form->textField($model,'parent_fk',array('size'=>20,'maxlength'=>20)); ?>
		<?php //echo $form->error($model,'parent_fk'); ?>
	</div>
    -->

	<div class="row">
		<?php //echo $form->labelEx($model,'strategy_description'); ?>
		<?php echo $form->textField($model,'strategy_description',array('size'=>60,'maxlength'=>500)); ?>
		<?php echo $form->error($model,'strategy_description'); ?>
	</div>

	<div class="row">
        <?php echo CHtml::link('Save',
        '',array('id'=>'saveStrategy',
        'onclick'=>CHtml::ajax(array(
            'url'=>array('/strategy/update','id'=>$model->strategy_oid),
            'type'=>'POST',
            'dataType'=>'json',
            'beforeSend'=>'function() { $("div#ajaxResponse").fadeOut().empty(); }',
            'success'=>'function(data) {
                if(data.status=="t") {
                    $.fn.yiiGridView.update("strategy-grid");
                    $("#addDialog").dialog("close");
                } else if(data.status=="f") {
                    $("div#ajaxResponse").html(data.response).fadeIn();
                } else {
                    $("div#ajaxResponse").html("<div class=\"flash flash-error\">There was a problem submitting this form.</div>").fadeIn();
                }
            }'
        )),
        'style'=>"cursor:pointer;",
        'class'=>'i_checkmark buttonLink'
        )
        ); ?>
    </div>
    <div id="ajaxResponse"></div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<?php echo CHtml::script('
var qualCont = $("div#qualitativeMethodContainer");
var quanCont = $("div#quantitativeMethodContainer");

var qualCheckbox = $(":checkbox#quantitativeCheckbox");
var quanCheckbox = $(":checkbox#qualitativeeCheckbox");

function checkboxClick(this) {

    if ($(this).is(":checked")) {
        $(this).next("input:hidden").val($(this).val());
    } else {
        $(this).next("input:hidden").val("delete");
    }

}
'); ?>