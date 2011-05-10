<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SearchForm extends CFormModel {

    public $keywords;
    public $start_date;
    public $end_date;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules() {
        return array(
            // k required
           // array('keywords', 'required')
           array('start_date,end_date','required','on'=>'statistics'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'search' => 'Search',
            'start_date' => 'Start',
            'end_date' => 'End',
        );
    }

    public function beforeValidate() {
        $dates = array('start_date','end_date');
        foreach($dates as $date) {
            if(isset($this->$date)) {
                $this->$date = date('Y-m-d',strtotime($this->$date));
            }
        }
        return true;
    }

}
