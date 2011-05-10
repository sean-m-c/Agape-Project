<?php

/**
 * This is the model class for table "has_method".
 *
 * The followings are the available columns in table 'has_method':
 * @property string $has_method_oid
 * @property string $strategy_fk
 * @property string $method_fk
 */
class HasMethod extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HasMethod the static model class
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
		return 'has_method';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('strategy_fk, method_fk', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('has_method_oid, strategy_fk, method_fk', 'safe', 'on'=>'search'),
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
			'strategy' => array(self::BELONGS_TO, 'Strategy', 'strategy_fk'),
			'method' => array(self::BELONGS_TO, 'Method', 'method_fk'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'has_method_oid' => 'Has Method Oid',
			'strategy_fk' => 'Strategy Fk',
			'method_fk' => 'Method Fk',
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

		$criteria->compare('has_method_oid',$this->has_method_oid,true);

		$criteria->compare('strategy_fk',$this->strategy_fk,true);

		$criteria->compare('method_fk',$this->method_fk,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}