<?php
$this->breadcrumbs=array(
	'Strategys'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List strategy', 'url'=>array('index')),
	array('label'=>'Manage strategy', 'url'=>array('admin')),
);
?>

<h1>Create strategy</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>