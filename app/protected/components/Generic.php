<?php

/**
 * Class with application-wide formatting and other useful functions
 */

class Generic extends CComponent {
    
    /**
     * Easy-access backdoor function. Should not be in live version.
     */
    public function backdoorLogin($credential=array('email'=>'cfrey@messiah.edu','password'=>'agape')) {
        if(isset($_GET['backdoor'])) {
            if(isset($_GET['backdoorType'])) {
                $credential=$_GET['backdoorType'];
            }
            array_unshift($credential,'site/login');


            return $credential;
        }
    }


    public function convertFlag($model, $attribute, $flagTags=array()) {
        if (empty($flagTags)) {
            return array('label' => $model->getAttributeLabel($attribute),
                'value' => ($model->$attribute) ? 'Yes' : 'No');
        } else {
            foreach ($flagTags as $flag => $tag) {
                if ($model->$attribute == $flag) {
                    return array('label' => $model->getAttributeLabel($attribute),
                        'value' => $tag);
                }
            }
        }
    }

    public function convertDecision($decision) {
        if ($decision === 0) {
            return "Accept";
        } elseif ($decision == 1) {
            return "Needs revision";
        } elseif ($decision == 2) {
            return "Reject";
        } else {
            return "No decision";
        };
    }

    public function reviewThisProject() {
        $retval = false;

        $makesReview = MakesReview::model()->find(array(
                    'select' => 'makes_review_oid',
                    'condition' => 'project_fk=:projectfk AND user_fk=:userfk',
                    'params' => array(
                        ':projectfk' => $_GET['id'],
                        ':userfk' => Yii::app()->user->id
                    )
                ));

        if (isset($makesReview->makes_review_oid)) {
            $retval = $makesReview->makes_review_oid;
        }


        return $retval;
    }

    public function formatPhone($phone) {
        $retval = NULL;

        if (isset($phone)) {
            switch (strlen($phone)) {
                case 7:
                    $retval = substr($phone, 0, 3) . '-' . substr($phone, 3, 4);
                    break;
                case 10:
                    $retval = substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
                    break;
                case 11:
                    $retval = substr($phone, 0, 1) . '-' . substr($phone, 1, 3) . '-' . substr($phone, 4, 3) . '-' . substr($phone, 7, 4);
                    break;
            }
        }
        return $retval;
    }

    public function convertDate($date) {
        list($year, $month, $day) = split("-", $date);
        return date('M j, Y', mktime(0, 0, 0, $month, $day, $year));
    }

    public function viewed($projectOID) {
        if (Yii::app()->user->aidadmin || Yii::app()->user->adminhead) {
            $model = StateChange::model()->findByPk($projectOID);
            $model->viewed = '1';
            return $model->save();
        }
    }

    public function newState($projectOID, $state) {
        $model = new StateChange;
        $model->state = $state;
        $model->project_fk = $projectOID;
        $model->time = new CDbExpression('NOW()');
        return $model->save();
    }

    function getOdd($i) {
        return ($i % 2) ? 'oddRow' : '';
    }

    function cleanPhone($phone) {
        return preg_replace("/[^0-9A-Za-z]/", "", $phone);
    }

    /**
     *
     * Convert an object to an array
     *
     * @param    object  $object The object to convert
     * @reeturn      array
     *
     */
    function objectToArray($object) {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }
        return array_map('objectToArray', $object);
    }

    // To make debugging easier
    function printArray($array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /**
     * Assigns a given role to user
     */
    function assignRole($role, $name, $id) {
        if ($role == 1) {
            // Assign the role if it hasn't been already
            if (!Yii::app()->authManager->isAssigned($name, $id)) {
                if (Yii::app()->authManager->assign($name, $id)) {
                    Yii::app()->authManager->save();
                }
            }
        } else {
            // Remove the role from the user
            if (Yii::app()->authManager->isAssigned($name, $id)) {
                if (Yii::app()->authManager->revoke($name, $id)) {
                    Yii::app()->authManager->save();
                }
            }
        }
    }

    /**
     * Assigns and deletes all the roles with one function.
     * 
     * @param <object> $model The model containing user information
     * @param <integer> $id    The user's OID
     */
    function assignAllRoles($model, $id) {
        $auth = Yii::app()->authManager; //initializes the authManager


        $cpadminStatus = 0;
        if (Involved::model()->count('is_cpadmin=1 AND user_fk=' . $id) != 0) {
            $cpadminStatus = 1;
        }

        $reviewStatus = 0;
        if (MakesReview::model()->exists('user_fk=' . $id)) {
            $reviewStatus = 1;
        }

        Generic::assignRole($model->is_aidadmin, 'aidadmin', $id);
        Generic::assignRole($cpadminStatus, 'cpadmin', $id);
        Generic::assignRole($model->is_adminhead, 'adminhead', $id);
        Generic::assignRole($model->is_volunteer, 'volunteer', $id);
        Generic::assignRole($reviewStatus, 'reviewer', $id);
    }

    /**
     *
     * @param string $firstName First name of person
     * @param string $lastName  Last name of person
     * @param string $initial   Middle initial of person
     * @return string           Concatenated first name, middle initial, and last name
     */
    public function getFullName($firstName,$lastName,$initial) {
        $mi=' ';       
        if(!empty($initial)) 
            $mi.=$initial.'. ';
          
        return ucwords($firstName).ucwords($mi).ucwords($lastName);
    }

   /**
    *
    * @param object $models Model with days to parse
    * @return array Array of dates for fullCalendar plugin
    */
    public function parseCalendarEvents($models) {
        $ret = array();
        if(is_array($models)) {
            foreach($models as $model) {
                $ret[] = Generic::createCalendarEvent($model);
            }
        } else {
            $ret[] = Generic::createCalendarEvent($models);
        }
        return $ret;

    }

    /**
    *
    * @param object $model Model with days to parse
    * @return array Event object for fullCalendar plugin
    */
   function createCalendarEvent($model) {
        return array(
            'id' => (isset($model->event_oid)) ? $model->event_oid : null,
            'title' => $model->name,
            'start' => $model->start,
            'end' => $model->end,
            'allDay' => false,
        );
    }
}

?>
