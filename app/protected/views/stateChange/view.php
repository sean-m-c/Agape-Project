<?php
$this->breadcrumbs=array(
	'State Changes'=>array('index'),
	$model->state_change_oid,
);

$this->menu=array(
	array('label'=>'List StateChange', 'url'=>array('index')),
	array('label'=>'Create StateChange', 'url'=>array('create')),
	array('label'=>'Update StateChange', 'url'=>array('update', 'id'=>$model->state_change_oid)),
	array('label'=>'Delete StateChange', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->state_change_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage StateChange', 'url'=>array('admin')),
);
?>

<h1>View StateChange #<?php echo $model->state_change_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'state_change_oid',
		'state',
		'time',
		'project_fk',
	),
)); ?>
