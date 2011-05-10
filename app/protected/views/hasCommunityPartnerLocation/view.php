<?php
$this->breadcrumbs=array(
	'Has Community Partner Locations'=>array('index'),
	$model->has_community_partner_location_oid,
);

$this->menu=array(
	array('label'=>'List HasCommunityPartnerLocation', 'url'=>array('index')),
	array('label'=>'Create HasCommunityPartnerLocation', 'url'=>array('create')),
	array('label'=>'Update HasCommunityPartnerLocation', 'url'=>array('update', 'id'=>$model->has_community_partner_location_oid)),
	array('label'=>'Delete HasCommunityPartnerLocation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->has_community_partner_location_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage HasCommunityPartnerLocation', 'url'=>array('admin')),
);
?>

<h1>View HasCommunityPartnerLocation #<?php echo $model->has_community_partner_location_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'has_community_partner_location_oid',
		'community_partner_fk',
		'location_fk',
	),
)); ?>
