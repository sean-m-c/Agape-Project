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
 * This file implements the pfreebusy table DAO
 *
**/
class dbiCal_pfreebusy_DAO {
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
   * @since    1.0 - 2010-11-08
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
   * @since    1.0 - 2010-11-13
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_pfreebusy_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-13
   */
  function delete( $owner_id ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere  = 'FROM pfreebusy WHERE pfreebusy_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql  = "SELECT pfreebusy_id AS id $fromWhere AND pfreebusy_sequence = ".$this->_db->quote( 1, 'integer' );
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
    foreach( $result as $pfreebusy_id) {
      $res = $dbiCal_parameter_DAO->delete( $pfreebusy_id, 'pfreebusy' );
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
      $this->_log->log( "$sql : $res".' for '.count( $result ).' pfreebusys', PEAR_LOG_INFO ); // show number of affected rows
    return TRUE;
  }
  /**
   * insert
   *
   * @access   public
   * @param    int    $owner_id
   * @param    int    $orderNo
   * @param    array  $pfreebusyVal
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $orderNo, $pfreebusyVal ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO pfreebusy (pfreebusy_owner_id, pfreebusy_order, pfreebusy_pfreebusytype, pfreebusy_sequence, pfreebusy_startdatetime';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $orderNo, 'integer' );
    $values   .= ', '.$this->_db->quote( $pfreebusyVal['value']['fbtype'], 'text' );
    $pfreebusycnt = 1;
    $pfreebusy_id = array();
    foreach( $pfreebusyVal['value'] as $fk => $period ) {
      if( 'fbtype' === strtolower( $fk ))
        continue;
      $value2  = ', '.$this->_db->quote( $pfreebusycnt, 'integer' );
          /* start date[time] */
      $value  = sprintf("%04d-%02d-%02d", $period[0]['year'], $period[0]['month'], $period[0]['day']);
      $value .= ' '.sprintf("%02d:%02d:%02d", $period[0]['hour'], $period[0]['min'], $period[0]['sec']);
      $value3 = $this->_db->quote( $value, 'timestamp' );
      if( array_key_exists( 'year', $period[1] )) { /* period end date[time] */
        $value   = sprintf("%04d-%02d-%02d", $period[1]['year'], $period[1]['month'], $period[1]['day']);
        $value  .= ' '.sprintf("%02d:%02d:%02d", $period[1]['hour'], $period[1]['min'], $period[1]['sec']);
        $sql3    = ', pfreebusy_enddatetime';
        $value3 .= ', '.$this->_db->quote( $value, 'timestamp' );
      }
      else { /* period duration */
        $sql3    = ', pfreebusy_periodduration';
        $value   = '';
        foreach( $period[1] as $k => $v )
          $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
        $value3 .= ', '.$this->_db->quote( $value, 'text' );
      }
      $sql4    = "$sql $sql3 $values $value2, $value3)";
      $res = & $this->_db->exec( $sql4 );
      if( PEAR::isError( $res )) {
        if( $this->_log )
          $this->_log->log( $sql4.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return $res;
      }
      $pfreebusy_id[$pfreebusycnt] = $this->_db->lastInsertID( 'pfreebusy', 'pfreebusy_id' );
      if( PEAR::isError( $pfreebusy_id[$pfreebusycnt] )) {
        if( $this->_log )
          $this->_log->log( 'lastInsertID error:'.$pfreebusy_id[$pfreebusycnt]->getUserInfo().PHP_EOL.$pfreebusy_id[$pfreebusycnt]->getMessage(), PEAR_LOG_ALERT );
        return $pfreebusy_id[$pfreebusycnt];
      }
      if( $this->_log )
        $this->_log->log( $sql4.PHP_EOL."pfreebusy_id[$pfreebusycnt]=".$pfreebusy_id[$pfreebusycnt], PEAR_LOG_INFO );
      $pfreebusycnt += 1;
    } // end foreach( $pfreebusy['value'] as $period )
    if( isset( $pfreebusyVal['params'] ) && !empty( $pfreebusyVal['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $pfreebusyVal['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $pfreebusy_id[1], 'pfreebusy', 'FREEBUSY', $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $pfreebusy_id[1];
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @return   mixed  $res (PEAR::Error or result-array)
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
    $sql  = 'SELECT * FROM pfreebusy WHERE pfreebusy_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' ORDER BY pfreebusy_order, pfreebusy_sequence';
    $types = array( 'integer', 'integer', 'integer', 'text', 'integer', 'timestamp', 'timestamp', 'text' );
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
      if( !isset( $result[$tablerow['pfreebusy_order']]['pfreebusy_id'] ) && ( 1 == $tablerow['pfreebusy_sequence'] )) {
        $result[$tablerow['pfreebusy_order']]['pfreebusy_id'] = $tablerow['pfreebusy_id'];
        $result[$tablerow['pfreebusy_order']]['value'] = array( 'fbtype' => $tablerow['pfreebusy_pfreebusytype'] );
      }
      $period = array();
      if( isset( $tablerow['pfreebusy_startdatetime'] ) && !empty( $tablerow['pfreebusy_startdatetime'] )) {
        $dt  = str_replace( '-', '', $tablerow['pfreebusy_startdatetime'] );
        $dt  = str_replace( ':', '', $dt );
        $period[0]  = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      if( isset( $tablerow['pfreebusy_enddatetime'] )   && !empty( $tablerow['pfreebusy_enddatetime'] )) {
        $dt = str_replace( '-', '', $tablerow['pfreebusy_enddatetime'] );
        $dt = str_replace( ':', '', $dt );
        $period[1]  = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      elseif( isset( $tablerow['pfreebusy_periodduration'] ) && !empty( $tablerow['pfreebusy_periodduration'] )) {
        $durParts = explode( '|', $tablerow['pfreebusy_periodduration'] );
        $duration = array();
        foreach( $durParts as $durPart ) {
          list( $key, $value ) = explode( '=', $durPart, 2 );
          $duration[$key] = $value;
        }
        $period[1] = iCalUtilityFunctions::_format_duration( $duration );
      }
      $result[$tablerow['pfreebusy_order']]['value'][] = $period;
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' pfreebusys', PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $pfreebusy_order => & $pfreebusyVal ) {
      $res = $dbiCal_parameter_DAO->select( $pfreebusyVal['pfreebusy_id'], 'pfreebusy', 'FREEBUSY' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $key => $value )
          $pfreebusyVal['params'][$key] = $value;
      }
      else
        $pfreebusyVal['params'] = FALSE;
    }
    return $result;
  }
}
?>