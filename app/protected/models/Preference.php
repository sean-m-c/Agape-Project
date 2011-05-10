<?php

/**
 * This is the model class for table "preference".
 */
class Preference extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'preference':
	 * @var string $preference_oid
	 * @var string $first_name
	 * @var string $last_name
	 * @var string $email
	 * @var string $project_fk
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return Preference the static model class
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
		return 'preference';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_fk', 'required'),
			array('first_name, last_name', 'length', 'max'=>50),
			array('email', 'length', 'max'=>80),
			array('project_fk', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('preference_oid, first_name, last_name, email, project_fk', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'preference_oid' => 'Preference ID',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'email' => 'Email',
			'project_fk' => 'Project Fk',
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

		$criteria->compare('preference_oid',$this->preference_oid,true);

		$criteria->compare('first_name',$this->first_name,true);

		$criteria->compare('last_name',$this->last_name,true);

		$criteria->compare('email',$this->email,true);

		$criteria->compare('project_fk',$this->project_fk,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}