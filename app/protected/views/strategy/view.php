<?php
$this->breadcrumbs=array(
	'Strategys'=>array('index'),
	$model->strategy_oid,
);

$this->menu=array(
	array('label'=>'List strategy', 'url'=>array('index')),
	array('label'=>'Create strategy', 'url'=>array('create')),
	array('label'=>'Update strategy', 'url'=>array('update', 'id'=>$model->strategy_oid)),
	array('label'=>'Delete strategy', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->strategy_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage strategy', 'url'=>array('admin')),
);
?>

<h1>View strategy #<?php echo $model->strategy_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'strategy_oid',
		'parent_fk',
		'strategy_description',
	),
)); ?>
