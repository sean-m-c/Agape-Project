<?php
$this->breadcrumbs = array(
    'Projects' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List project', 'url' => array('index')),
    array('label' => 'Create project', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('project-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Projects</h1>

<p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div><!-- search-form -->

<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'project-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            'id',
            'project_name',
            array(
                'name' => 'project_description',
                'value' => '!empty($data->project_description) ? substr($data->project_description,0,100).\'...\' : ""',
            ),
            'start_date',
            'end_date',
            /*

              'volunteer_lead_name',
              'volunteer_lead_email',
              'volunteer_lead_phone',
              'credit_bearing',
              'prep_work',
              'prep_work_help',
              'measure_results',
              'indoor_outdoor',
              'contingency_description',
              'rmp',
              'volunteer_count',
              'minimum_age',
              'apparel',
              'food_provided',
              'food_provider',
              'restroom',
              'handicap_friendly',
              'arrival_time',
              'parking_instructions',
              'community_partner_fk',
              'status',
              'user_fk',
              'overall_comment',
             */
            array(
                'class' => 'CButtonColumn',
                'htmlOptions' => array('width' => '100'),
                'deleteButtonLabel' => 'Delete this project.',
                'deleteConfirmation' => 'Are you sure you want to delete this project?',
                'deleteButtonOptions' => array('class' => 'showTooltip', 'height' => '25', 'width' => '25'),
                'deleteButtonImageUrl' => Yii::app()->theme->baseUrl . '/images/i_logout.png',
                'template' => '{viewProject} {delete} {editProject} {download}',
                'buttons' => array(
                    'viewProject' => array(
                        'label' => 'View project page.',
                        'url' => 'Yii::app()->controller->createUrl("project/view",array("id"=>$data->id,"ref"=>"myProjects"))',
                        'imageUrl' => Yii::app()->theme->baseUrl . '/images/i_glass.png',
                        'options' => array('height' => '25', 'width' => '25'),
                    ),
                    'editProject' => array(
                        'label' => 'Edit Project.',
                        'url' => 'Yii::app()->controller->createUrl("project/update",array("id"=>$data->id))',
                        'imageUrl' => Yii::app()->theme->baseUrl . '/images/i_edit.png',
                        'options' => array('height' => '25', 'width' => '25'),
                        'visible' => '$data->status==0 || $data->status==5',
                    ),
                    'download' => array(
                        'label' => 'Download project details to Excel.',
                        'url' => 'Yii::app()->controller->createUrl("project/download",array("type"=>"excel", "id"=>$data->id))',
                        'imageUrl' => Yii::app()->theme->baseUrl . '/images/i_login.png',
                        'options' => array('height' => '25', 'width' => '25'),
                    ),
                ),
                'htmlOptions' => array('width' => '120')
            ),
        ),
    ));
?>
