<?php
$this->breadcrumbs=array(
	'Descriptions'=>array('index'),
	$model->description_oid,
);

$this->menu=array(
	array('label'=>'List Description', 'url'=>array('index')),
	array('label'=>'Create Description', 'url'=>array('create')),
	array('label'=>'Update Description', 'url'=>array('update', 'id'=>$model->description_oid)),
	array('label'=>'Delete Description', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->description_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Description', 'url'=>array('admin')),
);
?>

<h1>View Description #<?php echo $model->description_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'description_oid',
		'field_name',
		'table_name',
		'text',
	),
)); ?>
