<?php

/**
 * This is the model class for table "makes_review".
 */
class MakesReview extends CActiveRecord
{
    public $email;
	/**
	 * The followings are the available columns in table 'makes_review':
	 * @var string $makes_review_oid
	 * @var string $user_fk
	 * @var string $project_fk
	 * @var integer $decision
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return MakesReview the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'makes_review';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_fk, project_fk', 'required'),
                        array('email', 'email'),
			array('decision', 'numerical', 'integerOnly'=>true),
			array('user_fk, project_fk', 'length', 'max'=>20),
                        array('project_fk+user_fk', 'application.extensions.uniqueMultiColumnValidator',
                            'message'=>'This user is already assigned as a reviewer.'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('makes_review_oid, user_fk, project_fk, decision', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'project' => array(self::BELONGS_TO, 'Project', 'project_fk'),
			'user' => array(self::BELONGS_TO, 'User', 'user_fk'),
			'review' => array(self::HAS_MANY, 'Review', 'makes_review_fk'),
            'reviewableProjects' => array(self::BELONGS_TO, 'Project','project_fk',
                'condition'=>'status=3'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'makes_review_oid' => 'Makes Review Oid',
			'user_fk' => 'User Fk',
			'project_fk' => 'Project Fk',
			'decision' => 'Decision',
                        'email'=>'Email',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('makes_review_oid',$this->makes_review_oid,true);

		$criteria->compare('user_fk',$this->user_fk,true);

		$criteria->compare('project_fk',$this->project_fk,true);

		$criteria->compare('decision',$this->decision);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}