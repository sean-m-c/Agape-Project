<?php

class NotificationCount extends CComponent {

    /*
     * All possible roles a user can have.
    */
    public static $roles = array('adminhead','aidadmin','cpadmin','volunteer','reviewer');

    /**
     * Counts notifications for the supplied role(s).
     * @param <mixed> $roles    Roles or function to
     *                          return notification count for.
     * @param <bool> $total     Whether to return total count for role.
     * @return <integer>        Number of notifications for supplied role(s).
     */
    public function count($roles='',$total=false,$params=array()) {
        $count=0;

        if(empty($roles)) {
            $roles = NotificationCount::$roles;
        } elseif(!is_array($roles)) {
            $roles = array($roles);
        }

        if(in_array('adminhead',$roles)) {
            $count += NotificationCount::totaladminhead();
        }
        if(in_array('aidadmin',$roles)) {
            $count += NotificationCount::totalaidadmin();
        }
        if(in_array('cpadmin',$roles)) {
            $count += NotificationCount::totalCpadmin();
        }
        if(in_array('reviewer',$roles)) {
            $count += NotificationCount::totalReviewer();
        }
        if(in_array('volunteer',$roles)) {
            $count += NotificationCount::totalVolunteer();
        }


        return $count;
    }
    /*
     * Aid partner admin head notification count ###################
    */

    /**
     * Counts projects needing final review by aid partner admin head
     */

    public function totaladminhead() {
        $count = 0;
        $count += NotificationCount::needFinalReview();
        $count += NotificationCount::needReviewers();
        $count += NotificationCount::sentToPartner();
        return $count;
    }

    public function needFinalReview() {
        if(Yii::app()->user->checkAccess('adminhead')) {
            $projects = Project::model()->with('reviewersWithoutDecisionCount',
                    'reviewerCount','makesReview')->findAll(array(
                    'select'=>'id',
                    'condition'=>'t.status=3'));

            $query = '';
            foreach($projects as $project) {
                if($project->reviewersWithoutDecisionCount==0) {
                    $query .= 'id='.$project->id.' OR ';
                }
            }
            if(!empty($query)) {
                $query=substr($query,0,-4);
                return Project::model()->count(array('condition'=>$query));
            } else {
                return 0;
            }
  
        }
    }
        /**
     * Counts projects that have been sent back to partners with a final decision.
     */
    public function sentToPartner() {
        if(Yii::app()->user->checkAccess('adminhead')) {
            return Project::model()->count('(status=4 OR status=5 OR status=6)');
        }
    }

    /**
     * Counts projects needing reviewers to be assigned
     */
    public function needReviewers() {
        if(Yii::app()->user->checkAccess('adminhead')) {
            return Project::model()->reviewable()->count();
        }
    }

    /**
     * Counts reviewers assigned to a project
     */
    public function countAssignedReviewers($id) {
        if(Yii::app()->user->checkAccess('adminhead')) {
            return MakesReview::model()->count('project_fk='.$id);
        }
    }

    /*
     * Aid partner admin notification count ########################
    */

    /*
     * Returns all aidadmin notifications
     */
    public function totalaidadmin() {
        $count = 0;
        $count += NotificationCount::inReview();
        $count += NotificationCount::needApproval();
        $count += NotificationCount::pendingCommunityPartners();
        return $count;
    }

    /**
     * Counts projects being reviewed
     */
    public function inReview() {
        if(Yii::app()->user->checkAccess('aidadmin')) {
            return Project::model()->reviewersAssigned()->count();
        }
    }

    /**
     * Counts projects needing not-spam approval
     */
    public function needApproval() {
        if(Yii::app()->user->checkAccess('aidadmin')) {
            return Project::model()->submitted()->count();
        }
    }

    /**
     * Gets pending community partners (aidadmin).
     */
    public function pendingCommunityPartners() {
        if(Yii::app()->user->checkAccess('aidadmin')) {
            return CommunityPartner::model()->pending()->count();
        }
    }

    /*
     * Community partner admin notification count ##################
    */

    /*
     * Returns all community partner admin notifications
    */

    public function totalCpadmin() {
        $count = 0;
        $count += NotificationCount::pendingCommunityPartnerVolunteers();
        return $count;
    }

    public function pendingCommunityPartnerVolunteers() {
        if(Yii::app()->user->checkAccess('cpadmin')) {
            $count=0;
            // Get all CP OIDs that this user is admin for
            $adminCPS = Involved::model()->findAll(array(
                    'select'=>'community_partner_fk',
                    'condition'=>'is_cpadmin=1 AND pending=0 AND user_fk='.Yii::app()->user->id));

            // Get count for each CP listing pending users in involved table
            foreach($adminCPS as $cp) {
                $count = $count + Involved::model()->count('pending=1 AND community_partner_fk='.$cp->community_partner_fk);
            }

            return $count;
        }
    }

    /*
     * Reviewer notification count
    */
    
    public function totalReviewer() {
        $count = 0;
        $count += NotificationCount::needMyReview();
        return $count;
    }
    
    /**
     * Gets projects that a user needs to finish their review decision for
     */
    public function needMyReview() {
        if(Yii::app()->user->checkAccess('reviewer')) {
            return MakesReview::model()->count('user_fk='.Yii::app()->user->id.'
                AND ISNULL(decision)');
        }
    }

    /*
     * Volunteer notification count
    */
    public function totalVolunteer() {
        $count=0;
        return $count;
    }

    public function projectQueue() {
        $count = 0;
        if(Yii::app()->user->checkAccess('aidadmin')) {
            $count = $count + NotificationCount::needApproval();
            $count = $count + NotificationCount::inReview();
        }
        if(Yii::app()->user->checkAccess('adminhead')) {
            $count = $count + NotificationCount::needFinalReview();
            $count = $count + NotificationCount::needReviewers();
        }
        return $count;
    }

}
?>
