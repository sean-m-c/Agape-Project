<?php
$this->breadcrumbs=array(
	'Emergency Contacts'=>array('index'),
	$model->emergency_contact_oid,
);

$this->menu=array(
	array('label'=>'List EmergencyContact', 'url'=>array('index')),
	array('label'=>'Create EmergencyContact', 'url'=>array('create')),
	array('label'=>'Update EmergencyContact', 'url'=>array('update', 'id'=>$model->emergency_contact_oid)),
	array('label'=>'Delete EmergencyContact', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->emergency_contact_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage EmergencyContact', 'url'=>array('admin')),
);
?>

<h1>View EmergencyContact #<?php echo $model->emergency_contact_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'emergency_contact_oid',
		'first_name',
		'middle_initial',
		'last_name',
		'phone',
		'project_fk',
	),
)); ?>
