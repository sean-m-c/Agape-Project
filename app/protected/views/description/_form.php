<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'description-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'table_name'); ?>
		<?php
                $schema = Yii::app()->db->schema->getTableNames();
                $exclude = array('authassignment','authitem','authitemchild');
                $tables=array();
                
                foreach($schema as $table) {
                    // Make sure it's not one of the tables there's no point in showing

                    if(!in_array($table,$exclude)) {
                        // Format the table name so it's all pretty
                        $words=explode('_',$table);
                        foreach($words as $k => $word) {
                            $tableWords[$k] = ucwords($word);
                        }
                        $tables[$table]=implode(' ',$tableWords);
                    }
                }
                echo $form->dropdownList($model,'table_name',$tables,
                    array(
                        'ajax' => array(
                        'type'=>'POST', //request type
                        'url'=>CController::createUrl('description/dynamicColumns'), //url to call.
                        //Style: CController::createUrl('currentController/methodToCall')
                        //'update'=>'#Description_field_name', //selector to update
                        //'data'=>'js:javascript statement'
                        //leave out the data key to pass all form values through
                        ))); ?>

		<?php echo $form->error($model,'table_name'); ?>
	</div>

        <div class="row">
		<?php echo $form->labelEx($model,'field_name'); ?>
		<?php echo $form->dropdownList($model,'field_name',array()); ?>
		<?php echo $form->error($model,'field_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
		<?php echo $form->textArea($model,'text',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'text'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'buttonLink i_add')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php CHtml::script('

'); ?>