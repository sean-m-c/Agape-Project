<div id="overallComment">
    <?php echo CHtml::link('What is this?',array('#'),
    array('class'=>'i_question buttonLink showTooltip noLoader',
    'onclick'=>'return false;',
    'title'=>'This review will be sent back to the project creator.
                        For your convenience, the project\'s details and all reviewer comments for this project are
                        available under their tabs on the left to guide you as you write the final review.<br /><br />
                        You may come back and edit this review at any time before sending the decision and review to
                        the project creator.')); ?>
    <div class="form">
        <?php
        $form=$this->beginWidget('CActiveForm', array(
                'id'=>'project-form',
                'enableAjaxValidation'=>true,
        ));

        echo $form->errorSummary($model);
        ?>

        <div class="row">
            <?php echo $form->textArea($model,'overall_comment',array('cols'=>25,'rows'=>10)); ?>
            <?php echo $form->error($model,'overall_comment'); ?>
        </div>

        <?php echo $form->hiddenField($model,'id'); ?>

        <div class="row buttons">
            <?php echo CHtml::link('Save Review',
            '',array('id'=>'saveComment',
            'onclick'=>CHtml::ajax(array
            (
            'url'=>array('/project/update','id'=>$model->id),
            'type'=>'POST',
            'success'=>'function(data) {
                            alert(data.substr(data.indexOf("|")+1));
                            $("#saved").val(data.substr(0,data.indexOf("|")));
                            $("#saveTab").removeClass("ajaxLoaderSmall");
                            $("#saveTab").addClass("i_checkmark");
                            }',

            )),
            'style'=>"cursor:pointer;",
            'class'=>'i_checkmark buttonLink'
            )
            ); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>