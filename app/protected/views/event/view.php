<?php
$this->breadcrumbs=array(
	'Events'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Event', 'url'=>array('index')),
	array('label'=>'Create Event', 'url'=>array('create')),
	array('label'=>'Update Event', 'url'=>array('update', 'id'=>$model->event_oid)),
	array('label'=>'Delete Event', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->event_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Event', 'url'=>array('admin')),
);
?>

<h1>View Event #<?php echo $model->event_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'event_oid',
		'start',
		'end',
		'name',
	),
)); ?>
