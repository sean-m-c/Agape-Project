<div id="cpadminBox">
    <h2>Community Partner Admin</h2>
    <ul>
        <li><?php echo CHtml::link('Create New Community Partner Member',array('user/create','role'=>'cpadmin')); ?></li>
        <li><?php echo CHtml::link('Pending Community Partner Members',array('notifications/home')); ?></li>
    </ul>
</div>
