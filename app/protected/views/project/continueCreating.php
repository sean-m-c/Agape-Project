<p>
    Your project has been created. Do you want to continue editing its details?
    You can come back to do this at any time.</p>
<p>
    <?php echo CHtml::link('Yes',array('/project/update','id'=>$projectOID),
    array('class'=>'i_forward noLoader buttonLink')); ?>
    <?php echo CHtml::link('No thanks, I\'ll do that later',array('#'),
    array('class'=>'i_logout noBGPad noLoader buttonLink',
        'onclick'=>'$("#createProjectDialog").dialog("close"); return false;')); ?>
</p>