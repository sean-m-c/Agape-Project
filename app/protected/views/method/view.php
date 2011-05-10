<?php
$this->breadcrumbs=array(
	'Methods'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Method', 'url'=>array('index')),
	array('label'=>'Create Method', 'url'=>array('create')),
	array('label'=>'Update Method', 'url'=>array('update', 'id'=>$model->method_oid)),
	array('label'=>'Delete Method', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->method_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Method', 'url'=>array('admin')),
);
?>

<h1>View Method #<?php echo $model->method_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'method_oid',
		'type',
		'name',
	),
)); ?>
