<?php
$this->breadcrumbs=array(
	'Tab Notes'=>array('index'),
	$tabNote->tab_note_oid,
);

$this->menu=array(
	array('label'=>'List TabNote', 'url'=>array('index')),
	array('label'=>'Create TabNote', 'url'=>array('create')),
	array('label'=>'Update TabNote', 'url'=>array('update', 'id'=>$tabNote->tab_note_oid)),
	array('label'=>'Delete TabNote', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$tabNote->tab_note_oid),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage TabNote', 'url'=>array('admin')),
);
?>

<h1>View TabNote #<?php echo $tabNote->tab_note_oid; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$tabNote,
	'attributes'=>array(
		'tab_note_oid',
		'tab_fk',
		'project_fk',
		'tab_note',
	),
)); ?>
