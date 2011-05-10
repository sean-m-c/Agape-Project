<?php
echo CHtml::css('
#decisionLabel { font-size:1.5em; margin-bottom:.5em; };
#decisionLabel.message0 { color:#3a9901; }
#decisionLabel.message1 { color:#dc9c03; }
#decisionLabel.message2 { color:#99231a; }
');

if(!Yii::app()->user->checkAccess('adminhead')) {
    $model=MakesReview::model()->find(array(
    'condition'=>'user_fk=:userfk AND project_fk=:projectfk',
    'params'=>array(':userfk'=>Yii::app()->user->id,':projectfk'=>$_GET['id'])));
}
 
?>
<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'makes-review-form',
            'enableAjaxValidation'=>false,
            'action'=>array('/makesReview/update','id'=>$model->makes_review_oid,'projectOID'=>$_GET['id']),
    )); ?>
    <p>You may change your decision at any time.</p>
    <?php echo $form->errorSummary($model); ?>
    
    <div class="row">
        <div id="decisionLabel">No decision</div>
        <p>
        <?php
        $this->widget('zii.widgets.jui.CJuiSlider', array(
            'id'=>'decisionSlider',
            'value'=>$model->decision,
            // additional javascript options for the slider plugin
            'options'=>array(
                'min'=>0,
                'max'=>2,
                'step'=>1,
                'slide'=>'js: function(event, ui) {
                            var decisionText = {
                                0: "Approve",
                                1: "Needs Revision",
                                2: "Reject",
                            }
                            var offset = $(".ui-slider-handle").offset();
                        $("#decisionInput").val(ui.value);
                        $("#decisionLabel").attr("class","message"+ui.value).text(decisionText[ui.value]);
                        
                    }',
                ),
            'htmlOptions'=>array(
                'style'=>' width:60px;'
            ),
        ));
        echo CHtml::script('
            $(document).ready(function() {
            var decisionText = {
                                0: "Approve",
                                1: "Needs Revision",
                                2: "Reject",
                            }
                $("#decisionLabel").text(decisionText[$("#decisionInput").val()]);
                
            });');
        ?>
        </p>
        <?php echo $form->hiddenField($model,'decision',array('id'=>'decisionInput')); ?>
        <?php echo $form->error($model,'decision'); ?>
        
    </div>

    <p>
        <?php echo CHtml::link('Decide',
                    '',array(
                    'onclick'=>CHtml::ajax(array
                    (
                            'url'=>array('/makesReview/update','id'=>$model->makes_review_oid,'projectOID'=>$_GET['id']),
                            'type'=>'POST',
                            'success'=>'function(data) {
                                    if(data!="true") {
                                        alert(data.substr(data.indexOf("|")+1));
                                    } else {
                                        $("#reviewDecision").slideToggle();
                                    }
                                }',

                    )),
                    'style'=>"cursor:pointer;",
                    'class'=>'i_checkmark buttonLink'
            )
    ); ?>
        <?php echo CHtml::link('Remove my decision',
            '',array(
            'onclick'=>CHtml::ajax(array
            (
            'url'=>array('/makesReview/update','id'=>$model->makes_review_oid,'decision'=>'NULL'),
            'type'=>'POST',
            'success'=>'function(data) {
                if(data.substr(0,5)=="false") {
                    alert(data.substr(data.indexOf("|")+1));
                } else {
                    alert("Your decision has been removed");
                    jQuery("div#reviewDecision").html(data);
                }
             }',
            )),
            'style'=>"cursor:pointer;",
            'class'=>'i_logout noBGPad buttonLink'
            )
            ); ?>
    </p>

    <?php $this->endWidget(); ?>

</div>