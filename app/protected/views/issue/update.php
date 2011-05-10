<?php
$this->breadcrumbs=array(
	'Issues'=>array('index'),
	$model->issue_oid=>array('view','id'=>$model->issue_oid),
	'Update',
);
?>

<h3>Update "<?php echo $model->issueType->type; ?>"</h3>

<?php echo $this->renderPartial('_form', array('model'=>$model,'projectOID'=>$_GET['id'])); ?>