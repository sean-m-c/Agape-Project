<?php
$this->breadcrumbs=array(
	'Clearances'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Clearance', 'url'=>array('index')),
	array('label'=>'Manage Clearance', 'url'=>array('admin')),
);
?>

<h1>Create Clearance</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>