
<div class="form">
    <div class="row">
        <?php echo CHtml::link('Send to Reviewers',
                '',array(
                'onclick'=>CHtml::ajax(array
                (
                        'url'=>array('/project/reviewersAssigned','id'=>$projectOID),
                        'update'=>'#needReviews',
                        'type'=>'GET',

                )),
                'style'=>"cursor:pointer;",
                'class'=>'i_checkmark icon_pad'
        )
); ?>
    </div>
</div>

<?php
$this->renderPartial('/makesReview/create',array('model'=>$model,'projectOID'=>$projectOID));
$this->renderPartial('/makesReview/admin',array('model'=>$model,'projectOID'=>$projectOID));
?>
