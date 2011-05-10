<div id="needReviews">

    <p>These projects need assigned reviewers. Once all reviewers have been assigned,
        the project can be marked as ready for reviewing.</p>

    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'needReviewers-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                    'project_name',
                /*
                    array(
                            'name'=>'Reviewer Count',
                            'value'=>'NotificationCount::countAssignedReviewers($data->id)',
                    ),*/
                    array(
                            'class'=>'CButtonColumn',
                            'template'=>'{editReviewers} {reviewersAssigned}',
                            'buttons'=>array(
                                    'editReviewers'=>array(
                                            'label'=>'Edit reviewers for this project.',
                                            'url'=>'Yii::app()->controller->createUrl("workflow/needReviewersDialog",array("projectOID"=>$data->id))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_editReviewers.png',
                                            'options'=>array('class'=>'editReviewers','height'=>'25','width'=>'25'),
                                    ),
                                    'reviewersAssigned'=>array(
                                            'label'=>'Mark all reviewers as assigned and allow project to be reviewed.',
                                            'url'=>'Yii::app()->controller->createUrl("project/reviewersAssigned",array("id"=>$data->id))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_checkmark.png',
                                            'options'=>array('class'=>'gridButtonClick','height'=>'25','width'=>'25'),
                                    ),
                            ),
                    ),
            ),
    ));?>
</div>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/all.js');

echo CHtml::script("
$('.gridButtonClick').click(function() {
    gridButtonClick($(this).attr('href'),'needReviewers-grid');
    return false;
});"); ?>

<div id="dialogContainer" style="display:none;">
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
                'id'=>'editReviewersDialog',
                'options'=>array(
                    'title'=>'Edit Reviewers',
                    'autoOpen'=>false,
                    'modal'=>'true',
                    'width'=>'auto',
                    'height'=>'auto',
                    'show'=>'fade',
                    'hide'=>'fade',
                ),
));?>
    
    <div id="dialogContainerForm"></div>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
</div>
