<div id="loading"></div>
<div id="ajaxGuiContainer" style="min-height:200px;">
<?php
/*
 $this->renderPartial('ajaxInterface',array('params'=>array(
    'tableName'=>'issue',
    'parentName'=>'project',
    'parentID'=>$_GET['id'],
    'childName'=>'goal',
    'action'=>'update')),false,true);
*/

$model = Goal::model()->findByPk(1);

//print_r($model->metaData->relations);
echo '<pre>';
//print_r($model);
echo '</pre>';
/*
$this->widget('application.extensions.SimpleTreeWidget',array(
    'model'=>$model,
    'ajaxUrl' => CController::createUrl('project/issue'),
    'onSelect'=>'
        var id = data.inst.get_selected().attr("id").replace("node_","");
        alert(data.inst.get_selected().attr());
        alert(id);
        $("#contentBox").load("/ajax/getContent/id/"+id);'
));
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/all.js');
*/
?>
</div>
