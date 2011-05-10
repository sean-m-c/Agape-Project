<?php

/**
 * This is the model class for table "Tab".
 */
class Tab extends CActiveRecord {
    /**
     * The followings are the available columns in table 'Tab':
     * @var string $tab_oid
     * @var string $name
     * @var integer $enabled
     */

    /**
     * Returns the static model of the specified AR class.
     * @return Tab the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tab';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array('enabled', 'numerical', 'integerOnly'=>true),
                array('name', 'length', 'max'=>50),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('tab_oid, name, enabled', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'review' => array(self::HAS_MANY, 'Review', 'tab_fk'),
                'tabNote' => array(self::HAS_MANY, 'TabNote', 'tab_fk'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
                'tab_oid' => 'Tab ID',
                'name' => 'Name',
                'enabled' => 'Enabled',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('tab_oid',$this->tab_oid,true);

        $criteria->compare('name',$this->name,true);

        $criteria->compare('enabled',$this->enabled);

        return new CActiveDataProvider(get_class($this), array(
                        'criteria'=>$criteria,
        ));
    }

    public function scopes() {
        return array(
                'createTab'=>array(
                        'condition'=>'name="General"',
                ),
        );
    }

}