<?php
$this->pageTitle=Yii::app()->name . ' - Search';
$this->breadcrumbs=array(
    'Search',
);
?>
<?php echo CHtml::beginForm(); ?>
<?php echo CHtml::errorSummary($search); ?>
<h2>Search Projects</h2>
<div id="row">
    <?php echo CHtml::activeTextField($search,'keywords'); ?>
    <?php echo CHtml::submitButton('Search'); ?>
</div>
<?php echo CHtml::endForm(); ?>
<?php echo CHtml::link('Advanced Search &raquo',array('site/advSearch')); ?>
