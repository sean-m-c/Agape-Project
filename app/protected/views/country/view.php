<?php
$this->breadcrumbs=array(
	'Countries'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Country', 'url'=>array('index')),
	array('label'=>'Create Country', 'url'=>array('create')),
	array('label'=>'Update Country', 'url'=>array('update', 'id'=>$model->country_oid)),
	array('label'=>'Delete Country', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->country_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Country', 'url'=>array('admin')),
);
?>

<h1>View Country #<?php echo $model->country_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'country_oid',
		'code',
		'name',
	),
)); ?>
