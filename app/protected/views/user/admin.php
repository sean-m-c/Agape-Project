<?php
$this->breadcrumbs=array(
	'Users'=>array('admin'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
);

if(Yii::app()->user->hasFlash('userCreateSuccess')): ?>
    <div class="flash flash-success">
        <?php
        echo Yii::app()->user->getFlash('userCreateSuccess'); 
        echo CHtml::link("Close",'',array('class'=>'closeFlash'));
        ?>
    </div>
<?php endif; 

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Users</h1>

<p style="margin-bottom:1em;">
<?php
echo CHtml::link('Create User',array('user/create'),array('
            style'=>"cursor:pointer;", 'class'=>'i_checkmark buttonLink')); ?>

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
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'user_oid',
                'organization_name',
		'fullName',
                'email',		
		array(
			'class'=>'CButtonColumn',
                        'deleteButtonLabel'=>'Delete this user.',
                            'deleteConfirmation'=>'Are you sure you want to delete this user from the system?',
                            'deleteButtonOptions'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                            'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                            'viewButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                            'viewButtonOptions'=>array('height'=>'25','width'=>'25'),
                            'viewButtonLabel'=>'View user\'s profile.',
                            'updateButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_edit.png',
                            'updateButtonOptions'=>array('height'=>'25','width'=>'25'),
                            'updateButtonLabel'=>'Edit user\'s profile.',
                            'template'=>'{view} {update} {delete}',
                           'htmlOptions'=>array('width'=>'90'),
		),
	),
)); ?>
