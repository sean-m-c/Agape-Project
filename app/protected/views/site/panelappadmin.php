<?php $notificationCount = NotificationCount::totalAppadmin();
$notifications='notifications';
if($notificationCount=='1')
    $notifications='notification';

?>
<div class="i_notifications icon_pad noBGPad">
    You have <?php echo $notificationCount; ?> new <?php echo $notifications; ?> for this role.</div>
<ul>
    <li><?php echo CHtml::link('Project Queue',array('notifications/index','#'=>'projectQueuePanel')); ?></li>
    <li><?php echo CHtml::link('Pending Community Partners',array('notifications/index','#'=>'pendingCommunityPartnersPanel')); ?></li>
    <li><?php echo CHtml::link('Reports',array('report/home')); ?></li>
    <li><?php echo CHtml::link('Manage Users',array('user/admin')); ?></li>
    <li><?php echo CHtml::link('View Projects',array('project/admin')); ?></li>
</ul>
