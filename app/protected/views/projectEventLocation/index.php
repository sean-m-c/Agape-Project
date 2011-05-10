<?php
$this->breadcrumbs=array(
	'Project Event Locations',
);

$this->menu=array(
	array('label'=>'Create ProjectEventLocation', 'url'=>array('create')),
	array('label'=>'Manage ProjectEventLocation', 'url'=>array('admin')),
);
?>

<h1>Project Event Locations</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
