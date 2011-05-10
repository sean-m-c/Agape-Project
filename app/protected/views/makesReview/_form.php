<div class="form">

    <fieldset>
        <legend>Choose existing user</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'makes-review-form',
                'enableAjaxValidation'=>false,
        )); ?>

        <?php echo $form->hiddenField($model,'project_fk',array('value'=>$projectOID)); ?>
        <?php echo $form->error($model,'project_fk'); ?>
        
        <div class="row">
            <?php echo $form->dropdownList($model,'user_fk',CHtml::listdata(User::model()->findAll(),'user_oid','NameAndEmail')); ?>


            <?php echo CHtml::link('Add Reviewer',
            '',array('id'=>'listAddReviewer',
            'onclick'=>CHtml::ajax(array(
            'url'=>array('/makesReview/create'),
            'type'=>'POST',
            'success'=>'function(data) {
                if(data.substr(0,1)=="f") {
                    $("#ajaxResponse").html("<p>There was a problem adding this reviewer.</p>").fadeIn();
                } else {
                    $.fn.yiiGridView.update("makes-review-grid");
                }
            }'
            )),
            'class'=>'i_add buttonLink'
            )); ?>
        </div>
    </fieldset>
    <?php $this->endWidget(); ?>


    <fieldset>
        <legend>Invite reviewer to application</legend>
        <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'makes-review-form',
                'enableAjaxValidation'=>false,
        )); ?>

        <?php echo $form->hiddenField($model,'project_fk',array('value'=>$projectOID)); ?>
        <?php echo $form->error($model,'project_fk'); ?>
        <!-- Textbox for inviting reviewers by email -->
        <div class="row">
            <?php echo CHtml::label('Email','email'); ?>
            <?php echo $form->textField($model,'email'); ?>
            <?php echo $form->hiddenField($model,'addReviewer',array('value'=>'1')); ?>



            <?php echo CHtml::link('Add Reviewer',
            '',array('id'=>'inviteAddReviewer',
            'onclick'=>CHtml::ajax(array(
            'url'=>array('/makesReview/create','invite'=>true),
            'type'=>'POST',
            'success'=>'function(data) {
                if(data.substr(0,1)=="f") {
                    $("#ajaxResponse").html("<p>There was a problem adding this reviewer.</p>").fadeIn();
                } else {
                    $.fn.yiiGridView.update("makes-review-grid");
                }
            }'
            )),
            'class'=>'i_add buttonLink'
            )); ?>
        </div>
    </fieldset>
    <?php $this->endWidget(); ?>

</div><!-- form -->