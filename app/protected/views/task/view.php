<?php
$this->breadcrumbs=array(
	'Tasks'=>array('index'),
	$model->task_oid,
);

$this->menu=array(
	array('label'=>'List Task', 'url'=>array('index')),
	array('label'=>'Create Task', 'url'=>array('create')),
	array('label'=>'Update Task', 'url'=>array('update', 'id'=>$model->task_oid)),
	array('label'=>'Delete Task', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->task_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Task', 'url'=>array('admin')),
);
?>

<h1>View Task #<?php echo $model->task_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'task_oid',
		'parent_fk',
		'task_description',
		'completed',
	),
)); ?>
