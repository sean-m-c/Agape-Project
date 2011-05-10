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
 * This file implements the mtext table DAO
 * ATTENDEE, CATEGORIES, COMMENT, CONTACT, DESCRIPTION, RELATED-TO, RESOURCES, REQUEST-STATUS
 *
**/
class dbiCal_mtext_DAO {
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
    self:$theInstance = FALSE;
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
      self::$theInstance = new dbiCal_mtext_DAO( $_db, $_log  );
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
    $fromWhere  = 'FROM mtext WHERE mtext_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND mtext_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql  = "SELECT mtext_id AS id $fromWhere";
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
    foreach( $result as $mtext_id) {
      $res = $dbiCal_parameter_DAO->delete( $mtext_id, 'mtext' );
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
   * @param    string $mtextName
   * @param    array  $mtextVal
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $mtextName, $mtextVal ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO mtext (mtext_owner_id, mtext_ownertype, mtext_name';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' ).', '.$this->_db->quote( $mtextName, 'text' );
    if( isset( $mtextVal['value'] ) && !empty( $mtextVal['value'] )) {
      if( 'REQUEST-STATUS' == $mtextName ) {
        $value = '';
        foreach( $mtextVal['value'] as $k => $v )
          $value .= ( empty( $value )) ? "$k=$v" : "|$k=$v";
        $mtextVal['value'] = $value;
      }
      $sql    .= ', mtext_mtext';
      $values .= ', '.$this->_db->quote( $mtextVal['value'], 'text' );
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $mtext_id = $this->_db->lastInsertID( 'mtext', 'mtext_id' );
    if( PEAR::isError( $mtext_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$mtext_id->getUserInfo().PHP_EOL.$attandee_id->getMessage(), PEAR_LOG_ALERT );
      return $mtext_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'mtext_id='.$mtext_id, PEAR_LOG_INFO );
    if( isset( $mtextVal['params'] ) && !empty( $mtextVal['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $mtextVal['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $mtext_id, 'mtext', $mtextName, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $mtext_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype
   * @param    string $mtextName
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-01-20
   */
  function select( $owner_id, $ownertype, $mtextName=FALSE ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql       = 'SELECT * FROM mtext WHERE mtext_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql      .= ' AND mtext_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $types     =  array( 'integer', 'integer', 'text', 'text', 'text' );
    if( $mtextName )
      $sql    .= ' AND mtext_name = '.$this->_db->quote( $mtextName, 'text' );
    $sql .= ' ORDER BY mtext_id';
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
      if( isset( $tablerow['mtext_name'] )  && !empty( $tablerow['mtext_name'] ) &&
          isset( $tablerow['mtext_mtext'] ) && !empty( $tablerow['mtext_mtext'] )) {
        if( 'REQUEST-STATUS' == $tablerow['mtext_name'] ) {
          $rstatusParts = explode( '|', $tablerow['mtext_mtext'] );
          $tablerow['mtext_mtext'] = array();
          foreach( $rstatusParts as $rstatus ) {
            list( $key, $value ) = explode( '=', $rstatus, 2 );
            $tablerow['mtext_mtext'][$key] = $value;
          }
          if( !isset( $tablerow['mtext_mtext']['extdata'] ))
            $tablerow['mtext_mtext']['extdata'] = FALSE;
        }
        $result[$tablerow['mtext_name']][$tablerow['mtext_id']]['value'] = $tablerow['mtext_mtext'];
        if( $this->_log )
          $this->_log->log( $tablerow['mtext_name'].' ('.$tablerow['mtext_id'].') '.$tablerow['mtext_mtext'], PEAR_LOG_DEBUG );
      }
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' mtexts', PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $mtext_name => $mtextVal ) {
      foreach( $mtextVal as $mtext_id => $mtextContent ) {
        $res = $dbiCal_parameter_DAO->select( $mtext_id, 'mtext', $mtext_name );
        if( PEAR::isError( $res ))
          return $res;
        if( !empty( $res )) {
          foreach( $res as $key => $value )
            $result[$mtext_name][$mtext_id]['params'][$key] = $value;
        }
        else
          $result[$mtext_name][$mtext_id]['params'] = FALSE;
      }
    }
    return $result;
  }
}
?>