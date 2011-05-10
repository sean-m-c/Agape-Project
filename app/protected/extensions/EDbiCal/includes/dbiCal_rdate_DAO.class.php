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
 * This file implements the rdate table DAO
 *
**/
class dbiCal_rdate_DAO {
  /**
   * @access   private
   * @var      object
   */
  private static $theInstance;
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
   * @since    1.0 - 2010-11-06
   */
  private function __construct( & $_db, & $_log ) {
    $this->_db = $_db;
    if( $_log )
      $this->_log = $_log;
    if( $this->_log )
      $this->_log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
    self::$theInstance = FALSE;
  }
 /**
   * singleton, getter method for creating/returning the single instance of this class
   *
   * @access   private
   * @param    object $_log (reference)
   * @param    object $_DBconnection (reference)
   * @return   void
   * @since    1.0 - 2010-11-06
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_rdate_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-13
   */
  function delete( $owner_id, $ownertype ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere  = 'FROM rdate WHERE rdate_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND rdate_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql        = "SELECT rdate_id AS id $fromWhere AND rdate_sequence = ".$this->_db->quote( 1, 'integer' );
    $res        = & $this->_db->query( $sql, array( 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $row = $res->fetchRow())
      if( isset( $row['id'] ) && !empty( $row['id'] ))
        $result[] = $row['id'];
    $res->free();
    if( $this->_log )
      $this->_log->log( 'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $result ))
      return null;
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $rdate_id) {
      $res = $dbiCal_parameter_DAO->delete( $rdate_id, 'rdate' );
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
      $this->_log->log( "$sql : $res".' for '.count( $result ).' rdates', PEAR_LOG_INFO ); // show number of affected rows
    return TRUE;
  }
  /**
   * insert
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @param    array  $rdateVal
   * @param    int    $orderNo
   * @return   mixed  $res (PEAR::Error eller table-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $rdateVal, $orderNo=1 ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO rdate (rdate_owner_id, rdate_ownertype, rdate_order, rdate_sequence';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' ).', '.$this->_db->quote( $orderNo, 'integer' );
    $rdatecnt = 1;
    $rdate_id = array();
    foreach( $rdateVal['value'] as $rdate ) {
      if( $this->_log )
        $this->_log->log( "rdate=".var_export( $rdate, TRUE ), PEAR_LOG_DEBUG );
      if( is_array( $rdate ) && ( 2 == count( $rdate ))) { // PERIOD  datetime - datetime  OR  datetime - duration
          /* period start datetime */
        $value  = sprintf("%04d-%02d-%02d", $rdate[0]['year'], $rdate[0]['month'], $rdate[0]['day']);
        if( isset( $rdate[0]['hour'] )) {
          $value  .= ' '.sprintf("%02d:%02d:%02d", $rdate[0]['hour'], $rdate[0]['min'], $rdate[0]['sec']);
          $sql2    = ', rdate_startdatetime';
          $value2  = ', '.$this->_db->quote( $value, 'timestamp' );
          if( isset( $rdate[0]['tz'] ) &&  in_array( $rdate[0]['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
            $sql2   .= ', rdate_startdatetimeutc';
            $value2 .= ', '.$this->_db->quote( 1, 'boolean' );
          }
        }
        if( array_key_exists( 'year', $rdate[1] )) {
          /* period end datetime */
          $value  = sprintf("%04d-%02d-%02d", $rdate[1]['year'], $rdate[1]['month'], $rdate[1]['day']);
          if( isset( $rdate[1]['hour'] )) {
            $value  .= ' '.sprintf("%02d:%02d:%02d", $rdate[1]['hour'], $rdate[1]['min'], $rdate[1]['sec']);
            $sql2   .= ', rdate_enddatetime';
            $value2 .= ', '.$this->_db->quote( $value, 'timestamp' );
            if( isset( $rdate[1]['tz'] ) &&  in_array( $rdate[1]['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
              $sql2   .= ', rdate_enddatetimeutc';
              $value2 .= ', '.$this->_db->quote( 1, 'boolean' );
            }
          }
        }
        else {
          /* period with duration */
          $value = '';
          foreach( $rdate[1] as $k => $v )
            $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
          $sql2    .= ', rdate_periodduration';
          $value2  .= ', '.$this->_db->quote( $value, 'text' );
        }
      } // PERIOD end
      else { // SINGLE date[time]
        $value  = sprintf("%04d-%02d-%02d", $rdate['year'], $rdate['month'], $rdate['day']);
        if( isset( $rdate['hour'] )) {
          $value  .= ' '.sprintf("%02d:%02d:%02d", $rdate['hour'], $rdate['min'], $rdate['sec']);
          $sql2    = ', rdate_startdatetime';
          $value2  = ', '.$this->_db->quote( $value, 'timestamp' );
          if( isset( $rdate['tz'] ) &&  in_array( $rdate['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
            $sql2   .= ', rdate_startdatetimeutc';
            $value2 .= ', '.$this->_db->quote( 1, 'boolean' );
          }
        }
        else {
          $sql2   .= ', rdate_startdate';
          $value2 .= ', '.$this->_db->quote( $value, 'date' );
        }
      } // end single date[time]
      $sql3    = "$sql $sql2 $values, ".$this->_db->quote( $rdatecnt, 'integer' ).$value2.')';
      $res = & $this->_db->exec( $sql3 );
      if( PEAR::isError( $res )) {
        if( $this->_log )
          $this->_log->log( $sql3.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return $res;
      }
      $rdate_id[$rdatecnt] = $this->_db->lastInsertID( 'rdate', 'rdate_id' );
      if( PEAR::isError( $rdate_id[$rdatecnt] )) {
        if( $this->_log )
          $this->_log->log( 'lastInsertID error:'.$rdate_id[$rdatecnt]->getUserInfo().PHP_EOL.$rdate_id[$rdatecnt]->getMessage(), PEAR_LOG_ALERT );
        return $rdate_id[$rdatecnt];
      }
      if( $this->_log )
        $this->_log->log( $sql3.PHP_EOL."rdate_id[$rdatecnt]=".$rdate_id[$rdatecnt], PEAR_LOG_INFO );
      $rdatecnt += 1;
    } // end foreach( $rdateVal['value'] as $rdate )
    if( isset( $rdateVal['params'] ) && !empty( $rdateVal['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $rdateVal['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $rdate_id[1], 'rdate', 'RDATE', $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $rdate_id[1];
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-01-20
   */
  function select( $owner_id, $ownertype ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'SELECT * FROM rdate WHERE rdate_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND rdate_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql .= ' ORDER BY rdate_order, rdate_sequence';
    $types = array( 'integer', 'integer', 'text', 'integer', 'integer', 'timestamp', 'boolean', 'date', 'timestamp', 'boolean', 'date', 'text' );
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
    $result = array();
    while( $tablerow = $res->fetchRow()) {
      if( !isset( $result[$tablerow['rdate_order']]['rdate_id'] ) && ( 1 == $tablerow['rdate_sequence'] ))
        $result[$tablerow['rdate_order']]['rdate_id'] = $tablerow['rdate_id'];
      $rdate = null;
      if( isset( $tablerow['rdate_startdatetime'] ) && !empty( $tablerow['rdate_startdatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['rdate_startdatetime'] );
        $dt = str_replace( ':', '', $dt );
        $dt = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['rdate_startdatetimeutc'] ) && !empty( $tablerow['rdate_startdatetimeutc'] ))
          $dt .= 'Z';
        if(( isset( $tablerow['rdate_enddatetime'] )    && !empty( $tablerow['rdate_enddatetime'] )) ||
           ( isset( $tablerow['rdate_periodduration'] ) && !empty( $tablerow['rdate_periodduration'] ))) // PERIOD
          $rdate = array( $dt ); // value=PERIOD start datetime
        else
          $rdate = $dt; // value=DATETIME
      }
      elseif( isset( $tablerow['rdate_startdate'] ) &&  !empty( $tablerow['rdate_startdate'] ))
        $rdate = str_replace( '-', '', $tablerow['rdate_startdate'] ); // value=DATE
      if( isset( $tablerow['rdate_enddatetime'] )   && !empty( $tablerow['rdate_enddatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['rdate_enddatetime'] );
        $dt = str_replace( ':', '', $dt );
        $dt = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['rdate_enddatetimeutc'] ) && !empty( $tablerow['rdate_enddatetimeutc'] ))
          $dt .= 'Z';
        $rdate[] = $dt; // value=PERIOD end date, always datetime if set
      }
      elseif( isset( $tablerow['rdate_periodduration'] ) && !empty( $tablerow['rdate_periodduration'] )) {
        $durParts = explode( '|', $tablerow['rdate_periodduration'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        $rdate[] = iCalUtilityFunctions::_format_duration( $duration ); // value=PERIOD with duration
      }
      $result[$tablerow['rdate_order']]['value'][] = $rdate;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' rdates', PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $rdate_order => & $rdateVal ) {
      $res = $dbiCal_parameter_DAO->select( $rdateVal['rdate_id'], 'rdate', 'RDATE' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $key => $value )
          $rdateVal['params'][$key] = $value;
      }
      else
        $rdateVal['params'] = FALSE;
    }
    return $result;
  }
}
?>