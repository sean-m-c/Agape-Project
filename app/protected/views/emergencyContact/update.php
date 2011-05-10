<?php
$this->breadcrumbs=array(
	'Emergency Contacts'=>array('index'),
	$emergencyContact->emergency_contact_oid=>array('view','id'=>$emergencyContact->emergency_contact_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List EmergencyContact', 'url'=>array('index')),
	array('label'=>'Create EmergencyContact', 'url'=>array('create')),
	array('label'=>'View EmergencyContact', 'url'=>array('view', 'id'=>$emergencyContact->emergency_contact_oid)),
	array('label'=>'Manage EmergencyContact', 'url'=>array('admin')),
);
?>

<h1>Update Emergency Contact <?php echo $emergencyContact->fullName; ?></h1>

<?php echo CHtml::link('Back to Project',$returnUrl); ?>

<?php echo $this->renderPartial('_form', array('emergencyContact'=>$emergencyContact)); ?>