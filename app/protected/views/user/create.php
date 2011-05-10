<?php
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	'Create',
);
?>

<h1>Register New User</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>