<?php
$this->breadcrumbs=array(
	'Tabs'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Tab', 'url'=>array('index')),
	array('label'=>'Create Tab', 'url'=>array('create')),
	array('label'=>'Update Tab', 'url'=>array('update', 'id'=>$model->tab_oid)),
	array('label'=>'Delete Tab', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->tab_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Tab', 'url'=>array('admin')),
);
?>

<h1>View Tab #<?php echo $model->tab_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'tab_oid',
		'name',
		'enabled',
	),
)); ?>
