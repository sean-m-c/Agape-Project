<?php

/**
 * This is the model class for table "has_community_partner_location".
 *
 * The followings are the available columns in table 'has_community_partner_location':
 * @property string $has_community_partner_location_oid
 * @property string $community_partner_fk
 * @property string $location_fk
 *
 * The followings are the available model relations:
 * @property Location $location
 * @property CommunityPartner $communityPartner
 */
class HasCommunityPartnerLocation extends CActiveRecord {

    public function behaviors() {
        return array('EAdvancedArBehavior' => array(
                'class' => 'application.extensions.EAdvancedArBehavior'));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return HasCommunityPartnerLocation the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'has_community_partner_location';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('community_partner_fk, location_fk', 'length', 'max' => 20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('has_community_partner_location_oid, community_partner_fk, location_fk', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'location' => array(self::BELONGS_TO, 'Location', 'location_fk'),
            'communityPartner' => array(self::BELONGS_TO, 'CommunityPartner', 'community_partner_fk'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'has_community_partner_location_oid' => 'Has Community Partner Location Oid',
            'community_partner_fk' => 'Community Partner Fk',
            'location_fk' => 'Location Fk',
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

        $criteria->compare('has_community_partner_location_oid', $this->has_community_partner_location_oid, true);
        $criteria->compare('community_partner_fk', $this->community_partner_fk, true);
        $criteria->compare('location_fk', $this->location_fk, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

}