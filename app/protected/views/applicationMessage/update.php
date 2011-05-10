<?php
$this->breadcrumbs=array(
	'Application Messages'=>array('index'),
	$model->name=>array('view','id'=>$model->application_message_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List ApplicationMessage', 'url'=>array('index')),
	array('label'=>'Create ApplicationMessage', 'url'=>array('create')),
	array('label'=>'View ApplicationMessage', 'url'=>array('view', 'id'=>$model->application_message_oid)),
	array('label'=>'Manage ApplicationMessage', 'url'=>array('admin')),
);
?>

<h1>Update ApplicationMessage <?php echo $model->application_message_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>