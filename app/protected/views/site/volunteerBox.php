<div id="volunteerBox">
    <h2>Volunteer Admin</h2>
    <ul>
        <li><?php echo CHtml::link('My Community Partner\'s Projects',array('project/projects')); ?></li>
        <li><?php echo CHtml::link('My Proposed Projects',array('project/projects','ref'=>'myprojects')); ?></li>
        <li><?php echo CHtml::link('Propose Community Partner',array('CommunityPartner/create')); ?></li>
        <li><?php echo CHtml::link('Propose New User',array('user/create')); ?></li>
        <li><?php echo CHtml::link('Propose Project',array('project/create')); ?></li>
    </ul>
</div>
