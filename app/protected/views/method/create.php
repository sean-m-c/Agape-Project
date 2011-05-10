<?php
$this->breadcrumbs=array(
	'Methods'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Method', 'url'=>array('index')),
	array('label'=>'Manage Method', 'url'=>array('admin')),
);
?>

<h1>Create Method</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>