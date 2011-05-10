<?php
$this->breadcrumbs=array(
	'Makes Reviews'=>array('index'),
	$model->review_oid=>array('view','id'=>$model->review_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List MakesReview', 'url'=>array('index')),
	array('label'=>'Create MakesReview', 'url'=>array('create')),
	array('label'=>'View MakesReview', 'url'=>array('view', 'id'=>$model->review_oid)),
	array('label'=>'Manage MakesReview', 'url'=>array('admin')),
);
?>

<h1>Update MakesReview <?php echo $model->review_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>