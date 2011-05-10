<?php
$this->breadcrumbs=array(
	'Community Partners'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List CommunityPartner', 'url'=>array('admin')),
	array('label'=>'Create CommunityPartner', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('community-partner-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Community Partners</h1>

<p style="margin-bottom:1em;">
<?php
/*
echo CHtml::link('Create Community Partner',array('communityPartner/create'),array('
            style'=>"cursor:pointer;", 'class'=>'i_checkmark buttonLink'));*/    ?>

</p>
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

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'community-partner-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'community_partner_oid',
		'agency_name',
		'pending:boolean:Approved',
		array(
			'class'=>'CButtonColumn',
                        'deleteButtonLabel'=>'Delete this community partner.',
                            'deleteConfirmation'=>'Are you sure you want to delete this community partner from the application?',
                            'deleteButtonOptions'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                            'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                            'viewButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                            'viewButtonOptions'=>array('height'=>'25','width'=>'25'),
                            'viewButtonLabel'=>'View community partner\'s profile.',
                            'updateButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_edit.png',
                            'updateButtonOptions'=>array('height'=>'25','width'=>'25'),
                            'updateButtonLabel'=>'Edit community partner\'s profile.',
                            'template'=>'{view} {update} {delete}',
                           'htmlOptions'=>array('width'=>'90'),
		),
	),
)); ?>
