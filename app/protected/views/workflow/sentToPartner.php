<?php
$this->breadcrumbs=array(
        'Workflow'=>array('/workflow'),
        'SentToPartner',
);?>

<?php 
function decisionFlag($flag) {
    switch ($flag) {
        case '4':
            return 'Approved';
            break;
        case '5':
            return 'Needs revision';
            break;
        case '6':
            return 'Rejected';
            break;
    }
}
?>
<p>These projects have receieved their final review and decision, and have been sent back to the community partner.<p>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'sentToPartner-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                    'project_name',
                    'communityPartner.agency_name',
                    array(
                        'name'=>'finalDecision',
                        'value'=>'decisionFlag($data->status)',
                    ),
                    array(
                            'class'=>'CButtonColumn',
                            'template'=>'{viewProject}',
                            'buttons'=>array(
                                    'viewProject'=>array(
                                            'label'=>'View project page.',
                                            'url'=>'Yii::app()->controller->createUrl("project/view",array("id"=>$data->id))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
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