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
 * This file implements the timezone table DAO
 *
**/
class dbiCal_timezone_DAO {
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
   * @since    1.0 - 2010-10-30
   */
  private function __construct( & $_db, & $_log ) {
    $this->_db = $_db;
    if( $_log )
      $this->_log = $_log;
    if( $this->_log )
      $this->_log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
    self::$singleProperties = array( 'TZID','LAST-MODIFIED', 'TZURL', );
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
      self::$theInstance = new dbiCal_timezone_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-19
   */
  function delete( $calendar_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere = 'FROM timezone WHERE timezone_owner_id = '.$this->_db->quote( $calendar_id, 'integer' );
    $sql  = "SELECT timezone_id, timezone_cnt_xprop, timezone_cnt_stddlght $fromWhere";
    $res  = & $this->_db->query( $sql, array_fill( 0, 3, 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $row = array();
      $row['timezone_cnt_xprop']    = ( isset( $tablerow['timezone_cnt_xprop'] )    && !empty( $tablerow['timezone_cnt_xprop'] ))    ? $tablerow['timezone_cnt_xprop']    : 0;
      $row['timezone_cnt_stddlght'] = ( isset( $tablerow['timezone_cnt_stddlght'] ) && !empty( $tablerow['timezone_cnt_stddlght'] )) ? $tablerow['timezone_cnt_stddlght'] : 0;
      $result[$tablerow['timezone_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_stddlght_DAO  = dbiCal_stddlght_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO     = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $timezone_id => $timezone_cnts ) {
      $res = $dbiCal_parameter_DAO->delete( $timezone_id, 'timezone' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $timezone_cnts['timezone_cnt_xprop'] )) {
        $res = $dbiCal_xprop_DAO->delete( $timezone_id, 'timezone' );
        if( PEAR::isError( $res ))
          return $res;
      }
      if( empty( $timezone_cnts['timezone_cnt_stddlght'] ))
        continue;
      $res = $dbiCal_stddlght_DAO->delete( $timezone_id );
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
   * @param    array  $timezone_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $calendar_id, & $timezone_values= array(), $calendar_order=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array( 'stddlght' => 0 );
    foreach( $timezone_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ) || ( 'ono' == $prop ))
        continue;
      if( in_array( $prop, array( 'STANDARD', 'DAYLIGHT' ) ))
        $cnts['stddlght'] = isset( $cnts['stddlght'] ) ? ( $cnts['stddlght'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]   = isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    $sql       = 'INSERT INTO timezone (timezone_owner_id';
    $values    = ') VALUES ('.$this->_db->quote( $calendar_id, 'integer' );
    if( isset( $calendar_order )) {
      $sql    .= ', timezone_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $timezone_values['TZID']['value'] )) {
      $sql    .= ', timezone_tzid';
      $values .= ', '.$this->_db->quote( $timezone_values['TZID']['value'], 'text' );
    }
    if( isset( $timezone_values['TZURL']['value'] )) {
      $sql    .= ', timezone_url';
      $values .= ', '.$this->_db->quote( $timezone_values['TZURL']['value'], 'text' );
    }
    if( isset( $timezone_values['LAST-MODIFIED']['value'] )) {
      $sql    .= ', timezone_last_modified';
      $value   = sprintf("%04d-%02d-%02d", $timezone_values['LAST-MODIFIED']['value']['year'], $timezone_values['LAST-MODIFIED']['value']['month'], $timezone_values['LAST-MODIFIED']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $timezone_values['LAST-MODIFIED']['value']['hour'], $timezone_values['LAST-MODIFIED']['value']['min'], $timezone_values['LAST-MODIFIED']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    foreach( $cnts as $prop => $cnt ) {
      if( !empty( $cnt )) {
        $sql    .= ', timezone_cnt_'.strtolower( $prop );
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
    $timezone_id = $this->_db->lastInsertID( 'timezone', 'timezone_id' );
    if( PEAR::isError( $timezone_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$timezone_id->getUserInfo().PHP_EOL.$timezone_id->getMessage(), PEAR_LOG_ALERT );
      return $timezone_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'timezone_id='.$timezone_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $timezone_values as $prop => $pValue ) {
      if( !isset( $pValue['params'] ) || empty( $pValue['params'] ))
        continue;
      if( !in_array( $prop, self::$singleProperties ))
        continue;
      foreach( $pValue['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $timezone_id, 'timezone', $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $timezone_values['XPROP'] ) && !empty( $timezone_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $timezone_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $timezone_id, 'timezone', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    if(( isset( $timezone_values['STANDARD'] ) && !empty( $timezone_values['STANDARD'] )) ||
       ( isset( $timezone_values['DAYLIGHT'] ) && !empty( $timezone_values['DAYLIGHT'] ))) {
      $dbiCal_stddlght_DAO = dbiCal_stddlght_DAO::singleton( $this->_db, $this->_log );
      $subix = 1;
      if( isset( $timezone_values['STANDARD'] )) {
        foreach( $timezone_values['STANDARD'] as $standard ) {
          $res = $dbiCal_stddlght_DAO->insert( $timezone_id, 'STANDARD', $standard, $subix );
          if( PEAR::isError( $res ))
            return $res;
          $subix += 1;
        }
      }
      if( isset( $timezone_values['DAYLIGHT'] )) {
        foreach( $timezone_values['DAYLIGHT'] as $daylight ) {
          $res = $dbiCal_stddlght_DAO->insert( $timezone_id, 'DAYLIGHT', $daylight, $subix );
          if( PEAR::isError( $res ))
            return $res;
          $subix += 1;
        }
      }
    }
    return $timezone_id;
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
    $sql  = 'SELECT * FROM timezone WHERE timezone_owner_id = '.$this->_db->quote( $calendar_id, 'integer' ).' ORDER BY timezone_id';
    $types = array( 'integer', 'integer', 'integer', 'text', 'text', 'timestamp' );
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
    $result = $timezone_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['timezone_cnt_xprop']    = ( isset( $tablerow['timezone_cnt_xprop'] )    && !empty( $tablerow['timezone_cnt_xprop'] ))    ? $tablerow['timezone_cnt_xprop']    : 0;
      $cnt['timezone_cnt_stddlght'] = ( isset( $tablerow['timezone_cnt_stddlght'] ) && !empty( $tablerow['timezone_cnt_stddlght'] )) ? $tablerow['timezone_cnt_stddlght'] : 0;
      $timezone_cnts[$tablerow['timezone_id']] = $cnt;
      if( isset( $tablerow['timezone_ono'] ))
        $row['ono'] = $tablerow['timezone_ono'];
      if( isset( $tablerow['timezone_tzid'] )           && !empty( $tablerow['timezone_tzid'] ))
        $row['TZID']['value'] = $tablerow['timezone_tzid'];
      if( isset( $tablerow['timezone_last_modified'] )  && !empty( $tablerow['timezone_last_modified'] )) {
        $dt = str_replace( '-', '', $tablerow['timezone_last_modified'] );
        $dt = str_replace( ':', '', $dt );
        $row['LAST-MODIFIED']['value'] = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['timezone_tzurl'] )          &&  empty( $tablerow['timezone_tzurl'] ))
        $row['TZURL']['value'] = $tablerow['timezone_tzurl'];
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      if( !empty( $row ))
        $result[$tablerow['timezone_id']] = $row;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' timezones', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $timezone_id => $propVal ) {
      foreach( $propVal as $prop => $pValue ) {
        if( 'ono' == $prop )
          continue;
        $res = $dbiCal_parameter_DAO->select( $timezone_id, 'timezone', $prop );
        if( PEAR::isError( $res ))
          return $res;
        $result[$timezone_id][$prop]['params'] = array();
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$timezone_id][$prop]['params'][$key] = $value;
        }
      }
    }
    $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $timezone_id => $propval ) {
      if( empty( $timezone_cnts[$timezone_id]['timezone_cnt_xprop'] ))
        continue;
      $res = $dbiCal_xprop_DAO->select( $timezone_id, 'timezone' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
          $result[$timezone_id][$propName] = $propValue;
      }
    }
    $dbiCal_stddlght_DAO = dbiCal_stddlght_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $timezone_id => & $propval ) {
      if( empty( $timezone_cnts[$timezone_id]['timezone_cnt_stddlght'] ))
        continue;
      $res = $dbiCal_stddlght_DAO->select( $timezone_id );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $stddlghttype => $stddlght ) {
          foreach( $stddlght as $stddlghtix => $stddlghtval )
            $propval[$stddlghttype][$stddlghtix] = $stddlghtval;
        }
      }
    }
    return $result;
  }
}
?>