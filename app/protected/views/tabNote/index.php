<?php
$this->breadcrumbs=array(
	'Tab Notes',
);

$this->menu=array(
	array('label'=>'Create TabNote', 'url'=>array('create')),
	array('label'=>'Manage TabNote', 'url'=>array('admin')),
);
?>

<h1>Tab Notes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
