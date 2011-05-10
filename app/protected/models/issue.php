<?php

/**
 * This is the model class for table "issue".
 *
 * The followings are the available columns in table 'issue':
 * @property string $id
 * @property string $issue_type_fk
 * @property string $parent_fk
 * @property integer $position
 *
 * The followings are the available model relations:
 * @property Goal[] $goals
 * @property Project $parentFk0
 */
class Issue extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Issue the static model class
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
		return 'issue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('position', 'numerical', 'integerOnly'=>true),
			array('issue_type_fk, parent_fk', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, issue_type_fk, parent_fk, position', 'safe', 'on'=>'search'),
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
			'goals' => array(self::HAS_MANY, 'Goal', 'parent_fk'),
			'project' => array(self::BELONGS_TO, 'Project', 'parent_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'issue_type_fk' => 'Issue Type Fk',
			'parent_fk' => 'Parent Fk',
			'position' => 'Position',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('issue_type_fk',$this->issue_type_fk,true);
		$criteria->compare('parent_fk',$this->parent_fk,true);
		$criteria->compare('position',$this->position);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}