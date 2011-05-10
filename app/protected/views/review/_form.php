<div class="form">
    <fieldset>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'review-form',
	'enableAjaxValidation'=>false,
    'action'=>$params['action'],
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'comment',array('style'=>'text-align:left;')); ?>
		<?php echo $form->textArea($model,'comment',array('rows'=>6, 'cols'=>40)); ?>
		<?php echo $form->error($model,'comment'); ?>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'tab_fk',array('value'=>$params['tab_fk'])); ?>
		<?php echo $form->error($model,'tab_fk'); ?>
	</div>
    
	<div class="row">
		<?php echo $form->hiddenField($model,'makes_review_fk',array('value'=>$params['makes_review_fk'])); ?>
		<?php echo $form->error($model,'makes_review_fk'); ?>
	</div>

    <?php echo CHtml::hiddenField('Review[projectOID]',$_GET['id']); ?>
	<div class="row buttons">
        <?php echo CHtml::link($model->isNewRecord ? 'Comment' : 'Save',
                    '',array(
                    'onclick'=>CHtml::ajax(array
                    (
                            'url'=>$params['action'],
                            'type'=>'POST',
                            'success'=>'function(data) {
                                    if(data!="true") {
                                        alert(data.substr(data.indexOf("|")+1));
                                    } else {
                                        $("#reviewBox'.$params['tab_fk'].'").slideToggle();
                                    }
                                }',

                    )),
                    'class'=>'i_checkmark buttonLink'
            )
    ); ?>
	</div>

<?php $this->endWidget(); ?>
    </fieldset>
</div><!-- form -->