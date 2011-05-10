<?php
$this->breadcrumbs=array(
	'Has Methods'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List HasMethod', 'url'=>array('index')),
	array('label'=>'Manage HasMethod', 'url'=>array('admin')),
);
?>

<h1>Create HasMethod</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>