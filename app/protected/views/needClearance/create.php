<?php
$this->breadcrumbs=array(
	'Need Clearances'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List NeedClearance', 'url'=>array('index')),
	array('label'=>'Manage NeedClearance', 'url'=>array('admin')),
);
?>

<h1>Create NeedClearance</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>