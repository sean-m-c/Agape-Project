<?php
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	$model->email=>array('view','id'=>$model->user_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->user_oid)),
	array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->email; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>