<?php $this->widget('application.components.Hierarchy', array(
        'tableName'=>'strategy',
        'parentName'=>'goal',
        'parentID'=>$parentID,
        'childName'=>'task',
        'action'=>'view'
)); ?>