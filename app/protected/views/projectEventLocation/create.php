<?php
$this->breadcrumbs=array(
	'Project Event Locations'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ProjectEventLocation', 'url'=>array('index')),
	array('label'=>'Manage ProjectEventLocation', 'url'=>array('admin')),
);
?>

<h1>Create ProjectEventLocation</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>