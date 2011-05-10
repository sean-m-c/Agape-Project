<?php
$this->breadcrumbs=array(
        'My Community Partners',
);?>

<?php
function decisionFlag($flag,$cpadmin=null) {
    if(!empty($cpadmin)) {
        return 'Community Partner Admin';
    } else {
        switch ($flag) {
            case '0':
                return 'Approved member';
                break;
            case '1':
                return 'Waiting for partner\'s approval';
                break;
        }
    }
}
?>

<div class="buttonRow">
<?php echo CHtml::link('Connect to a community partner',array(''),
            array('class'=>'i_add buttonLink noLoader','id'=>'connectPartnerLink',
                'onclick'=>'$("#connectPartnerDialog").dialog("open"); $("this").toggleClass("loading"); return false;')); ?>

<?php echo CHtml::link('Create new community partner',array(''),
        array('class'=>'i_add buttonLink noLoader','id'=>'createPartnerLink',
                'onclick'=>'$("#createPartnerDialog").dialog("open"); $("this").toggleClass("loading"); return false;')); ?>
</div>

<?php if(Yii::app()->user->hasFlash('needConnect')):?>
    <div class="flash flash-notice">
        <?php echo Yii::app()->user->getFlash('needConnect'); ?>
        <?php echo CHtml::link("Close",'',array('class'=>'closeFlash')); ?>
    </div>
<?php endif; ?>

<p>These are community partners you have affiliated yourself with.</p>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'myPartners-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                    'communityPartner.agency_name',
                    array(
                        'name'=>'Date Applied',
                        'value'=>'Generic::convertDate($data->date_applied)',
                    ),
                    array(
                        'name'=>'Status',
                        'value'=>'decisionFlag($data->pending,$data->is_cpadmin)',
                    ),
                    array(
                            'class'=>'CButtonColumn',                   
                            'deleteButtonLabel'=>'Remove myself from this community partner.',
                            'deleteConfirmation'=>'Are you sure you want to remove yourself from this community partner?',
                            'deleteButtonOptions'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                            'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                            'viewButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                            'viewButtonOptions'=>array('height'=>'25','width'=>'25'),
                            'viewButtonLabel'=>'View community partner\'s profile.',
                            'viewButtonUrl'=>'Yii::app()->controller->createUrl("communityPartner/view",array("id"=>$data->community_partner_fk))',
                            'buttons'=>array(
                                'updatePartner'=>array(
                                            'label'=>'Edit community partner\'s profile.',
                                            'url'=>'Yii::app()->controller->createUrl("communityPartner/update",array("id"=>$data->community_partner_fk))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_edit.png',
                                            'options'=>array('height'=>'25','width'=>'25'),
                                            'visible'=>'$data->is_cpadmin==1',
                                    ),
                            ),
                            'template'=>'{view} {delete} {updatePartner}',
                            'htmlOptions'=>array('width'=>'90'),
                    ),
            ),
    ));

?>

<div id="dialogContainer" style="display:none;">
<?php
// Create new connection to partner dialog box
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
                'id'=>'connectPartnerDialog',
                'options'=>array(
                    'title'=>'Connect to Community Partner',
                    'autoOpen'=>false,
                    'modal'=>'true',
                    'width'=>'auto',
                    'height'=>'auto',
                    'show'=>'fade',
                    'hide'=>'fade',
                ),
));

$this->renderPartial('/involved/_form', array('model'=>new Involved),false,true);

$this->endWidget('zii.widgets.jui.CJuiDialog');

// Create new community partner
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
                'id'=>'createPartnerDialog',
                'options'=>array(
                    'title'=>'Create Community Partner',
                    'autoOpen'=>false,
                    'modal'=>'true',
                    'width'=>'auto',
                    'height'=>'auto',
                    'show'=>'fade',
                    'hide'=>'fade',
                ),
));

$this->renderPartial('/communityPartner/_form', array('model'=>new CommunityPartner)); ?>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
</div>
