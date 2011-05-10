<?php
$this->breadcrumbs=array(
	'Emergency Contacts',
);

$this->menu=array(
	array('label'=>'Create EmergencyContact', 'url'=>array('create')),
	array('label'=>'Manage EmergencyContact', 'url'=>array('admin')),
);
?>

<h1>Emergency Contacts</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
