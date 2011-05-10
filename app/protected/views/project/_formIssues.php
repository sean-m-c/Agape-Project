<?php
echo CHtml::css('
div.demoTree ul {
    list-style-type:none;
    border-top:1px solid #ccc;
}
div.demoTree ul li {
    padding-left:1.5em;
    border-bottom:1px solid #ccc;
}
div.demoTree a.button {
    float:right;
    color:#fff;
    background-color:#666;
    padding:0 10px;
    -moz-border-radius:5px;
    -border-radius:5px;
    -webkit-border-radius:5px;
}
div.demoTree li, div.demoTree a {
    color:#333;
    font-size:1em;
}
');
/*
 $this->renderPartial('ajaxInterface',array('params'=>array(
    'tableName'=>'issue',
    'parentName'=>'project',
    'parentID'=>$_GET['id'],
    'childName'=>'goal',
    'action'=>'update')),false,true);

$this->widget('application.extensions.SimpleTreeWidget',array(
    'model'=>'Goal',
    'ajaxUrl' => CController::createUrl('project/issue'),
    'onSelect'=>'
        var id = data.inst.get_selected().attr("id").replace("node_","");
        $("#contentBox").load("/ajax/getContent/id/"+id);
    '
));*/
//Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/all.js');
?>
<div class="demoTree">
<ul class="telefilms cookie1">
        <li>
            Issue: Hunger
            <?php echo CHtml::link('Add New Goal &raquo',array('#'),array('class'=>'button')); ?>
            <ul><li></li></ul>
        </li>
        <li>
            Issue: Homelessness
            <?php echo CHtml::link('Add New Goal &raquo',array('#'),array('class'=>'button')); ?>
          <ul>
            <li>
              Goal: Raise awareness of the homeless in Harrisburg.
              <?php echo CHtml::link('Add New Strategy &raquo',array('#'),array('class'=>'button')); ?>
              <ul>
                <li>
                  <a href="#">Strategy: Spread brochures about homelessness. </a>
                  <?php echo CHtml::link('Add New Task &raquo',array('#'),array('class'=>'button')); ?>
                  <ul>
                    <li><a href="#">Task: Raise teams of volunteers.</a></li>
                    <li><a href="#">Task: Print brochures about homelessness.</a></li>
                    <li><a href="#">Task: Evaluate feedback and statistics.</a></li>
                  </ul>
                </li>
                <li>
                  <a href="#">Strategy: Send speakers to schools.</a>
                  <?php echo CHtml::link('Add New Task &raquo',array('#'),array('class'=>'button')); ?>
                  <ul>
                    <li><a href="#">Task: Find available speakers.</a></li>
                    <li><a href="#">Task: Determine how much payment speakers will need.</a></li>
                    <li><a href="#">Task: Contact schools to set up meetings.</a></li>
                    <li><a href="#">Task: Collect feedback from students to determine effectiveness.</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li>Goal: Help homeless people sustain themselves.
            <?php echo CHtml::link('Add New Strategy &raquo',array('#'),array('class'=>'button')); ?>
            <ul>
                <li>
                    <a href="#">Strategy: Help them find jobs. </a>
                    <ul>
                        <li>

                        </li>
                    </ul>
                </li>
            </ul>
          </ul>
      </ul>
</div>
<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.tree.js');
echo CHtml::script("
    $(document).ready(function() {
        $('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
    });");
?>