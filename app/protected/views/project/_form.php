<div class="form">
    <?php
    // Renders ajax tabs from project form
    $this->widget('zii.widgets.jui.CJuiTabs', array(
            'tabs'=>$this->enabledTabs($model->id,'_form'),
            'id'=>'projectFormTabs',
            // additional javascript options for the tabs plugin
            'options'=>array(
                    'collapsible'=>false,
                    'tabTemplate'=>'<li><a href="#{href}"><span>#{label}</span></a></li>',
                    'spinner'=>'Loading...',
            ),
    ));?>
</div><!-- form -->
