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
 * This file implements the attach table DAO
 *
**/
class dbiCal_attach_DAO {
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
   * @since    1.0 - 2010-11-07
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
   * @since    1.0 - 2010-11-07
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_attach_DAO( $_db, $_log  );
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
    $fromWhere  = 'FROM attach WHERE attach_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND attach_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql  = "SELECT attach_id AS id $fromWhere";
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
    foreach( $result as $attach_id) {
      $res = $dbiCal_parameter_DAO->delete( $attach_id, 'attach' );
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
   * @param    int    $owner_id
   * @param    string $ownertype
   * @param    array  $attachVal
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $attachVal ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO attach (attach_owner_id, attach_ownertype';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' );
    if( isset( $attachVal['value'] ) && !empty( $attachVal['value'] )) {
      if( isset( $attachVal['params']['VALUE'] )    && ( 'BINARY' == strtoupper( $attachVal['params']['VALUE'] )) &&
          isset( $attachVal['params']['ENCODING'] ) && ( 'BASE64' == strtoupper( $attachVal['params']['ENCODING'] ))) {
        $sql    .= ', attach_attach_bin';
        $values .= ', '.$this->_db->quote( $attachVal['value'], 'blob' );
      }
      else {
        $sql    .= ', attach_attach_uri';
        $values .= ', '.$this->_db->quote( $attachVal['value'], 'text' );
      }
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $attach_id = $this->_db->lastInsertID( 'attach', 'attach_id' );
    if( PEAR::isError( $attach_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$attach_id->getUserInfo().PHP_EOL.$attach_id->getMessage(), PEAR_LOG_ALERT );
      return $attach_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'attach_id='.$attach_id, PEAR_LOG_INFO );
    if( isset( $attachVal['params'] ) && !empty( $attachVal['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $attachVal['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $attach_id, 'attach', 'ATTACH', $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $attach_id;
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
    $sql  = 'SELECT * FROM attach WHERE attach_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND attach_ownertype = '.$this->_db->quote( $ownertype, 'text' ).' ORDER BY attach_id';
    $types = array( 'integer', 'integer', 'text', 'text', 'blob' );
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
      if( isset( $tablerow['attach_attach_bin'] ) && !empty( $tablerow['attach_attach_bin'] ) &&
          !PEAR::isError( $tablerow['attach_attach_bin'] ) && is_resource( $tablerow['attach_attach_bin'] )) {
        $result[$tablerow['attach_id']]['value'] = '';
        while( !feof( $tablerow['attach_attach_bin'] ))
          $result[$tablerow['attach_id']]['value'] .= fread( $tablerow['attach_attach_bin'], 8192 );
        $this->_db->datatype->destroyLOB( $tablerow['attach_attach_bin'] );
        if( $this->_log )
          $this->_log->log( 'blob len='. strlen( $tablerow['attach_attach'] ), PEAR_LOG_DEBUG );
      }
      elseif( isset( $tablerow['attach_attach_uri'] ) && !empty( $tablerow['attach_attach_uri'] ))
        $result[$tablerow['attach_id']]['value'] = $tablerow['attach_attach_uri'];
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' attachs', PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $attach_id => & $attachVal ) {
      $res = $dbiCal_parameter_DAO->select( $attach_id, 'attach', 'ATTACH' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $key => $value )
          $attachVal['params'][$key] = $value;
      }
      else
        $attachVal['params'] = FALSE;
    }
    return $result;
  }
}
?>