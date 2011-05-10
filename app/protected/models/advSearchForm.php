<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class AdvSearchForm extends CFormModel
{
        public $status; // Project status
        public $communityPartners; // Community Partners
        public $creditBearing;
        public $issueAreas;
        public $startDate;
        public $endDate;
        public $text;
        public $textType;


	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// k required
			//array('keywords', 'required')
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
                    'status'=>'Status',
                    'communityPartners'=>'Community Partner',
                    'creditBearing'=>'Credit Bearing',
                    'startDate'=>'Start Date',
                    'endDate'=>'End Date',
                    'name'=>'Project Name',
			//'search'=>'Search',
		);
	}

}
