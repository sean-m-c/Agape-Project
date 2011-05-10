<?php
echo CHtml::css('
#decisionLabel { font-size:1.5em; margin-bottom:.5em; };
#decisionLabel.message0 { color:#3a9901; }
#decisionLabel.message1 { color:#dc9c03; }
#decisionLabel.message2 { color:#99231a; }
');
?>
<div id="finalDecision">

    <?php echo CHtml::link('What is this?',array('#'),
        array('class'=>'i_question showTooltip buttonLink noLoader',
        'onclick'=>'return false;',
        'title'=>'This is the final decision for the project. You can choose to:

        <ul id="decisionTooltip">
            <li>Approve: This will mark the project as approved to be </li>
            <li>Needs Revision: This will reject the project, but allow the creator
                to make changes based on your final review and resubmit the project for
                review again.</li>
            <li>Reject: This will reject the project as unsuitable for collaboration.
                The creator will not be able to resubmit the project.</li>
        </ul>

        You may use the slider to make your decision, and click "Decide" to save your decision.
        This decision may be changed at any time.')); ?>

    <div class="form">
        <fieldset>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'project-form',
                'enableAjaxValidation'=>false
        )); ?>

        <?php echo $form->errorSummary($model); ?>

        <div class="row">
            <div id="decisionLabel">No decision</div>
            <div id="sliderContainer">
            <?php
            $this->widget('zii.widgets.jui.CJuiSlider', array(
                    'id'=>'decisionSlider',
                    'value'=>$model->status,
                    // additional javascript options for the slider plugin
                    'options'=>array(
                            'min'=>3,
                            'max'=>6,
                            'step'=>1,
                            'slide'=>'js: function(event, ui) {
                                var decisionText = {
                                    0: "None",
                                    1: "None",
                                    2: "None",
                                    3: "None",
                                    4: "Approve",
                                    5: "Needs Revision",
                                    6: "Reject",
                                }
                                var offset = $(".ui-slider-handle").offset();
                                $("#decisionInput").val(ui.value);
                                $("#decisionLabel").attr("class","message"+ui.value).text(decisionText[ui.value]);

                            }',
                    ),
                    'htmlOptions'=>array(
                            'style'=>'width:80px;margin:10px 0 20px 5px ;'
                    ),
            ));?>
            </div>
            <?php echo CHtml::script('
            $(document).ready(function() {
                var decisionText = {
                                0: "None",
                                1: "None",
                                2: "None",
                                3: "None",
                                4: "Approve",
                                5: "Needs Revision",
                                6: "Reject",
                            }
                $("#decisionLabel").text(decisionText[$("#decisionInput").val()]);

            });');
            ?>
            
            <?php echo $form->hiddenField($model,'status',array('id'=>'decisionInput')); ?>
            <?php echo $form->error($model,'status'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::link('Decide',
            '',array(
            'onclick'=>
            CHtml::ajax(array
            (
            'url'=>array('/project/update','id'=>$model->id),
            'type'=>'POST',
            'beforeSend'=>'function() {
                if(!confirm("You are about to make a final decision and send this project back to the project creator with the final review. Are you sure you want to do this?")) {
                    return false;
                }
            }',
            'success'=>'function(data) {
                if(data!="true") {
                    alert(data.substr(data.indexOf("|")+1));
                }
            }')),
            'style'=>"cursor:pointer;",
            'class'=>'i_checkmark buttonLink'
            )); ?>
        </div>
        </fieldset>
        <div class="row buttons" style="margin-top:1.5em;">
            <?php echo CHtml::link('Remove final decision',
            '',array(
            'onclick'=>CHtml::ajax(array
            (
            'url'=>array('/project/update','id'=>$model->id,'status'=>'3'),
            'type'=>'POST',
            'success'=>'function(data) {
                if(data.substr(0,5)=="false") {
                    alert(data.substr(data.indexOf("|")+1));
                } else {
                    alert("This project has been moved back to the \"in review\" stage");
                    jQuery("#finalDecision").html(data);
                }
             }',
            )),
            'style'=>"cursor:pointer;",
            'class'=>'i_logout noBGPad buttonLink'
            )
            ); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>