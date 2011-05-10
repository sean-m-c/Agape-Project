<?php
$this->pageTitle=Yii::app()->name . ' - Report';
$this->breadcrumbs=array(
	'Reports',
);
?>
<?php
$this->widget('application.components.TaskInterface', array(
        'navTitle'=>'Type',
        'panelTitle'=>'Report',
        'items'=>$panels,
));
?>
