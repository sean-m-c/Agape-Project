<?php
$this->breadcrumbs=array(
        'Project'=>array('project/update','id'=>$projectOID),
        'Issues'=>array('issue/main','projectOID'=>$projectOID, 'issueOID'=>$issueOID),
	'Goals'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Goal', 'url'=>array('index')),
	array('label'=>'Create Goal', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('goal-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Goals</h1>


<?php $data = new CActiveDataProvider('Goal',array(
			'criteria'=>array(
                            'condition'=>'parent_fk='.$issueOID,
                            ),
		)); ?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'goal-grid',
	'dataProvider'=>$data,
	'filter'=>$model,
	'columns'=>array(
		//'goal_oid',
		//'parent_fk',
		'goal_description',
		array(
			'class'=>'CButtonColumn',
                        'template'=>'{update} {delete} {strategy}',
                        'buttons'=>array(
                            'strategy'=>array(
                                'label'=>'Strategies >>',
                                'url'=>'Yii::app()->controller->createUrl("strategy/admin",array("goalOID"=>$data->goal_oid))',
                             ),
                        ),
		),
	),
)); ?>
