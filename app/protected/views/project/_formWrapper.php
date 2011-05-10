<?php echo CHtml::css('
    #formWrapper fieldset {
        margin-top:2em;
    }
'); ?>
<div class="form">
    <div id="formWrapper">

        <?php
        if(isset($render)) {

            $form=null;
            if($render!='Issues') {

                $form=$this->beginWidget('CActiveForm', array(
                        'id'=>'project-form',
                        'enableAjaxValidation'=>true,
                        'action'=>array('project/update','id'=>$model->id),
                ));

                echo $form->errorSummary($model);
                ?>

        <div class="row buttons">
                    <?php

                    echo CHtml::hiddenField('saved',"true");

                    echo CHtml::link('Save Tab',
                    '',array('id'=>'saveTab',
                    'onclick'=>CHtml::ajax(array(
                    'beforeSend'=>'function() { $(".response").fadeOut().empty(); }',
                    'url'=>array('/project/update','id'=>$model->id,'render'=>$render),
                    'type'=>'POST',
                    'dataType'=>'json',
                    'success'=>'function(data) {
                                if(data.status=="t") {
                                    $("#saved").val("true");
                                } else if (data.status=="f") {
                                    $("#saved").val("false");
                                }
                                $(".response").html(data.response).fadeIn();
                            }',

                    )),
                    'style'=>"cursor:pointer;",
                    'class'=>'i_checkmark buttonLink'
                    ));

                    // If the user is updating this, we want creator's ID stored with it.
                    //We use activeHIddenField because it automatically has the database value
                    if(!$model->isNewRecord && isset(Yii::app()->user->id)) {
                        echo $form->hiddenField($model,'user_fk');
                        echo $form->error($model,'user_fk');
                    }
                }
                ?>
            <p class="note">Fields with <span class="required">*</span> are required.</p>
                <div class="response"></div>

                <?php

                $this->renderPartial('_form'.$render,array('model'=>$model,'form'=>$form),false,true);
/*
                $tabNote = TabNote::model()->find(array(
                    'condition'=>'project_fk='.$model->id.' AND '.'tab_fk='.$tab_fk));

                if(!empty($tabNote)) {
                    $this->renderPartial('/tabNote/_form',array('tabNote'=>$tabNote,'project_fk'=>$model->id));
                } else {
                    $this->renderPartial('/tabNote/_form',
                            array('tabNote'=>new TabNote,'project_fk'=>$model->id,'tab_fk'=>$tab_fk));
                }
*/
                if($render!='Issues') $this->endWidget('CActiveForm');

            } else {
                echo 'No render command.';
            }
            if($render!='Issues') {
            echo CHtml::link('Save Tab',
                    '',array('id'=>'saveTab',
                    'onclick'=>CHtml::ajax(array(
                    'beforeSend'=>'function() { $(".response").fadeOut().empty(); }',
                    'url'=>array('/project/update','id'=>$model->id,'render'=>$render),
                    'type'=>'POST',
                    'dataType'=>'json',
                    'success'=>'function(data) {
                                if(data.status=="t") {
                                    $("#saved").val("true");
                                } else if (data.status=="f") {
                                    $("#saved").val("false");
                                }
                                $(".response").html(data.response).fadeIn();
                            }',

                    )),
                    'style'=>"cursor:pointer; float:left;",
                    'class'=>'i_checkmark buttonLink'
                    ));
            }
            ?>
        </div>
    </div>
</div>

<?php echo CHtml::script('
    var jInput = $(":input");
    jInput.change(function(objEvent){
        $("#saved").val("false");
    });

$("label").click(function() {

});
'); ?>
