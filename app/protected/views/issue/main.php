<div id="loadContainer" class="loading">
    <div id="ajaxPanel">
        <p>You can choose up to three issue areas to associate with your project.</p>

        <?php
        /*echo CHtml::ajaxLink("Add New Issue",array('issue/create','projectOID'=>$projectOID),
        array(
        'update' => '#issue',
        'onclick'=>
        'if ($(".issue").length >= 3) {
            alert("You are not allowed to insert more that three issues for a project.
            Please update the existing issues.");
            return false;',
        )); */?>
        <?php echo CHtml::link('Add Issue >>',array('#'),array('id'=>'add','class'=>'diffbutton small')); ?>

        <div id="create">
        <?php $this->renderPartial('/issue/create',array('model'=>$models['create'],'projectOID'=>$projectOID)); ?>
        </div>

        <div id="admin">
        <?php $this->renderPartial('/issue/admin',array('model'=>$models['admin'],'form'=>$form,'projectOID'=>$projectOID)); ?>
        </div>
    </div>
</div>
<?php echo CHtml::script("
 $(document).ready(function() {
    $('#loadContainer').removeClass('loading');


    $('#ajaxPanel').animate({
        marginLeft: parseInt($('#ajaxPanel').css('marginLeft'),10) == 0 ?
        $('#ajaxPanel').outerWidth() :
            0
    });

    $('#create').hide();
    $('a#add').click(function() {
        $('div#create').toggle();
        return false;
    });
});
"); ?>