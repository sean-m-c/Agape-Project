<?php

/**
 * Dialog box with which a user enters their comment for a project's tab.
 */

class CommentBox extends CWidget {

    public $makesReviewID;
    public $tab_fk;
    public $projectOID;

    public function run() {

        $action='';

        if(isset($this->makesReviewID) && $this->makesReviewID) {
            $count = Review::model()->count('makes_review_fk="'.$this->makesReviewID.'" AND tab_fk="'.$this->tab_fk.'"');
            if($count==0) {
                $model = new Review;
                $action=array('/review/create','projectOID'=>$this->projectOID);
            } else {
                $model = Review::model()->find('makes_review_fk="'.$this->makesReviewID.'" AND tab_fk="'.$this->tab_fk.'"');
                $action=array('/review/update','id'=>$model->review_oid,'projectOID'=>$this->projectOID);
            }
        } else {
            $model = new Review;
            $action=array('/review/create','projectOID'=>$this->projectOID);
        }

        // These get passed along to the form
        $params = array(
                'tab_fk'=>$this->tab_fk,
                'makes_review_fk'=>$this->makesReviewID,
                'action'=>$action,
                'projectOID'=>$this->projectOID);

        $this->render('commentBox',array('model'=>$model,'params'=>$params));
    }



}
?>
