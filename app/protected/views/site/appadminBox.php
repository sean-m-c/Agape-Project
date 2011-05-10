<div id="appadminBox">
    <h2><?php echo Yii::app()->name; ?> Admin</h2>
    <ul>
        <li><?php echo CHtml::link('Manage Users',array('user/admin')); ?></li>
        <li><?php echo CHtml::link('View Projects',array('project/admin')); ?></li>
        <li><?php echo CHtml::link('Pending Projects',array('notifications/home')); ?></li>
        <li><?php echo CHtml::link('Pending Community Partners',array('notifications/home','ref'=>'myprojects')); ?></li>
        <li><?php echo CHtml::link('Reports',array('report/home')); ?></li>
    </ul>
</div>
