<?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'pendingCommunityPartnerVolunteers-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
                'user.first_name',
                'user.middle_initial',
                'user.last_name',
                'communityPartner.agency_name',
                'date_applied',
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewUser} {approve}',
                        'buttons'=>array(
                                'approve'=>array(
                                        'label'=>'Approve User',
                                        'url'=>'Yii::app()->controller->createUrl("involved/approveUser",array("id"=>$data->involved_oid))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_checkmark.png',
                                        'options'=>array('class'=>'click showTooltip','height'=>'25','width'=>'25'),
                                ),
                                'viewUser'=>array(
                                        'label'=>'View User',
                                        'url'=>'Yii::app()->controller->createUrl("user/view",array("id"=>$data->user_fk))',
                                        'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                                        'options'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                                ),
                        ),
                ),
        ),
));
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/all.js');
echo CHtml::script('
$(document).ready(function() {
    $(".click").live("click",function() {
        gridButtonClick($(this).attr("href"),"pendingCommunityPartnerVolunteers-grid");
        return false;
    });
});
');
?>