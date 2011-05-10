<?php
$this->breadcrumbs=array(
	'Issue Types',
);

$this->menu=array(
	array('label'=>'Create IssueType', 'url'=>array('create')),
	array('label'=>'Manage IssueType', 'url'=>array('admin')),
);
?>

<h1>Issue Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
