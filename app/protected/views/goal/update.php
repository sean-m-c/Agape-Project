<?php
$this->breadcrumbs=array(
	'Goals'=>array('index'),
	$model->goal_oid=>array('view','id'=>$model->goal_oid),
	'Update',
);
?>

<h3>Edit "<?php echo (strlen($model->goal_description)<50) ? $model->goal_description : substr($model->goal_description,0,50).'...'; ?>"</h3>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>