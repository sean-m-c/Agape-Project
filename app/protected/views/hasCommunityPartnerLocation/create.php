<?php
$this->breadcrumbs=array(
	'Has Community Partner Locations'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List HasCommunityPartnerLocation', 'url'=>array('index')),
	array('label'=>'Manage HasCommunityPartnerLocation', 'url'=>array('admin')),
);
?>

<h1>Create HasCommunityPartnerLocation</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>