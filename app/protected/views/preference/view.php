<?php
$this->breadcrumbs=array(
	'Preferences'=>array('index'),
	$model->preference_oid,
);

$this->menu=array(
	array('label'=>'List preference', 'url'=>array('index')),
	array('label'=>'Create preference', 'url'=>array('create')),
	array('label'=>'Update preference', 'url'=>array('update', 'id'=>$model->preference_oid)),
	array('label'=>'Delete preference', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->preference_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage preference', 'url'=>array('admin')),
);
?>

<h1>View preference #<?php echo $model->preference_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'preference_oid',
		'first_name',
		'last_name',
		'email',
		'project_fk',
	),
)); ?>
