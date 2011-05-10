<?php
$this->breadcrumbs=array(
	'Locations'=>array('index'),
	$model->location_oid,
);

$this->menu=array(
	array('label'=>'List Location', 'url'=>array('index')),
	array('label'=>'Create Location', 'url'=>array('create')),
	array('label'=>'Update Location', 'url'=>array('update', 'id'=>$model->location_oid)),
	array('label'=>'Delete Location', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->location_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Location', 'url'=>array('admin')),
);
?>

<h1>View Location #<?php echo $model->location_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'location_oid',
		'address_line_1',
		'address_line_2',
		'city',
		'state',
		'zip',
		'country',
		'project_fk',
	),
)); ?>


