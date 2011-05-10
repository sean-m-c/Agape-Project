<?php

class ProjectQueueAction extends CAction
{
    public function run()
    {
        $tabs=array();

        $count = array(
          'needApproval'=>NotificationCount::needApproval(),
          'needReviewers'=>NotificationCount::needReviewers(),
          'inReview'=>NotificationCount::inReview(),
          'needFinalReview'=>NotificationCount::needFinalReview(),
          'sentToPartner'=>NotificationCount::sentToPartner()
        );
        // Set up tabs; if there are projects, show the tabs, otherwise no.
        if($count['needApproval']) {
            $tabs['Need Approval ('.$count['needApproval'].')']
            =array('ajax'=>array('/workflow/needApproval'));
        }
        if($count['needReviewers']) {
            $tabs['Need Reviewers ('.$count['needReviewers'].')']
            =array('ajax'=>array('/workflow/needReviewers'));
        }
        if($count['inReview']) {
            $tabs['In Review ('.$count['inReview'].')']
            =array('ajax'=>array('/workflow/inReview'));
        }
        if($count['needFinalReview']) {
            $tabs['Need Final Review ('.$count['needFinalReview'].')']
            =array('ajax'=>array('/workflow/needFinalReview'));
        }
        if($count['sentToPartner']) {
            $tabs['Sent to Partner ('.$count['sentToPartner'].')']
            =array('ajax'=>array('/workflow/sentToPartner'));
        }

        $this->controller->renderPartial('/workflow/projectQueue',array('tabs'=>$tabs),false,true);
    }
}
?>
