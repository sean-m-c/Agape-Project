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
 * This file implements the stddlght (timezone: syandard/daylight) table DAO
 *
**/
class dbiCal_stddlght_DAO {
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
   * @since    1.0 - 2010-11-19
   */
  private function __construct( & $_db, & $_log ) {
    $this->_db = $_db;
    if( $_log )
      $this->_log = $_log;
    if( $this->_log )
      $this->_log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
    self::$singleProperties = array( 'DTSTART', 'TZOFFSETFROM', 'TZOFFSETTO' );
    self::$mtextProperties  = array( // properties using mtext (DAO)
                                     'COMMENT', 'TZNAME' );
    self::$theInstance = FALSE;
  }
 /**
   * singleton, getter method for creating/returning the single instance of this class
   *
   * @access   private
   * @param    object $_log (reference)
   * @param    object $_DBconnection (reference)
   * @return   void
   * @since    1.0 - 2010-11-13
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_stddlght_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-22
   */
  function delete( $owner_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere  = 'FROM stddlght WHERE stddlght_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql  = "SELECT stddlght_id, stddlght_type, stddlght_cnt_mtext, stddlght_cnt_rdate, stddlght_cnt_rrule, stddlght_cnt_xprop $fromWhere";
    $res  = & $this->_db->query( $sql, array( 'integer', 'text', 'integer', 'integer', 'integer', 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      $cnt = array( 'stddlght_type' =>       $tablerow['stddlght_type'] );
      $cnt['stddlght_cnt_mtext']  = ( isset( $tablerow['stddlght_cnt_mtext'] ) && !empty( $tablerow['stddlght_cnt_mtext'] )) ? $tablerow['stddlght_cnt_mtext'] : 0;
      $cnt['stddlght_cnt_rdate']  = ( isset( $tablerow['stddlght_cnt_rdate'] ) && !empty( $tablerow['stddlght_cnt_rdate'] )) ? $tablerow['stddlght_cnt_rdate'] : 0;
      $cnt['stddlght_cnt_rrule']  = ( isset( $tablerow['stddlght_cnt_rrule'] ) && !empty( $tablerow['stddlght_cnt_rrule'] )) ? $tablerow['stddlght_cnt_rrule'] : 0;
      $cnt['stddlght_cnt_xprop']  = ( isset( $tablerow['stddlght_cnt_xprop'] ) && !empty( $tablerow['stddlght_cnt_xprop'] )) ? $tablerow['stddlght_cnt_xprop'] : 0;
      $result[$tablerow['stddlght_id']] = $cnt;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $stddlght_id => $stddlght_cnts ) {
      $res = $dbiCal_parameter_DAO->delete( $stddlght_id, $stddlght_cnts['stddlght_type'] );
      if( PEAR::isError( $res ))
        return $res;
    }
    $multiProps = array( 'mtext', 'rdate', 'rexrule', 'xprop' );
    foreach( $multiProps as $mProp ) {
      foreach( $result as $stddlght_id => $stddlght_cnts ) {
        if(( 'mtext'   == $mProp ) && empty( $stddlght_cnts['stddlght_cnt_mtext'] ))
          continue;
        if(( 'rdate'   == $mProp ) && empty( $stddlght_cnts['stddlght_cnt_rdate'] ))
          continue;
        if(( 'rexrule' == $mProp ) && empty( $stddlght_cnts['stddlght_cnt_rrule'] ))
          continue;
        if(( 'xprop'   == $mProp ) && empty( $stddlght_cnts['stddlght_cnt_xprop'] ))
          continue;
        $prop_DAO_name = 'dbiCal_'.$mProp.'_DAO';
        $prop_DAO = $prop_DAO_name::singleton( $this->_db, $this->_log );
        $res = $prop_DAO->delete( $stddlght_id, $stddlght_cnts['stddlght_type'] );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $sql  = "DELETE  $fromWhere";
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
   * @param    string $stddlghttype
   * @param    array  $stddlght_values
   * @param    int    $calendar_order
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $stddlghttype, $stddlght_values, $calendar_order=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $cnts = array();
    foreach( $stddlght_values as $prop => $propVals ) {
      if( in_array( $prop, self::$singleProperties ))
        continue;
      if( in_array( $prop, self::$mtextProperties ))
        $cnts['mtext'] = isset( $cnts['mtext'] ) ? ( $cnts['mtext'] + count( $propVals )) : count( $propVals );
      else
        $cnts[$prop]   = isset( $cnts[$prop] ) ? ( $cnts[$prop] + count( $propVals )) : count( $propVals );
    }
    $sql       = 'INSERT INTO stddlght (stddlght_owner_id, stddlght_type';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $stddlghttype, 'text' );
    if( isset( $calendar_order )) {
      $sql    .= ', stddlght_ono';
      $values .= ', '.$this->_db->quote( $calendar_order, 'integer' );
    }
    if( isset( $stddlght_values['DTSTART']['value'] )      && !empty( $stddlght_values['DTSTART']['value'] )) {
      $sql    .= ', stddlght_startdatetime';
      $value   = sprintf("%04d-%02d-%02d", $stddlght_values['DTSTART']['value']['year'], $stddlght_values['DTSTART']['value']['month'], $stddlght_values['DTSTART']['value']['day']);
      $value  .= ' '.sprintf("%02d:%02d:%02d", $stddlght_values['DTSTART']['value']['hour'], $stddlght_values['DTSTART']['value']['min'], $stddlght_values['DTSTART']['value']['sec']);
      $values .= ', '.$this->_db->quote( $value, 'timestamp' );
    }
    if( isset( $stddlght_values['TZOFFSETFROM']['value'] ) && !empty( $stddlght_values['TZOFFSETFROM']['value'] )) {
      $sql    .= ', stddlght_tzoffsetfrom';
      $values .= ', '.$this->_db->quote( $stddlght_values['TZOFFSETFROM']['value'], 'integer' );
    }
    if( isset( $stddlght_values['TZOFFSETTO']['value'] )   && !empty( $stddlght_values['TZOFFSETTO']['value'] )) {
      $sql    .= ', stddlght_tzoffsetto';
      $values .= ', '.$this->_db->quote( $stddlght_values['TZOFFSETTO']['value'], 'integer' );
    }
    foreach( $cnts as $prop => $cnt ) {
      if( !empty( $cnt )) {
        $sql    .= ', stddlght_cnt_'.strtolower( $prop );
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
    $stddlght_id = $this->_db->lastInsertID( 'stddlght', 'stddlght_id' );
    if( PEAR::isError( $stddlght_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$stddlght_id->getUserInfo().PHP_EOL.$stddlght_id->getMessage(), PEAR_LOG_ALERT );
      return $stddlght_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'stddlght_id='.$stddlght_id, PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $stddlght_values as $prop => $pValue ) {
      if( !isset( $pValue['params'] ) || empty( $pValue['params'] ))
        continue;
      if( in_array( $prop, array( 'RDATE', 'RRULE' )))
        continue;
      if( !in_array( $prop, self::$singleProperties ))
        continue;
      if( in_array( $prop, self::$mtextProperties ) || ( 'X-' == substr( $prop, 0, 2 )))
        continue;
      foreach( $pValue['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $stddlght_id, $stddlghttype, $prop, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $stddlght_values['RRULE'] ) && !empty( $stddlght_values['RRULE'] )) {
      $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
      foreach( $stddlght_values['RRULE'] as $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rexrule_DAO->insert( $stddlght_id, $stddlghttype, 'RRULE', $theProp );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    if( isset( $stddlght_values['RDATE'] ) || !empty( $stddlght_values['RDATE'] )) {
      $dbiCal_rdate_DAO = dbiCal_rdate_DAO::singleton( $this->_db, $this->_log );
      foreach( $stddlght_values['RDATE'] as $pix => $theProp ) {
        if( !isset( $theProp['value'] ))
           continue;
        $res = $dbiCal_rdate_DAO->insert( $stddlght_id, $stddlghttype, $theProp, $pix );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( self::$mtextProperties as $mProp ) {
      if( isset( $stddlght_values[$mProp] ) && !empty( $stddlght_values[$mProp] )) {
        foreach( $stddlght_values[$mProp] as $themProp ) {
          if( isset( $themProp['value'] ) && !empty( $themProp['value'] )) {
            $res = $dbiCal_mtext_DAO->insert( $stddlght_id, $stddlghttype, $mProp, $themProp );
            if( PEAR::isError( $res ))
              return $res;
          }
        }
      }
    }
    if( isset( $stddlght_values['XPROP'] ) && !empty( $stddlght_values['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $stddlght_values['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $stddlght_id, $stddlghttype, $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    return $stddlght_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @return   mixed  $res (PEAR::Error eller result-array)
   * @since    1.0 - 2011-01-20
   */
  function select( $owner_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'SELECT * FROM stddlght WHERE stddlght_owner_id = '.$this->_db->quote( $owner_id, 'integer' ).' ORDER BY stddlght_id';
    $types = array( 'integer', 'integer', 'text', 'integer', 'timestamp', 'integer', 'integer' );
    $types = array_merge( $types, array_fill( count( $types ), 4, 'integer' ));
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
    $result = $stddlght_cnts = array();
    while( $tablerow = $res->fetchRow()) {
      $row = $cnt = array();
      $cnt['stddlght_cnt_mtext'] = ( isset( $tablerow['stddlght_cnt_mtext'] ) && !empty( $tablerow['stddlght_cnt_mtext'] )) ? $tablerow['stddlght_cnt_mtext'] : 0;
      $cnt['stddlght_cnt_rdate'] = ( isset( $tablerow['stddlght_cnt_rdate'] ) && !empty( $tablerow['stddlght_cnt_rdate'] )) ? $tablerow['stddlght_cnt_rdate'] : 0;
      $cnt['stddlght_cnt_rrule'] = ( isset( $tablerow['stddlght_cnt_rrule'] ) && !empty( $tablerow['stddlght_cnt_rrule'] )) ? $tablerow['stddlght_cnt_rrule'] : 0;
      $cnt['stddlght_cnt_xprop'] = ( isset( $tablerow['stddlght_cnt_xprop'] ) && !empty( $tablerow['stddlght_cnt_xprop'] )) ? $tablerow['stddlght_cnt_xprop'] : 0;
      $stddlght_cnts[$tablerow['stddlght_id']] = $cnt;
      if( isset( $tablerow['stddlght_ono'] ))
        $row['ono'] = $tablerow['stddlght_ono'];
      if( isset( $tablerow['stddlght_startdatetime'] ) && !empty( $tablerow['stddlght_startdatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['stddlght_startdatetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['DTSTART']['value']      = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
      }
      if( isset( $tablerow['stddlght_tzoffsetfrom'] )  && ( !empty( $tablerow['stddlght_tzoffsetfrom'] ) || ( '0' == $tablerow['stddlght_tzoffsetfrom'] ))) {
        $format = (( 9999 < $tablerow['stddlght_tzoffsetfrom'] ) || ( -9999 > $tablerow['stddlght_tzoffsetfrom'] )) ? "%+07d" : "%+05d";
        $row['TZOFFSETFROM']['value'] = sprintf( $format, $tablerow['stddlght_tzoffsetfrom'] );
      }
      if( isset( $tablerow['stddlght_tzoffsetto'] )    && ( !empty( $tablerow['stddlght_tzoffsetto'] ) || ( '0' == $tablerow['stddlght_tzoffsetto'] ))) {
        $format = (( 9999 < $tablerow['stddlght_tzoffsetto'] ) || ( -9999 > $tablerow['stddlght_tzoffsetto'] )) ? "%+07d" : "%+05d";
        $row['TZOFFSETTO']['value'] = sprintf( $format, $tablerow['stddlght_tzoffsetto'] );
      }
      if( !empty( $row )) {
        $result[$tablerow['stddlght_type']][$tablerow['stddlght_id']] = $row;
        if( $this->_log )
          $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      }
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' stddlghts', PEAR_LOG_INFO );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $stddlghttype => $stddlghtVal ) {
      foreach( $stddlghtVal as $stddlght_id => $stddlghtprop ) {
        foreach( $stddlghtprop as $stddlghtpname => $stddlghtpval ) {
          if( 'ono' === $stddlghtpname )
            continue;
          $res = $dbiCal_parameter_DAO->select( $stddlght_id, $stddlghttype, $stddlghtpname );
          if( PEAR::isError( $res ))
            return $res;
          $result[$stddlghttype][$stddlght_id][$stddlghtpname]['params'] = FALSE;
          if( !empty( $res )) {
            foreach( $res as $key => $value )
              $result[$stddlghttype][$stddlght_id][$stddlghtpname]['params'][$key] = $value;
          }
        }
      }
    }
    $dbiCal_rdate_DAO   = dbiCal_rdate_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_rexrule_DAO = dbiCal_rexrule_DAO::singleton( $this->_db, $this->_log );
    $dbiCal_xprop_DAO   = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $stddlghttype => $stddlghtVal ) {
      foreach( $stddlghtVal as $stddlght_id => $stddlghtprop ) {
        if( !empty( $stddlght_cnts[$stddlght_id]['stddlght_cnt_rdate'] )) {
          $res = $dbiCal_rdate_DAO->select( $stddlght_id, $stddlghttype );
          if( PEAR::isError( $res ))
            return $res;
          if( !empty( $res ))
            $result[$stddlghttype][$stddlght_id]['RDATE'] = $res;
        }
        if( !empty( $stddlght_cnts[$stddlght_id]['stddlght_cnt_rrule'] )) {
          $res = $dbiCal_rexrule_DAO->select( $stddlght_id, $stddlghttype );
          if( PEAR::isError( $res ))
            return $res;
          if( !empty( $res )) {
            foreach( $res as $propValue )
              $result[$stddlghttype][$stddlght_id][$propValue['value']['rexrule_type']][] = $propValue;
          }
        }
        if( empty( $stddlght_cnts[$stddlght_id]['stddlght_cnt_xprop'] ))
          continue;
        $res = $dbiCal_xprop_DAO->select( $stddlght_id, $stddlghttype );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $propName => $propValue )
            $result[$stddlghttype][$stddlght_id][$propName] = $propValue;
        }
      }
    }
    $dbiCal_mtext_DAO = dbiCal_mtext_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $stddlghttype => $stddlghtVal ) {
      foreach( $stddlghtVal as $stddlght_id => $stddlghtprop ) {
        if( empty( $stddlght_cnts[$stddlght_id]['stddlght_cnt_mtext'] ))
          continue;
        $res = $dbiCal_mtext_DAO->select( $stddlght_id, $stddlghttype );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $mpropname => $mprops )
            $result[$stddlghttype][$stddlght_id][$mpropname] = $mprops;
        }
      }
    }
    return $result;
  }
}
?>