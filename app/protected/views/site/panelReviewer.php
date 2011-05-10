<?php $notificationCount = NotificationCount::totalReviewer();
$notifications='notifications';
if($notificationCount=='1')
    $notifications='notification';

$notifications = CHtml::link($notifications,array('notifications/index'));
?>
<div class="i_notifications icon_pad noBGPad">
    You have <?php echo $notificationCount; ?> new <?php echo $notifications; ?> for this role.</div>

<ul>
    <li><?php echo CHtml::link('Projects Needing My Review',array('notifications/index','#'=>'needMyReviewPanel')); ?></li>
    <li><?php echo CHtml::link('Reviewable Projects',array('makesReview/myReviewableProjects')); ?></li>
</ul>
