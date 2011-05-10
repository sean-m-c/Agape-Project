<?php
$this->breadcrumbs=array(
	'State Changes'=>array('index'),
	$model->state_change_oid=>array('view','id'=>$model->state_change_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List StateChange', 'url'=>array('index')),
	array('label'=>'Create StateChange', 'url'=>array('create')),
	array('label'=>'View StateChange', 'url'=>array('view', 'id'=>$model->state_change_oid)),
	array('label'=>'Manage StateChange', 'url'=>array('admin')),
);
?>

<h1>Update StateChange <?php echo $model->state_change_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>