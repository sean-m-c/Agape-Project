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
 * This file implements the dbiCal class
 *
**/
require_once 'dbiCal_calendar_DAO.class.php';
require_once 'dbiCal_metadata_DAO.class.php';
require_once 'dbiCal_parameter_DAO.class.php';
require_once 'dbiCal_timezone_DAO.class.php';
require_once 'dbiCal_stddlght_DAO.class.php';
require_once 'dbiCal_event_DAO.class.php';
require_once 'dbiCal_todo_DAO.class.php';
require_once 'dbiCal_journal_DAO.class.php';
require_once 'dbiCal_freebusy_DAO.class.php';
require_once 'dbiCal_alarm_DAO.class.php';
require_once 'dbiCal_attach_DAO.class.php';
require_once 'dbiCal_exdate_DAO.class.php';
require_once 'dbiCal_mtext_DAO.class.php';
require_once 'dbiCal_pfreebusy_DAO.class.php';
require_once 'dbiCal_rdate_DAO.class.php';
require_once 'dbiCal_rexrule_DAO.class.php';
require_once 'dbiCal_xprop_DAO.class.php';

class dbiCal {
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
   * @access   public
   * @param    object $_db
   * @param    object $_log
   * @return   void
   * @since    1.0 - 2011-01-18
   */
  function __construct( & $_db, & $_log ) {
    $this->_db = $_db;
    if( $_log )
      $this->_log = $_log;
    if( $this->_log )
      $this->_log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
  }
  /**
   * getCalendars
   *
   * @access   public
   * @param    array  $selectOptions
   * @return   mixed  $res (FALSE or array calendars)
   * @since    1.0 - 2011-01-20
   */
  function getCalendars( $selectOptions=array()) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start selectOptions=".var_export( $selectOptions, TRUE ), PEAR_LOG_INFO );
    $dbiCal_calendar_DAO = dbiCal_calendar_DAO::singleton( $this->_db, $this->_log );
    $calendars = $dbiCal_calendar_DAO->getCalendars( $selectOptions );
    if( PEAR::isError( $calendars ))
      return FALSE;
    return $calendars;
  }
  public function test() { echo 'test'; }
  /**
   * delete
   *
   * @access   public
   * @param    array  $selectOptions
   * @return   bool  $res
   * @since    1.0 - 2011-01-20
   */
  function delete( $selectOptions=array()) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start selectOptions=".var_export( $selectOptions, TRUE ), PEAR_LOG_INFO );
    if( FALSE === $this->transactionBegin())
      return FALSE;
    $dbiCal_calendar_DAO = dbiCal_calendar_DAO::singleton( $this->_db, $this->_log );
    if( isset( $selectOptions['calendar_id'] ))
      $calendars = array( 'calendar_id' => array( 'calendar_id' => $selectOptions['calendar_id'] ));
    else {
      $calendars = $dbiCal_calendar_DAO->getCalendars( $selectOptions, FALSE );
      if( PEAR::isError( $calendars )) {
        $this->transactionRollback();
        return FALSE;
      }
    }
    if( $this->_log )
      $this->_log->log( 'calendars='.var_export( $calendars, TRUE ), PEAR_LOG_DEBUG );
    foreach( $calendars as $calendar ) {
      $res = $dbiCal_calendar_DAO->delete( $calendar['calendar_id']  );
      if( PEAR::isError( $res )) {
        $this->transactionRollback();
        return FALSE;
      }
    }
    if( FALSE === $this->transactionCommit())
      return FALSE;
    return TRUE;
  }
  /**
   * insert
   *
   * @access   public
   * @param    object $calendar
   * @param    array  $metadata
   * @return   mixed  $res (FALSE or calendar-id)
   * @since    1.0 - 2011-01-21
   */
  function insert( & $calendar, $metadata=array()) {
    if( $this->_log )
      $this->_log->log( __METHOD__.' start', PEAR_LOG_INFO );
    if( FALSE === $this->transactionBegin())
      return FALSE;
    $data = array();
    if   ( $prop = $calendar->getProperty( 'calscale' ))         $data['CALSCALE']  = $prop;
    if   ( $prop = $calendar->getProperty( 'method'   ))         $data['METHOD']    = $prop;
    if   ( $prop = $calendar->getProperty( 'version'  ))         $data['VERSION']   = $prop;
    $unique_id   = $calendar->getConfig( 'unique_id' );
    if( !in_array( $unique_id, array( 'localhost', '127.0.0.1' )))
                                                                 $data['unique_id'] = $unique_id;
                                                                 $data['filename']  = $calendar->getConfig( 'filename' );
    $filesize    = $calendar->getConfig( 'filesize' );
    if( 0 < $filesize )                                          $data['filesize']  = $filesize;
    foreach( $calendar->components as $compix => & $comp )
      $data[$comp->objName] = isset( $data[$comp->objName] ) ? ( $data[$comp->objName] + 1 ) : 1;
    while( $prop = $calendar->getProperty( FALSE, FALSE, TRUE )) $data['XPROP'][]   = $prop;
    if( !empty( $data['XPROP'] ))
      $data['cnt_xprop'] = count( $data['XPROP'] );
    if( !empty( $metadata )) {
      $data['cnt_metadata'] = count( $metadata );
      $data['metadata']     = $metadata;
    }
    $dbiCal_calendar_DAO = dbiCal_calendar_DAO::singleton( $this->_db, $this->_log );
    $calendar_id = $dbiCal_calendar_DAO->insert( $data );
    if( PEAR::isError( $calendar_id )) {
      $this->transactionRollback();
      return FALSE;
    }
    foreach( $calendar->components as $compix => & $comp ) {
      $data = array( 'ono' => $compix );
      while( $prop = $comp->getProperty( 'attach',           FALSE, TRUE )) $data['ATTACH'][]         = $prop;
      while( $prop = $comp->getProperty( 'attendee',         FALSE, TRUE )) $data['ATTENDEE'][]       = $prop;
      while( $prop = $comp->getProperty( 'categories',       FALSE, TRUE )) $data['CATEGORIES'][]     = $prop;
      if   ( $prop = $comp->getProperty( 'class',            FALSE, TRUE )) $data['CLASS']            = $prop;
      while( $prop = $comp->getProperty( 'comment',          FALSE, TRUE )) $data['COMMENT'][]        = $prop;
      if   ( $prop = $comp->getProperty( 'completed',        FALSE, TRUE )) $data['COMPLETED']        = $prop;
      while( $prop = $comp->getProperty( 'contact',          FALSE, TRUE )) $data['CONTACT'][]        = $prop;
      if   ( $prop = $comp->getProperty( 'created',          FALSE, TRUE )) $data['CREATED']          = $prop;
      while( $prop = $comp->getProperty( 'description',      FALSE, TRUE )) $data['DESCRIPTION'][]    = $prop;
      if   ( $prop = $comp->getProperty( 'dtend',            FALSE, TRUE )) $data['DTEND']            = $prop;
      if   ( $prop = $comp->getProperty( 'dtstamp',          FALSE, TRUE )) $data['DTSTAMP']          = $prop;
      if   ( $prop = $comp->getProperty( 'dtstart',          FALSE, TRUE )) $data['DTSTART']          = $prop;
      if   ( $prop = $comp->getProperty( 'due',              FALSE, TRUE )) $data['DUE']              = $prop;
      if   ( $prop = $comp->getProperty( 'duration',         FALSE, TRUE )) $data['DURATION']         = $prop;
      while( $prop = $comp->getProperty( 'exdate',           FALSE, TRUE )) $data['EXDATE'][]         = $prop;
      while( $prop = $comp->getProperty( 'exrule',           FALSE, TRUE )) $data['EXRULE'][]         = $prop;
      while( $prop = $comp->getProperty( 'freebusy',         FALSE, TRUE )) $data['FREEBUSY'][]       = $prop;
      if   ( $prop = $comp->getProperty( 'geo',              FALSE, TRUE )) $data['GEO']              = $prop;
      if   ( $prop = $comp->getProperty( 'last-modified',    FALSE, TRUE )) $data['LAST-MODIFIED']    = $prop;
      if   ( $prop = $comp->getProperty( 'location',         FALSE, TRUE )) $data['LOCATION']         = $prop;
      if   ( $prop = $comp->getProperty( 'organizer',        FALSE, TRUE )) $data['ORGANIZER']        = $prop;
      if   ( $prop = $comp->getProperty( 'percent-complete', FALSE, TRUE )) $data['PERCENT-COMPLETE'] = $prop;
      if   ( $prop = $comp->getProperty( 'priority',         FALSE, TRUE )) $data['PRIORITY']         = $prop;
      while( $prop = $comp->getProperty( 'rdate',            FALSE, TRUE )) $data['RDATE'][]          = $prop;
      if   ( $prop = $comp->getProperty( 'recurrence-id',    FALSE, TRUE )) $data['RECURRENCE-ID']    = $prop;
      while( $prop = $comp->getProperty( 'related-to',       FALSE, TRUE )) $data['RELATED-TO'][]     = $prop;
      while( $prop = $comp->getProperty( 'request-status',   FALSE, TRUE )) $data['REQUEST-STATUS'][] = $prop;
      while( $prop = $comp->getProperty( 'resources',        FALSE, TRUE )) $data['RESOURCES'][]      = $prop;
      while( $prop = $comp->getProperty( 'rrule',            FALSE, TRUE )) $data['RRULE'][]          = $prop;
      if   ( $prop = $comp->getProperty( 'sequence',         FALSE, TRUE )) $data['SEQUENCE']         = $prop;
      if   ( $prop = $comp->getProperty( 'status',           FALSE, TRUE )) $data['STATUS']           = $prop;
      if   ( $prop = $comp->getProperty( 'summary',          FALSE, TRUE )) $data['SUMMARY']          = $prop;
      if   ( $prop = $comp->getProperty( 'transp',           FALSE, TRUE )) $data['TRANSP']           = $prop;
      if   ( $prop = $comp->getProperty( 'tzid',             FALSE, TRUE )) $data['TZID']             = $prop;
      if   ( $prop = $comp->getProperty( 'tzurl',            FALSE, TRUE )) $data['TZURL']            = $prop;
      if   ( $prop = $comp->getProperty( 'uid',              FALSE, TRUE )) $data['UID']              = $prop;
      if   ( $prop = $comp->getProperty( 'url',              FALSE, TRUE )) $data['URL']              = $prop;
      while( $prop = $comp->getProperty( FALSE,              FALSE, TRUE )) $data['XPROP'][]          = $prop;
      while( $cmp2 = $comp->getComponent()) {
        $subc = array();
        if   ( $prop = $cmp2->getProperty( 'action',         FALSE, TRUE )) $subc['ACTION']           = $prop;
        while( $prop = $cmp2->getProperty( 'attach',         FALSE, TRUE )) $subc['ATTACH'][]         = $prop;
        while( $prop = $cmp2->getProperty( 'attendee',       FALSE, TRUE )) $subc['ATTENDEE'][]       = $prop;
        while( $prop = $cmp2->getProperty( 'comment',        FALSE, TRUE )) $subc['COMMENT'][]        = $prop;
        if   ( $prop = $cmp2->getProperty( 'description',    FALSE, TRUE )) $subc['DESCRIPTION']      = $prop;
        if   ( $prop = $cmp2->getProperty( 'dtstart',        FALSE, TRUE )) $subc['DTSTART']          = $prop;
        if   ( $prop = $cmp2->getProperty( 'duration',       FALSE, TRUE )) $subc['DURATION']         = $prop;
        if   ( $prop = $cmp2->getProperty( 'repeat',         FALSE, TRUE )) $subc['REPEAT']           = $prop;
        while( $prop = $cmp2->getProperty( 'rdate',          FALSE, TRUE )) $subc['RDATE'][]          = $prop;
        while( $prop = $cmp2->getProperty( 'rrule',          FALSE, TRUE )) $subc['RRULE'][]          = $prop;
        if   ( $prop = $cmp2->getProperty( 'summary',        FALSE, TRUE )) $subc['SUMMARY']          = $prop;
        if   ( $prop = $cmp2->getProperty( 'trigger',        FALSE, TRUE )) $subc['TRIGGER']          = $prop;
        while( $prop = $cmp2->getProperty( 'tzname',         FALSE, TRUE )) $subc['TZNAME'][]         = $prop;
        if   ( $prop = $cmp2->getProperty( 'tzoffsetfrom',   FALSE, TRUE )) $subc['TZOFFSETFROM']     = $prop;
        if   ( $prop = $cmp2->getProperty( 'tzoffsetto',     FALSE, TRUE )) $subc['TZOFFSETTO']       = $prop;
        while( $prop = $cmp2->getProperty( FALSE,            FALSE, TRUE )) $subc['XPROP'][]          = $prop;
        $objName = ( 'valarm' == $cmp2->objName ) ? 'ALARM' : strtoupper( $cmp2->objName );
        $data[$objName][] = $subc;
      } // end while( $cmp2 = $comp->getComponent())
      switch( $comp->objName ) {
        case 'vtimezone':
          $dbiCal_timezone_DAO = dbiCal_timezone_DAO::singleton( $this->_db, $this->_log );
          $res = $dbiCal_timezone_DAO->insert( $calendar_id, $data, ( 1 + $compix ));
          if( PEAR::isError( $res )) {
            $this->transactionRollback();
            return FALSE;
          }
          break;
        case 'vevent':
          if( isset( $data['DESCRIPTION'][0] ) &&  !empty( $data['DESCRIPTION'][0] ))
            $data['DESCRIPTION'] = $data['DESCRIPTION'][0];
          $dbiCal_event_DAO = dbiCal_event_DAO::singleton( $this->_db, $this->_log );
          $res = $dbiCal_event_DAO->insert( $calendar_id, $data, ( 1 + $compix ));
          if( PEAR::isError( $res )) {
            $this->transactionRollback();
            return FALSE;
          }
          break;
        case 'vtodo':
          if( isset( $data['DESCRIPTION'] ) &&  !empty( $data['DESCRIPTION'] ))
            $data['DESCRIPTION'] = $data['DESCRIPTION'][0];
          $dbiCal_todo_DAO = dbiCal_todo_DAO::singleton( $this->_db, $this->_log );
          $res = $dbiCal_todo_DAO->insert( $calendar_id, $data, ( 1 + $compix ));
          if( PEAR::isError( $res )) {
            $this->transactionRollback();
            return FALSE;
          }
          break;
        case 'vjournal':
          $dbiCal_journal_DAO = dbiCal_journal_DAO::singleton( $this->_db, $this->_log );
          $res = $dbiCal_journal_DAO->insert( $calendar_id, $data, ( 1 + $compix ));
          if( PEAR::isError( $res )) {
            $this->transactionRollback();
            return FALSE;
          }
          break;
        case 'vfreebusy':
          if( isset( $data['CONTACT'] ) &&  !empty( $data['CONTACT'] ))
            $data['CONTACT'] = $data['CONTACT'][0];
          $dbiCal_freebusy_DAO = dbiCal_freebusy_DAO::singleton( $this->_db, $this->_log );
          $res = $dbiCal_freebusy_DAO->insert( $calendar_id, $data, ( 1 + $compix ));
          if( PEAR::isError( $res )) {
            $this->transactionRollback();
            return FALSE;
          }
          break;
      } // end switch( $comp->objName )
    } // end foreach( $calendar->components as $comp )
    if( FALSE === $this->transactionCommit())
      return FALSE;
    return $calendar_id;
  }
  /**
   * select
   *
   * @access   public
   * @param    int    $calendar_id
   * @param    string $unique_id
   * @return   mixed  $res (FALSE or calendar object)
   * @since    1.0 - 2011-01-21
   */
  function select( $calendar_id, $unique_id = FALSE ) {
    if( $this->_log )
      $this->_log->log( __METHOD__." start calendar_id=$calendar_id unique_id=$unique_id", PEAR_LOG_INFO );
    if( FALSE === $this->transactionBegin())
      return FALSE;
    $dbiCal_calendar_DAO = dbiCal_calendar_DAO::singleton( $this->_db, $this->_log );
    $calendarValues = $dbiCal_calendar_DAO->select( $calendar_id );
    if( PEAR::isError( $calendarValues )) {
      $this->transactionRollback();
      return FALSE;
    }
    if( FALSE === $calendarValues ) // not found
      return FALSE;
    if( $this->_log )
      $this->_log->log( var_export( $calendarValues, TRUE ), PEAR_LOG_DEBUG );

    $calendar = new vcalendar();
    if( isset( $calendarValues['filename'] ) && !empty( $calendarValues['filename'] ))
      $calendar->setConfig( 'filename', $calendarValues['filename'] );
    if( isset( $calendarValues['VERSION'] )  && !empty( $calendarValues['VERSION'] ))
      $calendar->setProperty( 'version',  $calendarValues['VERSION'] );
    if( $unique_id )
      $calendar->setConfig( 'unique_id', $unique_id );
    elseif( isset( $calendarValues['unique_id'] ) && !empty( $calendarValues['unique_id'] ))
      $calendar->setConfig( 'unique_id', $calendarValues['unique_id'] );
    if( isset( $calendarValues['CALSCALE'] ) && !empty( $calendarValues['CALSCALE'] ))
      $calendar->setProperty( 'calscale', $calendarValues['CALSCALE'] );
    if( isset( $calendarValues['METHOD'] )   && !empty( $calendarValues['METHOD'] ))
      $calendar->setProperty( 'method',   $calendarValues['METHOD'] );
    foreach( $calendarValues as $prop => $propVal ) {
      if( 'X-' == substr( $prop, 0, 2 ))
        $calendar->setProperty( $prop,  $propVal['value'], $propVal['params'] );
    }
    $calendar_data = array();

    if( isset( $calendarValues['calendar_cnt_timezone'] )) {
      $dbiCal_timezone_DAO = dbiCal_timezone_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_timezone_DAO->select( $calendar_id );
      if( PEAR::isError( $res )) {
        $this->transactionRollback();
        return FALSE;
      }
      if (!empty( $res ))
        $calendar_data['vtimezone'] = $res;
    }

    if( isset( $calendarValues['calendar_cnt_event'] )) {
      $dbiCal_event_DAO = dbiCal_event_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_event_DAO->select( $calendar_id );
      if( PEAR::isError( $res )) {
        $this->transactionRollback();
        return FALSE;
      }
      if (!empty( $res ))
        $calendar_data['vevent'] = $res;
    }

    if( isset( $calendarValues['calendar_cnt_freebusy'] )) {
      $dbiCal_freebusy_DAO = dbiCal_freebusy_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_freebusy_DAO->select( $calendar_id );
      if( PEAR::isError( $res )) {
        $this->transactionRollback();
        return FALSE;
      }
      if (!empty( $res ))
        $calendar_data['vfreebusy'] = $res;
    }

    if( isset( $calendarValues['calendar_cnt_journal'] )) {
      $dbiCal_journal_DAO = dbiCal_journal_DAO::singleton( $this->_db, $this->_log );
      $res  = $dbiCal_journal_DAO->select( $calendar_id );
      if( PEAR::isError( $res )) {
        $this->transactionRollback();
        return FALSE;
      }
      if (!empty( $res ))
        $calendar_data['vjournal'] = $res;
    }

    if( isset( $calendarValues['calendar_cnt_todo'] )) {
      $dbiCal_todo_DAO = dbiCal_todo_DAO::singleton( $this->_db, $this->_log );
      $res = $dbiCal_todo_DAO->select( $calendar_id );
      if( PEAR::isError( $res )) {
        $this->transactionRollback();
        return FALSE;
      }
      if (!empty( $res ))
        $calendar_data['vtodo'] = $res;
    }
    unset( $calendarValues );

    $cntcomps = $cntsubcomps = 0;
    foreach( $calendar_data as $compType => $compValues ){
      if( empty( $compValues ))
        continue;
      foreach( $compValues as $compdbix => $compdata ) {
        if( empty( $compdata ))
          continue;
        $compix = ( isset( $compdata['ono'] )) ? $compdata['ono'] : null;
        $comp = new $compType();
        foreach( $compdata as $propname => $propdata ) {
          $propname2 = ( 'X-' == substr( $propname, 0, 2 )) ? 'X-PROP' : $propname;
          if( $this->_log )
            $this->_log->log( "$propname=".PHP_EOL.var_export( $propdata, TRUE ), PEAR_LOG_DEBUG );
          switch( $propname2 ) {
            case 'ono':
              break;
            case 'UID':                           // single ocurrence
            case 'DTSTAMP':
            case 'DTSTART':
            case 'DTEND':
            case 'DUE':
            case 'DURATION':
            case 'CREATED':
            case 'LAST-MODIFIED':
            case 'SUMMARY':
            case 'LOCATION':
            case 'CLASS':
            case 'COMPLETED':
            case 'ORGANIZER':
            case 'PERCENT-COMPLETE':
            case 'PRIORITY':
            case 'RECURRENCE-ID':
            case 'SEQUENCE':
            case 'STATUS':
            case 'TRANSP':
            case 'TZID':
            case 'TZURL':
            case 'URL':
            case 'X-PROP':
              $comp->setProperty( $propname, $propdata['value'], $propdata['params'] );
              break;
            case 'GEO':
              $comp->setProperty( $propname, (float) $propdata['value']['latitude'], (float) $propdata['value']['longitude'], $propdata['params'] );
              break;
            case 'DESCRIPTION':                   // journal allowes multiple ocurrence
            case 'CONTACT':                       // freebusy allowes not multiple ocurrence
              if( isset( $propdata['value'] ))    // explode single occurence to multiple
                $propdata = array( $propdata );
            case 'ATTACH':                        // multiple ocurrence
            case 'ATTENDEE':
            case 'CATEGORIES':
            case 'COMMENT':
            case 'EXDATE':
            case 'EXRULE':
            case 'RDATE':
            case 'RELATED-TO':
            case 'RESOURCES':
            case 'RRULE':
// $this->_log->log( PHP_EOL."$propname=".var_export( $propdata, TRUE ), PEAR_LOG_DEBUG );
              foreach( $propdata as $propdat2 ) {
                if( in_array( $propname, array( 'EXRULE', 'RRULE' )) && isset( $propdat2['value']['rexrule_type'] ))
                  unset( $propdat2['value']['rexrule_type'] );
                $comp->setProperty( $propname, $propdat2['value'], $propdat2['params'] );
              }
              break;
            case 'FREEBUSY':
// if( $this->_log ) $this->_log->log( "$compType, db ix=$compdbix".PHP_EOL.$propname.PHP_EOL.var_export( $propdata, TRUE ), PEAR_LOG_INFO );   // ############################################ test !!!
              foreach( $propdata as $propdat2 ) {
                $fbperiods = array();
                foreach( $propdat2['value'] as $key => $fbvalue ) {
                  if( 'fbtype' !== $key )
                    $fbperiods[] = $fbvalue;
                }
                $comp->setProperty( 'FREEBUSY', $propdat2['value']['fbtype'], $fbperiods, $propdat2['params'] );
              }
              break;
            case 'REQUEST-STATUS':
              foreach( $propdata as $propdat2 )
                $comp->setProperty( $propname, $propdat2['value']['statcode'], $propdat2['value']['text'], $propdat2['value']['extdata'], $propdat2['params'] );
              break;
            case 'ALARM':                         // sub-components
            case 'STANDARD':
            case 'DAYLIGHT':
// $this->_log->log( PHP_EOL."$propname=".var_export( $propdata, TRUE ), PEAR_LOG_DEBUG );
              foreach( $propdata as $sub ) {
                if( 'ALARM' == $propname )
                  $subc = new valarm();
                elseif ( 'STANDARD' == $propname )
                  $subc = new vtimezone( 'STANDARD' );
                elseif( 'DAYLIGHT' == $propname )
                  $subc = new vtimezone( 'DAYLIGHT' );
                foreach( $sub as $subn => $subd ) {  // for each subcomponent, in order
                  $compsubix = ( isset( $sub['ono'] )) ? $sub['ono'] : null;
                  $subn2 = ( 'X-' == substr( $subn, 0, 2 )) ? 'X-PROP' : $subn;
// $this->_log->log( PHP_EOL."$propname($compsubix) $subn=".var_export( $subd, TRUE ), PEAR_LOG_DEBUG );
                  switch( $subn2 ) {
                    case 'ono':
                      break;
                    case 'ACTION':                // single ocurrence
                    case 'DESCRIPTION':
                    case 'DURATION':
                    case 'REPEAT':
                    case 'SUMMARY':
                    case 'TRIGGER':
                    case 'DTSTART':
                    case 'TZOFFSETFROM':
                    case 'TZOFFSETTO':
                    case 'X-PROP':
                      $subc->setProperty( $subn, $subd['value'], $subd['params'] );
                      break;
                    case 'ATTACH':                // multiple ocurrence
                    case 'ATTENDEE':
                    case 'COMMENT':
                    case 'RDATE':
                    case 'RRULE':
                    case 'TZNAME':
                      foreach( $subd as $d2 ) {
                        if( in_array( $subn, array( 'EXRULE', 'RRULE' )) && isset( $d2['value']['rexrule_type'] ))
                          unset( $d2['value']['rexrule_type'] );
                        $rupd = $subc->setProperty( $subn, $d2['value'], $d2['params'] );
// if( !$rupd )  $this->_log->log( "$propname($compsubix) setProperty = FALSE", PEAR_LOG_DEBUG );
                      }
                      break;
                  } // switch( $subn2 )
                } // end foreach( $sub as $subn => $subd )
                $cntsubcomps += 1;
                $comp->setComponent( $subc, $compsubix );
// $this->_log->log( PHP_EOL."compsubix=$compsubix, ant subcomps=".count( $comp->components), PEAR_LOG_DEBUG );   // test ### =====================================
              } // end foreach( $propdata as $sub )
              break;
          } // end switch( $propname )
        } // end foreach( $compdata as $propname => $propdata )
        $cntcomps += 1;
        $calendar->setComponent( $comp, $compix );
// $this->_log->log( PHP_EOL."compix=$compix, ant comps=".count( $calendar->components), PEAR_LOG_DEBUG );   // test ### =====================================
      } // end foreach( $compValues as $compdata )
    } // end foreach( $calendar_data as $compType => $compValues )
    if( $this->_log )
      $this->_log->log( "total $cntcomps components with $cntsubcomps subcomponents ", PEAR_LOG_INFO );
    if( FALSE === $this->transactionCommit())
      return FALSE;
    return $calendar;
  }
  /**
   * transactionBegin
   *
   * @access   public
   * @return   bool
   * @since    1.0 - 2011-01-18
   */
  function transactionBegin() {
    if( !$this->_db->supports( 'transactions' ))
      return TRUE;
    if( $this->_log )
      $this->_log->log( 'transaction Begin', PEAR_LOG_INFO );
    $res = $this->_db->beginTransaction();
    if( PEAR::isError( $res )) {
      if( $this->_log )
        $this->_log->log( PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
      return FALSE;
    }
    return TRUE;
  }
  /**
   * transactionCommit
   *
   * @access   public
   * @return   bool
   * @since    1.0 - 2011-01-18
   */
  function transactionCommit() {
    if( !$this->_db->inTransaction())
      return TRUE;
    if( $this->_log )
      $this->_log->log( 'transaction Commit', PEAR_LOG_INFO );
    if( $this->_db->supports( 'transactions' )) {
      $res = $this->_db->commit();
      if( PEAR::isError( $res )) {
        if( $this->_log )
          $this->_log->log( PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return FALSE;
      }
      return TRUE;
    }
    return TRUE;
  }
  /**
   * transactionRollback
   *
   * @access   public
   * @return   bool
   * @since    1.0 - 2011-01-18
   */
  function transactionRollback() {
    if( !$this->_db->inTransaction())
      return TRUE;
    if( $this->_log )
      $this->_log->log( 'transaction Rollback', PEAR_LOG_INFO );
    if( $this->_db->supports( 'transactions' )) {
      $res = $this->_db->rollback();
      if( PEAR::isError( $res )) {
        if( $this->_log )
          $this->_log->log( PHP_EOL.$res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_ALERT );
        return FALSE;
      }
      return TRUE;
    }
    return TRUE;
  }
}
?>