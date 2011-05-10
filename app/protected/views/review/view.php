<?php
$this->breadcrumbs=array(
	'Reviews'=>array('index'),
	$model->review_oid,
);

$this->menu=array(
	array('label'=>'List Review', 'url'=>array('index')),
	array('label'=>'Create Review', 'url'=>array('create')),
	array('label'=>'Update Review', 'url'=>array('update', 'id'=>$model->review_oid)),
	array('label'=>'Delete Review', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->review_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Review', 'url'=>array('admin')),
);
?>

<h1>View Review #<?php echo $model->review_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'review_oid',
		'comment',
		'tab.name',
		'makes_review_fk',
	),
)); ?>
