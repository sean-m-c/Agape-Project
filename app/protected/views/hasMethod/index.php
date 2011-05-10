<?php
$this->breadcrumbs=array(
	'Has Methods',
);

$this->menu=array(
	array('label'=>'Create HasMethod', 'url'=>array('create')),
	array('label'=>'Manage HasMethod', 'url'=>array('admin')),
);
?>

<h1>Has Methods</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
