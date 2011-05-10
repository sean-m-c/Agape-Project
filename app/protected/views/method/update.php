<?php
$this->breadcrumbs=array(
	'Methods'=>array('index'),
	$model->name=>array('view','id'=>$model->method_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Method', 'url'=>array('index')),
	array('label'=>'Create Method', 'url'=>array('create')),
	array('label'=>'View Method', 'url'=>array('view', 'id'=>$model->method_oid)),
	array('label'=>'Manage Method', 'url'=>array('admin')),
);
?>

<h1>Update Method <?php echo $model->method_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>