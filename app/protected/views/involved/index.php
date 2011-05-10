<?php
$this->breadcrumbs=array(
	'Involveds',
);

$this->menu=array(
	array('label'=>'Create Involved', 'url'=>array('create')),
	array('label'=>'Manage Involved', 'url'=>array('admin')),
);
?>

<h1>Involveds</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
