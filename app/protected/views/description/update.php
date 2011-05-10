<?php
$this->breadcrumbs=array(
	'Descriptions'=>array('index'),
	$model->description_oid=>array('view','id'=>$model->description_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Description', 'url'=>array('index')),
	array('label'=>'Create Description', 'url'=>array('create')),
	array('label'=>'View Description', 'url'=>array('view', 'id'=>$model->description_oid)),
	array('label'=>'Manage Description', 'url'=>array('admin')),
);
?>

<h1>Update Description <?php echo $model->description_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>