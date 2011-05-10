<?php
$this->breadcrumbs=array(
	'Project Event Locations'=>array('index'),
	$model->project_event_location_oid=>array('view','id'=>$model->project_event_location_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List ProjectEventLocation', 'url'=>array('index')),
	array('label'=>'Create ProjectEventLocation', 'url'=>array('create')),
	array('label'=>'View ProjectEventLocation', 'url'=>array('view', 'id'=>$model->project_event_location_oid)),
	array('label'=>'Manage ProjectEventLocation', 'url'=>array('admin')),
);
?>

<h1>Update ProjectEventLocation <?php echo $model->project_event_location_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>