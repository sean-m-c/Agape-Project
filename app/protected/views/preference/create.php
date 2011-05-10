<?php
$this->breadcrumbs=array(
	'Preferences'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List preference', 'url'=>array('index')),
	array('label'=>'Manage preference', 'url'=>array('admin')),
);
?>

<h1>Create preference</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>