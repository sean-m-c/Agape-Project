<?php
$this->breadcrumbs=array(
	'Events'=>array('index'),
	$model->name=>array('view','id'=>$model->event_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Event', 'url'=>array('index')),
	array('label'=>'Create Event', 'url'=>array('create')),
	array('label'=>'View Event', 'url'=>array('view', 'id'=>$model->event_oid)),
	array('label'=>'Manage Event', 'url'=>array('admin')),
);
?>

<h1>Update Event <?php echo $model->event_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>