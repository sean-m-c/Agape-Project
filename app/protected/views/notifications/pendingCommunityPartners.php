    <?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'pendingCommunityPartners-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
        //'issue_oid',
                'agency_name',
                'date_registered',
                //'project_fk',
                array(
                        'class'=>'CButtonColumn',
                        'viewButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                        'viewButtonUrl'=>'Yii::app()->controller->createUrl("communityPartner/view",
                                            array("id"=>$data->community_partner_oid))',
                        'viewButtonLabel'=>'View Community Partner\'s profile.',
                        'viewButtonOptions'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                        'template'=>'{view} {approve}',
                        'buttons'=>array(
                                'approve'=>array(
                                        'label'=>'Approve Community Partner as application member.',
                                        'url'=>'Yii::app()->controller->createUrl("communityPartner/approvePartner",array("id"=>$data->community_partner_oid))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_checkmark.png',
                                            'options'=>array('class'=>'showTooltip click','height'=>'25','width'=>'25'),
                                ),
                        ),
                ),
        ),
));

echo CHtml::script('
$(document).ready(function() {
    $(".click").live("click",function() {
        gridButtonClick($(this).attr("href"),"pendingCommunityPartners-grid");
        return false;
    });
});
');
?>