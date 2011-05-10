<?php
$this->breadcrumbs=array(
	'Involveds'=>array('index'),
	$model->involved_oid=>array('view','id'=>$model->involved_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Involved', 'url'=>array('index')),
	array('label'=>'Create Involved', 'url'=>array('create')),
	array('label'=>'View Involved', 'url'=>array('view', 'id'=>$model->involved_oid)),
	array('label'=>'Manage Involved', 'url'=>array('admin')),
);
?>

<h1>Update Involved <?php echo $model->involved_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>