<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
                'id'=>'createProjectDialog',
                'options'=>array(
                    'title'=>'Create Project',
                    'show'=>'fade',
                    'hide'=>'fade',
                    'autoOpen'=>false,
                    'resizable'=>'false',
                    'modal'=>'true',
                    'width'=>'auto',
                    'height'=>'auto',
                    'close'=>'js: function() {
                        $(this).find(":input:not(Project_user_fk)").val("");
                        $(this).find("div#ajaxResponse").empty();
                     }',
                ),
));?>

<div class="form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
            'id'=>'project-form',
            'enableAjaxValidation'=>true,
    ));
    
    echo $form->errorSummary($model);
    
    if(isset(Yii::app()->user->id)) {
        echo $form->hiddenField($model,'user_fk',array('value'=>Yii::app()->user->id));
        echo $form->error($model,'user_fk');
    }
    ?>
    
    <div class="row">
        <?php echo $form->labelEx($model,'project_name'); ?>
        <?php echo $form->textField($model,'project_name',array('size'=>45)); ?>
        <?php echo $form->error($model,'project_name'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'community_partner_fk'); ?>
        <?php echo $form->dropdownList($model,'community_partner_fk',
        CHtml::listData(Involved::model()->findAll('pending=0 AND user_fk='.Yii::app()->user->id),
                'community_partner_fk','communityPartner.agency_name')
        ); ?>
        <?php echo $form->error($model,'community_partner_fk'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::link('Create',
        '',array('id'=>'createProject',
        'onclick'=>CHtml::ajax(array(
            'url'=>array('/project/create'),
            'type'=>'POST',
            'dataType'=>'json',
            'success'=>'function(data) {
                if(data.status=="t") {
                    $.fn.yiiGridView.update("myProjects-grid");
                }
                $("#ajaxResponse").html(data.response).fadeIn();
            }'
        )),
        'style'=>"cursor:pointer;",
        'class'=>'i_checkmark buttonLink'
        )
        ); ?>
    </div>

    <div id="ajaxResponse"></div>

    <?php $this->endWidget(); ?>
</div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>