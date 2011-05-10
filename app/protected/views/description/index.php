<?php
$this->breadcrumbs=array(
	'Descriptions',
);

$this->menu=array(
	array('label'=>'Create Description', 'url'=>array('create')),
	array('label'=>'Manage Description', 'url'=>array('admin')),
);
?>

<h1>Descriptions</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
