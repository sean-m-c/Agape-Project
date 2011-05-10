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
 * This file implements the todo table DAO
 *
**/
class dbiCal_todo_DAO {
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
  private static $mtextProperties;
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
    self::$singleProperties = array( 'CLASS', 'COMPLETED', 'CREATED', 'DESCRIPTION', 'DTSTAMP', 'DTSTART'
                             , 'DURATION', 'DUE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                             , 'PERCENT-COMPLETE', 'PRIORITY', 'RECURRENCE-ID', 'SEQUENCE'
                             , 'STATUS', 'SUMMARY', 'UID', 'URL', );
    self::$mtextProperties  = array( // properties using mtext (DAO)
                               'ATTENDEE', 'CATEGORIES', 'COMMENT', 'CONTACT', 'RELATED-TO', 'RESOURCES', 'REQUEST-STATUS' );
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
      self::$theInstance = new dbiCal_todo_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-20
   */
  function delete( $calendar_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere = 'FROM todo WHERE todo_owner_id = '.$this->_db->quote( $calendar_id, 'integer' );
    $sql  = "SELECT todo_id, todo_cnt_mtext, todo_cnt_attach, todo_cnt_exdate, todo_cnt_exrule, todo_cnt_rdate, todo_cnt_rrule, todo_cnt_xprop, todo_cnt_alarm $fromWhere";
    $types = array_fill( 0, 9, 'integer' );
    $res  = & $this->_db->query( $sql, $types );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $cnt = array();
      $cnt['todo_cnt_mtext']  = ( isset( $tablerow['todo_cnt_mtext'] )  && !empty( $tablerow['todo_cnt_mtext'] ))  ? $tablerow['todo_cnt_mtext']  : 0;
      $cnt['todo_cnt_attach'] = ( isset( $tablerow['todo_cnt_attach'] ) && !empty( $tablerow['todo_cnt_attach'] )) ? $tablerow['todo_cnt_attach'] : 0;
      $cnt['todo_cnt_exdate'] = ( isset( $tablerow['todo_cnt_exdate'] ) && !empty( $tablerow['todo_cnt_exdate'] )) ? $tablerow['todo_cnt_exdate'] : 0;
      $cnt['todo_cnt_exrule'] = ( isset( $tablerow['todo_cnt_exrule'] ) && !empty( $tablerow['todo_cnt_exrule'] )) ? $tablerow['todo_cnt_exrule'] : 0;
      $cnt['todo_cnt_rdate']  = ( isset( $tablerow['todo_cnt_rdate'] )  && !empty( $tablerow['todo_cnt_rdate'] ))  ? $tablerow['todo_cnt_rdate']  : 0;
      $cnt['todo_cnt_rrule']  = ( isset( $tablerow['todo_cnt_rrule'] )  && !empty( $tablerow['todo_cnt_rrule'] ))  ? $tablerow['todo_cnt_rrule']  : 0;
      $cnt['todo_cnt_xprop']  = ( isset( $tablerow['todo_cnt_xprop'] )  && !empty( $tablerow['todo_cnt_xprop'] ))  ? $tablerow['todo_cnt_xprop']  : 0;
      $cnt['todo_cnt_alarm']  = ( isset( $tablerow['todo_cnt_alarm'] )  && !empty( $tablerow['todo_cnt_alarm'] ))  ? $tablerow['todo_cnt_alarm']  : 0;
      $result[$tablerow['todo_id']] = $cnt;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_alarm_DAO     = dbiCal_alarm_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO     = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $todo_id => $todo_cnts ) {
      $res = $dbiCal_parameter_DAO->delete( $todo_id, 'todo' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $todo_cnts['todo_cnt_alarm'] )) {
        $res = $dbiCal_alarm_DAO->delete( $todo_id, 'todo' );
        if( PEAR::isError( $res ))
          return $res;
      }
      if( empty( $todo_cnts['todo_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->delete( $todo_id, 'todo' );
      if( PEAR::isError( $res ))
        return $res;
    }
    $multiProps = array( 'mtext', 'attach', 'rexrule', 'exdate', 'rdate' );
    foreach( $multiProps as $mProp ) {
      foreach( $result as $todo_id => $todo_cnts ) {
        if(( 'mtext'   == $mProp ) && empty( $todo_cnts['todo_cnt_mtext'] ))
          continue;
        if(( 'attach'  == $mProp ) && empty( $todo_cnts['todo_cnt_attach'] ))
          continue;
        if(( 'rexrule' == $mProp ) && empty( $todo_cnts['todo_cnt_exrule'] ) && empty( $todo_cnts['todo_cnt_rrule'] ))
          continue;
        if(( 'exdate'  == $mProp ) && empty( $todo_cnts['todo_cnt_exdate'] ))
          continue;
        if(( 'rdate'   == $mProp ) && empty( $todo_cnts['todo_cnt_rdate'] ))
          continue;
        $prop_DAO_name = 'dbiCal_'.$mProp.'_DAO';
        $prop_DAO = $prop_DAO_name::singleton( $this->_db, $this->_log );
        $res = $prop_DAO->delete( $todo_id, 'todo' );
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
   * @param    int    $calendar_id
   * @param    array  $todo_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $calendar_id, & $todo_values= array(), $calendar_order ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array();
    foreach( $todo_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ) || ( 'ono' == $prop ))
        continue;
      if( in_array( $prop, self::$mtextProperties ))
        $cnts['mtext'] = isset( $cnts['mtext'] ) ? ( $cnts['mtext'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]   = isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    $sql       = 'INSERT INTO todo (todo_owner_id';
    $values    = ') VALUES ('.$this->_db->quote( $calendar_id, 'integer' );
    if( isset( $calendar_order )) {
      $sql    .= ', todo_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $todo_values['UID']['value'] )) {
      $sql    .= ', todo_uid';
      $values .= ', '.$this->_db->quote( $todo_values['UID']['value'], 'text' );
    }
    if( isset( $todo_values['DTSTAMP']['value'] )) {
      $sql    .= ', todo_dtstamp';
      $value   = sprintf("%04d-%02d-%02d", $todo_values['DTSTAMP']['value']['year'], $todo_values['DTSTAMP']['value']['month'], $todo_values['DTSTAMP']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['DTSTAMP']['value']['hour'], $todo_values['DTSTAMP']['value']['min'], $todo_values['DTSTAMP']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $todo_values['DTSTART']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $todo_values['DTSTART']['value']['year'], $todo_values['DTSTART']['value']['month'], $todo_values['DTSTART']['value']['day']);
      if( isset( $todo_values['DTSTART']['value']['hour'] )) {
        $sql    .= ', todo_startdatetime';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['DTSTART']['value']['hour'], $todo_values['DTSTART']['value']['min'], $todo_values['DTSTART']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $todo_values['DTSTART']['value']['tz'] ) &&  in_array( $todo_values['DTSTART']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', todo_startdatetimeutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', todo_startdate';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $todo_values['DUE']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $todo_values['DUE']['value']['year'], $todo_values['DUE']['value']['month'], $todo_values['DUE']['value']['day']);
      if( isset( $todo_values['DUE']['value']['hour'] )) {
        $sql    .= ', todo_duedatetime';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['DUE']['value']['hour'], $todo_values['DUE']['value']['min'], $todo_values['DUE']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $todo_values['DUE']['value']['tz'] ) &&  in_array( $todo_values['DUE']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', todo_duedatetimeutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', todo_duedate';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $todo_values['DURATION']['value'] )) {
      $value   = '';
      foreach( $todo_values['DURATION']['value'] as $k => $v )
        $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
      $sql    .= ', todo_duration';
      $values .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $todo_values['COMPLETED']['value'] )) {
      $sql    .= ', todo_completed';
      $value   = sprintf("%04d-%02d-%02d", $todo_values['COMPLETED']['value']['year'], $todo_values['COMPLETED']['value']['month'], $todo_values['COMPLETED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['COMPLETED']['value']['hour'], $todo_values['COMPLETED']['value']['min'], $todo_values['COMPLETED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $todo_values['SUMMARY']['value'] )) {
      $sql    .= ', todo_summary';
      $values .= ', '.$this->_db->quote( $todo_values['SUMMARY']['value'], 'text' );
    }
    if( isset( $todo_values['DESCRIPTION']['value'] )) {
      $sql    .= ', todo_description';
      $values .= ', '.$this->_db->quote( $todo_values['DESCRIPTION']['value'], 'text' );
    }
    if( isset( $todo_values['GEO']['value'] )) {
      $value = '';
      foreach( $todo_values['GEO']['value'] as $k => $v )
        $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
      $sql    .= ', todo_geo';
      $values .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $todo_values['LOCATION']['value'] )) {
      $sql    .= ', todo_location';
      $values .= ', '.$this->_db->quote( $todo_values['LOCATION']['value'], 'text' );
    }
    if( isset( $todo_values['ORGANIZER']['value'] )) {
      $sql    .= ', todo_organizer';
      $values .= ', '.$this->_db->quote( $todo_values['ORGANIZER']['value'], 'text' );
    }
    if( isset( $todo_values['CLASS']['value'] )) {
      $sql    .= ', todo_class';
      $values .= ', '.$this->_db->quote( $todo_values['CLASS']['value'], 'text' );
    }
    if( isset( $todo_values['STATUS']['value'] )) {
      $sql    .= ', todo_status';
      $values .= ', '.$this->_db->quote( $todo_values['STATUS']['value'], 'text' );
    }
    if( isset( $todo_values['URL']['value'] )) {
      $sql    .= ', todo_url';
      $values .= ', '.$this->_db->quote( $todo_values['URL']['value'], 'text' );
    }
    if( isset( $todo_values['PERCENT-COMPLETE']['value'] )) {
      $sql    .= ', todo_percent_complete';
      $values .= ', '.$this->_db->quote( $todo_values['PERCENT-COMPLETE']['value'], 'integer' );
    }
    if( isset( $todo_values['PRIORITY']['value'] )) {
      $sql    .= ', todo_priority';
      $values .= ', '.$this->_db->quote( $todo_values['PRIORITY']['value'], 'integer' );
    }
    if( isset( $todo_values['RECURRENCE-ID']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $todo_values['RECURRENCE-ID']['value']['year'], $todo_values['RECURRENCE-ID']['value']['month'], $todo_values['RECURRENCE-ID']['value']['day']);
      if( isset( $todo_values['RECURRENCE-ID']['value']['hour'] )) {
        $sql    .= ', todo_recurrence_id_dt';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['RECURRENCE-ID']['value']['hour'], $todo_values['RECURRENCE-ID']['value']['min'], $todo_values['RECURRENCE-ID']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $todo_values['RECURRENCE-ID']['value']['tz'] ) &&  in_array( $todo_values['RECURRENCE-ID']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', todo_recurrence_idutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', todo_recurrence_id';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $todo_values['SEQUENCE']['value'] )) {
      $sql    .= ', todo_sequence';
      $values .= ', '.$this->_db->quote( $todo_values['SEQUENCE']['value'], 'integer' );
    }
    if( isset( $todo_values['CREATED']['value'] )) {
      $sql    .= ', todo_created';
      $value   = sprintf("%04d-%02d-%02d", $todo_values['CREATED']['value']['year'], $todo_values['CREATED']['value']['month'], $todo_values['CREATED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['CREATED']['value']['hour'], $todo_values['CREATED']['value']['min'], $todo_values['CREATED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $todo_values['LAST-MODIFIED']['value'] )) {
      $sql    .= ', todo_last_modified';
      $value   = sprintf("%04d-%02d-%02d", $todo_values['LAST-MODIFIED']['value']['year'], $todo_values['LAST-MODIFIED']['value']['month'], $todo_values['LAST-MODIFIED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $todo_values['LAST-MODIFIED']['value']['hour'], $todo_values['LAST-MODIFIED']['value']['min'], $todo_values['LAST-MODIFIED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    foreach( $cnts as $prop => $cnt ) {
      if( !empty( $cnt )) {
        $sql    .= ', todo_cnt_'.strtolower( $prop );
        $values .= ', '.$this->_db->quote( $cnt, 'integer' );
      }
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $todo_id = $this->_db->lastInsertID( 'todo', 'todo_id' );
    if( PEAR::isError( $todo_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$todo_id->getUserInfo().PHP_EOL.$todo_id->getMessage(), PEAR_LOG_ALERT );
      return $todo_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'todo_id='.$todo_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $todo_values as $prop => $pValue ) {
      if( !isset( $pValue['params'] ) || empty( $pValue['params'] ))
        continue;
      if( in_array( $prop, array( 'ATTACH', 'EXDATE', 'EXRULE', 'RDATE', 'RRULE' )))
        continue;
      if( !in_array( $prop, self::$singleProperties ))
        continue;
      if( in_array( $prop, self::$mtextProperties ) || ( 'X-' == substr( $prop, 0, 2 )))
        continue;
      foreach( $pValue['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $todo_id, 'todo', $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $todo_values['ATTACH'] ) && !empty( $todo_values['ATTACH'] )) {
      $dbiCal_attach_DAO = dbiCal_attach_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['ATTACH'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_attach_DAO->insert( $todo_id, 'todo', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $todo_values['EXRULE'] ) && !empty( $todo_values['EXRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['EXRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $todo_id, 'todo', 'EXRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $todo_values['RRULE'] ) && !empty( $todo_values['RRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['RRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $todo_id, 'todo', 'RRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $todo_values['EXDATE'] ) && !empty( $todo_values['EXDATE'] )) {
      $dbiCal_exdate_DAO = dbiCal_exdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['EXDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_exdate_DAO->insert( $todo_id, 'todo', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $todo_values['RDATE'] ) && !empty( $todo_values['RDATE'] )) {
      $dbiCal_rdate_DAO = dbiCal_rdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['RDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rdate_DAO->insert( $todo_id, 'todo', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( self::$mtextProperties as $mProp ) {
      if( isset( $todo_values[$mProp] ) && !empty( $todo_values[$mProp] )) {
        foreach( $todo_values[$mProp] as $themProp ) {
          if( isset( $themProp['value'] ) && !empty( $themProp['value'] )) {
            $res = $dbiCal_mtext_DAO->insert( $todo_id, 'todo', $mProp, $themProp );
            if( PEAR::isError( $res ))
              return $res;
          }
        }
      }
    }
    if( isset( $todo_values['XPROP'] ) && !empty( $todo_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $todo_id, 'todo', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    if( isset( $todo_values['ALARM'] ) && !empty( $todo_values['ALARM'] )) {
      $dbiCal_alarm_DAO = dbiCal_alarm_DAO::singleton( $this->_db, $this->_log );
      foreach( $todo_values['ALARM'] as $alarmValues ) {
        $res = $dbiCal_alarm_DAO->insert( $todo_id, 'todo', $alarmValues );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $todo_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-02-22
   */
  function select( $calendar_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'SELECT * FROM todo WHERE todo_owner_id = '.$this->_db->quote( $calendar_id, 'integer' ).' ORDER BY todo_id';
    $types = array( 'integer', 'integer', 'integer', 'text', 'timestamp', 'timestamp', 'boolean', 'date'
                  , 'timestamp', 'boolean', 'date', 'text','timestamp', 'text', 'text', 'text', 'text', 'text', 'text'
                  , 'text', 'text', 'integer', 'integer', 'timestamp', 'boolean', 'date', 'integer', 'timestamp', 'timestamp' );
    $types = array_merge( $types, array_fill( count( $types ), 8, 'integer' ));
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
    $result = $todo_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['todo_cnt_mtext']  = ( isset( $tablerow['todo_cnt_mtext'] )  && !empty( $tablerow['todo_cnt_mtext'] ))  ? $tablerow['todo_cnt_mtext']  : 0;
      $cnt['todo_cnt_attach'] = ( isset( $tablerow['todo_cnt_attach'] ) && !empty( $tablerow['todo_cnt_attach'] )) ? $tablerow['todo_cnt_attach'] : 0;
      $cnt['todo_cnt_exdate'] = ( isset( $tablerow['todo_cnt_exdate'] ) && !empty( $tablerow['todo_cnt_exdate'] )) ? $tablerow['todo_cnt_exdate'] : 0;
      $cnt['todo_cnt_exrule'] = ( isset( $tablerow['todo_cnt_exrule'] ) && !empty( $tablerow['todo_cnt_exrule'] )) ? $tablerow['todo_cnt_exrule'] : 0;
      $cnt['todo_cnt_rdate']  = ( isset( $tablerow['todo_cnt_rdate'] )  && !empty( $tablerow['todo_cnt_rdate'] ))  ? $tablerow['todo_cnt_rdate']  : 0;
      $cnt['todo_cnt_rrule']  = ( isset( $tablerow['todo_cnt_rrule'] )  && !empty( $tablerow['todo_cnt_rrule'] ))  ? $tablerow['todo_cnt_rrule']  : 0;
      $cnt['todo_cnt_xprop']  = ( isset( $tablerow['todo_cnt_xprop'] )  && !empty( $tablerow['todo_cnt_xprop'] ))  ? $tablerow['todo_cnt_xprop']  : 0;
      $cnt['todo_cnt_alarm']  = ( isset( $tablerow['todo_cnt_alarm'] )  && !empty( $tablerow['todo_cnt_alarm'] ))  ? $tablerow['todo_cnt_alarm']  : 0;
      $todo_cnts[$tablerow['todo_id']] = $cnt;
      if( isset( $tablerow['todo_ono'] ))
        $row['ono'] = $tablerow['todo_ono'];
      if( isset( $tablerow['todo_uid'] )            && !empty( $tablerow['todo_uid'] ))
        $row['UID']['value'] = $tablerow['todo_uid'];
      if( isset( $tablerow['todo_dtstamp'] )        && !empty( $tablerow['todo_dtstamp'] )) {
        $dt = str_replace( '-', '', $tablerow['todo_dtstamp'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTAMP']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['todo_startdatetime'] )  && !empty( $tablerow['todo_startdatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['todo_startdatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTART']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['todo_startdatetimeutc'] )  && !empty( $tablerow['todo_startdatetimeutc'] ))
          $row['DTSTART']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['todo_startdate'] ) && !empty( $tablerow['todo_startdate'] )) {
        $row['DTSTART']['value'] = str_replace( '-', '', $tablerow['todo_startdate'] );
      }
      if( isset( $tablerow['todo_duedatetime'] )    && !empty( $tablerow['todo_duedatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['todo_duedatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DUE']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['todo_duedatetimeutc'] )  && !empty( $tablerow['todo_duedatetimeutc'] ))
          $row['DUE']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['todo_duedate'] )    && !empty( $tablerow['todo_duedate'] ))
        $row['DUE']['value'] = str_replace( '-', '', $tablerow['todo_duedate'] );
      if( isset( $tablerow['todo_duration'] )       && !empty( $tablerow['todo_duration'] )) {
        $durParts = explode( '|', $tablerow['todo_duration'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        $row['DURATION']['value'] = iCalUtilityFunctions::_format_duration( $duration );
      }
      if( isset( $tablerow['todo_completed'] )      && !empty( $tablerow['todo_completed'] )) {
        $dt = str_replace( '-', '', $tablerow['todo_completed'] );
        $dt = str_replace( ':', '', $dt );
        $row['COMPLETED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['todo_summary'] )        && !empty( $tablerow['todo_summary'] ))
        $row['SUMMARY']['value'] = $tablerow['todo_summary'];
      if( isset( $tablerow['todo_description'] )    && !empty( $tablerow['todo_description'] ))
        $row['DESCRIPTION']['value'] = $tablerow['todo_description'];
      if( isset( $tablerow['todo_geo'] )            && !empty( $tablerow['todo_geo'] )) {
        $geoParts = explode( '|', $tablerow['todo_geo'] );
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durpart, 2 );
          $row['GEO']['value'][$key] = $value;
        }
      }
      if( isset( $tablerow['todo_location'] )       && !empty( $tablerow['todo_location'] ))
        $row['LOCATION']['value'] = $tablerow['todo_location'];
      if( isset( $tablerow['todo_organizer'] )      && !empty( $tablerow['todo_organizer'] ))
        $row['ORGANIZER']['value'] = $tablerow['todo_organizer'];
      if( isset( $tablerow['todo_class'] )          && !empty( $tablerow['todo_class'] ))
        $row['CLASS']['value'] = $tablerow['todo_class'];
      if( isset( $tablerow['todo_status'] )         && !empty( $tablerow['todo_status'] ))
        $row['STATUS']['value'] = $tablerow['todo_status'];
      if( isset( $tablerow['todo_url'] )            && !empty( $tablerow['todo_url'] ))
        $row['URL']['value'] = $tablerow['todo_url'];
      if( isset( $tablerow['todo_percent_complete'] ) && !empty( $tablerow['todo_percent_complete'] ))
        $row['PERCENT-COMPLETE']['value'] = $tablerow['todo_percent_complete'];
      if( isset( $tablerow['todo_priority'] )       && !empty( $tablerow['todo_priority'] ))
        $row['PRIORITY']['value'] = $tablerow['todo_priority'];
      if( isset( $tablerow['todo_recurrence_id_dt'] ) && !empty( $tablerow['todo_recurrence_id_dt'] )) {
        $dt = str_replace( '-', '', $tablerow['todo_recurrence_id_dt'] );
        $dt = str_replace( ':', '', $dt );
        $row['RECURRENCE-ID']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['todo_recurrence_idutc'] ) && !empty( $tablerow['todo_recurrence_idutc'] ))
          $row['RECURRENCE-ID']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['todo_recurrence_id'] ) && !empty( $tablerow['todo_recurrence_id'] ))
        $row['RECURRENCE-ID']['value'] = str_replace( '-', '', $tablerow['todo_recurrence_id'] );
      if( isset( $tablerow['todo_sequence'] )       && ( !empty( $tablerow['todo_sequence'] ) || ( '0' == $tablerow['todo_sequence'] )))
        $row['SEQUENCE']['value'] = $tablerow['todo_sequence'];
      if( isset( $tablerow['todo_created'] )        && !empty( $tablerow['todo_created'] )) {
        $dt = str_replace( '-', '', $tablerow['todo_created'] );
        $dt = str_replace( ':', '', $dt );
        $row['CREATED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['todo_last_modified'] )  && !empty( $tablerow['todo_last_modified'] )) {
        $dt = str_replace( '-', '', $tablerow['last_modified'] );
        $dt = str_replace( ':', '', $dt );
        $row['LAST-MODIFIED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      if( !empty( $row ))
        $result[$tablerow['todo_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' todos', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $todo_id => $propVal ) {
      foreach( $propVal as $prop => $pValue ) {
        if( 'ono' === $prop )
          continue;
        $res = $dbiCal_parameter_DAO->select( $todo_id, 'todo', $prop );
        if( PEAR::isError( $res ))
          return $res;
        $result[$todo_id][$prop]['params'] = array();
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$todo_id][$prop]['params'][$key] = $value;
        }
      }
    }
    $dbiCal_attach_DAO  = dbiCal_attach_DAO::singleton(  $this->_db, $this->_log );
    $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_exdate_DAO  = dbiCal_exdate_DAO::singleton(  $this->_db, $this->_log );
    $dbiCal_rdate_DAO   = dbiCal_rdate_DAO::singleton(   $this->_db, $this->_log );
    foreach( $result as $todo_id => $propval ) {
      if( !empty( $todo_cnts[$todo_id]['todo_cnt_attach'] )) {
        $res = $dbiCal_attach_DAO->select( $todo_id, 'todo' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$todo_id]['ATTACH'] = $res;
      }
      if( !empty( $todo_cnts[$todo_id]['todo_cnt_exrule'] ) ||
          !empty( $todo_cnts[$todo_id]['todo_cnt_rrule'] )) {
        $res = $dbiCal_rexrule_DAO->select( $todo_id, 'todo' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $propValue )
            $result[$todo_id][$propValue['value']['rexrule_type']][] = $propValue;
        }
      }
      if( !empty( $todo_cnts[$todo_id]['todo_cnt_exdate'] )) {
        $res = $dbiCal_exdate_DAO->select( $todo_id, 'todo' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$todo_id]['EXDATE'] = $res;
      }
      if( empty( $todo_cnts[$todo_id]['todo_cnt_rdate'] ))
        continue;
      $res = $dbiCal_rdate_DAO->select( $todo_id, 'todo' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res ))
          $result[$todo_id]['RDATE'] = $res;
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $todo_id => $propval ) {
      if( empty( $todo_cnts[$todo_id]['todo_cnt_mtext'] ))
        continue;
      $res = $dbiCal_mtext_DAO->select( $todo_id, 'todo' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $mpropname => $mpropval ) {
          $result[$todo_id][$mpropname] = $mpropval;
        }
      }
    }
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $todo_id => $propval ) {
      if( empty( $todo_cnts[$todo_id]['todo_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->select( $todo_id, 'todo' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
          $result[$todo_id][$propName] = $propValue;
      }
    }
    $dbiCal_alarm_DAO = dbiCal_alarm_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $todo_id => $propval ) {
      if( empty( $todo_cnts[$todo_id]['todo_cnt_alarm'] ))
        continue;
      $res = $dbiCal_alarm_DAO->select( $todo_id, 'todo' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res ))
        $result[$todo_id]['ALARM'] = $res;
    }
    return $result;
  }
}
?>