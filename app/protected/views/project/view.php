<?php
$isFinal=true;
$breadcrumbs=array(
        'Projects'=>array('index'),
        $model->project_name,
);
if(Yii::app()->user->checkAccess('volunteer') && isset($_GET['ref']) && $_GET['ref']=='myProjects') {
    $breadcrumbs = array(
        'My Projects'=>array('project/myProjects'),
        'View "'.$model->project_name.'"',
    );
} elseif(Yii::app()->user->checkAccess('reviewer')) {
    $breadcrumbs = array(
        'Reviewer'=>array('site/home','#'=>'reviewerPanel'),
        'My Reviewable Projects'=>array('makesReview/myReviewableProjects'),
        'Review "'.$model->project_name.'"',
    );
}
$this->breadcrumbs=$breadcrumbs;

$this->menu=array(
        array('label'=>'List project', 'url'=>array('index')),
        array('label'=>'Create project', 'url'=>array('create')),
        array('label'=>'Update project', 'url'=>array('update', 'id'=>$model->id)),
        array('label'=>'Delete project', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
        array('label'=>'Manage project', 'url'=>array('admin')),
);
?>

<?php if(!$isFinal) : ?>
    <h1>View Project <?php echo $model->project_name; ?></h1>
<?php endif; ?>

<div class="reviewButtons">
<?php
if((Generic::reviewThisProject() || Yii::app()->user->checkAccess('adminhead')) &&
        $isFinal) {
    // Link to open final decision box
    echo CHtml::link('Evaluation Decision','#',array('class'=>'review decision buttonLink',
    'onclick'=>'
        if('.$model->status.'!=3) {
            if($("#reviewDecision").is(":hidden")) {
                if(confirm("This project is not currently in the review stage. Do you still want to continue?")) {
                    $("#reviewDecision").slideToggle();
                }
            } else {
                $("#reviewDecision").slideToggle();
            }
         } else {
            $("#reviewDecision").slideToggle();
         }
         return false;'));

    // Decision dialog box
    echo '<div id="reviewDecision">';
    $this->renderPartial('webroot.protected.views.makesReview.decision',
            array('model'=>MakesReview::model()->find('user_fk='.Yii::app()->user->id)));
    echo '</div>';

}
if(Yii::app()->user->checkAccess('adminhead') && $isFinal) {
    echo CHtml::link('Make Final Review and Decision',
            array('/project/final','projectOID'=>$_GET['id']),
            array('class'=>'review decision buttonLink',
                'onClick'=>'if('.$model->status.'!=3) {
                    if(confirm("This project is not currently in the review stage. Do you still want to continue?")) {
                        return true;
                    } else {
                        return false;
                    }
                 };'));
}
?>
</div>

<?php
$this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=>$this->enabledTabs($model->id,'_view',$isFinal),
        // additional javascript options for the tabs plugin
        'options'=>array(
                'collapsible'=>false,
        ),
));
?>