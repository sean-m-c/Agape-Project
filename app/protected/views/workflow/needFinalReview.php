<?php
$this->breadcrumbs=array(
        'Workflow'=>array('/workflow'),
        'NeedFinalReview',
);?>

<p>These projects have a review decision from all assigned reviewers, or were not assigned reviewers.<p>

    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'needFinalReview-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                    'project_name',
                    array(
                            'name'=>'Description',
                            'value'=>'(!empty($data->project_description)) ? substr($data->project_description,0,200)."..." : "None set"',
                    ),
                    array(
                            'class'=>'CButtonColumn',
                            'template'=>'{makeReview}',
                            'buttons'=>array(
                                    'makeReview'=>array(
                                            'label'=>'Make final review for this project.',
                                            'url'=>'Yii::app()->controller->createUrl("project/view",array("id"=>$data->id,"isFinal"=>true))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_comment.png',
                                            'options'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                                            'click'=>'js: function() {
                                                window:location = $(this).attr("href);
                                                return false;
                                             }'
                                    ),
                            ),
                    ),
            ),
    ));
echo CHtml::scriptFile(Yii::app()->theme->baseUrl.'/js/jquery.tools.min.js');
echo CHtml::scriptFile(Yii::app()->theme->baseUrl.'/js/gridViewTooltip.js');
?>