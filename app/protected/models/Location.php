<?php

/**
 * This is the model class for table "location".
 *
 * The followings are the available columns in table 'location':
 * @property string $location_oid
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $project_fk
 */
class Location extends CActiveRecord {

    public $latlng;

    public function behaviors() {
        return array('EAdvancedArBehavior' => array(
                'class' => 'application.extensions.EAdvancedArBehavior'));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return Location the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'location';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('latlng, country, city', 'required'),
            array('address_line_1, state, zip', 'required', 'on' => 'national'),
            array('address_line_1, address_line_2', 'length', 'max' => 80),
            array('city, country', 'length', 'max' => 50),
            array('state', 'length', 'max' => 2),
            array('zip', 'length', 'max' => 5),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('location_oid, address_line_1, address_line_2, city, state, zip, country, lat, lng', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'project' => array(self::MANY_MANY, 'Project',
                'project_event_location(location_fk,project_fk)'),
            'communityPartner' => array(self::MANY_MANY, 'CommunityPartner',
                'has_community_partner_location(location_fk,community_partner_fk)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'location_oid' => 'Location ID',
            'address_line_1' => 'Address Line 1',
            'address_line_2' => 'Address Line 2',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'country' => 'Country',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('location_oid', $this->location_oid, true);

        $criteria->compare('address_line_1', $this->address_line_1, true);

        $criteria->compare('address_line_2', $this->address_line_2, true);

        $criteria->compare('city', $this->city, true);

        $criteria->compare('state', $this->state, true);

        $criteria->compare('zip', $this->zip, true);

        $criteria->compare('country', $this->country, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    public function beforeValidate() {
        if (!empty($this->latlng)) {
            $latlng = explode(',', substr($this->latlng, 1, -1));
            $this->lat = $latlng[0];
            $this->lng = $latlng[1];
            return true;
        }
    }

}