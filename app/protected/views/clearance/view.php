<?php
$this->breadcrumbs=array(
	'Clearances'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Clearance', 'url'=>array('index')),
	array('label'=>'Create Clearance', 'url'=>array('create')),
	array('label'=>'Update Clearance', 'url'=>array('update', 'id'=>$model->clearance_oid)),
	array('label'=>'Delete Clearance', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->clearance_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Clearance', 'url'=>array('admin')),
);
?>

<h1>View Clearance #<?php echo $model->name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		array(
                    'label'=>$model->getAttributeLabel('is_default'),
                    'type'=>'boolean',
                    'value'=>'is_default',
                ),
		'name',
		'url',
	),
)); ?>
