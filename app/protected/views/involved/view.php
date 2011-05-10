<?php
$this->breadcrumbs=array(
	'Involveds'=>array('index'),
	$model->involved_oid,
);

$this->menu=array(
	array('label'=>'List Involved', 'url'=>array('index')),
	array('label'=>'Create Involved', 'url'=>array('create')),
	array('label'=>'Update Involved', 'url'=>array('update', 'id'=>$model->involved_oid)),
	array('label'=>'Delete Involved', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->involved_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Involved', 'url'=>array('admin')),
);
?>

<h1>View Involved #<?php echo $model->involved_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'involved_oid',
		'user_fk',
		'community_partner_fk',
		'pending',
		'is_cpadmin',
	),
)); ?>
