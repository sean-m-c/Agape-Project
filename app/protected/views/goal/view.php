<?php
$this->breadcrumbs=array(
	'Goals'=>array('index'),
	$model->goal_oid,
);

$this->menu=array(
	array('label'=>'List Goal', 'url'=>array('index')),
	array('label'=>'Create Goal', 'url'=>array('create')),
	array('label'=>'Update Goal', 'url'=>array('update', 'id'=>$model->goal_oid)),
	array('label'=>'Delete Goal', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->goal_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Goal', 'url'=>array('admin')),
);
?>

<h1>View Goal #<?php echo $model->goal_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'goal_oid',
		'parent_fk',
		'goal_description',
	),
)); ?>
