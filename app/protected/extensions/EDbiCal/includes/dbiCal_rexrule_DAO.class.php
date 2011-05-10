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
 * This file implements the rexrule table DAO
 *
**/
class dbiCal_rexrule_DAO {
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
      self::$theInstance = new dbiCal_rexrule_DAO( $_db, $_log  );
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
    $fromWhere  = 'FROM rexrule WHERE rexrule_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $fromWhere .= ' AND rexrule_ownertype = '.$this->_db->quote( $ownertype, 'text' );
    $sql        = "SELECT rexrule_id AS id $fromWhere";
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
    foreach( $result as $rexrule_id) {
      $res = $dbiCal_parameter_DAO->delete( $rexrule_id, 'rexrule' );
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
   * @param    string $rexruletype
   * @param    array  $rexrule
   * @return   mixed  $res (PEAR::Error eller tabell-id)
   * @since    1.0 - 2011-02-20
   */
  public function insert( $owner_id, $ownertype, $rexruletype, $rexrule ) {
    if( $this->_log ) {
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
      $arglist    = func_get_args();
      foreach( $arglist as $aix => $arg )
        $this->_log->log( "argument [$aix]=".var_export( $arg, TRUE ), PEAR_LOG_DEBUG );
    }
    $sql       = 'INSERT INTO rexrule (rexrule_owner_id, rexrule_ownertype, rexrule_type';
    $values    = ') VALUES ('.$this->_db->quote( $owner_id, 'integer' ).', '.$this->_db->quote( $ownertype, 'text' ).', '.$this->_db->quote( $rexruletype, 'text' );
    if( isset( $rexrule['value']['FREQ'] )       && !empty( $rexrule['value']['FREQ'] )) {
      $sql    .= ', rexrule_freq';
      $values .= ', '.$this->_db->quote( $rexrule['value']['FREQ'], 'text' );
    }
    if( isset( $rexrule['value']['COUNT'] )      && !empty( $rexrule['value']['COUNT'] ))  {
      $sql    .= ', rexrule_count';
      $values .= ', '.$this->_db->quote( $rexrule['value']['COUNT'], 'integer' );
    }
    if( isset( $rexrule['value']['UNTIL'] )      && !empty( $rexrule['value']['UNTIL'] )) {
      $value   = sprintf("%04d-%02d-%02d", $rexrule['value']['UNTIL']['year'], $rexrule['value']['UNTIL']['month'], $rexrule['value']['UNTIL']['day']);
      if( isset( $rexrule['value']['UNTIL']['hour'] )) {
        $sql    .= ', rexrule_until_datetime';
        $value  .= ' '.sprintf("%02d:%02d:%02d", $rexrule['value']['UNTIL']['hour'], $rexrule['value']['UNTIL']['min'], $rexrule['value']['UNTIL']['sec']);
        $values .= ', '.$this->_db->quote( $value, 'timestamp' );
      }
      else {
        $sql    .= ', rexrule_until_date';
        $values .= ', '.$this->_db->quote( $value, 'date' );
      }
    }
    if( isset( $rexrule['value']['INTERVAL'] )   && !empty( $rexrule['value']['INTERVAL'] )) {
      $sql    .= ', rexrule_interval';
      $values .= ', '.$this->_db->quote( $rexrule['value']['INTERVAL'], 'integer' );
    }
    if( isset( $rexrule['value']['BYSECOND'] )   && !empty( $rexrule['value']['BYSECOND'] )) {
      $sql    .= ', rexrule_bysecond';
      if( is_array( $rexrule['value']['BYSECOND'] ) )
        $rexrule['value']['BYSECOND'] = implode( ',', $rexrule['value']['BYSECOND'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYSECOND'], 'text' );
    }
    if( isset( $rexrule['value']['BYMINUTE'] )   && !empty( $rexrule['value']['BYMINUTE'] )) {
      $sql    .= ', rexrule_byminute';
      if( is_array( $rexrule['value']['BYMINUTE'] ) )
        $rexrule['value']['BYMINUTE'] = implode( ',', $rexrule['value']['BYMINUTE'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYMINUTE'], 'text' );
    }
    if( isset( $rexrule['value']['BYHOUR'] )     && !empty( $rexrule['value']['BYHOUR'] )) {
      $sql    .= ', rexrule_byhour';
      if( is_array( $rexrule['value']['BYHOUR'] ) )
        $rexrule['value']['BYHOUR'] = implode( ',', $rexrule['value']['BYHOUR'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYHOUR'], 'text' );
    }
    if( isset( $rexrule['value']['BYDAY'] )      && !empty( $rexrule['value']['BYDAY'] )) {
      $sql    .= ', rexrule_byday';
      if( isset( $rexrule['value']['BYDAY']['DAY'] ))
        $rexrule['value']['BYDAY'] = array( $rexrule['value']['BYDAY'] );
      $value = '';
      foreach( $rexrule['value']['BYDAY'] as $byday ) {
        $byday  = implode( ':', array_values( $byday ));
        $value .= ( empty( $value )) ? $byday : "|$byday";
      }
      $values .= ', '.$this->_db->quote( $value, 'text' );
    }
    if( isset( $rexrule['value']['BYMONTHDAY'] ) && !empty( $rexrule['value']['BYMONTHDAY'] )) {
      $sql    .= ', rexrule_bymonthday';
      if( is_array( $rexrule['value']['BYMONTHDAY'] ) )
        $rexrule['value']['BYMONTHDAY'] = implode( ',', $rexrule['value']['BYMONTHDAY'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYMONTHDAY'], 'text' );
    }
    if( isset( $rexrule['value']['BYYEARDAY'] )  && !empty( $rexrule['value']['BYYEARDAY'] )) {
      $sql    .= ', rexrule_byyearday';
      if( is_array( $rexrule['value']['BYYEARDAY'] ) )
        $rexrule['value']['BYYEARDAY'] = implode( ',', $rexrule['value']['BYYEARDAY'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYYEARDAY'], 'text' );
    }
    if( isset( $rexrule['value']['BYWEEKNO'] )   && !empty( $rexrule['value']['BYWEEKNO'] )) {
      $sql    .= ', rexrule_byweekno';
      if( is_array( $rexrule['value']['BYWEEKNO'] ) )
        $rexrule['value']['BYWEEKNO'] = implode( ',', $rexrule['value']['BYWEEKNO'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYWEEKNO'], 'text' );
    }
    if( isset( $rexrule['value']['BYMONTH'] )    && !empty( $rexrule['value']['BYMONTH'] )) {
      $sql    .= ', rexrule_bymonth';
      if( is_array( $rexrule['value']['BYMONTH'] ) )
        $rexrule['value']['BYMONTH'] = implode( ',', $rexrule['value']['BYMONTH'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYMONTH'], 'text' );
    }
    if( isset( $rexrule['value']['BYSETPOS'] )   && !empty( $rexrule['value']['BYSETPOS'] )) {
      $sql    .= ', rexrule_bysetpos';
      if( is_array( $rexrule['value']['BYSETPOS'] ))
        $rexrule['value']['BYSETPOS'] = implode( ',', $rexrule['value']['BYSETPOS'] );
      $values .= ', '.$this->_db->quote( $rexrule['value']['BYSETPOS'], 'text' );
    }
    if( isset( $rexrule['value']['WKST'] )       && !empty( $rexrule['value']['WKST'] )) {
      $sql    .= ', rexrule_wkst';
      $values .= ', '.$this->_db->quote( $rexrule['value']['WKST'], 'text' );
    }
    $sql .= $values.')';
    $res = & $this->_db->exec( $sql );
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( $sql.PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return $res;
    }
    $rexrule_id = $this->_db->lastInsertID( 'rexrule', 'rexrule_id' );
    if( PEAR::isError( $rexrule_id )) {
      if( $this->_log )
        $this->_log->log( 'lastInsertID error:'.$rexrule_id->getUserInfo().PHP_EOL.$rexrule_id->getMessage(), PEAR_LOG_ALERT );
      return $rexrule_id;
    }
    if( $this->_log )
      $this->_log->log( $sql.PHP_EOL.'rexrule_id='.$rexrule_id, PEAR_LOG_INFO );
    if( isset( $rexrule['params'] ) && !empty( $rexrule['params'] )) {
      $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
      foreach( $rexrule['params'] as $key => $value ) {
        if( ctype_digit( (string) $key ))
          continue;
        $res = $dbiCal_parameter_DAO->insert( $rexrule_id, 'rexrule', $rexruletype, $key, $value );
        if( PEAR::isError( $res ))
          return $res;
      }
    }
    return $rexrule_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $owner_id
   * @param    string $ownertype ))
   * @return   mixed  $res (PEAR::Error eller tabell-id)
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
    $sql  = 'SELECT * FROM rexrule WHERE rexrule_owner_id = '.$this->_db->quote( $owner_id, 'integer' );
    $sql .= ' AND rexrule_ownertype = '.$this->_db->quote( $ownertype, 'text' ).' ORDER BY rexrule_id';
    $types = array( 'integer', 'integer', 'text', 'text', 'text', 'integer', 'timestamp', 'date', 'integer'
                  , 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text' );
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
      $row = array( 'rexrule_type' => $tablerow['rexrule_type'] );
      if( isset( $tablerow['rexrule_freq'] )           && !empty( $tablerow['rexrule_freq'] ))
        $row['FREQ']        = $tablerow['rexrule_freq'];
      if( isset( $tablerow['rexrule_count'] )          && !empty( $tablerow['rexrule_count'] ))
        $row['COUNT']       = $tablerow['rexrule_count'];
      if( isset( $tablerow['rexrule_until_datetime'] ) && !empty( $tablerow['rexrule_until_datetime'] )) {
        $dt = str_replace( '-', '', $tablerow['rexrule_until_datetime'] );
        $dt = str_replace( ':', '', $dt );
        $row['UNTIL']       = substr( $dt, 0, 8 ).'T'.substr( $dt, 9, 6 ).'Z';
      }
      elseif( isset( $tablerow['rexrule_until_date'] ) && !empty( $tablerow['rexrule_until_date'] ))
        $row['UNTIL']       = str_replace( '-', '', $tablerow['rexrule_until_date'] );
      if( isset( $tablerow['rexrule_interval'] )       && !empty( $tablerow['rexrule_interval'] ))
        $row['INTERVAL']    = $tablerow['rexrule_interval'];
      if( isset( $tablerow['rexrule_bysecond'] )       && !empty( $tablerow['rexrule_bysecond'] )) {
        $row['BYSECOND']    = explode( ',', $tablerow['rexrule_bysecond'] );
        if( 1 == count( $row['BYSECOND'] ))
          $row['BYSECOND']  = $row['BYSECOND'][0];
      }
      if( isset( $tablerow['rexrule_byminute'] )       && !empty( $tablerow['rexrule_byminute'] )) {
        $row['BYMINUTE']    = explode( ',', $tablerow['rexrule_byminute'] );
        if( 1 == count( $row['BYMINUTE'] ))
          $row['BYMINUTE']  = $row['BYMINUTE'][0];
      }
      if( isset( $tablerow['rexrule_byhour'] )         && !empty( $tablerow['rexrule_byhour'] )) {
        $row['BYHOUR']      = explode( ',', $tablerow['rexrule_byhour'] );
        if( 1 == count( $row['BYHOUR'] ))
          $row['BYHOUR']  = $row['BYHOUR'][0];
      }
      if( isset( $tablerow['rexrule_byday'] )          && !empty( $tablerow['rexrule_byday'] )) {
        $bydays = explode( '|', $tablerow['rexrule_byday'] );
        foreach( $bydays as $byday ) {
          $byday = explode( ':', $byday );
          if( 1 == count( $byday ))
            $row['BYDAY'][] = array( 'DAY' => $byday[0] );
          else
            $row['BYDAY'][] = array( $byday[0], 'DAY' => $byday[1] );
        }
        if( 1 == count( $row['BYDAY'] ))
          $row['BYDAY']     = $row['BYDAY'][0];
      }
      if( isset( $tablerow['rexrule_bymonthday'] )     && !empty( $tablerow['rexrule_bymonthday'] )) {
        $row['BYMONTHDAY']  = explode( ',', $tablerow['rexrule_bymonthday'] );
        if( 1 == count( $row['BYMONTHDAY'] ))
          $row['BYMONTHDAY']  = $row['BYMONTHDAY'][0];
      }
      if( isset( $tablerow['rexrule_byyearday'] )      && !empty( $tablerow['rexrule_byyearday'] )) {
        $row['BYYEARDAY']   = explode( ',', $tablerow['rexrule_byyearday'] );
        if( 1 == count( $row['BYYEARDAY'] ))
          $row['BYYEARDAY']  = $row['BYYEARDAY'][0];
      }
      if( isset( $tablerow['rexrule_byweekno'] )       && !empty( $tablerow['rexrule_byweekno'] )) {
        $row['BYWEEKNO']    = explode( ',', $tablerow['rexrule_byweekno'] );
        if( 1 == count( $row['BYWEEKNO'] ))
          $row['BYWEEKNO']  = $row['BYWEEKNO'][0];
      }
      if( isset( $tablerow['rexrule_bymonth'] )        && !empty( $tablerow['rexrule_bymonth'] )) {
        $row['BYMONTH']     = explode( ',', $tablerow['rexrule_bymonth'] );
        if( 1 == count( $row['BYMONTH'] ))
          $row['BYMONTH']  = $row['BYMONTH'][0];
      }
      if( isset( $tablerow['rexrule_bysetpos'] )       && !empty( $tablerow['rexrule_bysetpos'] )) {
        $row['BYSETPOS']    = explode( ',', $tablerow['rexrule_bysetpos'] );
        if( 1 == count( $row['BYSETPOS'] ))
          $row['BYSETPOS']  = $row['BYSETPOS'][0];
      }
      if( isset( $tablerow['rexrule_wkst'] )           && !empty( $tablerow['rexrule_wkst'] ))
        $row['WKST']        = $tablerow['rexrule_wkst'];
      if( !empty( $row )) {
        $result[$tablerow['rexrule_id']]['value'] = $row;
      if( $this->_log )
        $this->_log->log( var_export( $row, TRUE ), PEAR_LOG_DEBUG );
      }
    }
    $res->free();
    if( $this->_log )
      $this->_log->log( $sql.' : '.count( $result ).' rexrules', PEAR_LOG_INFO );
    $dbiCal_parameter_DAO = dbiCal_parameter_DAO::singleton( $this->_db, $this->_log );
    foreach( $result as $rexrule_id => & $rexruleVal ) {
      $res = $dbiCal_parameter_DAO->select( $rexrule_id, 'rexrule', $rexruleVal['value']['rexrule_type'] );
      if( PEAR::isError( $res ))
        return $res;
      if( !empty( $res )) {
        foreach( $res as $key => $value )
          $rexruleVal['params'][$key] = $value;
      }
      else
        $rexruleVal['params'] = FALSE;
    }
    return $result;
  }
}
?>