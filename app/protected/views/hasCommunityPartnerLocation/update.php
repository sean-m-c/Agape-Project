<?php
$this->breadcrumbs=array(
	'Has Community Partner Locations'=>array('index'),
	$model->has_community_partner_location_oid=>array('view','id'=>$model->has_community_partner_location_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List HasCommunityPartnerLocation', 'url'=>array('index')),
	array('label'=>'Create HasCommunityPartnerLocation', 'url'=>array('create')),
	array('label'=>'View HasCommunityPartnerLocation', 'url'=>array('view', 'id'=>$model->has_community_partner_location_oid)),
	array('label'=>'Manage HasCommunityPartnerLocation', 'url'=>array('admin')),
);
?>

<h1>Update HasCommunityPartnerLocation <?php echo $model->has_community_partner_location_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>