<?php

$this->widget('application.components.Hierarchy', array(
        'tableName'=>$params['tableName'],
        'parentID'=>$params['parentID'],
        //'grandparentID'=>$params['grandparentID'],
        'idTrail'=>$params['idTrail'],
        'action'=>$params['action'],
));
Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl.'/js/all.js');
?>
