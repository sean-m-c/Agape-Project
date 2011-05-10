<?php
$this->breadcrumbs=array(
        'Makes Reviews'=>array('index'),
        'Manage',
);

$this->menu=array(
        array('label'=>'List MakesReview', 'url'=>array('index')),
        array('label'=>'Create MakesReview', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('makes-review-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<?php
$data = new CActiveDataProvider('MakesReview',array(
                'criteria'=>array(
                        'condition'=>'project_fk='.$projectOID
                ),
));

$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'makes-review-grid',
        'dataProvider'=>$data,
        'columns'=>array(
                'user.first_name',
                'user.middle_initial',
                'user.last_name',
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewUser} {delete}',
                        'deleteButtonLabel'=>'Delete this project.',
                        'deleteConfirmation'=>'Are you sure you want to remove this reviewer from the project?',
                        'deleteButtonOptions'=>array('height'=>'25','width'=>'25'),
                        'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                        'deleteButtonUrl'=>'Yii::app()->controller->createUrl("makesReview/delete",array("id"=>$data->user_fk))',
                        'buttons'=>array(
                                'viewUser'=>array(
                                        'label'=>'View User >>',
                                        'url'=>'Yii::app()->controller->createUrl("user/view",array("id"=>$data->user_fk))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                                ),
                        ),
                ),
        ),
)); ?>
