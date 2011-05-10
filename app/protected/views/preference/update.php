<?php
$this->breadcrumbs=array(
	'Preferences'=>array('index'),
	$model->preference_oid=>array('view','id'=>$model->preference_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List preference', 'url'=>array('index')),
	array('label'=>'Create preference', 'url'=>array('create')),
	array('label'=>'View preference', 'url'=>array('view', 'id'=>$model->preference_oid)),
	array('label'=>'Manage preference', 'url'=>array('admin')),
);
?>

<h1>Update preference <?php echo $model->preference_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>