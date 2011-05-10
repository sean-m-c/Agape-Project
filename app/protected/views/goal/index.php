<?php
$this->breadcrumbs=array(
	'Goals',
);

$this->menu=array(
	array('label'=>'Create Goal', 'url'=>array('create')),
	array('label'=>'Manage Goal', 'url'=>array('admin')),
);
?>

<h1>Goals</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
