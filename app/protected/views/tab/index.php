<?php
$this->breadcrumbs=array(
	'Tabs',
);

$this->menu=array(
	array('label'=>'Create Tab', 'url'=>array('create')),
	array('label'=>'Manage Tab', 'url'=>array('admin')),
);
?>

<h1>Tabs</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
