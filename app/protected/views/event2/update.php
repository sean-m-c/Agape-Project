<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog',array(
                'id'=>'createEventDialog',
                'options'=>array(
                    'title'=>'Edit Event',
                    'show'=>'fade',
                    'hide'=>'fade',
                    'autoOpen'=>true,
                    'resizable'=>'false',
                    'modal'=>'true',
                    'width'=>'auto',
                    'height'=>'auto',
                    'close'=>'js: function() {
                        $(this).dialog("destroy");
                        $(this).remove();
                     }',
                ),
));?>

<?php echo $this->renderPartial('_form', array('model'=>$model), false, true); ?>


    <div id="ajaxResponse"></div>

<?php $this->endWidget('zii.widgets.jui.CJuiDialog');?>