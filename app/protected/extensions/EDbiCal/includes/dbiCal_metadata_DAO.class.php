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
 * This file implements the metadata table DAO
 *
**/
class dbiCal_metadata_DAO {
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
      self::$theInstance = new dbiCal_metadata_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $owner_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2011-01-21
   */
  function delete( $owner_id ) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start owner_id=$owner_id", PEAR_LOG_INFO );
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql  = 'DELETE FROM metadata WHERE metadata_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $res  = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return $res;
    }
    if( $this->_log )
      $this->_log->log( "$sql : $res deleted rows", PEAR_LOG_INFO ); // show number of affected rows
    return TRUE;
  }
  /**
   * insert
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $metadataKey
   * @param    array  $metadataValue
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-01-22
   */
  public function insert( $owner_id, $metadataKey, $metadataValue ) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start owner_id=$owner_id, metadataKey=$metadataKey, metadataValue=$metadataValue", PEAR_LOG_INFO );
    $sql       = 'INSERT INTO metadata (metadata_owner_id';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' );
    if( !empty( $metadataKey )) {
      $sql    .= ', metadata_key';
      $values .= ', '.$this->_db->quote( $metadataKey, 'text' );
    }
    if( !empty( $metadataValue )) {
      $sql    .= ', metadata_text';
      $values .= ', '.$this->_db->quote( $metadataValue, 'text' );
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $metadata_id = $this->_db->lastInsertID( 'metadata', 'metadata_id' );
    if( PEAR::isError( $metadata_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$metadata_id->getUserInfo().PHP_EOL.$metadata_id->getMessage(), PEAR_LOG_ALERT );
      return $metadata_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'metadata_id='.$metadata_id, PEAR_LOG_INFO );
    return $metadata_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    array  $selectOptions
   * @return   mixed  $res (PEAR::Error eller array)
   * @since    1.0 - 2011-02-15
   */
  function select( $selectOptions=array()) {
    if( $this->_log )
      $this->_log->log( __METHOD__.' start selectOptions='.var_export($selectOptions, TRUE ), PEAR_LOG_INFO );
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql      = 'SELECT metadata_owner_id AS calendar_id, metadata_key, metadata_text FROM metadata WHERE';
    if( isset( $selectOptions['calendar_id'] ))
      $sql   .= ' metadata_owner_id = '.$this->_db->quote( $selectOptions['calendar_id'], 'integer' );
    else {
      $orsw  = FALSE;
      foreach( $selectOptions as $metadataKey => $metadataValue ) {
        if(( 'X-' == strtoupper( substr( $metadataKey, 0, 2 ))) ||
           ( in_array( strtolower( $metadataKey ), array( 'date', 'filename' ))))
          continue;
        if( $orsw )
          $sql .= ' OR';
        $sql .= ' (LOWER(metadata_key) = '.$this->_db->quote( strtolower( $metadataKey ), 'text' );
        if( !empty( $metadataValue ))
          $sql .= ' AND metadata_text = '.$this->_db->quote( $metadataValue, 'text' );
        $sql .= ')';
        $orsw  = TRUE;
      }
    }
	$sql     .= ' ORDER BY 1 DESC, 2';
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
      $result[$row['calendar_id']][$row['metadata_key']] = $row['metadata_text'];
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'result='.var_export( $result, TRUE ), PEAR_LOG_DEBUG );
    return $result;
  }
}
?>