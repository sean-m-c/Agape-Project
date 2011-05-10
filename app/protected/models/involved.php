<?php

/**
 * This is the model class for table "involved".
 */
class Involved extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'involved':
	 * @var string $involved_oid
	 * @var string $user_fk
	 * @var string $community_partner_fk
	 * @var integer $pending
	 * @var integer $is_cpadmin
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return Involved the static model class
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
		return 'involved';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_fk, community_partner_fk', 'required'),
			array('pending, is_cpadmin', 'numerical', 'integerOnly'=>true),
			array('user_fk, community_partner_fk', 'length', 'max'=>20),
            array('user_fk+community_partner_fk', 'application.extensions.uniqueMultiColumnValidator',
                'message'=>'You have already connected to this community partner.'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('involved_oid, user_fk, community_partner_fk, pending, is_cpadmin', 'safe', 'on'=>'search'),
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
			'communityPartner' => array(self::BELONGS_TO, 'CommunityPartner', 'community_partner_fk'),
			'user' => array(self::BELONGS_TO, 'User', 'user_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'involved_oid' => 'Involved ID',
			'user_fk' => 'User Fk',
			'community_partner_fk' => 'Community Partner Name',
			'pending' => 'Pending',
			'is_cpadmin' => 'Is Cpadmin',
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

		$criteria->compare('involved_oid',$this->involved_oid,true);

		$criteria->compare('user_fk',$this->user_fk,true);

		$criteria->compare('community_partner_fk',$this->community_partner_fk,true);

		$criteria->compare('pending',$this->pending);

		$criteria->compare('is_cpadmin',$this->is_cpadmin);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        public function scopes()
        {
            return array(
                'pending'=>array(
                    // User hasn't been approved yet to join a community partner
                    'condition'=>'t.pending=1',
                ),
                'approved'=>array(
                  'condition'=>'t.pending=0',
                ),
            );
        }

        public function beforeSave() {
            if(!isset($this->date_applied) || empty($this->date_applied))
                $this->date_applied = new CDbExpression('NOW()');
            
            return true;
        }
}