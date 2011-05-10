<?php
$this->breadcrumbs=array(
	'Need Clearances'=>array('index'),
	$model->needs_clearance_oid=>array('view','id'=>$model->needs_clearance_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List NeedClearance', 'url'=>array('index')),
	array('label'=>'Create NeedClearance', 'url'=>array('create')),
	array('label'=>'View NeedClearance', 'url'=>array('view', 'id'=>$model->needs_clearance_oid)),
	array('label'=>'Manage NeedClearance', 'url'=>array('admin')),
);
?>

<h1>Update NeedClearance <?php echo $model->needs_clearance_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>