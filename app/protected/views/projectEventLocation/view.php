<?php
$this->breadcrumbs=array(
	'Project Event Locations'=>array('index'),
	$model->project_event_location_oid,
);

$this->menu=array(
	array('label'=>'List ProjectEventLocation', 'url'=>array('index')),
	array('label'=>'Create ProjectEventLocation', 'url'=>array('create')),
	array('label'=>'Update ProjectEventLocation', 'url'=>array('update', 'id'=>$model->project_event_location_oid)),
	array('label'=>'Delete ProjectEventLocation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->project_event_location_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ProjectEventLocation', 'url'=>array('admin')),
);
?>

<h1>View ProjectEventLocation #<?php echo $model->project_event_location_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'project_event_location_oid',
		'project_fk',
		'event_fk',
		'location_fk',
	),
)); ?>
