<?php
$this->breadcrumbs=array(
	'Issues'=>array('index'),
	'Create',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'form'=>$form,'projectOID'=>$projectOID)); ?>