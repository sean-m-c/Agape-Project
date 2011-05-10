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
 * This file implements the xprop table DAO
 *
**/
class dbiCal_xprop_DAO {
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
      self::$theInstance = new dbiCal_xprop_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * getOwners
   *
   * @access   public
   * @param    array  $selectOptions
   * @param    array  $xpropValue
   * @return   mixed  $res (PEAR::Error eller array)
   * @since    1.0 - 2011-02-08
   */
  function getOwners( $ownertype, $selectOptions=array()) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start ownertype=$ownertype selectOptions=".var_export($selectOptions, TRUE ), PEAR_LOG_INFO );
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql      = 'SELECT xprop_owner_id AS calendar_id, xprop_key, xprop_text ';
    $sql     .= 'FROM xprop WHERE xprop_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    if( isset( $selectOptions['calendar_id'] ))
      $sql   .= ' AND xprop_owner_id = '.$this->_db->quote( $selectOptions['calendar_id'], 'integer' );
    elseif( 0 < count( $selectOptions )) {
      $sql   .= ' AND (';
      $orsw   = FALSE;
      foreach( $selectOptions as $xpropName => $xpropValue ) {
        if(( 'X-' != strtoupper( substr( $xpropName, 0, 2 ))) ||
           ( in_array( strtolower( $xpropName ), array( 'date', 'filename' ))))
          continue;
        if( $orsw )
          $sql .= ' OR';
        $sql   .= ' (UPPER(xprop_key) = '.$this->_db->quote( strtoupper( $xpropName ), 'text' );
        if( !empty( $xpropValue ))
          $sql .= ' AND xprop_text = '.$this->_db->quote( $xpropValue, 'text' );
        $sql .= ')';
        $orsw  = TRUE;
      }
      if( !$orsw )
        return array(); // exit if no hits!!
      $sql   .= ')';
    }
    $sql     .= ' ORDER BY 1 DESC';
    $res      = & $this->_db->query( $sql, array( 'integer', 'text', 'text' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $result = array();
    while( $row = $res->fetchRow()) {
      if( !isset( $row['calendar_id'] ) || empty( $row['calendar_id'] ))
        continue;
      if( !isset( $result[$row['calendar_id']] ))
        $result[$row['calendar_id']] = array( 'calendar_id' => $row['calendar_id'] );
      $result[$row['calendar_id']][$row['xprop_key']] = $row['xprop_text'];
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    return $result;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2011-11-13
   */
  function delete( $owner_id, $ownertype ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere  = 'FROM xprop WHERE xprop_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND xprop_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql  = "SELECT xprop_id AS id $fromWhere ORDER BY 1";
    $res  = & $this->_db->query( $sql );
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
    foreach( $result as $xprop_id) {
      $res = $dbiCal_parameter_DAO->delete( $xprop_id, 'xprop' );
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
   * @param    string $xpropName
   * @param    array  $xpropValue
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $xpropName, $xpropValue ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO xprop (xprop_owner_id, xprop_ownertype';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' );
    if( !empty( $xpropName )) {
      $sql    .= ', xprop_key';
      $values .= ', '.$this->_db->quote( $xpropName, 'text' );
    }
    if( isset( $xpropValue['value'] ) && !empty( $xpropValue['value'] )) {
      $sql    .= ', xprop_text';
      $values .= ', '.$this->_db->quote( $xpropValue['value'], 'text' );
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $xprop_id = $this->_db->lastInsertID( 'xprop', 'xprop_id' );
    if( PEAR::isError( $xprop_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$xprop_id->getUserInfo().PHP_EOL.$xprop_id->getMessage(), PEAR_LOG_ALERT );
      return $xprop_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'xprop_id='.$xprop_id, PEAR_LOG_INFO );
    if( isset( $xpropValue['params'] ) && !empty( $xpropValue['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $xpropValue['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $xprop_id, 'xprop', $xpropName, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $xprop_id;
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
    $sql  = 'SELECT * FROM xprop WHERE xprop_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND xprop_ownertype = '.$this->_db->quote( $ownertype, 'text' ).' ORDER BY xprop_id';
    $types = array( 'integer', 'integer', 'text', 'text', 'text' );
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
      if( isset( $tablerow['xprop_key'] )  && !empty( $tablerow['xprop_key'] ) &&
          isset( $tablerow['xprop_text'] ) && !empty( $tablerow['xprop_text'] )) {
        $result[ $tablerow['xprop_key']]['id']    = $tablerow['xprop_id'];
        $result[ $tablerow['xprop_key']]['value'] = $tablerow['xprop_text'];
        if( $this->_log )
          $this->_log->log( $ownertype.' : '.$tablerow['xprop_key'].' : '.$tablerow['xprop_text'], PEAR_LOG_DEBUG );
      }
    }
    $res->free();
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $xpropName => & $pValue ) {
      $res = $dbiCal_parameter_DAO->select( $pValue['id'], 'xprop', $xpropName);
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $key => $value )
          $pValue['params'][$key] = $value;
      }
      else
        $pValue['params'] = FALSE;
    }
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' xprops', PEAR_LOG_INFO );
    return $result;
  }
}
?>