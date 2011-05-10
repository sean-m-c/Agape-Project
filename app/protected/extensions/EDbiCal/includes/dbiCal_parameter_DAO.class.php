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
 * This file implements the parameter table DAO
 *
**/
class dbiCal_parameter_DAO {
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
   * @since    1.0 - 2010-11-04
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
   * @since    1.0 - 2010-11-04
   */
  public static function singleton( & $_db, & $_log ) {
    if (!self::$theInstance)
      self::$theInstance = new dbiCal_parameter_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @param    string $ownerProperty
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2010-11-13
   */
  function delete( $owner_id, $ownertype, $ownerProperty=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql  = 'DELETE FROM parameter WHERE parameter_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND parameter_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    if( $ownerProperty )
      $sql .= ' AND parameter_property = '.$this->_db->quote( $ownerProperty, 'text' );
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
   * @param    string $ownerProperty
   * @param    string $parameterKey
   * @param    string $parameterValue
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $ownerProperty, $parameterKey, $parameterValue ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql     = 'INSERT INTO parameter (parameter_owner_id, parameter_ownertype, parameter_property';
    $values  = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' );
    $values .= ', '.$this->_db->quote( strtoupper( $ownerProperty ), 'text' );
    $sql    .= ', parameter_key';
    $values .= ', '.$this->_db->quote( $parameterKey, 'text' );
    $sql    .= ', parameter_value';
    if( is_array( $parameterValue ))
      $parameterValue = implode( '|', $parameterValue );
    $values .= ', '.$this->_db->quote( $parameterValue, 'text' );
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $parameter_id = $this->_db->lastInsertID( 'parameter', 'parameter_id' );
    if( PEAR::isError( $parameter_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$parameter_id->getUserInfo().PHP_EOL.$parameter_id->getMessage(), PEAR_LOG_ALERT );
      return $parameter_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'parameter_id='.$parameter_id, PEAR_LOG_INFO );
    return $parameter_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @param    string $ownerProperty
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-02-20
   */
  function select( $owner_id, $ownertype, $ownerProperty=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'SELECT * FROM parameter WHERE parameter_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND parameter_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $types = array( 'integer', 'integer', 'text', 'text', 'text', 'text' );
    if( $ownerProperty ) {
      $ownerProperty = strtoupper( $ownerProperty );
      $sql .= ' AND parameter_property = '.$this->_db->quote( $ownerProperty, 'text' );
    }
    $sql .= ' ORDER BY parameter_id';
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
      if( isset( $tablerow['parameter_key'] ) && isset( $tablerow['parameter_value'] )) {
        if( FALSE !==  strpos( $tablerow['parameter_value'], '|' ))
          $tablerow['parameter_value'] = explode( '|', $tablerow['parameter_value'] );
        if( !$ownerProperty )
          $result[$tablerow['parameter_property']][$tablerow['parameter_key']] = $tablerow['parameter_value'];
        else
          $result[$tablerow['parameter_key']] = $tablerow['parameter_value'];
        if( $this->_log )
          $this->_log->log( $tablerow['parameter_property'].' : '.$tablerow['parameter_key'].' : '.$tablerow['parameter_value'], PEAR_LOG_DEBUG );
      }
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' parameters', PEAR_LOG_INFO );
    return $result;
  }
}
?>