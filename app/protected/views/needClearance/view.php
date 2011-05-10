<?php
$this->breadcrumbs=array(
	'Need Clearances'=>array('index'),
	$model->needs_clearance_oid,
);

$this->menu=array(
	array('label'=>'List NeedClearance', 'url'=>array('index')),
	array('label'=>'Create NeedClearance', 'url'=>array('create')),
	array('label'=>'Update NeedClearance', 'url'=>array('update', 'id'=>$model->needs_clearance_oid)),
	array('label'=>'Delete NeedClearance', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->needs_clearance_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage NeedClearance', 'url'=>array('admin')),
);
?>

<h1>View NeedClearance #<?php echo $model->needs_clearance_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'needs_clearance_oid',
		'project_fk',
		'clearance_fk',
	),
)); ?>
