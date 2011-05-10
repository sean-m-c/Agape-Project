<?php
$this->breadcrumbs=array(
        'Workflow'=>array('/workflow'),
        'NeedApproval',
);?>

<p>These projects have been submitted by a community partner, and need to be approved for reviewers to be assigned
    (this prevents spam projects).<p>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
            'id'=>'needApproval-grid',
            'dataProvider'=>$dataProvider,
            'ajaxUpdate'=>true,
            'columns'=>array(
                    'project_name',
                    array(
                            'name'=>'Description',
                            'value'=>'(!empty($data->project_description)) ? substr($data->project_description,0,200)."..." : "None set"',
                    ),
                    array(
                            'class'=>'CButtonColumn',
                            'template'=>'{viewProject} {approve}',
                            'buttons'=>array(
                                    'approve'=>array(
                                            'label'=>'Approve this project as not spam and make available to be assigned reviewers.',
                                            'url'=>'Yii::app()->controller->createUrl("project/makeReviewable",array("id"=>$data->id))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_checkmark.png',
                                            'options'=>array('class'=>'gridButtonClick','height'=>'25','width'=>'25'),
                                    ),
                                    'viewProject'=>array(
                                            'label'=>'View project page.',
                                            'url'=>'Yii::app()->controller->createUrl("/project/view",array("id"=>$data->id))',
                                            'imageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                                            'options'=>array('height'=>'25','width'=>'25'),
                                    ),
                            ),
                    ),
            ),
    ));
    Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/all.js'); 
    echo CHtml::script("
        $(document).ready(function() {
            $('.gridButtonClick').click(function() {
                gridButtonClick($(this).attr('href'),'needApproval-grid');
                return false;
            });
         });");
    ?>