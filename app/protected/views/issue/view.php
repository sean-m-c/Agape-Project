<?php
$this->breadcrumbs=array(
	'Issues'=>array('index'),
	$model->issue_oid,
);

$this->menu=array(
	array('label'=>'List Issue', 'url'=>array('index')),
	array('label'=>'Create Issue', 'url'=>array('create')),
	array('label'=>'Update Issue', 'url'=>array('update', 'id'=>$model->issue_oid)),
	array('label'=>'Delete Issue', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->issue_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Issue', 'url'=>array('admin')),
);
?>

<h1>View Issue #<?php echo $model->issue_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'issue_oid',
		'issue_type_fk',
		'parent_fk',
	),
)); ?>
