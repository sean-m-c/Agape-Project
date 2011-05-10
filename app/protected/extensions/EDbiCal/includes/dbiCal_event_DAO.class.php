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
 * This file implements the event table DAO
 *
**/
class dbiCal_event_DAO {
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
   * @since    1.0 - 2010-10-30
   */
  private function __construct( & $_db, & $_log ) {
    $this->_db = $_db;
    if( $_log )
      $this->_log = $_log;
    if( $this->_log )
      $this->_log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
    self::$singleProperties = array( 'CLASS', 'CREATED', 'DESCRIPTION', 'DTEND', 'DTSTAMP', 'DTSTART'
                             , 'DURATION', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                             , 'PRIORITY', 'RECURRENCE-ID', 'SEQUENCE'
                             , 'STATUS', 'SUMMARY', 'TRANSP', 'UID', 'URL', );
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
      self::$theInstance = new dbiCal_event_DAO( $_db, $_log  );
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
    $fromWhere = 'FROM event WHERE event_owner_id = '.$this->_db->quote( $calendar_id, 'integer' );
    $sql  = "SELECT event_id, event_cnt_mtext, event_cnt_attach, event_cnt_exdate, event_cnt_exrule, event_cnt_rdate, event_cnt_rrule, event_cnt_xprop, event_cnt_alarm $fromWhere";
    $res  = & $this->_db->query( $sql, array_fill( 0, 9, 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $cnt = array();
      $cnt['event_cnt_mtext']  = ( isset( $tablerow['event_cnt_mtext'] )  && !empty( $tablerow['event_cnt_mtext'] ))  ? $tablerow['event_cnt_mtext']  : 0;
      $cnt['event_cnt_attach'] = ( isset( $tablerow['event_cnt_attach'] ) && !empty( $tablerow['event_cnt_attach'] )) ? $tablerow['event_cnt_attach'] : 0;
      $cnt['event_cnt_exdate'] = ( isset( $tablerow['event_cnt_exdate'] ) && !empty( $tablerow['event_cnt_exdate'] )) ? $tablerow['event_cnt_exdate'] : 0;
      $cnt['event_cnt_exrule'] = ( isset( $tablerow['event_cnt_exrule'] ) && !empty( $tablerow['event_cnt_exrule'] )) ? $tablerow['event_cnt_exrule'] : 0;
      $cnt['event_cnt_rdate']  = ( isset( $tablerow['event_cnt_rdate'] )  && !empty( $tablerow['event_cnt_rdate'] ))  ? $tablerow['event_cnt_rdate']  : 0;
      $cnt['event_cnt_rrule']  = ( isset( $tablerow['event_cnt_rrule'] )  && !empty( $tablerow['event_cnt_rrule'] ))  ? $tablerow['event_cnt_rrule']  : 0;
      $cnt['event_cnt_xprop']  = ( isset( $tablerow['event_cnt_xprop'] )  && !empty( $tablerow['event_cnt_xprop'] ))  ? $tablerow['event_cnt_xprop']  : 0;
      $cnt['event_cnt_alarm']  = ( isset( $tablerow['event_cnt_alarm'] )  && !empty( $tablerow['event_cnt_alarm'] ))  ? $tablerow['event_cnt_alarm']  : 0;
      $result[$tablerow['event_id']] = $cnt;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_alarm_DAO     = dbiCal_alarm_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO     = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $event_id => $event_cnts ) {
      $res = $dbiCal_parameter_DAO->delete( $event_id, 'event' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $event_cnts['event_cnt_alarm'] )) {
        $res = $dbiCal_alarm_DAO->delete( $event_id, 'event' );
        if( PEAR::isError( $res ))
          return $res;
      }
      if( empty( $event_cnts['event_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->delete( $event_id, 'event' );
      if( PEAR::isError( $res ))
        return $res;
    }
    $multiProps = array( 'mtext', 'attach', 'rexrule', 'exdate', 'rdate' );
    foreach( $multiProps as $mProp ) {
      foreach( $result as $event_id => $event_cnts ) {
        if(( 'mtext'       == $mProp ) && empty( $event_cnts['event_cnt_mtext'] ))
          continue;
        elseif(( 'attach'  == $mProp ) && empty( $event_cnts['event_cnt_attach'] ))
          continue;
        elseif(( 'rexrule' == $mProp ) && empty( $event_cnts['event_cnt_exrule'] ) && empty( $event_cnts['event_cnt_rrule'] ))
          continue;
        elseif(( 'exdate'  == $mProp ) && empty( $event_cnts['event_cnt_exdate'] ))
          continue;
        elseif(( 'rdate'   == $mProp ) && empty( $event_cnts['event_cnt_rdate'] ))
          continue;
        $prop_DAO_name = 'dbiCal_'.$mProp.'_DAO';
        $prop_DAO = $prop_DAO_name::singleton( $this->_db, $this->_log );
        $res = $prop_DAO->delete( $event_id, 'event' );
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
   * @param    array  $event_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
   public function insert( $calendar_id, & $event_values= array(), $calendar_order=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array();
    foreach( $event_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ) || ( 'ono' == $prop ))
        continue;
      if( in_array( $prop, self::$mtextProperties ))
        $cnts['mtext'] = isset( $cnts['mtext'] ) ? ( $cnts['mtext'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]   = isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    $sql       = 'INSERT INTO event (event_owner_id';
    $values    = ') VALUES ('.$this->_db->quote( $calendar_id, 'integer' );
    if( isset( $calendar_order )) {
      $sql    .= ', event_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $event_values['UID']['value'] )) {
      $sql    .= ', event_uid';
      $values .= ', '.$this->_db->quote( $event_values['UID']['value'], 'text' );
    }
    if( isset( $event_values['DTSTAMP']['value'] )) {
      $sql    .= ', event_dtstamp';
      $value   = sprintf("%04d-%02d-%02d", $event_values['DTSTAMP']['value']['year'], $event_values['DTSTAMP']['value']['month'], $event_values['DTSTAMP']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $event_values['DTSTAMP']['value']['hour'], $event_values['DTSTAMP']['value']['min'], $event_values['DTSTAMP']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $event_values['DTSTART']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $event_values['DTSTART']['value']['year'], $event_values['DTSTART']['value']['month'], $event_values['DTSTART']['value']['day']);
      if( isset( $event_values['DTSTART']['value']['hour'] )) {
        $sql    .= ', event_startdatetime';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $event_values['DTSTART']['value']['hour'], $event_values['DTSTART']['value']['min'], $event_values['DTSTART']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $event_values['DTSTART']['value']['tz'] ) &&  in_array( $event_values['DTSTART']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', event_startdatetimeutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', event_startdate';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $event_values['DTEND']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $event_values['DTEND']['value']['year'], $event_values['DTEND']['value']['month'], $event_values['DTEND']['value']['day']);
      if( isset( $event_values['DTEND']['value']['hour'] )) {
        $sql    .= ', event_enddatetime';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $event_values['DTEND']['value']['hour'], $event_values['DTEND']['value']['min'], $event_values['DTEND']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $event_values['DTEND']['value']['tz'] ) &&  in_array( $event_values['DTEND']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', event_enddatetimeutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', event_enddate';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $event_values['DURATION']['value'] )) {
      $sql    .= ', event_duration';
      $value = '';
      foreach( $event_values['DURATION']['value'] as $k => $v )
        $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
      $values .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $event_values['SUMMARY']['value'] )) {
      $sql    .= ', event_summary';
      $values .= ', '.$this->_db->quote( $event_values['SUMMARY']['value'], 'text' );
    }
    if( isset( $event_values['DESCRIPTION']['value'] )) {
      $sql    .= ', event_description';
      $values .= ', '.$this->_db->quote( $event_values['DESCRIPTION']['value'], 'text' );
    }
    if( isset( $event_values['GEO']['value'] )) {
      $sql    .= ', event_geo';
      $value = '';
      foreach( $event_values['GEO']['value'] as $k => $v )
        $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
      $values .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $event_values['LOCATION']['value'] )) {
      $sql    .= ', event_location';
      $values .= ', '.$this->_db->quote( $event_values['LOCATION']['value'], 'text' );
    }
    if( isset( $event_values['ORGANIZER']['value'] )) {
      $sql    .= ', event_organizer';
      $values .= ', '.$this->_db->quote( $event_values['ORGANIZER']['value'], 'text' );
    }
    if( isset( $event_values['CLASS']['value'] )) {
      $sql    .= ', event_class';
      $values .= ', '.$this->_db->quote( $event_values['CLASS']['value'], 'text' );
    }
    if( isset( $event_values['TRANSP']['value'] )) {
      $sql    .= ', event_transp';
      $values .= ', '.$this->_db->quote( $event_values['TRANSP']['value'], 'text' );
    }
    if( isset( $event_values['STATUS']['value'] )) {
      $sql    .= ', event_status';
      $values .= ', '.$this->_db->quote( $event_values['STATUS']['value'], 'text' );
    }
    if( isset( $event_values['URL']['value'] )) {
      $sql    .= ', event_url';
      $values .= ', '.$this->_db->quote( $event_values['URL']['value'], 'text' );
    }
    if( isset( $event_values['PRIORITY']['value'] )) {
      $sql    .= ', event_priority';
      $values .= ', '.$this->_db->quote( $event_values['PRIORITY']['value'], 'integer' );
    }
    if( isset( $event_values['RECURRENCE-ID']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $event_values['RECURRENCE-ID']['value']['year'], $event_values['RECURRENCE-ID']['value']['month'], $event_values['RECURRENCE-ID']['value']['day']);
      if( isset( $event_values['RECURRENCE-ID']['value']['hour'] )) {
        $sql    .= ', event_recurrence_id_dt';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $event_values['RECURRENCE-ID']['value']['hour'], $event_values['RECURRENCE-ID']['value']['min'], $event_values['RECURRENCE-ID']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $event_values['RECURRENCE-ID']['value']['tz'] ) &&  in_array( $event_values['RECURRENCE-ID']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', event_recurrence_idutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', event_recurrence_id';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $event_values['SEQUENCE']['value'] )) {
      $sql    .= ', event_sequence';
      $values .= ', '.$this->_db->quote( $event_values['SEQUENCE']['value'], 'integer' );
    }
    if( isset( $event_values['CREATED']['value'] )) {
      $sql    .= ', event_created';
      $value   = sprintf("%04d-%02d-%02d", $event_values['CREATED']['value']['year'], $event_values['CREATED']['value']['month'], $event_values['CREATED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $event_values['CREATED']['value']['hour'], $event_values['CREATED']['value']['min'], $event_values['CREATED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $event_values['LAST-MODIFIED']['value'] )) {
      $sql    .= ', event_last_modified';
      $value   = sprintf("%04d-%02d-%02d", $event_values['LAST-MODIFIED']['value']['year'], $event_values['LAST-MODIFIED']['value']['month'], $event_values['LAST-MODIFIED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $event_values['LAST-MODIFIED']['value']['hour'], $event_values['LAST-MODIFIED']['value']['min'], $event_values['LAST-MODIFIED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    foreach( $cnts as $prop => $cnt ) {
      if( !empty( $cnt )) {
        $sql    .= ', event_cnt_'.strtolower( $prop );
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
    $event_id = $this->_db->lastInsertID( 'event', 'event_id' );
    if( PEAR::isError( $event_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$event_id->getUserInfo().PHP_EOL.$event_id->getMessage(), PEAR_LOG_ALERT );
      return $event_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'event_id='.$event_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $event_values as $prop => $pValue ) {
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
        $res = $dbiCal_parameter_DAO->insert( $event_id, 'event', $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $event_values['ATTACH'] ) && !empty( $event_values['ATTACH'] )) {
      $dbiCal_attach_DAO = dbiCal_attach_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['ATTACH'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_attach_DAO->insert( $event_id, 'event', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $event_values['EXRULE'] ) && !empty( $event_values['EXRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['EXRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $event_id, 'event', 'EXRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $event_values['RRULE'] ) && !empty( $event_values['RRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['RRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $event_id, 'event', 'RRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $event_values['EXDATE'] ) && !empty( $event_values['EXDATE'] )) {
      $dbiCal_exdate_DAO = dbiCal_exdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['EXDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_exdate_DAO->insert( $event_id, 'event', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $event_values['RDATE'] ) && !empty( $event_values['RDATE'] )) {
      $dbiCal_rdate_DAO = dbiCal_rdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['RDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rdate_DAO->insert( $event_id, 'event', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( self::$mtextProperties as $mProp ) {
      if( isset( $event_values[$mProp] ) && !empty( $event_values[$mProp] )) {
        foreach( $event_values[$mProp] as $themProp ) {
          if( isset( $themProp ) && !empty( $themProp['value'] )) {
            $res = $dbiCal_mtext_DAO->insert( $event_id, 'event', $mProp, $themProp );
            if( PEAR::isError( $res ))
              return $res;
          }
        }
      }
    }
    if( isset( $event_values['XPROP'] ) && !empty( $event_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $event_id, 'event', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    if( isset( $event_values['ALARM'] ) && !empty( $event_values['ALARM'] )) {
      $dbiCal_alarm_DAO = dbiCal_alarm_DAO::singleton( $this->_db, $this->_log );
      foreach( $event_values['ALARM'] as $aix => $alarmValues ) {
        $res = $dbiCal_alarm_DAO->insert( $event_id, 'event', $alarmValues, ( $aix + 1 ));
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $event_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-01-20
   */
  function select( $calendar_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'SELECT * FROM event WHERE event_owner_id = '.$this->_db->quote( $calendar_id, 'integer' ).' ORDER BY event_id';
    $types = array( 'integer', 'integer', 'integer', 'text', 'timestamp', 'timestamp', 'boolean', 'date', 'timestamp', 'boolean'
                  , 'date', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'integer', 'timestamp'
                  , 'boolean', 'date', 'integer', 'timestamp', 'timestamp' );
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
    $result = $event_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['event_cnt_mtext']  = ( isset( $tablerow['event_cnt_mtext'] )  && !empty( $tablerow['event_cnt_mtext'] ))  ? $tablerow['event_cnt_mtext']  : 0;
      $cnt['event_cnt_attach'] = ( isset( $tablerow['event_cnt_attach'] ) && !empty( $tablerow['event_cnt_attach'] )) ? $tablerow['event_cnt_attach'] : 0;
      $cnt['event_cnt_exdate'] = ( isset( $tablerow['event_cnt_exdate'] ) && !empty( $tablerow['event_cnt_exdate'] )) ? $tablerow['event_cnt_exdate'] : 0;
      $cnt['event_cnt_exrule'] = ( isset( $tablerow['event_cnt_exrule'] ) && !empty( $tablerow['event_cnt_exrule'] )) ? $tablerow['event_cnt_exrule'] : 0;
      $cnt['event_cnt_rdate']  = ( isset( $tablerow['event_cnt_rdate'] )  && !empty( $tablerow['event_cnt_rdate'] ))  ? $tablerow['event_cnt_rdate']  : 0;
      $cnt['event_cnt_rrule']  = ( isset( $tablerow['event_cnt_rrule'] )  && !empty( $tablerow['event_cnt_rrule'] ))  ? $tablerow['event_cnt_rrule']  : 0;
      $cnt['event_cnt_xprop']  = ( isset( $tablerow['event_cnt_xprop'] )  && !empty( $tablerow['event_cnt_xprop'] ))  ? $tablerow['event_cnt_xprop']  : 0;
      $cnt['event_cnt_alarm']  = ( isset( $tablerow['event_cnt_alarm'] )  && !empty( $tablerow['event_cnt_alarm'] ))  ? $tablerow['event_cnt_alarm']  : 0;
      $event_cnts[$tablerow['event_id']] = $cnt;
      if( isset( $tablerow['event_ono'] ))
        $row['ono'] = $tablerow['event_ono'];
      if( isset( $tablerow['event_uid'] )            && !empty( $tablerow['event_uid'] ))
        $row['UID']['value'] = $tablerow['event_uid'];
      if( isset( $tablerow['event_dtstamp'] )        && !empty( $tablerow['event_dtstamp'] )) {
        $dt = str_replace( '-', '', $tablerow['event_dtstamp'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTAMP']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['event_startdatetime'] )  && !empty( $tablerow['event_startdatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['event_startdatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTART']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['event_startdatetimeutc'] )  && !empty( $tablerow['event_startdatetimeutc'] ))
          $row['DTSTART']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['event_startdate'] ) && !empty( $tablerow['event_startdate'] )) {
        $row['DTSTART']['value'] = str_replace( '-', '', $tablerow['event_startdate'] );
      }
      if( isset( $tablerow['event_enddatetime'] )    && !empty( $tablerow['event_enddatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['event_enddatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTEND']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['event_enddatetimeutc'] )  && !empty( $tablerow['event_enddatetimeutc'] ))
          $row['DTEND']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['event_enddate'] )   && !empty( $tablerow['event_enddate'] ))
        $row['DTEND']['value'] = str_replace( '-', '', $tablerow['event_enddate'] );
      if( isset( $tablerow['event_duration'] )       && !empty( $tablerow['event_duration'] )) {
        $durParts = explode( '|', $tablerow['event_duration'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        $row['DURATION']['value'] = iCalUtilityFunctions::_format_duration( $duration );
      }
      if( isset( $tablerow['event_summary'] )        && !empty( $tablerow['event_summary'] ))
        $row['SUMMARY']['value'] = $tablerow['event_summary'];
      if( isset( $tablerow['event_description'] )    && !empty( $tablerow['event_description'] ))
        $row['DESCRIPTION']['value'] = $tablerow['event_description'];

      if( isset( $tablerow['event_geo'] )            && !empty( $tablerow['event_geo'] )) {
        $geoParts = explode( '|', $tablerow['event_geo'] );
        foreach( $geoParts as $geoPart ) {
          list( $key, $value ) = explode( '=', $geoPart, 2 );
          $row['GEO']['value'][$key] = $value;
        }
      }
      if( isset( $tablerow['event_location'] )       && !empty( $tablerow['event_location'] ))
        $row['LOCATION']['value'] = $tablerow['event_location'];
      if( isset( $tablerow['event_organizer'] )      && !empty( $tablerow['event_organizer'] ))
        $row['ORGANIZER']['value'] = $tablerow['event_organizer'];
      if( isset( $tablerow['event_class'] )          && !empty( $tablerow['event_class'] ))
        $row['CLASS']['value'] = $tablerow['event_class'];
      if( isset( $tablerow['event_transp'] )         && !empty( $tablerow['event_transp'] ))
        $row['TRANSP']['value'] = $tablerow['event_transp'];
      if( isset( $tablerow['event_status'] )         && !empty( $tablerow['event_status'] ))
        $row['STATUS']['value'] = $tablerow['event_status'];
      if( isset( $tablerow['event_sequence'] )       && ( !empty( $tablerow['event_sequence'] ) || ( '0' == $tablerow['event_sequence'] )))
        $row['SEQUENCE']['value'] = $tablerow['event_sequence'];
      if( isset( $tablerow['event_url'] )            && !empty( $tablerow['event_url'] ))
        $row['URL']['value'] = $tablerow['event_url'];
      if( isset( $tablerow['event_priority'] )       && !empty( $tablerow['event_priority'] ))
        $row['PRIORITY']['value'] = $tablerow['event_priority'];
      if( isset( $tablerow['event_recurrence_id_dt'] ) && !empty( $tablerow['event_recurrence_id_dt'] )) {
        $dt = str_replace( '-', '', $tablerow['event_recurrence_id_dt'] );
        $dt = str_replace( ':', '', $dt );
        $row['RECURRENCE-ID']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['event_recurrence_idutc'] ) && !empty( $tablerow['event_recurrence_idutc'] ))
          $row['RECURRENCE-ID']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['event_recurrence_id'] ) && !empty( $tablerow['event_recurrence_id'] ))
        $row['RECURRENCE-ID']['value'] = str_replace( '-', '', $tablerow['event_recurrence_id'] );
      if( isset( $tablerow['event_created'] )        && !empty( $tablerow['event_created'] )) {
        $dt = str_replace( '-', '', $tablerow['event_created'] );
        $dt = str_replace( ':', '', $dt );
        $row['CREATED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['event_last_modified'] )  && !empty( $tablerow['event_last_modified'] )) {
        $dt = str_replace( '-', '', $tablerow['event_last_modified'] );
        $dt = str_replace( ':', '', $dt );
        $row['LAST-MODIFIED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      if( !empty( $row ))
        $result[$tablerow['event_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' events', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $event_id => $propVal ) {
      foreach( $propVal as $prop => $pValue ) {
        if( 'ono' == $prop )
          continue;
        $res = $dbiCal_parameter_DAO->select( $event_id, 'event', $prop );
        if( PEAR::isError( $res ))
          return $res;
        $result[$event_id][$prop]['params'] = array();
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$event_id][$prop]['params'][$key] = $value;
        }
      }
    }
    $dbiCal_attach_DAO  = dbiCal_attach_DAO::singleton(  $this->_db, $this->_log );
    $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_exdate_DAO  = dbiCal_exdate_DAO::singleton(  $this->_db, $this->_log );
    $dbiCal_rdate_DAO   = dbiCal_rdate_DAO::singleton(   $this->_db, $this->_log );
    foreach( $result as $event_id => $propval ) {
      if( !empty( $event_cnts[$event_id]['event_cnt_attach'] )) {
        $res = $dbiCal_attach_DAO->select( $event_id, 'event' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$event_id]['ATTACH'] = $res;
      }
      if( !empty( $event_cnts[$event_id]['event_cnt_exrule'] ) ||
          !empty( $event_cnts[$event_id]['event_cnt_rrule'] )) {
        $res = $dbiCal_rexrule_DAO->select( $event_id, 'event' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $propValue )
            $result[$event_id][$propValue['value']['rexrule_type']][] = $propValue;
        }
      }
      if( !empty( $event_cnts[$event_id]['event_cnt_exdate'] )) {
        $res = $dbiCal_exdate_DAO->select( $event_id, 'event' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$event_id]['EXDATE'] = $res;
      }
      if( empty( $event_cnts[$event_id]['event_cnt_rdate'] ))
        continue;
      $res = $dbiCal_rdate_DAO->select( $event_id, 'event' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res ))
          $result[$event_id]['RDATE'] = $res;
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $event_id => $propval ) {
      if( empty( $event_cnts[$event_id]['event_cnt_mtext'] ))
        continue;
      $res = $dbiCal_mtext_DAO->select( $event_id, 'event' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $mpropname => $mpropval ) {
          $result[$event_id][$mpropname] = $mpropval;
        }
      }
    }
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $event_id => $propval ) {
      if( empty( $event_cnts[$event_id]['event_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->select( $event_id, 'event' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
          $result[$event_id][$propName] = $propValue;
      }
    }
    $dbiCal_alarm_DAO = dbiCal_alarm_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $event_id => $propval ) {
      if( empty( $event_cnts[$event_id]['event_cnt_alarm'] ))
        continue;
      $res = $dbiCal_alarm_DAO->select( $event_id, 'event' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res ))
        $result[$event_id]['ALARM'] = $res;
    }
    return $result;
  }
}
?>