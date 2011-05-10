<?php
$this->breadcrumbs=array(
	'State Changes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List StateChange', 'url'=>array('index')),
	array('label'=>'Manage StateChange', 'url'=>array('admin')),
);
?>

<h1>Create StateChange</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>