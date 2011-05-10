<?php
$this->breadcrumbs=array(
	'Settings'=>array('settings/index'),
	'Available Countries',
);

$this->menu=array(
	array('label'=>'List Country', 'url'=>array('index')),
	array('label'=>'Create Country', 'url'=>array('create')),
); ?>

<p>These are countries available in your application to users - for example, in the list of available countries for a project location.
You may add, edit, or remove these countries as you see fit.</p>

<div class="form">
    <?php echo CHtml::form(array('country/loadCountryFile'),'post',array('enctype'=>'multipart/form-data')); ?>
    <fieldset>
        <legend><h2><?php echo CHtml::link("Upload country file",array('#'),array('id'=>'uploadCountryLink')); ?></h2></legend>
        <div id="uploadCountry" style="display:none;">
        <p>
            You can replace all countries in the database with an uploaded .txt file containing countries.
            Current country lists are available <?php echo CHtml::link('here','http://www.iso.org/iso/list-en1-semic-3.txt'); ?>.
            If you create your own .txt file, the structure must be the same as the file linked here.
        </p>
        <p>Please remove the top line from the linked file before uploading.</p>
        <div class="row">
            <?php echo CHtml::activeLabel($model,'countriesFile',array('style'=>'width:auto; text-align:left;')); ?>
            <?php echo CHtml::activeFileField($model, 'countriesFile'); ?>
            <?php echo CHtml::error($model,'countriesFile'); ?>
            <?php echo CHtml::submitButton('Upload',array('class'=>'buttonLink i_add')); ?>
        </div>
        </div>
    </fieldset>
    <?php echo CHtml::endForm(); ?>
</div>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('country-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Countries</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'country-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'country_oid',
		'code',
		'name',
		array(
			'class'=>'CButtonColumn',
                        'htmlOptions'=>array('width'=>'90'),
                        'updateButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_edit.png',
                        'viewButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_glass.png',
                        'deleteButtonImageUrl'=>Yii::app()->theme->baseUrl.'/images/i_logout.png',
		),
	),
)); ?>

<?php echo CHtml::script('
    jQuery(document).ready(function() {
        $("a#uploadCountryLink").click(function() {
            $("div#uploadCountry").slideToggle();
            return false;
        });
    });
'); ?>
