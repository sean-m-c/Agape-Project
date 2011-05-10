<?php
$this->breadcrumbs=array(
	'Has Methods'=>array('index'),
	$model->has_method_oid,
);

$this->menu=array(
	array('label'=>'List HasMethod', 'url'=>array('index')),
	array('label'=>'Create HasMethod', 'url'=>array('create')),
	array('label'=>'Update HasMethod', 'url'=>array('update', 'id'=>$model->has_method_oid)),
	array('label'=>'Delete HasMethod', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->has_method_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage HasMethod', 'url'=>array('admin')),
);
?>

<h1>View HasMethod #<?php echo $model->has_method_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'has_method_oid',
		'strategy_fk',
		'method_fk',
	),
)); ?>
