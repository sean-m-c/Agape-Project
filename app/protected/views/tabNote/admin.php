<?php
$this->breadcrumbs=array(
	'Tab Notes'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List TabNote', 'url'=>array('index')),
	array('label'=>'Create TabNote', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tab-note-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Tab Notes</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$tabNote,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'tab-note-grid',
	'dataProvider'=>$tabNote->search(),
	'filter'=>$tabNote,
	'columns'=>array(
		'tab_note_oid',
		'tab_fk',
		'project_fk',
		'tab_note',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
