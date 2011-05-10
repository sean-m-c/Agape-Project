<fieldset>

    <div class="row">
        <?php echo CHtml::label("What is your capacity for volunteers?",false); ?>
    </div>


    <div class="row">
        <?php echo $form->labelEx($model, "volunteer_count_min"); ?>
        <?php echo $form->textField($model, "volunteer_count_min", array("size" => 2, "maxlength" => 4)); ?>
        <?php echo $form->error($model, "volunteer_count_min"); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, "volunteer_count_max"); ?>
        <?php echo $form->textField($model, "volunteer_count_max", array("size" => 2, "maxlength" => 4)); ?>
        <?php echo $form->error($model, "volunteer_count_max"); ?>
    </div>
</fieldset>

<fieldset>
    <div class="row">
        <?php echo $form->labelEx($model, "apparel"); ?>
        <?php echo $form->textField($model, "apparel", array("size" => 60, "maxlength" => 250)); ?>
        <?php echo $form->error($model, "apparel"); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, "food_provided"); ?>
        <?php echo $form->checkbox($model, "food_provided"); ?>
        <?php echo $form->error($model, "food_provided"); ?>
    </div>

    <div class="row hiddenChild">
        <?php echo $form->labelEx($model, "food_provider"); ?>
        <?php echo $form->textField($model, "food_provider", array("size" => 30, "maxlength" => 30)); ?>
        <?php echo $form->error($model, "food_provider"); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, "restroom"); ?>
        <?php echo $form->checkbox($model, "restroom"); ?>
        <?php echo $form->error($model, "restroom"); ?>
    </div>

    <div class="row hiddenChild">
        <?php echo $form->labelEx($model, "handicap_friendly"); ?>
        <?php echo $form->checkbox($model, "handicap_friendly"); ?>
        <?php echo $form->error($model, "handicap_friendly"); ?>
    </div>
</fieldset>

<fieldset>

    <div class="row">
        <?php echo $form->labelEx($model, "parking_instructions"); ?>
        <?php echo $form->textField($model, "parking_instructions", array("size" => 50, "maxlength" => 50)); ?>
        <?php echo $form->error($model, "parking_instructions"); ?>
    </div>

    <?php
    $hours=null;
    $minutes=null;;
    $iH=1;
    while($iH<=12) {
        $hours[$iH]=$iH;
        $iH++;
    }
    $iM=0;
    while($iM<=60){
        if(strlen($iM)===1) {
            $minutes["0".$iM]="0".$iM;
        } else {
            $minutes[$iM]=$iM;
        }
        $iM+=5;
    }
    ?>
    <div class="row">
        <?php echo $form->label($model, "arrival_time", array("required"=>true)); ?>
        <?php echo $form->dropdownList($model,"arrival_time_hour",array($hours),array('style'=>'display:inline;'));?>
        <?php echo $form->dropdownList($model,"arrival_time_minute",array($minutes),array('style'=>'display:inline;'));?>
        <?php echo $form->dropdownList($model,"arrival_time_meridian",array("AM"=>"AM","PM"=>"PM"),array('style'=>'display:inline;'));?>
        <?php //echo $form->textField($model, "arrival_time"); ?>
        <?php echo $form->error($model, "arrival_time"); ?>
    </div>
</fieldset>
<?php echo CHtml::script("

var hiddenChildPrevDiv = $('div.hiddenChild').prev('div');

hiddenChildPrevDiv.find('input:checked').parent('div').next('div').show();

hiddenChildPrevDiv.find('input:checkbox').change(function() {
    var childDiv = $(this).parent('div').next('div');
    var childInput = childDiv.find('input');
    if($(this).attr('checked')==false) {
        if(childInput.next().attr('type')=='checkbox') {
            childInput.next().attr('checked',false);
        } else {
            childInput.val('');
        }
    }
    childDiv.slideToggle();

});



"); ?>