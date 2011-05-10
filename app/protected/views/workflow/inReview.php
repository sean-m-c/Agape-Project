<?php
$this->breadcrumbs=array(
        'Workflow'=>array('/workflow'),
        'In Review',
);?>

<p>These projects have had all reviewers assigned, and are in the review process.</p>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'inReview-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
                'project_name',
                array(
                        'name'=>'Description',
                        'value'=>'(!empty($data->project_description)) ? substr($data->project_description,0,200)."..." : "None set"',
                ),
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewProject} {editReviewers}',
                        'buttons'=>array(
                                'editReviewers'=>array(
                                        'label'=>'Remove project from review stage to add or remove reviewers.',
                                        'url'=>'Yii::app()->controller->createUrl("project/makeReviewable",array("id"=>$data->id))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_editReviewers.png',
                                        'options'=>array('class'=>'gridButtonClick','height'=>'25','width'=>'25'),
                                ),
                                'viewProject'=>array(
                                        'label'=>'View project page.',
                                        'url'=>'Yii::app()->controller->createUrl("project/view",array("id"=>$data->id))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                                        'options'=>array('height'=>'25','width'=>'25'),
                                ),
                        ),
                ),
        ),
));
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/all.js'); 
echo CHtml::script("
$('.gridButtonClick').click(function() {
    gridButtonClick($(this).attr('href'),'inReview-grid');
    return false;
});");
?>