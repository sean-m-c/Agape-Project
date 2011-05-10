<?php
$this->pageTitle=Yii::app()->name . ' - Notifications';
$this->breadcrumbs=array(
        'Notifications',
);

$this->widget('application.components.TaskInterface', array(
        'navTitle'=>'Category',
        'panelTitle'=>'Notifications',
        'items'=>$tabs,
));
?>

