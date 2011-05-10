<?php
$issue = Issue::model()->find($parentID);
?>
<ul id="ajaxBreadcrumbs">
    <li>Issue <?php echo CHtml::link($issue->issueType->type,array("project/renderTab",
        array("ajaxPanel"=>$params['table'],'id'=>$parentID)));?> >></li>
    <li>Goals</li>
</ul>
<?php

$this->widget('application.components.Hierarchy', array(
        'tableName'=>'goal',
        'parentName'=>'issue',
        'parentID'=>$parentID,
        'childName'=>'strategy',
        'action'=>'view'
)); ?>
