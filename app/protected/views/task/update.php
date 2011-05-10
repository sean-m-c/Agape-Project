<?php
$this->breadcrumbs=array(
	'Tasks'=>array('index'),
	$model->task_oid=>array('view','id'=>$model->task_oid),
	'Update',
);

?>

<h3>Update "<?php echo (strlen($model->task_description)<50) ? $model->task_description : substr($model->task_description,0,50).'...'; ?>"</h3>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>