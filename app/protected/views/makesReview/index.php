<?php
$this->breadcrumbs=array(
	'Makes Reviews',
);

$this->menu=array(
	array('label'=>'Create MakesReview', 'url'=>array('create')),
	array('label'=>'Manage MakesReview', 'url'=>array('admin')),
);
?>

<h1>Makes Reviews</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
