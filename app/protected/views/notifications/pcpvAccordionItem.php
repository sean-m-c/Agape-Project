<?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'pendingCommunityPartnerVolunteers-grid'.$i,
        'dataProvider'=>$dataProvider,
        'columns'=>array(
        //'issue_oid',
                'user.first_name',
                'user.middle_initial',
                'user.last_name',
                'date_applied',
                //'project_fk',
                array(
                        'class'=>'CButtonColumn',
                        'template'=>'{viewUser} {approve}',
                        'buttons'=>array(
                                'approve'=>array(
                                        'label'=>'Approve User',
                                        'url'=>'Yii::app()->controller->createUrl("involved/approveUser",array("id"=>$data->involved_oid))',
                                        'imageUrl'=>Yii::app()->request->baseUrl.'/images/i_checkmark.png',
                                        'click'=>'js: function(){
                                                     gridButtonClick($(this).attr("href"),"pendingCommunityPartnerVolunteers-grid'.$i.'");
                                                     return false;
                                                     }',
                                        'options'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                                ),
                                'viewUser'=>array(
                                        'label'=>'View User',
                                        'url'=>'Yii::app()->controller->createUrl("user/view",array("id"=>$data->user_fk))',
                                        'imageUrl'=>Yii::app()->request->baseUrl.'/images/i_glass.png',
                                        'options'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                                ),
                        ),
                ),
        ),
));
?>