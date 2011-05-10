<?php
$this->breadcrumbs=array(
	'Tabs'=>array('index'),
	$model->name=>array('view','id'=>$model->tab_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List Tab', 'url'=>array('index')),
	array('label'=>'Create Tab', 'url'=>array('create')),
	array('label'=>'View Tab', 'url'=>array('view', 'id'=>$model->tab_oid)),
	array('label'=>'Manage Tab', 'url'=>array('admin')),
);
?>

<h1>Update Tab <?php echo $model->tab_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>