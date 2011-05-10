<?php
$this->breadcrumbs=array(
        'Reviewer'=>array('index'),
        'My Reviewed Projects',
);

$this->menu=array(
        array('label'=>'List MakesReview', 'url'=>array('index')),
        array('label'=>'Create MakesReview', 'url'=>array('create')),
);
?>
<p>These are projects that you have made a review decision on, but can
    still change your reviews or decision for.</p>
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
                'project.project_name',
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewProject}',
                        'buttons'=>array(
                                'viewProject'=>array(
                                        'label'=>'Review Project >>',
                                        'url'=>'Yii::app()->controller->createUrl("user/view",array("id"=>$data->project_fk))',
                                        'imageUrl'=>Yii::app()->request->baseUrl.'/images/i_comment.png',
                                ),
                        ),
                ),
        ),
)); ?>
