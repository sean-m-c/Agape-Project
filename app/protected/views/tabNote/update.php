<?php
$this->breadcrumbs=array(
	'Tab Notes'=>array('index'),
	$tabNote->tab_note_oid=>array('view','id'=>$tabNote->tab_note_oid),
	'Update',
);

$this->menu=array(
	array('label'=>'List TabNote', 'url'=>array('index')),
	array('label'=>'Create TabNote', 'url'=>array('create')),
	array('label'=>'View TabNote', 'url'=>array('view', 'id'=>$tabNote->tab_note_oid)),
	array('label'=>'Manage TabNote', 'url'=>array('admin')),
);
?>

<h1>Update TabNote <?php echo $tabNote->tab_note_oid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$tabNote)); ?>