<?php
$this->breadcrumbs=array(
	'Application Messages'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List ApplicationMessage', 'url'=>array('index')),
	array('label'=>'Create ApplicationMessage', 'url'=>array('create')),
	array('label'=>'Update ApplicationMessage', 'url'=>array('update', 'id'=>$model->application_message_oid)),
	array('label'=>'Delete ApplicationMessage', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->application_message_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ApplicationMessage', 'url'=>array('admin')),
);
?>

<h1>View ApplicationMessage #<?php echo $model->application_message_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'application_message_oid',
		'name',
		'text',
	),
)); ?>
