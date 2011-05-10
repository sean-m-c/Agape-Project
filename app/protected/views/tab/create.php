<?php
$this->breadcrumbs=array(
	'Tabs'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Tab', 'url'=>array('index')),
	array('label'=>'Manage Tab', 'url'=>array('admin')),
);
?>

<h1>Create Tab</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>