<?php
$this->breadcrumbs=array(
	'Application Messages'=>array('index'),
	'Manage',
);
?>
<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('application-message-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Application Messages</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<div class="buttonRow">
<?php echo CHtml::link('Add new message',array('/applicationMessage/create'),array('class'=>'buttonLink i_add'));?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'application-message-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'application_message_oid',
		'name',
		'text',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
