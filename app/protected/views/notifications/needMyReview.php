<?php
if(!empty($dataProvider)) {
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'needMyReview-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
            //'issue_oid',
                    'project.project_name',
                    'project.project_description',
                    array(
                            'class'=>'CButtonColumn',
                            'template'=>'{review}',
                            'buttons'=>array(
                                    'review'=>array(
                                            'label'=>'Review this project.',
                                            'url'=>'Yii::app()->controller->createUrl("/project/view",array("id"=>$data->project_fk))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_comment.png',
                                            'options'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                                    ),
                            ),
                    ),
            ),
    ));

}
echo CHtml::scriptFile(Yii::app()->theme->baseUrl.'/js/jquery.tools.min.js');
echo CHtml::scriptFile(Yii::app()->theme->baseUrl.'/js/gridViewTooltip.js');
?>