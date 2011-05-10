<?php
$this->breadcrumbs=array(
	'Strategys',
);

$this->menu=array(
	array('label'=>'Create strategy', 'url'=>array('create')),
	array('label'=>'Manage strategy', 'url'=>array('admin')),
);
?>

<h1>Strategys</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
