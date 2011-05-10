<?php

/**
 * This is the model class for table "need_clearance".
 *
 * The followings are the available columns in table 'need_clearance':
 * @property string $needs_clearance_oid
 * @property string $project_fk
 * @property string $clearance_fk
 */
class NeedClearance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return NeedClearance the static model class
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
		return 'need_clearance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('project_fk, clearance_fk', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('needs_clearance_oid, project_fk, clearance_fk', 'safe', 'on'=>'search'),
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
			'clearance' => array(self::BELONGS_TO, 'Clearance', 'clearance_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'needs_clearance_oid' => 'Needs Clearance ID',
			'project_fk' => 'Project Fk',
			'clearance_fk' => 'Clearance Fk',
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

		$criteria->compare('needs_clearance_oid',$this->needs_clearance_oid,true);

		$criteria->compare('project_fk',$this->project_fk,true);

		$criteria->compare('clearance_fk',$this->clearance_fk,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}