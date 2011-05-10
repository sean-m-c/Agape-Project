<div class="form">
    <fieldset>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'makes-review-form',
                'enableAjaxValidation'=>true,
        )); ?>

        <?php echo $form->hiddenField($model,'project_fk',array('value'=>$projectOID)); ?>
        <?php echo $form->error($model,'project_fk'); ?>

        <div class="row">
            <?php echo CHtml::label('Registered users','user_fk',array('style'=>'width:auto;')); ?>
        </div>

        <div class="row">
            <?php echo $form->dropdownList($model,'user_fk',CHtml::listdata(User::model()->findAll(),'user_oid','NameAndEmail')); ?>

            <?php echo CHtml::link('Add as reviewer',
            '',array('id'=>'listAddReviewer',
            'onclick'=>CHtml::ajax(array(
            'url'=>array('/makesReview/create'),
            'type'=>'POST',
            'success'=>'function(data) {              
                $.fn.yiiGridView.update("makes-review-grid");
                $("#ajaxResponse").html(data);
            }')),
            'class'=>'i_add buttonLink'
            )); ?>
        </div>

        <?php $this->endWidget(); ?>

        <div id="ajaxResponse"></div>
        
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'makes-review-form',
                'action'=>array('/makesReview/create'),
                'enableAjaxValidation'=>true,
        )); ?>

        <?php echo $form->hiddenField($model,'project_fk',array('value'=>$projectOID)); ?>
        <?php echo $form->error($model,'project_fk'); ?>

        <div class="row">
            <?php echo $form->labelEx($model,'email',array('style'=>'width:auto;')); ?>
        </div>
        <!-- Textbox for inviting reviewers by email -->
        <div class="row">
            <?php echo $form->textField($model,'email'); ?>
            <?php echo $form->hiddenField($model,'addReviewer',array('value'=>'1')); ?>

            <?php echo CHtml::link('Invite to application as reviewer',
            '',array('id'=>'inviteAddReviewer',
            'onclick'=>CHtml::ajax(array(
            'url'=>array('/makesReview/create','invite'=>true),
            'type'=>'POST',
            'success'=>'function(data) {
                $.fn.yiiGridView.update("makes-review-grid");
                $("#ajaxResponse").html(data);
            }')),
            'class'=>'i_add buttonLink'
            )); ?>
        </div>
    </fieldset>
    <?php $this->endWidget(); ?>

</div><!-- form -->

<h3 style="margin-bottom:0;">Assigned Reviewers</h3>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'makes-review-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
                array(
                    'name'=>'Reviewer',
                    'value'=>'$data->user->NameAndEmail'
                ),
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewUser} {delete}',
                        'deleteButtonLabel'=>'Remove reviewer from project.',
                        'deleteConfirmation'=>'Are you sure you want to remove this reviewer from the project?',
                        'deleteButtonOptions'=>array('height'=>'25','width'=>'25'),
                        'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                        'deleteButtonUrl'=>'Yii::app()->controller->createUrl("/makesReview/delete",array("id"=>$data->makes_review_oid))',
                        'buttons'=>array(
                                'viewUser'=>array(
                                        'label'=>'View User >>',
                                        'url'=>'Yii::app()->controller->createUrl("user/view",array("id"=>$data->user_fk))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                                ),
                        ),
                ),
        ),
));
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/all.js'); ?>
