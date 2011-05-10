<?php

/*
 * EDbiCal is free software. It is released under the terms of the following BSD
   License.
 *
 * Copyright © 2011 by Sean Clark <smclark89@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
   modification, are permitted provided that the following conditions are met:
 *
 *    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer
 *      in the documentation and/or other materials provided with the
	distribution.
 *    * Neither the name of SummerCode.ru nor the names of its contributors may
      be used to endorse or promote products derived from
 *      this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
   AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
   A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENTSHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
   DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
   SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
   WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
   OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'dbiCal.class.php');

class EDbiCal extends CComponent {

    /**
     * Enable dbiCal logging
     *
     * @var bool logFlag */
    public $logFlag=false;

    /**
    * The internal dbiCal object.
    *
    * @var object dbiCal */
   private $_dbiCal;


   /**
    * Init method for the application component mode. */
   public function init() {}


   /**
    * Constructor. Here the instance of dbiCal is created. */
	public function __construct() {
		$this->_dbiCal= new
		dbiCal(Yii::app()->getDb()->connectionString, false);
		var_dump(Yii::app()->getDb()->connectionString);
	}


    /**
    * Call a dbiCal method
    *
    * @param string $method the method to call
    * @param array $params the parameters
    * @return mixed */
	public function __call($method, $params) {
	    if (is_object($this->_dbiCal) &&
	    get_class($this->_dbiCal)==='dbiCal') return
	    call_user_func_array(array($this->_dbiCal, $method), $params); else
	    throw new CException(Yii::t('EDbiCal', 'Can not call a method of a
	    non existent object'));
	}

   /**
    * Setter
    *
    * @param string $name the property name
    * @param string $value the property value */
	public function __set($name, $value) {
	   if (is_object($this->_dbiCal) &&
	   get_class($this->_dbiCal)==='dbiCal') $this->_dbiCal->$name = $value;
	   else throw new CException(Yii::t('EDbiCal', 'Can not set a property
	   of a non existent object'));
	}

   /**
    * Getter
    *
    * @param string $name
    * @return mixed */
	public function __get($name) {
	   if (is_object($this->_dbiCal) &&
	   get_class($this->_dbiCal)==='dbiCal') return $this->_dbiCal->$name;
	   else throw new CException(Yii::t('EDbiCal', 'Can not access a
	   property of a non existent object'));
	}


}