<?php
$this->breadcrumbs=array(
	'Tab Notes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List TabNote', 'url'=>array('index')),
	array('label'=>'Manage TabNote', 'url'=>array('admin')),
);
?>

<h1>Create TabNote</h1>

<?php echo $this->renderPartial('_form', array('model'=>$tabNote)); ?>