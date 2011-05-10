<div id="dialogContainer" style="display:none;">
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
                'id'=>'addDialog',
                'options'=>array(
                    'title'=>'Edit '.ucwords($params->table),
                    'autoOpen'=>false,
                    'modal'=>'true',
                    'width'=>'auto',
                    'height'=>'auto',
                    'show'=>'fade',
                    'hide'=>'fade',
                ),
));
?>
    <div id="dialogContainerForm"></div>
<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>
</div>

<div id="parentList">
    <?php echo $this->getParents(); ?>
</div>

<!-- <h3 id="ajaxBreadcrumbs"><?php //echo $this->getBreadcrumbs(); ?></h3> -->
<?php// if(!empty($params->range)) echo $params->range; ?>
<?php
$ret='';
switch($params->table) {
    
    case 'issue':
        $ret = 'You must have at least one issue, and no more than three issues';
        break;
    case 'goal':
        echo 'You may have up to three goals for this issue.';
        break;
}
if(!empty($ret))
    echo '<p>'.$ret.'</p>';
?>
<p>
<?php
//var_dump($params->tableID-1);
//var_dump($params->parentName);

//Generic::printArray($params->idTrail);
// Back button
if($this->tableID>1) {
    echo CHtml::ajaxLink('Back to '.ucwords($params->parentName),
    array('project/renderTab',
    'tableName'=>$params->parentName,
    'parentID'=>$this->getGrandparentID($params->parentName,$params->parentID),
    'action'=>$params->action,
    'idTrail'=>urlencode(serialize($params->idTrail)),
    'ajaxPanel'=>'true'),
    array(
        'dataType'=>'json',
        'beforeSend'=>'function(data) {
            $("div#ajaxGuiContainer").hide("slide", { direction: "right" });
            $("div#loading").addClass("loading").fadeIn("fast");
        }',
        'success'=>'function(data) {
            $("#addDialog").remove();
            $("div#ajaxGuiContainer").empty().html(data.response);
        }',
        'complete'=>'function(data) {
            $("#ajaxGuiContainer").show("slide", { direction: "left" });
            $("div#loading").fadeOut("fast").removeClass("loading");
            }'),
    array('class'=>'i_back buttonLink','id'=>$params->table.'Back',
            'style'=>'margin-right:1em;'));
}

// Add new item form open button
if($params->action=='update') :
    echo CHtml::link('Add New '.ucwords($params->table),
    array('#'),
    array('class'=>'i_add buttonLink noLoader',
    'onclick'=>'$("#addNewForm").toggle("fast");
    return false;'));
    ?>
</p>
<div id="addNewForm" style="display:none;">
    <div class="form">
        <fieldset>
                <?php

                $form=$this->beginWidget('CActiveForm', array(
                        'id'=>$params->table.'-form'
                )); ?>
                <?php echo $form->errorSummary($model); ?>
            <div class="row">
                    <?php
                    if($params->table!='issue') {
                        echo $form->textField($model,$params->table.'_description',array('size'=>60,'maxlength'=>500));
                        echo $form->hiddenField($model,$params->parentName.'_fk',array('value'=>$params->parentID));
                        echo $form->error($model,$params->table.'_description');
                    } else {
                        echo $form->dropDownList($model,'issue_type_fk',
                        CHtml::listdata(IssueType::model()->findAll(array('order'=>'type ASC')),'issue_type_oid','type'));
                        echo $form->hiddenField($model,'project_fk',array('value'=>$params->parentID));
                        echo $form->error($model,$params->table.'_description');
                    }?>

                <?php
                echo CHtml::link('Add '.ucwords($params->table),
                '',array(
                'onclick'=>CHtml::ajax(array
                (
                'url'=>array('/'.$params->table.'/create'),
                'beforeSend'=>'function() { $("#ajaxInterfaceResponse").fadeOut(); }',
                'type'=>'POST',
                'dataType'=>'json',
                'error'=>'function (xhr, ajaxOptions, thrownError){
                    alert(xhr.statusText);
                    alert(thrownError);
                }',
                'success'=>'function(data) {
                    if(data.status=="f") {
                        $("#ajaxInterfaceResponse").html(data.response).fadeIn();
                    } else {
                        $("#'.ucwords($params->table).'_'.$params->table.'_description").html("");
                    }
                    $.fn.yiiGridView.update("'.$params->table.'-grid");
                 }',
                )),
                'style'=>"cursor:pointer;",
                'class'=>'i_checkmark buttonLink'
                ));
                ?>
                <?php $this->endWidget(); ?>
                </div>
            <div id="ajaxInterfaceResponse"></div>
        </fieldset>
    </div>
</div>
<?php endif; // end add item form ?>

<?php
$label = ($params->childName!='strategy') ? ucwords($params->childName.'s >>') : 'Strategies >>';
$children = $this->getChildren();

$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>$params->table.'-grid',
        'dataProvider'=>$dataProvider,
        'columns'=>array(
                $params->displayColumn,
                array(
                        'class'=>'CButtonColumn',
                        'htmlOptions'=>array('width'=>'250','float'=>'left'),
                        'deleteButtonImageUrl'=>Yii::app()->request->baseUrl.'/images/i_logout.png',
                        'deleteButtonUrl'=>'Yii::app()->controller->createUrl("'.$params->table.'/delete",
                                                    array("id"=>"$data->'.$params->table.'_oid"))',
                        'deleteButtonLabel'=>'Remove this '.$params->table.' from project.',
                        'deleteButtonOptions'=>array('height'=>'25','width'=>'25'),
                        'deleteConfirmation'=>'Are you sure you want to remove this '.$params->table.' from '.$params->parentName.'?'.$children,
                        'updateButtonOptions'=>array('height'=>'25','width'=>'25'),
                        'updateButtonImageUrl'=>Yii::app()->request->baseUrl.'/images/i_edit.png',
                        'template'=>'{updateItem} {delete} {viewChild}',
                        'buttons'=>array(
                                'viewChild'=>array(
                                        'label'=>$label,
                                        'url'=>'Yii::app()->controller->createUrl("project/renderTab",
                                                array(
                                                    "tableName"=>"'.$params->childName.'",
                                                    "parentID"=>"$data->'.$params->table.'_oid",
                                                    "action"=>"'.$params->action.'",
                                                    "idTrail"=>"'.urlencode(serialize($params->idTrail)).'"));',
                                        //'imageUrl'=>Yii::app()->request->baseUrl.'/images/i_glass.png',
                                        'options'=>array('class'=>'IGSTClick buttonLink i_forward',
                                            'style'=>'position:relative; top:-7px;'),
                                        'visible'=>$params->table.'!="task"',
                                ),
                                'updateItem'=>array(
                                        'label'=>'Edit '.ucwords($params->table),
                                        'url'=>'Yii::app()->controller->createUrl("'.$params->table.'/update",
                                                array("id"=>"$data->'.$params->table.'_oid"));',
                                        'imageUrl'=>Yii::app()->request->baseUrl.'/images/i_edit.png',
                                        'options'=>array('class'=>'IGSTUpdate','height'=>'25','width'=>'25'),
                                ),
                        ),
                ),
        ),
));

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/all.js');
?>

