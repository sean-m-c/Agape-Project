<?php
$this->breadcrumbs=array(
	'Application Messages',
);

$this->menu=array(
	array('label'=>'Create ApplicationMessage', 'url'=>array('create')),
	array('label'=>'Manage ApplicationMessage', 'url'=>array('admin')),
);
?>

<h1>Application Messages</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
