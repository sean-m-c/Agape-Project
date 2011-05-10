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
 * This file implements the journal table DAO
 *
**/
class dbiCal_journal_DAO {
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
    self::$singleProperties = array( 'CLASS', 'CREATED', 'DTSTAMP', 'DTSTART', 'LAST-MODIFIED', 'ORGANIZER'
                                   , 'RECURRENCE-ID', 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL', );
    self::$mtextProperties  = array( // properties using mtext (DAO)
                               'ATTENDEE', 'CATEGORIES', 'COMMENT', 'CONTACT', 'DESCRIPTION', 'RELATED-TO', 'REQUEST-STATUS' );
    self::$theInstance = FALSE;
  }
 /**
   * singleton, getter method for creating/returning the single instance of this class
   *
   * @access   private
   * @param    object $_log (reference)
   * @param    object $_DBconnection (reference)
   * @return   void
   * @since    1.0 - 2010-11-20
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_journal_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-13
   */
  function delete( $calendar_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere = 'FROM journal WHERE journal_owner_id = '.$this->_db->quote( $calendar_id, 'integer' );
    $sql  = "SELECT journal_id, journal_cnt_mtext, journal_cnt_attach, journal_cnt_exdate, journal_cnt_exrule, journal_cnt_rdate, journal_cnt_rrule, journal_cnt_xprop $fromWhere";
    $types = array_fill( 0, 8, 'integer' );
    $res  = & $this->_db->query( $sql, $types );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $cnt = array();
      $cnt['journal_cnt_mtext']  = ( isset( $tablerow['journal_cnt_mtext'] )  && !empty( $tablerow['journal_cnt_mtext'] ))  ? $tablerow['journal_cnt_mtext']  : 0;
      $cnt['journal_cnt_attach'] = ( isset( $tablerow['journal_cnt_attach'] ) && !empty( $tablerow['journal_cnt_attach'] )) ? $tablerow['journal_cnt_attach'] : 0;
      $cnt['journal_cnt_exdate'] = ( isset( $tablerow['journal_cnt_exdate'] ) && !empty( $tablerow['journal_cnt_exdate'] )) ? $tablerow['journal_cnt_exdate'] : 0;
      $cnt['journal_cnt_exrule'] = ( isset( $tablerow['journal_cnt_exrule'] ) && !empty( $tablerow['journal_cnt_exrule'] )) ? $tablerow['journal_cnt_exrule'] : 0;
      $cnt['journal_cnt_rdate']  = ( isset( $tablerow['journal_cnt_rdate'] )  && !empty( $tablerow['journal_cnt_rdate'] ))  ? $tablerow['journal_cnt_rdate']  : 0;
      $cnt['journal_cnt_rrule']  = ( isset( $tablerow['journal_cnt_rrule'] )  && !empty( $tablerow['journal_cnt_rrule'] ))  ? $tablerow['journal_cnt_rrule']  : 0;
      $cnt['journal_cnt_xprop']  = ( isset( $tablerow['journal_cnt_xprop'] )  && !empty( $tablerow['journal_cnt_xprop'] ))  ? $tablerow['journal_cnt_xprop']  : 0;
      $result[$tablerow['journal_id']] = $cnt;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $journal_id => $journal_cnts ) {
      $res = $dbiCal_parameter_DAO->delete( $journal_id, 'journal' );
      if( PEAR::isError( $res ))
        return $res;
      if( empty( $journal_cnts['journal_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->delete( $journal_id, 'journal' );
      if( PEAR::isError( $res ))
        return $res;
    }
    $multiProps = array( 'mtext', 'attach', 'rexrule', 'exdate', 'rdate' ); // DAOs
    foreach( $multiProps as $mProp ) {
      foreach( $result as $journal_id => $journal_cnts ) {
        if(( 'mtext'   == $mProp ) && empty( $journal_cnts['journal_cnt_mtext'] ))
          continue;
        if(( 'attach'  == $mProp ) && empty( $journal_cnts['journal_cnt_attach'] ))
          continue;
        if(( 'rexrule' == $mProp ) && empty( $journal_cnts['journal_cnt_exrule'] ) && empty( $journal_cnts['journal_cnt_rrule'] ))
          continue;
        if(( 'exdate'  == $mProp ) && empty( $journal_cnts['journal_cnt_exdate'] ))
          continue;
        if(( 'rdate'   == $mProp ) && empty( $journal_cnts['journal_cnt_rdate'] ))
          continue;
        $prop_DAO_name = 'dbiCal_'.$mProp.'_DAO';
        $prop_DAO = $prop_DAO_name::singleton( $this->_db, $this->_log );
        $res = $prop_DAO->delete( $journal_id, 'journal' );
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
   * @param    array  $journal_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $calendar_id, & $journal_values= array(), $calendar_order=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array();
    foreach( $journal_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ) || ( 'ono' == $prop ))
        continue;
      if( in_array( $prop, self::$mtextProperties ))
        $cnts['mtext'] = isset( $cnts['mtext'] ) ? ( $cnts['mtext'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]   = isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    foreach( $journal_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ) || ( 'ono' == $prop ))
        continue;
      foreach( $propVals as $propv ) {
        if( in_array( $prop, self::$mtextProperties ))
          $cnts['mtext'] += 1;
        else
          $cnts[$prop]   += 1;
      }
    }
    $sql       = 'INSERT INTO journal (journal_owner_id';
    $values    = ') VALUES ('.$this->_db->quote( $calendar_id, 'integer' );
    if( isset( $calendar_order )) {
      $sql    .= ', journal_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $journal_values['UID']['value'] )) {
      $sql    .= ', journal_uid';
      $values .= ', '.$this->_db->quote( $journal_values['UID']['value'], 'text' );
    }
    if( isset( $journal_values['DTSTAMP']['value'] )) {
      $sql    .= ', journal_dtstamp';
      $value   = sprintf("%04d-%02d-%02d", $journal_values['DTSTAMP']['value']['year'], $journal_values['DTSTAMP']['value']['month'], $journal_values['DTSTAMP']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $journal_values['DTSTAMP']['value']['hour'], $journal_values['DTSTAMP']['value']['min'], $journal_values['DTSTAMP']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $journal_values['DTSTART']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $journal_values['DTSTART']['value']['year'], $journal_values['DTSTART']['value']['month'], $journal_values['DTSTART']['value']['day']);
      if( isset( $journal_values['DTSTART']['value']['hour'] )) {
        $sql    .= ', journal_startdatetime';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $journal_values['DTSTART']['value']['hour'], $journal_values['DTSTART']['value']['min'], $journal_values['DTSTART']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $journal_values['DTSTART']['value']['tz'] ) &&  in_array( $journal_values['DTSTART']['value']['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', journal_startdatetimeutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', journal_startdate';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $journal_values['SUMMARY']['value'] )) {
      $sql    .= ', journal_summary';
      $values .= ', '.$this->_db->quote( $journal_values['SUMMARY']['value'], 'text' );
    }
    if( isset( $journal_values['ORGANIZER']['value'] )) {
      $sql    .= ', journal_organizer';
      $values .= ', '.$this->_db->quote( $journal_values['ORGANIZER']['value'], 'text' );
    }
    if( isset( $journal_values['CLASS']['value'] )) {
      $sql    .= ', journal_class';
      $values .= ', '.$this->_db->quote( $journal_values['CLASS']['value'], 'text' );
    }
    if( isset( $journal_values['URL']['value'] )) {
      $sql    .= ', journal_url';
      $values .= ', '.$this->_db->quote( $journal_values['URL']['value'], 'text' );
    }
    if( isset( $journal_values['RECURRENCE-ID']['value'] )) {
      $value = sprintf("%04d-%02d-%02d", $journal_values['RECURRENCE-ID']['value']['year'], $journal_values['RECURRENCE-ID']['value']['month'], $journal_values['RECURRENCE-ID']['value']['day']);
      if( isset( $journal_values['RECURRENCE-ID']['value']['hour'] )) {
        $sql    .= ', journal_recurrence_id_dt';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $journal_values['RECURRENCE-ID']['value']['hour'], $journal_values['RECURRENCE-ID']['value']['min'], $journal_values['RECURRENCE-ID']['value']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $journal_values['RECURRENCE']['value']['tz'] ) &&  in_array( $journal_values['RECURRENCE']['value']['tz'], array( 'Z', 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql    .= ', journal_recurrence_idutc';
          $values .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql    .= ', journal_recurrence_id';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $journal_values['SEQUENCE']['value'] )) {
      $sql    .= ', journal_sequence';
      $values .= ', '.$this->_db->quote( $journal_values['SEQUENCE']['value'], 'integer' );
    }
    if( isset( $journal_values['STATUS']['value'] )) {
      $sql    .= ', journal_status';
      $values .= ', '.$this->_db->quote( $journal_values['STATUS']['value'], 'text' );
    }
    if( isset( $journal_values['CREATED']['value'] )) {
      $sql    .= ', journal_created';
      $value   = sprintf("%04d-%02d-%02d", $journal_values['CREATED']['value']['year'], $journal_values['CREATED']['value']['month'], $journal_values['CREATED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $journal_values['CREATED']['value']['hour'], $journal_values['CREATED']['value']['min'], $journal_values['CREATED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $journal_values['LAST-MODIFIED']['value'] )) {
      $sql    .= ', journal_last_modified';
      $value   = sprintf("%04d-%02d-%02d", $journal_values['LAST-MODIFIED']['value']['year'], $journal_values['LAST-MODIFIED']['value']['month'], $journal_values['LAST-MODIFIED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $journal_values['LAST-MODIFIED']['value']['hour'], $journal_values['LAST-MODIFIED']['value']['min'], $journal_values['LAST-MODIFIED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    foreach( $cnts as $prop => $cnt ) {
      if( !empty( $cnt )) {
        $sql    .= ', journal_cnt_'.strtolower( $prop );
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
    $journal_id = $this->_db->lastInsertID( 'journal', 'journal_id' );
    if( PEAR::isError( $journal_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$journal_id->getUserInfo().PHP_EOL.$journal_id->getMessage(), PEAR_LOG_ALERT );
      return $journal_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'journal_id='.$journal_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $journal_values as $prop => $pValue ) {
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
        $res = $dbiCal_parameter_DAO->insert( $journal_id, 'journal', $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $journal_values['EXRULE'] ) && !empty( $journal_values['EXRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $journal_values['EXRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $journal_id, 'journal', 'EXRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $journal_values['RRULE'] ) && !empty( $journal_values['RRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $journal_values['RRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $journal_id, 'journal', 'RRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $journal_values['EXDATE'] ) && !empty( $journal_values['EXDATE'] )) {
      $dbiCal_exdate_DAO = dbiCal_exdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $journal_values['EXDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_exdate_DAO->insert( $journal_id, 'journal', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $journal_values['RDATE'] ) && !empty( $journal_values['RDATE'] )) {
      $dbiCal_rdate_DAO = dbiCal_rdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $journal_values['RDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rdate_DAO->insert( $journal_id, 'journal', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $journal_values['ATTACH'] ) && !empty( $journal_values['ATTACH'] )) {
      $dbiCal_attach_DAO = dbiCal_attach_DAO::singleton( $this->_db, $this->_log );
      foreach( $journal_values['ATTACH'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_attach_DAO->insert( $journal_id, 'journal', $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( self::$mtextProperties as $mProp ) {
      if( isset( $journal_values[$mProp] ) && !empty( $journal_values[$mProp] )) {
        foreach( $journal_values[$mProp] as $themProp ) {
          if( isset( $themProp['value'] ) && !empty( $themProp['value'] )) {
            $res = $dbiCal_mtext_DAO->insert( $journal_id, 'journal', $mProp, $themProp );
            if( PEAR::isError( $res ))
              return $res;
          }
        }
      }
    }
    if( isset( $journal_values['XPROP'] ) && !empty( $journal_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $journal_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $journal_id, 'journal', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    return $journal_id;
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
    $sql  = 'SELECT * FROM journal WHERE journal_owner_id = '.$this->_db->quote( $calendar_id, 'integer' ).' ORDER BY journal_id';
    $types = array( 'integer', 'integer', 'integer', 'text', 'timestamp', 'timestamp', 'boolean', 'date'
                  , 'text', 'text', 'text', 'text', 'timestamp', 'boolean', 'date', 'integer', 'text', 'timestamp', 'timestamp'  );
    $types = array_merge( $types, array_fill( count( $types ), 7, 'integer' ));
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
    $result = $journal_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['journal_cnt_mtext']  = ( isset( $tablerow['journal_cnt_mtext'] )  && !empty( $tablerow['journal_cnt_mtext'] ))  ? $tablerow['journal_cnt_mtext']  : 0;
      $cnt['journal_cnt_attach'] = ( isset( $tablerow['journal_cnt_attach'] ) && !empty( $tablerow['journal_cnt_attach'] )) ? $tablerow['journal_cnt_attach'] : 0;
      $cnt['journal_cnt_exdate'] = ( isset( $tablerow['journal_cnt_exdate'] ) && !empty( $tablerow['journal_cnt_exdate'] )) ? $tablerow['journal_cnt_exdate'] : 0;
      $cnt['journal_cnt_exrule'] = ( isset( $tablerow['journal_cnt_exrule'] ) && !empty( $tablerow['journal_cnt_exrule'] )) ? $tablerow['journal_cnt_exrule'] : 0;
      $cnt['journal_cnt_rdate']  = ( isset( $tablerow['journal_cnt_rdate'] )  && !empty( $tablerow['journal_cnt_rdate'] ))  ? $tablerow['journal_cnt_rdate']  : 0;
      $cnt['journal_cnt_rrule']  = ( isset( $tablerow['journal_cnt_rrule'] )  && !empty( $tablerow['journal_cnt_rrule'] ))  ? $tablerow['journal_cnt_rrule']  : 0;
      $cnt['journal_cnt_xprop']  = ( isset( $tablerow['journal_cnt_xprop'] )  && !empty( $tablerow['journal_cnt_xprop'] ))  ? $tablerow['journal_cnt_xprop']  : 0;
      $journal_cnts[$tablerow['journal_id']] = $cnt;
      if( isset( $tablerow['journal_ono'] ))
        $row['ono'] = $tablerow['journal_ono'];
      if( isset( $tablerow['journal_uid'] )            && !empty( $tablerow['journal_uid'] ))
        $row['UID']['value'] = $tablerow['journal_uid'];
      if( isset( $tablerow['journal_dtstamp'] )        && !empty( $tablerow['journal_dtstamp'] )) {
        $dt = str_replace( '-', '', $tablerow['journal_dtstamp'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTAMP']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['journal_startdatetime'] )  && !empty( $tablerow['journal_startdatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['journal_startdatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTART']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['journal_startdatetimeutc'] ) && !empty( $tablerow['journal_startdatetimeutc'] ))
          $row['DTSTART']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['journal_startdate'] )  && !empty( $tablerow['journal_startdate'] ))
        $row['DTSTART']['value'] = str_replace( '-', '', $tablerow['journal_startdate'] );
     if( isset( $tablerow['journal_summary'] )        && !empty( $tablerow['journal_summary'] ))
        $row['SUMMARY']['value'] = $tablerow['journal_summary'];
      if( isset( $tablerow['journal_description'] )    && !empty( $tablerow['journal_description'] ))
        $row['ORGANIZER']['value'] = $tablerow['journal_organizer'];
      if( isset( $tablerow['journal_class'] )          && !empty( $tablerow['journal_class'] ))
        $row['CLASS']['value'] = $tablerow['journal_class'];
      if( isset( $tablerow['journal_url'] )            && !empty( $tablerow['journal_url'] ))
        $row['URL']['value'] = $tablerow['journal_url'];
      if( isset( $tablerow['journal_recurrence_id_dt'] ) && !empty( $tablerow['journal_recurrence_id_dt'] )) {
        $dt = str_replace( '-', '', $tablerow['journal_recurrence_id_dt'] );
        $dt = str_replace( ':', '', $dt );
        $row['RECURRENCE-ID']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['journal_recurrence_idutc'] )  && !empty( $tablerow['journal_recurrence_idutc'] ))
          $row['RECURRENCE-ID']['value'] .= 'Z';
      }
      elseif( isset( $tablerow['journal_recurrence_id'] ) && !empty( $tablerow['journal_recurrence_id'] ))
        $row['RECURRENCE-ID']['value'] = str_replace( '-', '', $tablerow['journal_recurrence_id'] );
      if( isset( $tablerow['journal_sequence'] )       && ( !empty( $tablerow['journal_sequence'] ) || ( '0' == $tablerow['journal_sequence'])))
        $row['SEQUENCE']['value'] = $tablerow['journal_sequence'];
      if( isset( $tablerow['journal_status'] )         && !empty( $tablerow['journal_status'] ))
        $row['STATUS']['value'] = $tablerow['journal_status'];
      if( isset( $tablerow['journal_created'] )        && !empty( $tablerow['journal_created'] )) {
        $dt = str_replace( '-', '', $tablerow['journal_created'] );
        $dt = str_replace( ':', '', $dt );
        $row['CREATED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['journal_last_modified'] )  && !empty( $tablerow['journal_last_modified'] )) {
        $dt = str_replace( '-', '', $tablerow['journal_last_modified'] );
        $dt = str_replace( ':', '', $dt );
        $row['LAST-MODIFIED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      if( !empty( $row ))
        $result[$tablerow['journal_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' journals', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $journal_id => $propVal ) {
      foreach( $propVal as $prop => $pValue ) {
        if( 'ono' == $prop )
          continue;
        $res = $dbiCal_parameter_DAO->select( $journal_id, 'journal', $prop );
        if( PEAR::isError( $res ))
          return $res;
        $result[$journal_id][$prop]['params'] = array();
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$journal_id][$prop]['params'][$key] = $value;
        }
      }
    }
    $dbiCal_attach_DAO  = dbiCal_attach_DAO::singleton(  $this->_db, $this->_log );
    $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_exdate_DAO  = dbiCal_exdate_DAO::singleton(  $this->_db, $this->_log );
    $dbiCal_rdate_DAO   = dbiCal_rdate_DAO::singleton(   $this->_db, $this->_log );
    foreach( $result as $journal_id => $propval ) {
      if( !empty( $journal_cnts[$journal_id]['journal_cnt_attach'] )) {
        $res = $dbiCal_attach_DAO->select( $journal_id, 'journal' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$journal_id]['ATTACH'] = $res;
      }
      if( !empty( $journal_cnts[$journal_id]['journal_cnt_exrule'] ) ||
          !empty( $journal_cnts[$journal_id]['journal_cnt_rrule'] )) {
        $res = $dbiCal_rexrule_DAO->select( $journal_id, 'journal' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $propValue )
            $result[$journal_id][$propValue['rexrule_type']][] = $propValue;
        }
      }
      if( !empty( $journal_cnts[$journal_id]['journal_cnt_exdate'] )) {
        $res = $dbiCal_exdate_DAO->select( $journal_id, 'journal' );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res ))
          $result[$journal_id]['EXDATE'] = $res;
      }
      if( empty( $journal_cnts[$journal_id]['journal_cnt_rdate'] ))
        continue;
      $res = $dbiCal_rdate_DAO->select( $journal_id, 'journal' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res ))
        $result[$journal_id]['RDATE'] = $res;
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $journal_id => $propval ) {
      if( empty( $journal_cnts[$journal_id]['journal_cnt_mtext'] ))
        continue;
      $res = $dbiCal_mtext_DAO->select( $journal_id, 'journal' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as  $mpropname => $mprops )
          $result[$journal_id][$mpropname] = $mprops;
      }
    }
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $journal_id => $propval ) {
      if( empty( $journal_cnts[$journal_id]['journal_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->select( $journal_id, 'journal' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
          $result[$journal_id][$propName] = $propValue;
      }
    }
    return $result;
  }
}
?>