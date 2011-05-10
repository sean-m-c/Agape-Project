<?php
$this->breadcrumbs=array(
	'Issue Types'=>array('index'),
	$model->issue_type_oid,
);

$this->menu=array(
	array('label'=>'List IssueType', 'url'=>array('index')),
	array('label'=>'Create IssueType', 'url'=>array('create')),
	array('label'=>'Update IssueType', 'url'=>array('update', 'id'=>$model->issue_type_oid)),
	array('label'=>'Delete IssueType', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->issue_type_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage IssueType', 'url'=>array('admin')),
);
?>

<h1>View IssueType #<?php echo $model->issue_type_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'issue_type_oid',
		'reviewer_fk',
		'type',
		'description',
	),
)); ?>
