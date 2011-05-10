<?php
$this->breadcrumbs = array(
    'Settings' => array('settings/index'),
    'Clearances',
);

$this->menu = array(
    array('label' => 'List Clearance', 'url' => array('index')),
    array('label' => 'Create Clearance', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('clearance-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Clearances</h1>

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
        'id' => 'clearance-grid',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'columns' => array(
            'name',
            'url',
            'is_default:boolean',
            array(
                'class' => 'CButtonColumn',
                'htmlOptions' => array('width' => '100'),
                'deleteButtonLabel' => 'Delete this clearance.',
                'deleteConfirmation' => 'Are you sure you want to delete this clearance?',
                'deleteButtonOptions' => array('class' => 'showTooltip', 'height' => '25', 'width' => '25'),
                'deleteButtonImageUrl' => Yii::app()->theme->baseUrl . '/images/i_delete.png',
                'updateButtonLabel' => 'Edit this clearance.',
                'updateButtonImageUrl' => Yii::app()->theme->baseUrl . '/images/i_edit.png',
                'viewButtonLabel' => 'View this clearance.',
                'viewButtonImageUrl' => Yii::app()->theme->baseUrl . '/images/i_glass.png',
                'template' => '{view} {update} {delete}',
                'buttons'=>array(
                    'delete'=>array(
                        'visible'=>'$data->is_default=="1";',
                    )
                )
            ),
        ),
    ));
?>
