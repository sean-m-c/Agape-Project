<?php
$this->breadcrumbs=array(
	'Community Partners',
);

$this->menu=array(
	array('label'=>'Create CommunityPartner', 'url'=>array('create')),
	array('label'=>'Manage CommunityPartner', 'url'=>array('admin')),
);
?>

<h1>Community Partners</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
