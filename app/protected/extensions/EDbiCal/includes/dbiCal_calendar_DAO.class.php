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
 * This file implements the calendar table DAO
 *
**/
class dbiCal_calendar_DAO {
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
   * @since    1.0 - 2010-10-30
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
      self::$theInstance = new dbiCal_calendar_DAO( $_db, $_log  );
    return self::$theInstance;
  }
  /**
   * getCalendars
   *
   * @access   public
   * @param    array  $selectOptions
   * @param    bool   $fullSearch
   * @return   mixed  $res (PEAR::Error or array calendars)
   * @since    1.0 - 2011-02-08
   */
  function getCalendars( $selectOptions=array(), $fullSearch=TRUE ) {
    if( $this->_log )
      $this->_log->log( __METHOD__.' start selectOptions'.var_export($selectOptions, TRUE )." fullSearch=$fullSearch", PEAR_LOG_INFO );
    $calendars = $cx = $cm = $cc = array();
    $xpropSearch = $metadataSearch = FALSE;
            // selectOption 'calendar_id' overrides all
    if( isset( $selectOptions['calendar_id'] ))
      $calendars = array( $selectOptions['calendar_id'] => array( 'calendar_id' => $selectOptions['calendar_id'] ));
    else {
      foreach( $selectOptions as $xpropName => $xpropValue ) {
        if( 'X-' == strtoupper( substr( $xpropName, 0, 2 ))) {
          $xpropSearch = TRUE;
          continue;
        }
        if( in_array( strtolower( $xpropName ), array( 'calendar_id', 'date', 'filename' )))
          continue;
        $metadataSearch = TRUE;
      }
    }
            // check and fetch calendar_id(-s) from xprop table
    if( $xpropSearch ) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      $cx = $dbiCal_xprop_DAO->getOwners( 'calendar', $selectOptions );
      if( PEAR::isError( $cx ))
        return $cx;
    }
            // check and fetch calendar_id(-s) from metadata table
    if( $metadataSearch ) {
      $dbiCal_metadata_DAO = dbiCal_metadata_DAO::singleton( $this->_db, $this->_log );
      $cm = $dbiCal_metadata_DAO->select( $selectOptions );
      if( PEAR::isError( $cm ))
        return $cm;
    }
            // check and fetch calendar_id(-s) from calendar table
    if( isset( $selectOptions['date'] ) || isset( $selectOptions['filename'] ) || empty( $selectOptions )) {
      $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
      $sql = 'SELECT calendar_id FROM calendar WHERE 1=1';
      if( isset( $selectOptions['date'] ))
        $sql .= ' AND DATE(calendar_create_date) = '.$this->_db->quote( $selectOptions['date'], 'date' );
      if( isset( $selectOptions['filename'] ))
        $sql .= ' AND calendar_filename = '.$this->_db->quote( $selectOptions['filename'], 'text' );
      $sql .= ' ORDER BY 1 DESC';
      $res  = & $this->_db->query( $sql, array( 'integer' ));
      if( PEAR::isError( $res )) {
        if( $this->_log )
          $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return $res;
      }
      while( $tablerow = $res->fetchRow())
        $cc[$tablerow['calendar_id']] = array( 'calendar_id' => $tablerow['calendar_id'] );
      $res->free();
      if( $this->_log )
        $this->_log->log( $sql.' result='.implode(',', array_keys( $cc )), PEAR_LOG_DEBUG );
    }
    if( !empty( $cx )) {
      $calendars = $cx;
      if( $this->_log )
        $this->_log->log( 'xprop ids='.implode(',', array_keys( $cx )), PEAR_LOG_DEBUG );
    }
    if( !empty( $cm )) {
      if( $this->_log )
        $this->_log->log( 'meta  ids='.implode(',', array_keys( $cm )), PEAR_LOG_DEBUG );
      if( !empty( $calendars ))
        $calendars = array_intersect_key( $calendars, $cm ); // fix the intersection of arrays
      else
        $calendars = $cm;
    }
    if( !empty( $cc )) {
      if( $this->_log )
        $this->_log->log( 'clndr ids='.implode(',', array_keys( $cc )), PEAR_LOG_DEBUG );
      if( !empty( $calendars ))
        $calendars = array_intersect_key( $calendars, $cc ); // fix the intersection of arrays
      else
        $calendars = $cc;
    }
    if( $this->_log )
      $this->_log->log( 'comb  ids='.implode(',', array_keys( $calendars )), PEAR_LOG_INFO );
    if( !$fullSearch ) {
      krsort( $calendars );
      return $calendars;
    }
    foreach( $calendars as $calendar_id => & $calendar ) {
      $res = $this->select( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
      foreach( $res as $cname => $cvalue )
        $calendar[$cname] = $cvalue;
    }
    return $calendars;
  }
  /**
   * delete
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller TRUE)
   * @since    1.0 - 2011-01-21
   */
  function delete( $calendar_id ) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start calendar_id=$calendar_id", PEAR_LOG_INFO );
    if( empty( $calendar_id ))
      return FALSE;
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $fromWhere = 'FROM calendar WHERE calendar_id = '.$this->_db->quote( $calendar_id, 'integer' );
    $sql  = "SELECT calendar_cnt_metadata, calendar_cnt_xprop, calendar_cnt_timezone, calendar_cnt_event, calendar_cnt_freebusy, calendar_cnt_journal, calendar_cnt_todo $fromWhere LIMIT 1";
    $res  = & $this->_db->query( $sql, array_fill( 0, 7, 'integer' ));
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $calendar_cnts = $res->fetchRow();
    $res->free();
    if( $this->_log )
      $this->_log->log( 'sql='.$sql.PHP_EOL.'result='.var_export( $calendar_cnts, TRUE ), PEAR_LOG_DEBUG );
    if( empty( $calendar_cnts))
      return null;

    if( !empty( $calendar_cnts['calendar_cnt_metadata'] )) {
      $dbiCal_metadata_DAO = dbiCal_metadata_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_metadata_DAO->delete( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
    }
    if( !empty( $calendar_cnts['calendar_cnt_timezone'] )) {
      $dbiCal_timezone_DAO = dbiCal_timezone_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_timezone_DAO->delete( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
    }
    if( !empty( $calendar_cnts['calendar_cnt_event'] )) {
      $dbiCal_event_DAO = dbiCal_event_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_event_DAO->delete( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
    }
    if( !empty( $calendar_cnts['calendar_cnt_todo'] )) {
      $dbiCal_todo_DAO = dbiCal_todo_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_todo_DAO->delete( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
    }
    if( !empty( $calendar_cnts['calendar_cnt_journal'] )) {
      $dbiCal_journal_DAO = dbiCal_journal_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_journal_DAO->delete( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
    }
    if( !empty( $calendar_cnts['calendar_cnt_freebusy'] )) {
      $dbiCal_freebusy_DAO = dbiCal_freebusy_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_freebusy_DAO->delete( $calendar_id );
      if( PEAR::isError( $res ))
        return $res;
    }
    if( !empty( $calendar_cnts['calendar_cnt_xprop'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_xprop_DAO->delete( $calendar_id, 'calendar' );
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
   * @param    array $calendarValues
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-01-20
   */
  public function insert( & $calendarValues= array() ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO calendar (calendar_create_date';
    $values    = ') VALUES (NOW()';
    if( isset( $calendarValues['filename'] )) {
      $sql    .= ', calendar_filename';
      $values .= ', '.$this->_db->quote( $calendarValues['filename'], 'text' );
    }
    if( isset( $calendarValues['VERSION'] )) {
      $sql    .= ', calendar_version';
      $values .= ', '.$this->_db->quote( $calendarValues['VERSION'], 'text' );
    }
    if( isset( $calendarValues['unique_id'] )) {
      $sql    .= ', calendar_unique_id';
      $values .= ', '.$this->_db->quote( $calendarValues['unique_id'], 'text' );
    }
    if( isset( $calendarValues['CALSCALE'] )) {
      $sql    .= ', calendar_calscale';
      $values .= ', '.$this->_db->quote( $calendarValues['CALSCALE'], 'text' );
    }
    if( isset( $calendarValues['METHOD'] )) {
      $sql    .= ', calendar_method';
      $values .= ', '.$this->_db->quote( $calendarValues['METHOD'], 'text' );
    }
    if( isset( $calendarValues['filesize'] )) {
      $sql    .= ', calendar_filesize';
      $values .= ', '.$this->_db->quote( $calendarValues['filesize'], 'integer' );
    }
    if( isset( $calendarValues['cnt_metadata'] )) {
      $sql    .= ', calendar_cnt_metadata';
      $values .= ', '.$this->_db->quote( $calendarValues['cnt_metadata'], 'integer' );
    }
    if( isset( $calendarValues['cnt_xprop'] )) {
      $sql    .= ', calendar_cnt_xprop';
      $values .= ', '.$this->_db->quote( $calendarValues['cnt_xprop'], 'integer' );
    }
    if( isset( $calendarValues['vtimezone'] )) {
      $sql    .= ', calendar_cnt_timezone';
      $values .= ', '.$this->_db->quote( $calendarValues['vtimezone'], 'integer' );
    }
    if( isset( $calendarValues['vevent'] )) {
      $sql    .= ', calendar_cnt_event';
      $values .= ', '.$this->_db->quote( $calendarValues['vevent'], 'integer' );
    }
    if( isset( $calendarValues['vfreebusy'] )) {
      $sql    .= ', calendar_cnt_freebusy';
      $values .= ', '.$this->_db->quote( $calendarValues['vfreebusy'], 'integer' );
    }
    if( isset( $calendarValues['vjournal'] )) {
      $sql    .= ', calendar_cnt_journal';
      $values .= ', '.$this->_db->quote( $calendarValues['vjournal'], 'integer' );
    }
    if( isset( $calendarValues['vtodo'] )) {
      $sql    .= ', calendar_cnt_todo';
      $values .= ', '.$this->_db->quote( $calendarValues['vtodo'], 'integer' );
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $calendar_id = $this->_db->lastInsertID( 'calendar', 'calendar_id' );
    if( PEAR::isError( $calendar_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$calendar_id->getUserInfo().PHP_EOL.$calendar_id->getMessage(), PEAR_LOG_ALERT );
      return $calendar_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'calendar_id='.$calendar_id, PEAR_LOG_INFO );
// if( $this->_log ) $this->_log->log( var_export( $calendarValues['XPROP'], TRUE ), PEAR_LOG_DEBUG );   // test ### ================================================0
    if( isset( $calendarValues['XPROP'] ) && !empty( $calendarValues['XPROP'] ) && is_array( $calendarValues['XPROP'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      foreach( $calendarValues['XPROP'] as $xprop ) {
        if( isset( $xprop[1]['value'] ) && !empty( $xprop[1]['value'] )) {
          $res = $dbiCal_xprop_DAO->insert( $calendar_id, 'calendar', $xprop[0], $xprop[1] );
          if( PEAR::isError( $res ))
            return $res;
        }
      }
    }
    if( isset( $calendarValues['metadata'] ) && !empty( $calendarValues['metadata'] ) && is_array( $calendarValues['metadata'] )) {
      $dbiCal_metadata_DAO = dbiCal_metadata_DAO::singleton( $this->_db, $this->_log );
      foreach( $calendarValues['metadata'] as $metadataKey => $metadataValue ) {
        $res = $dbiCal_metadata_DAO->insert( $calendar_id, $metadataKey, $metadataValue );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $calendar_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $calendar_id
   * @return   mixed  $res (PEAR::Error eller resultat-array)
   * @since    1.0 - 2011-02-15
   */
  function select( $calendar_id ) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start calendar_id=$calendar_id", PEAR_LOG_INFO );
    if( empty( $calendar_id ))
      return FALSE;
    $this->_db->setFetchMode( MDB2_FETCHMODE_ASSOC );
    $sql = 'SELECT * FROM calendar WHERE calendar_id = '.$this->_db->quote( $calendar_id, 'integer' ).' LIMIT 1';
    $types = array( 'integer', 'timestamp', 'text', 'text', 'text', 'text', 'text' );
    $types = array_merge( $types, array_fill( count( $types ), 8, 'integer' ));
    $res  = & $this->_db->query( $sql, $types );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $tablerow = $res->fetchRow();
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql, PEAR_LOG_INFO );
    $result = array();
    if( !isset( $tablerow['calendar_id'] )) // not found
      return FALSE;
    else
      $result['calendar_id'] = $tablerow['calendar_id'];
    if( $this->_log )
      $this->_log->log( var_export( $tablerow, TRUE ), PEAR_LOG_DEBUG );
    if( isset( $tablerow['calendar_create_date'] ))
      $result['create_date'] = $tablerow['calendar_create_date'];
    if( isset( $tablerow['calendar_filename'] ))
      $result['filename']    = $tablerow['calendar_filename'];
    if( isset( $tablerow['calendar_filesize'] ))
      $result['filesize']    = $tablerow['calendar_filesize'];
    if( isset( $tablerow['calendar_version'] ))
      $result['VERSION']     = $tablerow['calendar_version'];
    if( isset( $tablerow['calendar_unique_id'] ))
      $result['unique_id']   = $tablerow['calendar_unique_id'];
    if( isset( $tablerow['calendar_calscale'] ))
      $result['CALSCALE']    = $tablerow['calendar_calscale'];
    if( isset( $tablerow['calendar_method'] ))
      $result['METHOD']      = $tablerow['calendar_method'];
    if( isset( $tablerow['calendar_cnt_metadata'] ) && !empty( $tablerow['calendar_cnt_metadata'] ))
      $result['calendar_cnt_metadata'] = $tablerow['calendar_cnt_metadata'];
    if( isset( $tablerow['calendar_cnt_timezone'] ) && !empty( $tablerow['calendar_cnt_timezone'] ))
      $result['calendar_cnt_timezone'] = $tablerow['calendar_cnt_timezone'];
    if( isset( $tablerow['calendar_cnt_event'] )    && !empty( $tablerow['calendar_cnt_event'] ))
      $result['calendar_cnt_event']    = $tablerow['calendar_cnt_event'];
    if( isset( $tablerow['calendar_cnt_freebusy'] ) && !empty( $tablerow['calendar_cnt_freebusy'] ))
      $result['calendar_cnt_freebusy'] = $tablerow['calendar_cnt_freebusy'];
    if( isset( $tablerow['calendar_cnt_journal'] )  && !empty( $tablerow['calendar_cnt_journal'] ))
      $result['calendar_cnt_journal']  = $tablerow['calendar_cnt_journal'];
    if( isset( $tablerow['calendar_cnt_todo'] )     && !empty( $tablerow['calendar_cnt_todo'] ))
      $result['calendar_cnt_todo']     = $tablerow['calendar_cnt_todo'];
    if( !empty( $tablerow['calendar_cnt_metadata'] )) {
      $dbiCal_metadata_DAO = dbiCal_metadata_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_metadata_DAO->select( array( 'calendar_id' => $calendar_id ));
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $metadata )
          $result['metadata'] = $metadata;
      }
    }
    if( !empty( $tablerow['calendar_cnt_xprop'] )) {
      $dbiCal_xprop_DAO = dbiCal_xprop_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_xprop_DAO->select( $calendar_id, 'calendar' );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $propName => $propValue )
          $result[$propName] = $propValue;
      }
    }
    return $result;
  }
}
?>