<?php
$this->breadcrumbs=array(
	'Emergency Contacts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List EmergencyContact', 'url'=>array('index')),
	array('label'=>'Manage EmergencyContact', 'url'=>array('admin')),
);
?>

<h1>Create EmergencyContact</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>