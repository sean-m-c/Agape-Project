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
 * This file implements the freebusy table DAO
 *
**/
class dbiCal_freebusy_DAO {
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
    self::$singleProperties = array( 'CONTACT', 'DTSTART', 'DTEND', 'DURATION', 'DTSTAMP', 'DTSTART', 'ORGANIZER', 'UID', 'URL', );
    self::$mtextProperties  = array( // properties using mtext (DAO)
                                     'ATTENDEE', 'COMMENT', 'REQUEST-STATUS' );
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
      self::$theInstance = new dbiCal_freebusy_DAO( $_db, $_log  );
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
    $fromWhere = 'FROM freebusy WHERE freebusy_owner_id = '.$this->_db->quote( $calendar_id, 'integer' );
    $sql  = "SELECT freebusy_id, freebusy_cnt_mtext, freebusy_cnt_freebusy, freebusy_cnt_xprop $fromWhere";
    $res  = & $this->_db->query( $sql, array_fill( 0, 4, 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $cnt = array();
      $cnt['freebusy_cnt_mtext']    = ( isset( $tablerow['freebusy_cnt_mtext'] )    && !empty( $tablerow['freebusy_cnt_mtext'] ))    ? $tablerow['freebusy_cnt_mtext']    : 0;
      $cnt['freebusy_cnt_freebusy'] = ( isset( $tablerow['freebusy_cnt_freebusy'] ) && !empty( $tablerow['freebusy_cnt_freebusy'] )) ? $tablerow['freebusy_cnt_freebusy'] : 0;
      $cnt['freebusy_cnt_xprop']    = ( isset( $tablerow['freebusy_cnt_xprop'] )    && !empty( $tablerow['freebusy_cnt_xprop'] ))    ? $tablerow['freebusy_cnt_xprop']    : 0;
      $result[$tablerow['freebusy_id']] = $cnt;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_pfreebusy_DAO = dbiCal_pfreebusy_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO     = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $freebusy_id => $freebusy_cnts ) {
      $res = $dbiCal_parameter_DAO->delete( $freebusy_id, 'freebusy' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $freebusy_cnts['freebusy_cnt_freebusy'] )) {
        $res = $dbiCal_pfreebusy_DAO->delete( $freebusy_id );
        if( PEAR::isError( $res ))
          return $res;
      }
      if( !empty( $freebusy_cnts['freebusy_cnt_xprop'] )) {
        $res = $dbiCal_xprop_DAO->delete( $freebusy_id, 'freebusy' );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $freebusy_id => $freebusy_cnts ) {
      if( empty( $freebusy_cnts['freebusy_cnt_mtext'] ))
        continue;
      $res = $dbiCal_mtext_DAO->delete( $freebusy_id, 'freebusy' );
      if( PEAR::isError( $res ))
        return $res;
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
   * @param    array  $freebusy_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $calendar_id, & $freebusy_values= array(), $calendar_order=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array();
    foreach( $freebusy_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ) || ( 'ono' == $prop ))
        continue;
      if( in_array( $prop, self::$mtextProperties ))
        $cnts['mtext'] = isset( $cnts['mtext'] ) ? ( $cnts['mtext'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]= isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    $sql       = 'INSERT INTO freebusy (freebusy_owner_id';
    $values    = ') VALUES ('.$this->_db->quote( $calendar_id, 'integer' );
    if( isset( $calendar_order )) {
      $sql    .= ', freebusy_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $freebusy_values['UID']['value'] )) {
      $sql    .= ', freebusy_uid';
      $values .= ', '.$this->_db->quote( $freebusy_values['UID']['value'], 'text' );
    }
    if( isset( $freebusy_values['DTSTAMP']['value'] )) {
      $sql    .= ', freebusy_dtstamp';
      $value   = sprintf("%04d-%02d-%02d", $freebusy_values['DTSTAMP']['value']['year'], $freebusy_values['DTSTAMP']['value']['month'], $freebusy_values['DTSTAMP']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $freebusy_values['DTSTAMP']['value']['hour'], $freebusy_values['DTSTAMP']['value']['min'], $freebusy_values['DTSTAMP']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $freebusy_values['DTSTART']['value'] )) {
      $sql    .= ', freebusy_startdatetime';
      $value = sprintf("%04d-%02d-%02d", $freebusy_values['DTSTART']['value']['year'], $freebusy_values['DTSTART']['value']['month'], $freebusy_values['DTSTART']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $freebusy_values['DTSTART']['value']['hour'], $freebusy_values['DTSTART']['value']['min'], $freebusy_values['DTSTART']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $freebusy_values['DTEND']['value'] )) {
      $sql    .= ', freebusy_enddatetime';
      $value = sprintf("%04d-%02d-%02d", $freebusy_values['DTEND']['value']['year'], $freebusy_values['DTEND']['value']['month'], $freebusy_values['DTEND']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $freebusy_values['DTEND']['value']['hour'], $freebusy_values['DTEND']['value']['min'], $freebusy_values['DTEND']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $freebusy_values['DURATION']['value'] )) {
      $sql    .= ', freebusy_duration';
      $value = '';
      foreach( $freebusy_values['DURATION']['value'] as $k => $v )
        $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
      $values .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $freebusy_values['ORGANIZER']['value'] )) {
      $sql    .= ', freebusy_organizer';
      $values .= ', '.$this->_db->quote( $freebusy_values['ORGANIZER']['value'], 'text' );
    }
    if( isset( $freebusy_values['CONTACT']['value'] )) {
      $sql    .= ', freebusy_contact';
      $values .= ', '.$this->_db->quote( $freebusy_values['CONTACT']['value'], 'text' );
    }
    if( isset( $freebusy_values['URL']['value'] )) {
      $sql    .= ', freebusy_url';
      $values .= ', '.$this->_db->quote( $freebusy_values['URL']['value'], 'text' );
    }
    foreach( $cnts as $prop => $cnt ) {
      if( !empty( $cnt )) {
        $sql    .= ', freebusy_cnt_'.strtolower( str_replace( '-', '_', $prop ));
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
    $freebusy_id = $this->_db->lastInsertID( 'freebusy', 'freebusy_id' );
    if( PEAR::isError( $freebusy_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$freebusy_id->getUserInfo().PHP_EOL.$freebusy_id->getMessage(), PEAR_LOG_ALERT );
      return $freebusy_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'freebusy_id='.$freebusy_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $freebusy_values as $prop => $pValue ) {
      if( !isset( $pValue['params'] ) || empty( $pValue['params'] ))
        continue;
      if( !in_array( $prop, self::$singleProperties ))
        continue;
      if( in_array( $prop, self::$mtextProperties ) || ( 'X-' == substr( $prop, 0, 2 )))
        continue;
      foreach( $pValue['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $freebusy_id, 'freebusy', $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $freebusy_values['FREEBUSY'] ) && !empty( $freebusy_values['FREEBUSY'] )) {
      $dbiCal_pfreebusy_DAO = dbiCal_pfreebusy_DAO::singleton( $this->_db, $this->_log );
      foreach( $freebusy_values['FREEBUSY'] as $freebusy_ix => $pfreebusy ) {
        $res = $dbiCal_pfreebusy_DAO->insert( $freebusy_id, $freebusy_ix, $pfreebusy );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( self::$mtextProperties as $mProp ) {
      if( isset( $freebusy_values[$mProp] ) && !empty( $freebusy_values[$mProp] )) {
        foreach( $freebusy_values[$mProp] as $themProp ) {
          if( isset( $themProp ) && !empty( $themProp['value'] )) {
            $res = $dbiCal_mtext_DAO->insert( $freebusy_id, 'freebusy', $mProp, $themProp );
            if( PEAR::isError( $res ))
              return $res;
          }
        }
      }
    }
    if( isset( $freebusy_values['XPROP'] ) && !empty( $freebusy_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $freebusy_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $freebusy_id, 'freebusy', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    return $freebusy_id;
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
    $sql  = 'SELECT * FROM freebusy WHERE freebusy_owner_id = '.$this->_db->quote( $calendar_id, 'integer' ).' ORDER BY freebusy_id';
    $types = array( 'integer', 'integer', 'integer', 'text', 'timestamp'
                  , 'text', 'timestamp', 'timestamp', 'text', 'text', 'text' );
    $types = array_merge( $types, array_fill( count( $types ), 3, 'integer' ));
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
    $result = $freebusy_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['freebusy_cnt_mtext']    = ( isset( $tablerow['freebusy_cnt_mtext'] )    && !empty( $tablerow['freebusy_cnt_mtext'] ))    ? $tablerow['freebusy_cnt_mtext']    : 0;
      $cnt['freebusy_cnt_freebusy'] = ( isset( $tablerow['freebusy_cnt_freebusy'] ) && !empty( $tablerow['freebusy_cnt_freebusy'] )) ? $tablerow['freebusy_cnt_freebusy'] : 0;
      $cnt['freebusy_cnt_xprop']    = ( isset( $tablerow['freebusy_cnt_xprop'] )    && !empty( $tablerow['freebusy_cnt_xprop'] ))    ? $tablerow['freebusy_id']           : 0;
      $freebusy_cnts[$tablerow['freebusy_id']] = $cnt;
      if( isset( $tablerow['freebusy_ono'] ))
        $row['ono'] = $tablerow['freebusy_ono'];
      if( isset( $tablerow['freebusy_uid'] )            && !empty( $tablerow['freebusy_uid'] ))
        $row['UID']['value'] = $tablerow['freebusy_uid'];
      if( isset( $tablerow['freebusy_dtstamp'] )        && !empty( $tablerow['freebusy_dtstamp'] )) {
        $dt = str_replace( '-', '', $tablerow['freebusy_dtstamp'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTAMP']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['freebusy_contact'] )        && !empty( $tablerow['freebusy_contact'] ))
        $row['CONTACT']['value'] = $tablerow['freebusy_contact'];
      if( isset( $tablerow['freebusy_startdatetime'] )  && !empty( $tablerow['freebusy_startdatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['freebusy_startdatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTART']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['freebusy_enddatetime'] )    && !empty( $tablerow['freebusy_enddatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['freebusy_enddatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTEND']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['freebusy_duration'] )       && !empty( $tablerow['freebusy_duration'] )) {
        $durParts = explode( '|', $tablerow['freebusy_duration'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        $row['DURATION']['value'] = iCalUtilityFunctions::_format_duration( $duration );
      }
      if( isset( $tablerow['freebusy_organizer'] )      && !empty( $tablerow['freebusy_organizer'] ))
        $row['ORGANIZER']['value'] = $tablerow['freebusy_organizer'];
      if( isset( $tablerow['freebusy_url'] )            && !empty( $tablerow['freebusy_url'] ))
        $row['URL']['value'] = $tablerow['freebusy_url'];
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      if( !empty( $row ))
        $result[$tablerow['freebusy_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' freebusys', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $freebusy_id => $propVal ) {
      foreach( $propVal as $prop => $pValue ) {
        if( 'ono' == $prop )
          continue;
        $res = $dbiCal_parameter_DAO->select( $freebusy_id, 'freebusy', $prop );
        if( PEAR::isError( $res ))
          return $res;
        $result[$freebusy_id][$prop]['params'] = array();
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$freebusy_id][$prop]['params'][$key] = $value;
        }
      }
    }
    $dbiCal_pfreebusy_DAO = dbiCal_pfreebusy_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $freebusy_id => $propval ) {
      if( empty( $freebusy_cnts[$freebusy_id]['freebusy_cnt_freebusy'] ))
        continue;
      $res = $dbiCal_pfreebusy_DAO->select( $freebusy_id );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res ))
        $result[$freebusy_id]['FREEBUSY'] = $res;
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $freebusy_id => $propval ) {
      if( empty( $freebusy_cnts[$freebusy_id]['freebusy_cnt_mtext'] ))
        continue;
      $res = $dbiCal_mtext_DAO->select( $freebusy_id, 'freebusy' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $mpropname => $mpropval )
          $result[$freebusy_id][$mpropname] = $mpropval;
      }
    }
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $freebusy_id => $propval ) {
      if( empty( $freebusy_cnts[$freebusy_id]['freebusy_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->select( $freebusy_id, 'freebusy' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
          $result[$freebusy_id][$propName] = $propValue;
      }
    }
    return $result;
  }
}
?>