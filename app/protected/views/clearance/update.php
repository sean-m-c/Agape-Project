<?php
$this->breadcrumbs=array(
	'Clearances'=>array('index'),
	$model->name=>array('view','id'=>$model->clearance_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Clearance', 'url'=>array('index')),
	array('label'=>'Create Clearance', 'url'=>array('create')),
	array('label'=>'View Clearance', 'url'=>array('view', 'id'=>$model->clearance_oid)),
	array('label'=>'Manage Clearance', 'url'=>array('admin')),
);
?>

<h1>Update Clearance <?php echo $model->name; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>