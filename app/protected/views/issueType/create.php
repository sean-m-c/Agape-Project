<?php
$this->breadcrumbs=array(
	'Issue Types'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List IssueType', 'url'=>array('index')),
	array('label'=>'Manage IssueType', 'url'=>array('admin')),
);
?>

<h1>Create IssueType</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>