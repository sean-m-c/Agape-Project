<div id="ajaxTab">
<?php
$id = Generic::reviewThisProject();
$showReview = false;
if(
(Yii::app()->user->checkAccess('adminhead') ||
Yii::app()->user->checkAccess('aidadmin') ||
(isset($id) && $id)) &&
$model->user_fk != Yii::app()->user->id &&
Review::model()->with('makesReview')->count(array(
    'condition'=>'makesReview.project_fk=:projectfk AND t.tab_fk=:tab_fk',
    'params'=>array(':tab_fk'=>$tab_fk,':projectfk'=>$model->id)))!='0') {
  
    $showReview = true;
}

if((isset($id) && $id) || Yii::app()->user->checkAccess('adminhead')) : ?>
    <div class="reviewButtons">
    <?php
    if(!$isFinal) {
        // Link to open comment dialog box
        echo CHtml::link('Review this tab','#',array('style'=>'margin-right:1em','class'=>'review comment buttonLink',
            'onclick'=>'
                if('.$model->status.'!=3) {
                if($("#reviewBox'.$tab_fk.'").is(":hidden")) {
                    if(confirm("This project is not currently in the review stage. Do you still want to continue?")) {
                        $("#reviewBox'.$tab_fk.'").slideToggle();
                    }
                } else {
                    $("#reviewBox'.$tab_fk.'").slideToggle();
                }
             } else {
                $("#reviewBox'.$tab_fk.'").slideToggle();
             }
             return false;'));
    }
    if($showReview)
        echo CHtml::link('Reviewer Comments','#',array('class'=>'review i_commentOpen buttonLink noLoader',
            'onclick'=>'$(".otherComments").slideToggle();
                $(this).toggleClass("i_commentClose");
                return false;'));

    ?>
    </div>
    <?php
    $this->widget('application.components.CommentBox', array(
        'tab_fk' => $tab_fk,
        'projectOID'=>$model->id,
        'makesReviewID'=>$id,
    ));
endif;

// Shows reviews for this tab
if($showReview) {
    $this->widget('application.components.Reviews', array(
        'tab_fk' => $tab_fk,
        'projectOID'=>$model->id
    ));
}

$this->renderPartial('_view'.$render,array('model'=>$model),false,true);
?>  
</div>