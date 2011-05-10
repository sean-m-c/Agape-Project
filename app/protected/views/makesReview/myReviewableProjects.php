<?php
$this->breadcrumbs=array(
        'Reviewer'=>array('site/home','#'=>'reviewerPanel'),
        'My Reviewable Projects',
);

$this->menu=array(
        array('label'=>'List MakesReview', 'url'=>array('index')),
        array('label'=>'Create MakesReview', 'url'=>array('create')),
);
?>
<p>These are projects which you have been assigned to review. You may still review, make a decision recommendation,
or change your reviews and recommendation for these.</p>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('makes-review-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'makes-review-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
                array(
                  'name'=>'Project',
                  'value'=>'$data->project->project_name',
                ),
                array(
                    'name'=>'My Decision',
                    'value'=>'Generic::convertDecision($data->decision)',
                ),
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewProject}',
                        'buttons'=>array(
                                'viewProject'=>array(
                                        'label'=>'Review Project >>',
                                        'url'=>'Yii::app()->controller->createUrl("project/view",array("id"=>$data->project_fk))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_comment.png',
                                ),
                        ),
                ),
        ),
)); ?>
