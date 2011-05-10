<?php
$this->breadcrumbs=array(
	'Application Messages'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ApplicationMessage', 'url'=>array('index')),
	array('label'=>'Manage ApplicationMessage', 'url'=>array('admin')),
);
?>

<h1>Create Application Message</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>