<?php $notificationCount = NotificationCount::totaladminhead();
$notifications='notifications';
if($notificationCount=='1')
    $notifications='notification';

?>
<div class="i_notifications icon_pad noBGPad">
    You have <?php echo $notificationCount; ?> new <?php echo $notifications; ?> for this role.</div>
<ul>
    <li><?php echo CHtml::link('Reports',array('report/index')); ?></li>
    <li><?php echo CHtml::link('Project Queue',array('notifications/index','#'=>'projectQueuePanel')); ?></li>
    <li><?php echo CHtml::link('Edit Issue Types',array('issueType/admin')); ?></li>
    <li><?php echo CHtml::link('All Community Partners',array('communityPartner/admin')); ?></li>
    <li><?php echo CHtml::link('All Projects',array('project/admin')); ?></li>
    <li><?php echo CHtml::link('All Users',array('user/admin')); ?></li>
    <li><?php echo CHtml::link('Application Settings',array('settings/index')); ?></li>
</ul>
