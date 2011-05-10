<?php $notificationCount = NotificationCount::totalCpadmin();
$notifications='notifications';
if($notificationCount=='1')
    $notifications='notification';

?>
<div class="i_notifications icon_pad noBGPad">
    You have <?php echo $notificationCount; ?> new <?php echo $notifications; ?> for this role.</div>
<ul>
    <li><?php echo CHtml::link('My Administered Community Partners',
            array('involved/myPartners','role'=>'cpadmin')); ?></li>
    <li><?php echo CHtml::link('Pending Community Partner Members',
            array('notifications/index','#'=>'pendingCommunityPartnerVolunteersPanel')); ?></li>
</ul>
