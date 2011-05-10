<?php
/**
 * dbiCal
 * ver 3.0
 *
 * The iCal calendar database interface, using PEAR MDB2 package
 *
 * copyright (c) 2011 Kjell-Inge Gustafsson kigkonsult
 * www.kigkonsult.se/index.php
 * ical@kigkonsult.se
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * This file implements the alarm table DAO
 *
**/
class dbiCal_alarm_DAO {
  /**
   * @access   private
   * @var      object
   */
  private static $theInstance;
  /**
   * @access   private
   * @var      object
   */
  private static $singleProperties;
  /**
   * @access   private
   * @var      object
   */
  private static $multiProperties;
  /**
   * @access   private
   * @var      object
   */
  private $_log;
  /**
   * @access   private
   * @var      object
   */
  private $_db;
  /**
   * __construct
   *
   * @access   private
   * @param    object $_db
   * @param    object $_log
   * @return   void
   * @since    1.0 - 2010-11-13
   */
  private function __construct( & $_db, & $_log ) {
    $this->_db = $_db;
    if( $_log )
      $this->_log = $_log;
    if( $this->_log )
      $this->_log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
    self::$singleProperties = array( 'ACTION', 'DESCRIPTION', 'DURATION', 'REPEAT', 'SUMMARY', 'TRIGGER' );
    self::$multiProperties  = array( // property name => db table name
                               'ATTACH'         => 'attach'
                             , 'ATTENDEE'       => 'mtext' );
    self::$theInstance = FALSE;
  }
 /**
   * singleton, getter method for creating/returning the single instance of this class
   *
   * @access   private
   * @param    object $_log (reference)
   * @param    object $_DBconnection (reference)
   * @return   void
   * @since    1.0 - 2010-10-31
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_alarm_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownerType
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-20
   */
  function delete( $owner_id, $ownerType ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere  = 'FROM alarm WHERE alarm_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND alarm_ownertype = '.$this->_db->quote( $ownerType, 'text' );
    $sql  = "SELECT alarm_id, alarm_cnt_attach, alarm_cnt_mtext, alarm_cnt_xprop $fromWhere";
    $res  = & $this->_db->query( $sql, array( 'integer', 'integer', 'integer', 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $cnt = array();
      $cnt['alarm_cnt_attach'] = ( isset( $tablerow['alarm_cnt_attach'] ) && !empty( $tablerow['alarm_cnt_attach'] )) ? $tablerow['alarm_cnt_attach'] : 0;
      $cnt['alarm_cnt_mtext']  = ( isset( $tablerow['alarm_cnt_mtext'] )  && !empty( $tablerow['alarm_cnt_mtext'] ))  ? $tablerow['alarm_cnt_mtext']  : 0;
      $cnt['alarm_cnt_xprop']  = ( isset( $tablerow['alarm_cnt_xprop'] )  && !empty( $tablerow['alarm_cnt_xprop'] ))  ? $tablerow['alarm_cnt_xprop']  : 0;
      $result[$tablerow['alarm_id']] = $cnt;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $alarm_id => $alarm_cnts) {
      $res = $dbiCal_parameter_DAO->delete( $alarm_id, 'alarm' );
      if( PEAR::isError( $res ))
        return $res;
      if( empty( $alarm_cnts['alarm_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->delete( $alarm_id, 'alarm' );
      if( PEAR::isError( $res ))
        return $res;
    }
    foreach( self::$multiProperties as $mProp => $mPropdb ) {
      $prop_DAO_name = 'dbiCal_'.$mPropdb.'_DAO';
      $prop_DAO = $prop_DAO_name::singleton( $this->_db, $this->_log );
      foreach( $result as $alarm_id => $alarm_cnts ) {
        if(( 'ATTACH'   == $mProp ) && empty( $alarm_cnts['alarm_cnt_attach'] ))
          continue;
        if(( 'ATTENDEE' == $mProp ) && empty( $alarm_cnts['alarm_cnt_mtext'] ))
          continue;
        $res = $prop_DAO->delete( $alarm_id, 'alarm' );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $sql  = "DELETE $fromWhere";
    $res  = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    if( $this->_log )
      $this->_log->log( "$sql : $res", PEAR_LOG_INFO ); // show number of affected rows
    return TRUE;
  }
  /**
   * insert
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownerType
   * @param    array  $alarm_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownerType, & $alarm_values= array(), $calendar_order=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array();
    foreach( $alarm_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ))
        continue;
      if( 'ATTENDEE' == $prop )
        $cnts['mtext'] = isset( $cnts['mtext'] ) ? ( $cnts['mtext'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]= isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    $sql       = 'INSERT INTO alarm (alarm_owner_id, alarm_ownertype';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownerType, 'text' );
    if( isset( $calendar_order )) {
      $sql    .= ', alarm_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $alarm_values['ACTION']['value'] )) {
      $sql    .= ', alarm_action';
      $values .= ', '.$this->_db->quote( $alarm_values['ACTION']['value'], 'text' );
    }
    if( isset( $alarm_values['DESCRIPTION']['value'] )) {
      $sql    .= ', alarm_description';
      $values .= ', '.$this->_db->quote( $alarm_values['DESCRIPTION']['value'], 'text' );
    }
    if( isset( $alarm_values['DURATION']['value'] )) {
      $sql    .= ', alarm_duration';
      $value   = '';
      foreach( $alarm_values['DURATION']['value'] as $k => $v )
        $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
      $values  .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $alarm_values['REPEAT']['value'] )) {
      $sql    .= ', alarm_repeat';
      $values .= ', '.$this->_db->quote( $alarm_values['REPEAT']['value'], 'text' );
    }
    if( isset( $alarm_values['SUMMARY']['value'] )) {
      $sql    .= ', alarm_summary';
      $values .= ', '.$this->_db->quote( $alarm_values['SUMMARY']['value'], 'text' );
    }
    if( isset( $alarm_values['TRIGGER']['value'] )) {
      if( isset( $alarm_values['TRIGGER']['value']['year'] )) {
        $sql    .= ', alarm_trigger_datetime';
        $value   = sprintf("%04d-%02d-%02d", $alarm_values['TRIGGER']['value']['year'], $alarm_values['TRIGGER']['value']['month'], $alarm_values['TRIGGER']['value']['day']);
        $value  .= ' '.sprintf("%02d:%02d:%02d", $alarm_values['TRIGGER']['value']['hour'], $alarm_values['TRIGGER']['value']['min'], $alarm_values['TRIGGER']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
      }
      else {
        $sql    .= ', alarm_trigger';
        $value = '';
        foreach( $alarm_values['TRIGGER']['value'] as $k => $v ) {
          if( in_array( strtolower( $k ), array( 'relatedstart', 'before' )) && is_bool( $v ))
            $v = ( $v ) ? 1 : 0;
          $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
        }
        $values .= ', '.$this->_db->quote( $value, 'text' );
      }
    }
    if( !empty( $cnts['ATTACH'] )) {
      $sql    .= ', alarm_cnt_attach';
      $values .= ', '.$this->_db->quote( $cnts['ATTACH'], 'integer' );
    }
    if( !empty( $cnts['mtext'] )) {
      $sql    .= ', alarm_cnt_mtext';
      $values .= ', '.$this->_db->quote( $cnts['mtext'], 'integer' );
    }
    if( !empty( $cnts['XPROP'] )) {
      $sql    .= ', alarm_cnt_xprop';
      $values .= ', '.$this->_db->quote( $cnts['XPROP'], 'integer' );
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $alarm_id = $this->_db->lastInsertID( 'alarm', 'alarm_id' );
    if( PEAR::isError( $alarm_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$alarm_id->getUserInfo().PHP_EOL.$alarm_id->getMessage(), PEAR_LOG_ALERT );
      return $alarm_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'alarm_id='.$alarm_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $alarm_values as $prop => $pValue ) {
      if( !isset( $pValue['params'] ) || empty( $pValue['params'] ))
        continue;
      if( !in_array( $prop, self::$singleProperties ))
        continue;
      if( in_array( $prop, array_keys( self::$multiProperties )) || ( 'X-' == substr( $prop, 0, 2 )))
        continue;
      foreach( $pValue['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $alarm_id, 'alarm', $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    foreach( self::$multiProperties as $mProp => $mPropdb ) {
      if( isset( $alarm_values[$mProp] ) && !empty( $alarm_values[$mProp] )) {
        $prop_DAO_name = 'dbiCal_'.$mPropdb.'_DAO';
        $prop_DAO = $prop_DAO_name::singleton( $this->_db, $this->_log );
        foreach( $alarm_values[$mProp] as $themProp ) {
          if( isset( $themProp['value'] ) && !empty( $themProp['value'] )) {
            if( 'ATTENDEE' == $mProp )
              $res = $prop_DAO->insert( $alarm_id, 'alarm', $mProp, $themProp );
            else
              $res = $prop_DAO->insert( $alarm_id, 'alarm', $themProp );
            if( PEAR::isError( $res ))
              return $res;
          }
        }
      }
    }
    if( isset( $alarm_values['XPROP'] ) && !empty( $alarm_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $alarm_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $alarm_id, 'alarm', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    return $alarm_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownerType
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-01-29
   */
  function select( $owner_id, $ownerType ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'SELECT * FROM alarm WHERE alarm_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND alarm_ownerType = '.$this->_db->quote( $ownerType, 'text' ).' ORDER BY alarm_id';
    $types = array( 'integer', 'integer', 'text', 'integer', 'text',  'text', 'text', 'integer', 'text', 'timestamp', 'text', 'integer', 'integer', 'integer' );
    $res  = & $this->_db->query( $sql, $types );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    elseif( $this->_db->getOption('result_buffering') && ( 1 > $res->numRows())) {
      $res->free();
      return null;
    }
    $result = $alarm_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['alarm_cnt_attach'] = ( isset( $tablerow['alarm_cnt_attach'] ) && !empty( $tablerow['alarm_cnt_attach'] )) ? $tablerow['alarm_cnt_attach'] : 0;
      $cnt['alarm_cnt_mtext']  = ( isset( $tablerow['alarm_cnt_mtext'] )  && !empty( $tablerow['alarm_cnt_mtext'] ))  ? $tablerow['alarm_cnt_mtext']  : 0;
      $cnt['alarm_cnt_xprop']  = ( isset( $tablerow['alarm_cnt_xprop'] )  && !empty( $tablerow['alarm_cnt_xprop'] ))  ? $tablerow['alarm_cnt_xprop']  : 0;
      $alarm_cnts[$tablerow['alarm_id']] = $cnt;
      if( isset( $tablerow['alarm_ono'] ))
        $row['ono'] = $tablerow['alarm_ono'];
      if( isset( $tablerow['alarm_action'] )         && !empty( $tablerow['alarm_action'] ))
        $row['ACTION']['value'] = $tablerow['alarm_action'];
      if( isset( $tablerow['alarm_description'] )    && !empty( $tablerow['alarm_description'] ))
        $row['DESCRIPTION']['value'] = $tablerow['alarm_description'];
      if( isset( $tablerow['alarm_duration'] )       && !empty( $tablerow['alarm_duration'] )) {
        $durParts = explode( '|', $tablerow['alarm_duration'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        $row['DURATION']['value'] = iCalUtilityFunctions::_format_duration( $duration );
      }
      if( isset( $tablerow['alarm_repeat'] )         && !empty( $tablerow['alarm_repeat'] ))
        $row['REPEAT']['value'] = $tablerow['alarm_repeat'];
      if( isset( $tablerow['alarm_summary'] )        && !empty( $tablerow['alarm_summary'] ))
        $row['SUMMARY']['value'] = $tablerow['alarm_summary'];
      if( isset( $tablerow['alarm_trigger_datetime'] )  && !empty( $tablerow['alarm_trigger_datetime'] )) {
        $dt = str_replace( '-', '', $tablerow['alarm_trigger_datetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['TRIGGER']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      elseif( isset( $tablerow['alarm_trigger'] )    &&  !empty( $tablerow['alarm_trigger'] )) {
        $durParts = explode( '|', $tablerow['alarm_trigger'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        if( isset( $duration['relatedStart'] ) && ( 1 != $duration['relatedStart'] ))
          $row['TRIGGER']['params']['RELATED'] = 'END';
        $row['TRIGGER']['value']  = ( isset( $duration['before'] ) && ( 1 == $duration['before'] )) ? '-' : '';
        $row['TRIGGER']['value'] .= iCalUtilityFunctions::_format_duration( $duration );
      }
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      if( !empty( $row ))
        $result[$tablerow['alarm_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' alarms', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $alarm_id => $propVal ) {
      foreach( $propVal as $prop => $pValue ) {
        if( 'ono' == $prop )
          continue;
        $res = $dbiCal_parameter_DAO->select( $alarm_id, 'alarm', $prop );
        if( PEAR::isError( $res ))
          return $res;
        if( !isset( $result[$alarm_id][$prop]['params'] ))
          $result[$alarm_id][$prop]['params'] = array();
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$alarm_id][$prop]['params'][$key] = $value;
        }
      }
    }
    $dbiCal_attach_DAO = dbiCal_attach_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_mtext_DAO  = dbiCal_mtext_DAO::singleton(  $this->_db, $this->_log );
    foreach( $result as $alarm_id => $propval ) {
      if( !empty( $alarm_cnts[$alarm_id]['alarm_cnt_attach'] )) {
        $res = $dbiCal_attach_DAO->select( $alarm_id, 'alarm' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$alarm_id]['ATTACH'] = $res;
      }
      if( !empty( $alarm_cnts[$alarm_id]['alarm_cnt_mtext'] )) {
        $res = $dbiCal_mtext_DAO->select( $alarm_id, 'alarm' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $mpropname => $mpropval )
            $result[$alarm_id][$mpropname] = $mpropval;
        }
      }
    }
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $alarm_id => $propval ) {
      if( empty( $alarm_cnts[$alarm_id]['alarm_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->select( $alarm_id, 'alarm' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
            $result[$alarm_id][$propName] = $propValue;
      }
    }
    return $result;
  }
}
?>