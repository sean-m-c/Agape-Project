<?php
$this->breadcrumbs=array(
	'Strategys'=>array('index'),
	$model->strategy_oid=>array('view','id'=>$model->strategy_oid),
	'Update',
);

?>

<h3>Update "<?php echo (strlen($model->strategy_description)<50) ? $model->strategy_description : substr($model->strategy_description,0,50).'...'; ?>"</h3>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>