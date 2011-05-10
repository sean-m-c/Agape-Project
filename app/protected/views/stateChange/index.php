<?php
$this->breadcrumbs=array(
	'State Changes',
);

$this->menu=array(
	array('label'=>'Create StateChange', 'url'=>array('create')),
	array('label'=>'Manage StateChange', 'url'=>array('admin')),
);
?>

<h1>State Changes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
