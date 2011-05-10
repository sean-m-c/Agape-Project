<?php

/**
 * This is the model class for table "Description".
 *
 * The followings are the available columns in table 'Description':
 * @property string $description_oid
 * @property string $field_name
 * @property string $table_name
 * @property string $text
 */
class Description extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Description the static model class
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
		return 'description';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('field_name, table_name, text', 'required'),
			array('field_name', 'length', 'max'=>100),
			array('table_name', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('description_oid, field_name, table_name, text', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'description_oid' => 'Description Oid',
			'field_name' => 'Field Name',
			'table_name' => 'Table Name',
			'text' => 'Text',
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

		$criteria->compare('description_oid',$this->description_oid,true);

		$criteria->compare('field_name',$this->field_name,true);

		$criteria->compare('table_name',$this->table_name,true);

		$criteria->compare('text',$this->text,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

        public function getDescription($table,$attribute) {
            $text=null;
            var_dump($table->tableSchema); // Trying to get table name from model? or no? maybe nam is more efficient
            $text = $this->find(array(
                'select'=>'text',
                'condition'=>'table_name=:tableName AND field_name=:fieldName',
                'params'=>array(':tableName'=>$table,':fieldName'=>$attribute)));

            if(!empty($text)) {
                return $text;
            } else {
                return false;
            }
        }
}