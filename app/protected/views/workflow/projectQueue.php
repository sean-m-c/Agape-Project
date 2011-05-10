<?php
// Render jQuery tab widget. Clicking on tab redirects to WorkflowController action
$this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=>$tabs,
        'id'=>'submittedWorkflowTabs',
        // additional javascript options for the tabs plugin
        'options'=>array(
                'collapsible'=>false,
        ),
));
?>