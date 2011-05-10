<?php
$this->breadcrumbs=array(
	'Issue Types'=>array('index'),
	$model->issue_type_oid=>array('view','id'=>$model->issue_type_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List IssueType', 'url'=>array('index')),
	array('label'=>'Create IssueType', 'url'=>array('create')),
	array('label'=>'View IssueType', 'url'=>array('view', 'id'=>$model->issue_type_oid)),
	array('label'=>'Manage IssueType', 'url'=>array('admin')),
);
?>

<h1>Update IssueType <?php echo $model->issue_type_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>