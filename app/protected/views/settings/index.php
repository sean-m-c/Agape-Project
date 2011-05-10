<?php
$this->breadcrumbs = array(
    'Settings',
);
?>

<?php echo CHtml::css('
ul#settingsList {
    list-style-type:none;
}
ul#settingsList li {
    margin-bottom:1em;
}

'); ?>

<h1>Application Settings</h1>

<div class="form">
    <ul id="settingsList">
        <fieldset>
            <legend>Data</legend>
            <li><?php echo CHtml::link('Edit countries', array('country/admin'), array('class' => 'buttonLink i_edit')); ?><li>
            <li><?php echo CHtml::link('Edit form field descriptions', array('description/admin'), array('class' => 'buttonLink i_edit')); ?><li>
            <li><?php echo CHtml::link('Edit application messages', array('applicationMessage/admin'), array('class' => 'buttonLink i_edit')); ?><li>
            <li><?php echo CHtml::link('Edit project clearances', array('clearance/admin'), array('class' => 'buttonLink i_edit')); ?><li>
            <li><?php echo CHtml::link('Edit quantitative and qualitative methods', array('method/admin'), array('class' => 'buttonLink i_edit')); ?><li>
        </fieldset>
        <fieldset>
            <legend>Layout</legend>
            <li><?php echo CHtml::link('Edit project tabs', array('tab/admin'), array('class' => 'buttonLink i_edit')); ?></li>
        </fieldset>

    </ul>
</div>