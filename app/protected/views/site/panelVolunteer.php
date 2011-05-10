<?php $notificationCount = NotificationCount::totalVolunteer();
$notifications='notifications';
if($notificationCount=='1')
    $notifications='notification';

?>
<div class="i_notifications icon_pad noBGPad">
    You have <?php echo $notificationCount; ?> new <?php echo $notifications; ?> for this role.</div>
<ul>
    <li><?php echo CHtml::link('Projects',array('project/myProjects')); ?></li>
    <li><?php echo CHtml::link('Community Partners',array('involved/myPartners')); ?></li>
    <li><?php //echo CHtml::link('Create New User',array('user/create')); ?></li>
</ul>