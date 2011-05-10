<?php
$this->breadcrumbs=array(
	'Has Methods'=>array('index'),
	$model->has_method_oid=>array('view','id'=>$model->has_method_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List HasMethod', 'url'=>array('index')),
	array('label'=>'Create HasMethod', 'url'=>array('create')),
	array('label'=>'View HasMethod', 'url'=>array('view', 'id'=>$model->has_method_oid)),
	array('label'=>'Manage HasMethod', 'url'=>array('admin')),
);
?>

<h1>Update HasMethod <?php echo $model->has_method_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>