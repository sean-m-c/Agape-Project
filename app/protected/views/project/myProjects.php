<?php
$this->breadcrumbs=array(
        'My Projects'
);

function decisionFlag($flag) {
    switch ($flag) {
        case '0':
            return 'Draft';
            break;
        case '1':
            return 'Submitted';
            break;
        case '2':
            return 'Submitted';
            break;
        case '3':
            return 'In Review';
            break;
        case '4':
            return 'Approved';
            break;
        case '5':
            return 'Needs revision';
            break;
        case '6':
            return 'Declined';
            break;
    }
}

if(Yii::app()->user->hasFlash('needPartner')) {
    CController::renderPartial('/messages/flash',array('key'=>'needPartner','type'=>'notice','close'=>'true'));
} else {

    echo '<div class="buttonContainer">'.CHtml::link('Create new project','',
        array('class'=>'i_add buttonLink noLoader',
            'onclick'=>'$("#createProjectDialog").dialog("open"); return false;')).'</div>';
}

if(Yii::app()->user->hasFlash('projectSubmitted'))
     CController::renderPartial('/messages/flash',array('key'=>'projectSubmitted','type'=>'success'));
?>

<p>These are projects which you have created.</p>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'myProjects-grid',
            'dataProvider'=>$dataProvider,
            'columns'=>array(
                    'project_name',
                    'project_description',
                    array(
                        'name'=>'Status',
                        'value'=>'decisionFlag($data->status)',
                    ),
                    array(
                            'class'=>'CButtonColumn',
                            'htmlOptions'=>array('width'=>'100'),
                            'deleteButtonLabel'=>'Delete this project.',
                            'deleteConfirmation'=>'Are you sure you want to delete this project?',
                            'deleteButtonOptions'=>array('class'=>'showTooltip','height'=>'25','width'=>'25'),
                            'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
                            'template'=>'{viewProject} {delete} {editProject}',
                            'buttons'=>array(
                                    'viewProject'=>array(
                                            'label'=>'View project page.',
                                            'url'=>'Yii::app()->controller->createUrl("project/view",array("id"=>$data->id,"ref"=>"myProjects"))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                                            'options'=>array('height'=>'25','width'=>'25'),
                                    ),
                                    'editProject'=>array(
                                            'label'=>'Edit Project.',
                                            'url'=>'Yii::app()->controller->createUrl("project/update",array("id"=>$data->id))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_edit.png',
                                            'options'=>array('height'=>'25','width'=>'25'),
                                            'visible'=>'$data->status==0 || $data->status==5',
                                    ),
                            ),
                            'htmlOptions'=>array('width'=>'90')
                    ),
            ),
    ));
?>

<div id="dialogContainer" style="display:none;">
<?php $this->renderPartial('create', array('model'=>new Project)); ?>
</div>