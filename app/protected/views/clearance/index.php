<?php
$this->breadcrumbs=array(
	'Clearances',
);

$this->menu=array(
	array('label'=>'Create Clearance', 'url'=>array('create')),
	array('label'=>'Manage Clearance', 'url'=>array('admin')),
);
?>

<h1>Clearances</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
