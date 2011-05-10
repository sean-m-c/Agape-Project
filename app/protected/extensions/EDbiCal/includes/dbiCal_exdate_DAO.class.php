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
 * This file implements the exdate table DAO
 *
**/
class dbiCal_exdate_DAO {
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
   * @since    1.0 - 2010-10-31
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
   * @since    1.0 - 2010-10-31
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_exdate_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-10-31
   */
  function delete( $owner_id, $ownertype ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere  = 'FROM exdate WHERE exdate_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND exdate_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql  = "SELECT exdate_id AS id $fromWhere AND exdate_sequence = ".$this->_db->quote( 1, 'integer' );
    $res  = & $this->_db->query( $sql, array( 'integer' ));
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
    foreach( $result as $exdate_id) {
      $res = $dbiCal_parameter_DAO->delete( $exdate_id, 'exdate' );
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
      $this->_log->log( "$sql : $res".' for '.count( $result ).' exdates', PEAR_LOG_INFO ); // show number of affected rows
    return TRUE;
  }
  /**
   * insert
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @param    array  $exdateVal
   * @param    int    $orderNo
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $exdateVal, $orderNo=1 ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO exdate (exdate_owner_id, exdate_ownertype, exdate_order, exdate_sequence';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' ).', '.$this->_db->quote( $orderNo, 'integer' );
    $exdatecnt = 1;
    $exdate_id = array();
    foreach( $exdateVal['value'] as $exdate ) {
      $value  = sprintf("%04d-%02d-%02d", $exdate['year'], $exdate['month'], $exdate['day']);
      if( isset( $exdate['hour'] )) {
        $value  .= ' '.sprintf("%02d:%02d:%02d", $exdate['hour'], $exdate['min'], $exdate['sec']);
        $sql2    = ', exdate_datetime';
        $value2  = ', '.$this->_db->quote( $value, 'timestamp' );
        if( isset( $exdate['tz'] ) &&  in_array( $exdate['tz'], array( 'utc', 'gmt', 'z', 'UTC', 'GMT', 'Z' ))) {
          $sql2   .= ', exdate_datetimeutc';
          $value2 .= ', '.$this->_db->quote( 1, 'boolean' );
        }
      }
      else {
        $sql2    = ', exdate_date';
        $value2  = ', '.$this->_db->quote( $value, 'date' );
      }
      $sql3    = "$sql $sql2 $values, ".$this->_db->quote( $exdatecnt, 'integer' ).$value2.')';
      $res = & $this->_db->exec( $sql3 );
      if( PEAR::isError( $res )) {
        if( $this->_log )
          $this->_log->log( $sql3.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return $res;
      }
      $exdate_id[$exdatecnt] = $this->_db->lastInsertID( 'exdate', 'exdate_id' );
      if( PEAR::isError( $exdate_id[$exdatecnt] )) {
        if( $this->_log )
          $this->_log->log( 'lastInsertID error:'.$exdate_id[$exdatecnt]->getUserInfo().PHP_EOL.$exdate_id[$exdatecnt]->getMessage(), PEAR_LOG_ALERT );
        return $exdate_id[$exdatecnt];
      }
      if( $this->_log )
        $this->_log->log( $sql3.PHP_EOL.'exdate_id[$exdatecnt]='.$exdate_id[$exdatecnt], PEAR_LOG_INFO );
      $exdatecnt += 1;
    } // end foreach( $exdateVal['value'] as $exdate )
    if( isset( $exdateVal['params'] ) && !empty( $exdateVal['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $exdateVal['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $exdate_id[1], 'exdate', 'EXDATE', $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $exdate_id[1];
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
    $sql  = 'SELECT * FROM exdate WHERE exdate_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND exdate_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql .= ' ORDER BY exdate_order, exdate_sequence';
    $types = array( 'integer', 'integer', 'text', 'integer', 'integer', 'timestamp', 'boolean', 'date' );
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
      if( !isset( $result[$tablerow['exdate_order']]['exdate_id'] ) && ( 1 == $tablerow['exdate_sequence'] ))
        $result[$tablerow['exdate_order']]['exdate_id'] = $tablerow['exdate_id'];
      if( isset( $tablerow['exdate_datetime'] )   && !empty( $tablerow['exdate_datetime'] )) {
        $dt = str_replace( '-', '', $tablerow['exdate_datetime'] );
        $dt = str_replace( ':', '', $dt );
        $dt = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 );
        if( isset( $tablerow['exdate_datetimeutc'] ) && !empty( $tablerow['exdate_datetimeutc'] ))
          $dt .= 'Z';
        $result[$tablerow['exdate_order']]['value'][]  = $dt;
      }
      elseif( isset( $tablerow['exdate_date'] )   &&  !empty( $tablerow['exdate_date'] ))
        $result[$tablerow['exdate_order']]['value'][]  = str_replace( '-', '', $tablerow['exdate_date'] );
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' exdates', PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $exdate_order => & $exdateVal ) {
      $res = $dbiCal_parameter_DAO->select( $exdateVal['exdate_id'], 'exdate', 'EXDATE' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $key => $value )
          $exdateVal['params'][$key] = $value;
      }
      else
        $exdateVal['params'] = FALSE;
    }
    return $result;
  }
}
?>