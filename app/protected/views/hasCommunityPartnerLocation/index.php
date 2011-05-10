<?php
$this->breadcrumbs=array(
	'Has Community Partner Locations',
);

$this->menu=array(
	array('label'=>'Create HasCommunityPartnerLocation', 'url'=>array('create')),
	array('label'=>'Manage HasCommunityPartnerLocation', 'url'=>array('admin')),
);
?>

<h1>Has Community Partner Locations</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
