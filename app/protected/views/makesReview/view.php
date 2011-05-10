<?php
$this->breadcrumbs=array(
	'Makes Reviews'=>array('index'),
	$model->review_oid,
);

$this->menu=array(
	array('label'=>'List MakesReview', 'url'=>array('index')),
	array('label'=>'Create MakesReview', 'url'=>array('create')),
	array('label'=>'Update MakesReview', 'url'=>array('update', 'id'=>$model->review_oid)),
	array('label'=>'Delete MakesReview', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->review_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage MakesReview', 'url'=>array('admin')),
);
?>

<h1>View MakesReview #<?php echo $model->review_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'review_oid',
		'user_fk',
		'project_fk',
	),
)); ?>
