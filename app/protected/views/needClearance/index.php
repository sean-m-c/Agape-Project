<?php
$this->breadcrumbs=array(
	'Need Clearances',
);

$this->menu=array(
	array('label'=>'Create NeedClearance', 'url'=>array('create')),
	array('label'=>'Manage NeedClearance', 'url'=>array('admin')),
);
?>

<h1>Need Clearances</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
