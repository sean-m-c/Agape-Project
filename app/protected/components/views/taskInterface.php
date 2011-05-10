<div id="taskWrapper">
    <ul id="taskNav" style="display:<?php echo $params['display']; ?>;">
        <?php if(!empty($params['panels'])) : ?>
        <h1><?php echo $params['navTitle']; ?></h1>
        <?php foreach($params['panels'] as $title => $panel) {
            echo '<li>'.CHtml::ajaxLink($title,$panel['url'],
                    array('success'=>'js: function(data) {
                        location.hash = "'.$panel['id'].'Panel";
                        $("#taskPanel").hide().replaceWith("<div id=\'taskPanel\'>" + data + "</div>").fadeIn("fast");
                    }'),
                    array('id'=>$panel['id'],'class'=>'taskNavLink showTooltip','title'=>$panel['title']))."\n";
        } ?>
        <?php endif; ?>
    </ul>
    <h1><?php echo $params['panelTitle']; ?></h1>
    <div id="taskPanel"><?php if(empty($params['panels'])) { echo 'You have no notifications'; } else { echo '<div class="loading">'; } ?></div></div>
</div>
<?php echo CHtml::script("
    jQuery(document).ready(function() {
        if(!unescape(self.document.location.hash.substring(1))) {
            location.hash = $('#taskNav').find('a').slice(0,1).attr('id')+'Panel';
        }
        loadPanel(location.hash.substr(1,location.hash.indexOf('Panel')-1));
    });");?>
