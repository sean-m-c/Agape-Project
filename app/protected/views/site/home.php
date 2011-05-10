<?php
$this->pageTitle=Yii::app()->name . ' - Home';
$this->breadcrumbs=array(
        'Home',
);

$this->widget('application.components.TaskInterface', array(
        'navTitle'=>'Role',
        'panelTitle'=>'Actions',
        'items'=>$panels,
));

?>

