<div id="superBox">
    <h2>Super Admin</h2>
    <ul>
        <li><?php echo CHtml::link('Manage Community Partners',array('community_partner/admin','ref'=>'myprojects')); ?></li>
        <li><?php echo CHtml::link('Manage Projects',array('project/admin')); ?></li>
        <li><?php echo CHtml::link('Manage Users',array('user/admin')); ?></li>
		<li><?php echo CHtml::link('Issue Type Editor',array('issue_type/admin')); ?></li>
        <li><?php echo CHtml::link('Reports',array('report/home')); ?></li>
    </ul>
</div>
