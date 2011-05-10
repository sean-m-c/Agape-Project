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
 * This file implements the dbiCal web admin interface
 *
**/
/* ************************************************************************* */
/* init and defines                                                          */
/* ************************************************************************* */
$start = microtime( TRUE );

/* if not set elsewhere */
date_default_timezone_set('Europe/Berlin');

/* define directory where to store calendar files, r+w access rights */
define( 'DBICALDIRECTORY', dirname( __FILE__ ).DIRECTORY_SEPARATOR.'calendars' );
/* define directory used when comparing calendar file and db, r+w access rights */
define( 'DBICALCACHE', dirname( __FILE__ ).DIRECTORY_SEPARATOR.'cache' );

/* define default loglevel */
$loglevels = array( 'PEAR_LOG_EMERG'   => 0    // System is unusable
                  , 'PEAR_LOG_ALERT'   => 1    // Immediate action required
                  , 'PEAR_LOG_CRIT'    => 2    // Critical conditions
                  , 'PEAR_LOG_ERR'     => 3    // Error conditions
                  , 'PEAR_LOG_WARNING' => 4    // Warning conditions
                  , 'PEAR_LOG_NOTICE'  => 5    // Normal but significant
                  , 'PEAR_LOG_INFO'    => 6    // Informational
                  , 'PEAR_LOG_DEBUG'   => 7    // Debug-level messages
                  , 'NO_LOG'           => 8 ); // no logging at all
define( 'DBICALLOGLEVEL', $loglevels['PEAR_LOG_ERR'] );

/* Maximal upload file size, autosetting from PHP config size? */
$mfs = (string) ini_get( 'upload_max_filesize' );
$maxfilesize = 0;
for($x=0; $x < strlen($mfs); $x++ ) {
  if( ctype_digit( $mfs[$x] ))
    $maxfilesize .= $mfs[$x];
  elseif( 'M' == $mfs[$x] ) {
    $maxfilesize = ((int) $maxfilesize) * 1024 * 1024;
    break;
  }
  elseif( 'K' == $mfs[$x] ) {
    $maxfilesize = ((int) $maxfilesize) * 1024;
    break;
  }
}
define( 'DBICALFILEUPLOADMAXSIZE', $maxfilesize);


/* define dsn, Data Source Name */
define( 'DBICALDSN', 'mysqli://dbiCal:dbiCal@localhost/dbiCal2' );

define( 'DBICALVERSION', 'dbIcal 3.0' );

/* default form to display */
$displayItem = 'filesForm';

/* ************************************************************************* */
/* PHP includes                                                              */
/* ************************************************************************* */
require_once 'includes/iCalcreator.class.php';
require_once 'includes/dbiCal.class.php';

/* ************************************************************************* */
/* some help functions                                                       */
/* ************************************************************************* */
function cmpia( $a, $b ) {
  if( (int) $a['calendar_id'] < (int) $b['calendar_id'] ) return -1;
  if( (int) $a['calendar_id'] > (int) $b['calendar_id'] ) return  1;
  return 0;
}
function cmpid( $a, $b ) {
  if( (int) $a['calendar_id'] < (int) $b['calendar_id'] ) return  1;
  if( (int) $a['calendar_id'] > (int) $b['calendar_id'] ) return -1;
  return 0;
}
function cmpfa( $a, $b ) {
  if( strtolower( $a['filename'] ) ==  strtolower( $b['filename'] ))
    return strcmp( $a['create_date'], $b['create_date'] );
  return strcmp( strtolower( $a['filename'] ), strtolower( $b['filename'] ));
}
function cmpfd( $a, $b ) {
  if( strtolower( $a['filename'] ) ==  strtolower( $b['filename'] ))
    return strcmp( $a['create_date'], $b['create_date'] );
  return strcmp( strtolower( $b['filename'] ), strtolower( $a['filename'] ));
}
function cmpda( $a, $b ) {
  if( $a['create_date'] == $b['create_date'] )
    return strcmp( strtolower( $a['filename'] ), strtolower( $b['filename'] ));
  return strcmp( $a['create_date'], $b['create_date'] );
}
function cmpdd( $a, $b ) {
  if( $a['create_date'] == $b['create_date'] )
    return strcmp( strtolower( $a['filename'] ), strtolower( $b['filename'] ));
  return strcmp( $b['create_date'], $a['create_date'] );
}
function cmp2( $a, $b ) {
  if(   in_array( $a, array( 'calendar_id', 'date', 'filename' ))) {
    if( in_array( $b, array( 'calendar_id', 'date', 'filename' )))
      return strcmp( strtolower( $a ), strtolower( $b ));
    else
      return -1;
  }
  if(   'x-' == substr( strtolower( $a ),0, 2 )) {
    if( in_array( $b, array( 'calendar_id', 'date', 'filename' )))
      return 1;
    if( 'x-' != substr( strtolower( $b ),0, 2 ))
      return -1;
    else
      return strcmp( strtolower( $a ), strtolower( $b ));
  }
  if(   in_array( $b, array( 'calendar_id', 'date', 'filename' )))
      return 1;
  if( 'x-' == substr( strtolower( $b ),0, 2 ))
      return 1;
  else
      return strcmp( strtolower( $a ), strtolower( $b ));
}
function dispDBcal( $displayStr, $unique, & $calendars, & $selectTest, $selectOptions=array(), $sort='fa' ) {
  global $commonVars;
  $iam  = $idm = $fam = $fdm = $dam = $ddm = $url = '';
  $path_parts = pathinfo( $_SERVER['SCRIPT_NAME'] );
  $ref  = 'http://'.$_SERVER['SERVER_NAME'].$path_parts['dirname'];
  switch( $sort ) {
    case 'ia': usort( $calendars, 'cmpia' ); $iam = " border='1'"; break; // calendar id, ascending
    case 'id': usort( $calendars, 'cmpid' ); $idm = " border='1'"; break; // "-,          descending
    case 'da': usort( $calendars, 'cmpda' ); $dam = " border='1'"; break; // insert date, ascending
    case 'dd': usort( $calendars, 'cmpdd' ); $ddm = " border='1'"; break; // "-,          descending
    case 'fd': usort( $calendars, 'cmpfd' ); $fdm = " border='1'"; break; // filename,    descending
    case 'fa':
    default:   usort( $calendars, 'cmpfa' ); $fam = " border='1'"; break; // "-,          ascending
  }
  $formId = "form$unique";
  $str  = "$displayStr\n";
  $str .= "<FORM name='$formId' id='$formId' method='post' action=''>\n";
  $str .= '<input type="hidden" name="fileId" id="fileId" value=""/>'."\n";
  $str .= '<input type="hidden" name="fileName" id="fileName" value=""/>'."\n";
  $str .= $commonVars;
  $str .= '<input type="hidden" name="formName" id="formName" value="'.$formId.'"/>'."\n";
  $str .= '<input type="hidden" name="sort" id="sort" value=""/>'."\n";
  $opt = ( 2 == $unique ) ? 'selectTest' : 'getCalendars';
  $cursormgnt   = ' onMouseOver="this.style[\'cursor\']=\'pointer\'" onMouseOut="this.style[\'cursor\']=\'default\'"';
  $titleTxtasc  = ' title="sort ascending"';
  $titleTxtdesc = ' title="sort decending"';
  $click1 = ' onClick="'.$formId.".elements[2].value='$opt';".$formId.".elements[5].value='";
  $click2 = "';".$formId.'.submit();"';
  if( is_array( $selectOptions ) && !empty( $selectOptions )) {
    foreach( $selectOptions as $skey => $svalue ) {
      if( empty( $svalue ))
        $svalue = 'empty';
      $str .= '<input type="hidden" name="selectTest['.$skey.']" id="selectTest['.$skey.']" value="'.$svalue.'"/>'."\n";
    }
  }
  $str .= "<TABLE>\n<TR>\n";
  $str .= "<TH class='box w6'>id&nbsp;\n";
  $str .= "<IMG src='$ref/includes/images/asc.png'  type='image/png'$titleTxtasc$cursormgnt $iam $click1".'ia'.$click2."><IMG src='$ref/includes/images/desc.png'$titleTxtdesc$cursormgnt type='image/png' $idm $click1".'id'.$click2."></TH>\n";
  $str .= "<TH class='box w35'>Filename&nbsp;\n";
  $str .= "<IMG src='$ref/includes/images/asc.png'  type='image/png'$titleTxtasc$cursormgnt $fam $click1".'fa'.$click2.">&nbsp;\n";
  $str .= "<IMG src='$ref/includes/images/desc.png' type='image/png'$titleTxtdesc$cursormgnt $fdm $click1".'fd'.$click2."><BR />X-properties - metadata</TH>\n";
  $str .= "<TH class='box w18'>insert Date&nbsp;\n";
  $str .= "<IMG src='$ref/includes/images/asc.png'  type='image/png'$titleTxtasc$cursormgnt $dam $click1".'da'.$click2.">&nbsp;\n";
  $str .= "<IMG src='$ref/includes/images/desc.png' type='image/png'$titleTxtdesc$cursormgnt $ddm $click1".'dd'.$click2."></TH>\n";
  $str .= "<TH class='box w6'>file- size</TH>\n";
  $str .= "<TH class='box w6'>time- zones</TH>\n";
  $str .= "<TH class='box w6'>events</TH>\n";
  $str .= "<TH class='box w6'>free- busys</TH>\n";
  $str .= "<TH class='box w6'>journals</TH>\n";
  $str .= "<TH class='box w6'>todos</TH>\n";
  $str .= "<TH class='box wr'>&nbsp;</TH>\n";
  $str .= "</TR>\n";
  foreach( $calendars as $calendar ) {
    $selectTest['calendar_id'][]   = $calendar['calendar_id'];
    if( !isset( $selectTest['filename'] ) || !in_array( $calendar['filename'], $selectTest['filename'] ))
      $selectTest['filename'][]    = $calendar['filename'];
    if( !isset( $selectTest['date'] ) || !in_array(substr( $calendar['create_date'], 0, 10 ), $selectTest['date'] ))
      $selectTest['date'][] = substr( $calendar['create_date'], 0, 10 );
    $partcnt = 0;
    $str2 = '';
    foreach( $calendar as $xkey => $xvalue ) {
      if( 'X-' == substr( $xkey, 0, 2 )) {
        $partcnt += 1;
        $str2 .= "<TR><TD><SPAN>$xkey</SPAN></TD><TD colspan='7'><SPAN>".$xvalue['value']."</SPAN></TD></TR>\n";
        if( !isset( $selectTest[$xkey] ) || !in_array( $xvalue['value'], $selectTest[$xkey] ))
          $selectTest[$xkey][] = $xvalue['value'];
      }
    }
    if( isset( $calendar['metadata'] ) && !empty( $calendar['metadata'] )) {
      $str2 .= "<TR><TD colspan='7'><SPAN class='span2'>_ _ metadata _ _</SPAN></TD></TR>\n";
      $partcnt += 1;
      foreach( $calendar['metadata'] as $mkey => $mvalue ) {
        if( 'calendar_id' == $mkey )
          continue;
        $partcnt += 1;
        $str2 .= "<TR><TD><SPAN>$mkey</SPAN></TD><TD colspan='7'><SPAN>$mvalue</SPAN></TD></TR>\n";
        if( !isset( $selectTest[$mkey] ) || !in_array( $mvalue, $selectTest[$mkey] ))
          $selectTest[$mkey][] = $mvalue;
      }
    }
    $str2 .= '</TR>';
    $str .= '<TR><TD class="box w5" rowspan="'.($partcnt +1).'">'.$calendar['calendar_id']."</TD>\n";
    $str .= '<TD class="box w35 note"><A title="download from database" href="'.$ref.'/'.$path_parts['basename'].'?iCalAction=downloadDB&amp;fileId='.$calendar['calendar_id'].'">'.$calendar['filename'].'</A>'."</TD>\n";
    $str .= '<TD class="box w18">'.$calendar['create_date']."</small></TD>\n";
    $str .= '<TD class="box w6 r">'; if( isset( $calendar['filesize'] ))              $str .= $calendar['filesize'];              $str .= "</TD>\n";
    $str .= '<TD class="box w6 r">'; if( isset( $calendar['calendar_cnt_timezone'] )) $str .= $calendar['calendar_cnt_timezone']; $str .= "</TD>\n";
    $str .= '<TD class="box w6 r">'; if( isset( $calendar['calendar_cnt_event'] ))    $str .= $calendar['calendar_cnt_event'];    $str .= "</TD>\n";
    $str .= '<TD class="box w6 r">'; if( isset( $calendar['calendar_cnt_freebusy'] )) $str .= $calendar['calendar_cnt_freebusy']; $str .= "</TD>\n";
    $str .= '<TD class="box w6 r">'; if( isset( $calendar['calendar_cnt_journal'] ))  $str .= $calendar['calendar_cnt_journal'];  $str .= "</TD>\n";
    $str .= '<TD class="box w6 r">'; if( isset( $calendar['calendar_cnt_todo'] ))     $str .= $calendar['calendar_cnt_todo'];     $str .= "</TD>\n";
    $str .= '<TD class="box wr" rowspan="'.($partcnt +1).'">all';
    $str .= "<INPUT name='all' type='checkbox' id='all' value='deleteAll' title='remove all with this filename'/>\n";
    $str .= '<BUTTON name="Remove"   type="button" onClick="if(confirm(\'Remove??\')){'.$formId.'.elements[0].value=\''.$calendar['calendar_id'].'\';'.$formId.'.elements[1].value=\''.$calendar['filename'].'\';'.$formId.'.submit();}"'." title='remove this instance'>remove</BUTTON>\n";
    $existsStr = ( !file_exists( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$calendar['filename'] )) ? ' disabled="disabled"' : '';
    $str .= '<BUTTON name="compare"  type="button" onClick="'.$formId.'.elements[0].value=\''.$calendar['calendar_id'].'\';'.$formId.'.elements[1].value=\''.$calendar['filename'].'\';'.$formId.'.elements[2].value=\'compare\';'.$formId.'.submit();"'." title='compare this instance'$existsStr>compare</BUTTON>\n";
    $str .= '<BUTTON name="download" type="button" onClick="'.$formId.'.elements[0].value=\''.$calendar['calendar_id'].'\';'.$formId.'.elements[1].value=\''.$calendar['filename'].'\';'.$formId.'.elements[2].value=\'downloadDB\';'.$formId.'.submit();"'." title='download db instance'>download</BUTTON>\n";
    $str .= "</TD>\n</TR>\n";
    $str .= $str2;
  }
  $str .= "</TABLE>\n";
  $str .= "</FORM>\n";
  $str .= '<script type="text/javascript">var '.$formId.'=document.getElementById("'.$formId.'");'.$formId.'.elements[2].value=\'dbDelete\';</script>'."\n";
  return $str;
}
/* ************************************************************************* */
/* include, extend and config pear LOG (file handler)                        */
/* ************************************************************************* */
if( isset( $_REQUEST ) && isset( $_REQUEST['iCalAction'] ) && ( 'showLog' == $_REQUEST['iCalAction'] ))
  $logLevel = $loglevels['NO_LOG']; // stop logging when viewing log
elseif( isset( $_REQUEST ) && isset( $_REQUEST['logLevel'] ))
  $logLevel = $_REQUEST['logLevel'];
elseif( DBICALLOGLEVEL != $loglevels['NO_LOG'] )
  $logLevel = DBICALLOGLEVEL;
else
  $logLevel = $loglevels['NO_LOG'];
$conf = array( 'append'     => TRUE,
               'locking'    => TRUE,
               'lineFormat' => '%1$s %3$s %8$s.%7$s:%6$s %4$s',
            // Timestamp, priority, class, function, line number, text
            // 'lineFormat' => '%1$s %4$s', 'timeFormat' => '%X'), // only time + text
               'timeFormat' => '%Y%m%d %X');
$logfile = 'dbiCal_'.date('Ymd').'.log'; // creates a new logfile every day
if( isset( $logLevel ) && ( $loglevels['NO_LOG'] != $logLevel )) {
  require_once 'Log.php';
  class iCalLog extends Log {
    function __destruct() {
      $this->flush();
    }
  }
  $_log = iCalLog::singleton( 'file', $logfile, 'dbiCal', $conf, $logLevel );
  $_log->log( PHP_EOL.str_repeat( '* ', 50 ).' start', PEAR_LOG_NOTICE );
}
else
  $_log = false; // if not using pear LOG extension
/* ************************************************************************* */
/* include and config pear MDB2                                              */
/* ************************************************************************* */
require_once 'MDB2.php';
$_DBconnection = & MDB2::singleton( DBICALDSN );
if (PEAR::isError( $_DBconnection )) {
  if( $_log ) {
    $_log->log( $_DBconnection->getUserInfo().PHP_EOL.$_DBconnection->getMessage(), PEAR_LOG_EMERG );
    $_log->flush();
  }
  exit();
}
$res = & $_DBconnection->exec( 'SET autocommit=0' ); // MySQL fix turning autocommit off
if (PEAR::isError( $res )) {
  if( $_log ) {
    $_log->log( $res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_EMERG );
    $_log->flush();
  }
  exit();
}

/* MDB2 transacton isolation level, if supported by the database and/or MDB2
 * READ UNCOMMITTED (allows dirty reads)
 * READ COMMITTED (prevents dirty reads)
 * REPEATABLE READ (prevents nonrepeatable reads)
 * SERIALIZABLE (prevents phantom reads)
*/
if( $_DBconnection->supports( 'transactions' )) {
  $res = $_DBconnection->setTransactionIsolation( 'SERIALIZABLE', array( 'wait' => 'WAIT', 'rw' => 'READ WRITE' ));
  if( PEAR::isError( $res )) {
    if( $_log ) {
      $_log->log( 'Unable to set TransactionIsolation', PEAR_LOG_ERR );
      $_log->log( $res->getUserInfo().PHP_EOL.$res->getMessage(), PEAR_LOG_WARNING );
    }
  }
}

/* ************************************************************************* */
/* check filesystem and fire up dbiCal                                       */
/* ************************************************************************* */
$fileSystemCheckText = '';
if( !file_exists( DBICALDIRECTORY ))
  $fileSystemCheckText = '"'.DBICALDIRECTORY.'" does not exist??? Check filesystem!!';
elseif( !is_writable( DBICALDIRECTORY )) {
  $fileSystemCheckText = '"'.DBICALDIRECTORY.'" is not writable??? Check filesystem privileges!!';
}
elseif( !file_exists( DBICALCACHE )) {
  $fileSystemCheckText = '"'.DBICALCACHE.'" does not exist??? Check filesystem!!';
}
elseif( !is_writable( DBICALCACHE )) {
  $fileSystemCheckText = '"'.DBICALCACHE.'" is not writable??? Check filesystem privileges!!';
}
if( !empty( $fileSystemCheckText )) {
  if( $_log ) {
    $_log->log( $fileSystemCheckText, PEAR_LOG_EMERG );
    $_log->flush();
  }
  exit(); // quit when filesystem error occurs.. .
}
elseif( $_log ) {
  $_log->log( 'Exist and is writeable: "'.DBICALDIRECTORY.'"', PEAR_LOG_DEBUG );
  $_log->log( 'Exist and is writeable: "'.DBICALCACHE.'"', PEAR_LOG_DEBUG );
}

$dbiCal = new dbiCal( $_DBconnection, $_log );
$execTime = array();

/* ************************************************************************* */
/* check output from HTML forms and excute                                   */
/* ************************************************************************* */
$commonVars  = "<INPUT type='hidden' name='iCalAction' id='iCalAction' value=''/>\n";
$commonVars .= "<INPUT type='hidden' name='logLevel'   id='logLevel'   value='$logLevel'/>\n";
if( isset( $_REQUEST ) && isset( $_REQUEST['iCalAction'] )) {
  if( $_log ) {
    $_log->log( 'iCalAction='.$_REQUEST['iCalAction'], PEAR_LOG_INFO );
    $_log->log( 'REQUEST='.var_export( $_REQUEST, TRUE), PEAR_LOG_DEBUG );
    $_log->flush();
  }
  switch( $_REQUEST['iCalAction'] ) {
    case 'logLevelchg': // from 'configForm'
      unset( $_REQUEST['startRow'] );
      $displayItem = ( isset( $_REQUEST['formName'] ) && ( 'logForm' == $_REQUEST['formName'] )) ? 'viewForm' : 'configForm';
      break;

    case 'getCalendars': // from 'statusForm'
      $displayItem = 'statusForm';
      break;

    case 'clearLog': // from 'viewForm'
      file_put_contents( $logfile, '' );
      unset( $_REQUEST['startRow'] );
    case 'showLog': // from 'viewForm'
      $displayItem = ( isset( $_REQUEST['formName'] ) && ( 'logForm' == $_REQUEST['formName'] )) ? 'viewForm' : 'configForm';
      break;

    case 'Insert': // from 'filesForm'
      if( !isset( $_REQUEST['fileName'] ) || empty( $_REQUEST['fileName'] ))
        break;
            /** first create and parse iCalcreator object */
      $execStart = microtime( TRUE );
      $icalcfg = array( 'unique_id' => 'dbiCal_test.se',
                        'directory' => DBICALDIRECTORY,
                        'filename'  => $_REQUEST['fileName'] );
      $v = new vcalendar( $icalcfg );
      $fs1 = $v->getConfig( 'filesize' );                        // fetch file size for later use, below
      $v->parse();
      $execTime['insert file parse'] = microtime( TRUE ) - $execStart;

            /** then insert file into database with metadata */
      $execStart = microtime( TRUE );
      $metadata = array();
      if( isset( $_REQUEST['metadata'] )) {
        foreach( $_REQUEST['metadata'] as $row )
          if( !empty( $row['key'] ) && ! empty( $row['value'] ))
          $metadata[$row['key']] = $row['value'];
      }
      if( FALSE === ($fileId = $dbiCal->insert( $v, $metadata ))) {
        if( $_log ) {
          $_log->log( 'insert - databas-katastof!!!', PEAR_LOG_LOG_EMERG );
          $_log->flush();
        }
        exit();
      }
      if( $_log )
        $_log->log( 'insert of '.$_REQUEST['fileName'].' (id='.$fileId.') OK', PEAR_LOG_NOTICE );
      $displayItem = 'filesForm';
      $execTime['db Insert'] = microtime( TRUE ) - $execStart;
      break;

    case 'Inspect': // from 'filesForm'
      if( !isset( $_REQUEST['fileName'] ) || empty( $_REQUEST['fileName'] ))
        break;
      $content = file_get_contents( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'] );
      if( isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) && ( FALSE !== strpos( strtolower( $_SERVER['HTTP_ACCEPT_ENCODING'] ), 'gzip' ))) {
        $content = gzencode( $content, 9 );
        header( 'Content-Encoding: gzip');
        header( 'Vary: *');
      }
      header( 'Content-Type: text/calendar; charset=utf-8' );
      header( 'Content-Length: '.strlen( $content ));
      header( 'Content-Disposition: attachment; filename="'.$_REQUEST['fileName'].'"' );
      header( 'Cache-Control: max-age=10' );
      echo $content;
      exit;

    case 'fileRemove': // from 'filesForm'
      if( !isset( $_REQUEST['fileName'] ) || empty( $_REQUEST['fileName'] ))
        break;
      $execStart = microtime( TRUE );
      if( $_log )
        $_log->log( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'].' to be removed', PEAR_LOG_DEBUG );
      if( !is_file( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'] )) {
        if( $_log )
          $_log->log( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'].' no file???', PEAR_LOG_CRIT );
      }
      elseif( FALSE === @unlink( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'] )) {
        if( $_log )
          $_log->log( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'].' unable to remove!!!', PEAR_LOG_CRIT );
      }
      elseif( $_log )
        $_log->log( DBICALDIRECTORY.DIRECTORY_SEPARATOR.$_REQUEST['fileName'].' REMOVED!!!', PEAR_LOG_NOTICE );
      $displayItem = 'filesForm';
      $execTime['file Delete'] = microtime( TRUE ) - $execStart;
      break;

    case 'Upload': // from 'filesForm'
      $execStart = microtime( TRUE );
      $fileerror = FALSE;
      $tmpfile   = ( isset( $_FILES['uploadfile']['tmp_name']) ?       $_FILES['uploadfile']['tmp_name'] : '' );
      $filename  = ( isset( $_FILES['uploadfile']['name'])     ? trim( $_FILES['uploadfile']['name'] )   : '' );
      if( empty( $_FILES['uploadfile'] ) || empty( $_FILES['uploadfile']['name'] ))
        $fileerror = 10;
      else {
        $path_parts    = pathinfo( $filename );
        if( eregi( "[^0-9a-zA-Z_]", $path_parts['filename'] ) || ( 3 > strlen( $path_parts['extension'] )))
          $fileerror = 2;
        elseif (0 >= $_FILES['uploadfile']['size'])
          $fileerror = 4;
        elseif(strtolower( $path_parts['extension'] ) != 'ics' )
          $fileerror = 3;
        $dirfile = DBICALDIRECTORY.DIRECTORY_SEPARATOR.$filename;
        if( !move_uploaded_file( $tmpfile, $dirfile )) {
          switch ($_FILES['uploadfile']['error']) {
            case 4: $fileerror = 5; break;
            case 3: $fileerror = 6; break;
            case 2: $fileerror = 7; break;
            case 1: $fileerror = 8; break;
           default: //            UPLOAD_ERR_OK
            case 0: break;
          }
        }
      }
      if( $fileerror ) {
        if( is_file( $tmpfile ))
          @unlink( $tmpfile );
        $errtexts = array(
           2 => 'File name (min 3 pos) can only contain alphanum. characters.'
         , 3 => 'Incorrect file extension.'
         , 4 => 'Filesize 0 (?) or to large.'
         , 5 => 'Upload incomplete: No file was uploaded.'
         , 6 => 'The uploaded file was only partially uploaded.'
         , 7 => 'Upload incomplete: The uploaded file exceeds form upload directive size.'
         , 8 => 'Upload incomplete: The uploaded file exceeds the php directive size.'
         , 9 => 'File uploaded but error when changing the mode of the file.'
         ,10 => 'No file to upload.' );
        if( $_log )
          $_log->log( "Upload error ($filename) :  ($fileerror) ".$errtexts[$fileerror], PEAR_LOG_ERR );
      }
      if( $_log )
        $_log->log( 'upload of '.$filename.' OK', PEAR_LOG_NOTICE );
      $displayItem = 'filesForm';
      $execTime['file Upload'] = microtime( TRUE ) - $execStart;
      break;

    case 'dbDelete': // from 'statusForm' or 'selectTestForm'
      $displayItem = ( isset( $_REQUEST['formName'] ) && ( 'form1' == $_REQUEST['formName'] )) ? 'statusForm' : 'selectTestForm';
      if(( !isset( $_REQUEST['fileId'] )   || empty( $_REQUEST['fileId'] )) &&
         ( !isset( $_REQUEST['fileName'] ) || empty( $_REQUEST['fileName'] )))
        break;
            /** if exist, delete from database */
      $execStart = microtime( TRUE );
      if( isset( $_REQUEST['all'] ) && ( 'deleteAll' == $_REQUEST['all'] )) {
        if( FALSE === $dbiCal->delete( array( 'filename' => $_REQUEST['fileName'] ))) {
          if( $_log ) {
              $_log->log( 'delete - databas-katastof!!!', PEAR_LOG_LOG_EMERG );
              $_log->flush();
          }
          exit();
        }
        if( $_log )
          $_log->log( 'delete in database of all instances with filename '.$_REQUEST['fileName'].' OK', PEAR_LOG_NOTICE );
      }
      elseif( FALSE === $dbiCal->delete( array( 'calendar_id' => $_REQUEST['fileId'] ))) {
        if( $_log ) {
            $_log->log( 'delete - databas-katastof!!!', PEAR_LOG_LOG_EMERG );
            $_log->flush();
        }
        exit();
      }
      elseif( $_log )
        $_log->log( 'delete in database of instance with id '.$_REQUEST['fileId'].' OK', PEAR_LOG_NOTICE );
      $execTime['db Delete'] = microtime( TRUE ) - $execStart;
      if( 'statusForm' == $displayItem )
        break;                                          // operation 'dbDelete' from 'statusForm' ends here

    case 'compare':    // from 'statusForm' or 'selectTestForm'
    case 'selectTest': // from selectTestForm
      if( isset( $_REQUEST['selectTest'] ) && !empty( $_REQUEST['selectTest'] )) {
        $selectLabel  = "<H5>Result of database intersection</H5><SPAN>selectOptions:</SPAN>\n<TABLE>\n";
        $selectLabel .= "<TR><TD>key</TD><TD>value</TD></TR>";
        $selectOptions = array();
        foreach( $_REQUEST['selectTest'] as $skey => $svalue ) {
          if( empty( $skey ) || empty( $svalue ))
            continue;
          $selectLabel .= "<TR><TD><SPAN>$skey</SPAN></TD>";
          $selectLabel .= '<TD><SPAN>';
          $selectLabel .= ( empty( $svalue )) ? '' : "'$svalue'";
          $selectLabel .= "</SPAN></TD></TR>";
          $selectOptions[$skey] = ( 'empty' == $svalue ) ? '' : $svalue;
        }
        if( !empty( $selectOptions )) {
          $execStart = microtime( TRUE );
          $calendars = $dbiCal->getCalendars( $selectOptions );
          if( FALSE === $calendars ) {
            if( $_log ) {
              $_log->log( 'getCalendars - databas-katastof!!!', PEAR_LOG_LOG_EMERG );
              $_log->flush();
            }
            exit();
          }
          $execTime['select Test (from db)'] = microtime( TRUE ) - $execStart;
            /* save display result for later use */
          $execStart = microtime( TRUE );
          $selectLabel .= "</TABLE><SPAN>".count( $calendars )." hits</SPAN><BR /><SPAN>(exec.time select Test below)</SPAN>\n";
          $selectTest = array();
          $sort = ( isset( $_REQUEST['sort'] ) && !empty( $_REQUEST['sort'] )) ? $_REQUEST['sort'] : 'fa';
          $selectTestResult = "<br\>\n".dispDBcal( $selectLabel, 2, $calendars, $selectTest, $selectOptions, $sort );
          $selectTest = array();
          if( $_log )
            $_log->log( 'select of '.count( $calendars ).'. Criteria='.var_export( $calendars, TRUE), PEAR_LOG_NOTICE );
          $execTime['select Test display prep'] = microtime( TRUE ) - $execStart;
        }
      }
      if( in_array( $_REQUEST['iCalAction'], array( 'dbDelete', 'selectTest' ))) {
        $displayItem = 'selectTestForm';
        break;                                          // operation 'dbDelete' (from 'selectTestForm') OR 'selectTest' ends here
      }

    case 'downloadDB': // from 'statusForm' or 'selectTestForm'
      if( !isset( $_REQUEST['fileId'] ) || empty( $_REQUEST['fileId'] ))
        break;
            /** select - from database  */
      $execStart = microtime( TRUE );
      $v2 = $dbiCal->select( $_REQUEST['fileId'] );
      $str = ( FALSE !== $v2 ) ? $v2->createCalendar() : FALSE;
      if( !$str ) {
        if( $_log ) {
            $_log->log( 'select - databas-katastof!!!', PEAR_LOG_EMERG );
            $_log->flush();
        }
        exit(); // exit if error
      }
      elseif( 'downloadDB' == $_REQUEST['iCalAction'] ) {
        $v2->returnCalendar();
        if( $_log )
          $_log->flush();
        exit;                                          // 'downloadDB' ends here
      }
      $execTime['db file Select'] = microtime( TRUE ) - $execStart;
            /** save outfile2 */
      $execStart = microtime( TRUE );
      $f1a = $v2->getConfig( 'filename' );
      $v2->sort();
      $f2  = 'filecmp2.ics';
      $v2->setConfig( array( 'directory' => DBICALCACHE,
                             'filename' => $f2 ));
      $v2->saveCalendar();                             // save (db) file (2) in cache folder
      $fs2 = $v2->getConfig('filesize');               // fetch (db) file (2) size for later use, below
      $execTime["'db' file sort+save"] = microtime( TRUE ) - $execStart;
            /** parse, sort and save diskfile (1) */
      $f1b = 'filecmp1.ics';
      $v1  = new vcalendar(array( 'unique_id' => 'dbiCal_test.se',
                                  'directory' => DBICALDIRECTORY,
                                  'filename'  => $f1a ));
      $v1->parse();
      $v1->sort();
      $v1->setConfig( array( 'directory' => DBICALCACHE,
                             'filename' => $f1b ));
      $v1->saveCalendar();                             // save (disc) file (1) in cache folder
      $fs1 = $v1->getConfig('filesize');               // fetch (disc) file (1) size for later use, below
      $execTime['disc file parse, sort+save'] = microtime( TRUE ) - $execStart;
            /* execute and show results */
      $execStart = microtime( TRUE );
      $str1 = " 'disc' file 1 filesize=$fs1 file='$f1a".PHP_EOL;
      $str2 = " 'db'   file 2 filesize=$fs2 file='$f2".PHP_EOL;
      // $d   = str_replace(' ', chr(92).' ', $d);     // add Backslash-character.. .
      $df1b = DBICALCACHE.DIRECTORY_SEPARATOR.$f1b;
      $df2  = DBICALCACHE.DIRECTORY_SEPARATOR.$f2;
      $f1b  = str_replace(' ', chr(92).' ', $df1b);
      $f2   = str_replace(' ', chr(92).' ', $df2 );
      $cmd  = "diff -b -H --side-by-side $df1b $df2";
      $str3 = ' cmd='.$cmd.PHP_EOL;
      if( $_log ) {
        $_log->log( $str1, PEAR_LOG_NOTICE );
        $_log->log( $str2, PEAR_LOG_NOTICE );
        $_log->log( $str3, PEAR_LOG_NOTICE );
      }
      $compareResult = $str1.$str2.$str3;
      $a=array();
      exec( $cmd, $a );
      $n=chr(10);
      $compareResult .= " diff result:$n".implode( $n, $a );
      if( $_log ) {
        $_log->log( $compareResult, PEAR_LOG_NOTICE );
        $_log->flush();
      }
      $displayItem = 'compareForm';
      $execTime['file and db diff'] = microtime( TRUE ) - $execStart;
      break;

    default:
      if( $_log )
        $_log->log( 'no action on '.$_REQUEST['iCalAction'], PEAR_LOG_DEBUG );
      break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<TITLE><?php echo DBICALVERSION;?> Admin</title>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<META name="author"      content="kigkonsult - Kjell-Inge Gustafsson">
<META name="copyright"   content="2011 kigkonsult">
<META name="keywords"    content="ical, xcal, rss, calendar, rfc2445, php, create, generate, icalender mysql database mdb2">
<META name="description" content="dbiCal is a solution storing iCal files in database">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<META HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">
<STYLE>
BODY {
 font            : small serif;
}
BUTTON {
 font            : small sans-serif;
}
H5 {
 font            : medium monospace;
 letter-spacing  : 0.2em;
 line-height     : 90%;
}
INPUT {
 background-color: white;
}
OPTION {
 font-family     : monospace;
}
TABLE {
 border-top      : black solid thin;
 border-collapse : collapse;
 width           : 800px;
}
TEXTAREA {
 font            : small monospace;
 width           : 800px;
}
SPAN {
 font            : small sans-serif;
 letter-spacing  : 0.1em;
}
.box {
 border-top      : black solid thin;
 border-right    : silver dotted thin;
 font            : x-small sans-serif;
 letter-spacing  : 0.1em;
 vertical-align  : top;
}
.bt {
 border-top      : thin dotted gray;
}
.c {
text-align       : center;
}
.label {
 border-bottom   : thin dotted gray;
 font            : small monospace;
 vertical-align  : bottom;
}
.menu {
 background-color: white;
 border          : none;
 margin          : 0;
 padding         : 0;
}
.noborder {
 border          : none;
}
.note {
 font-size       : medium;
}
.r {
 text-align      : right;
}
.span2 {
 background-color: silver;
 font            : small-caps 500 xx-small sans-serif;
 letter-spacing  : 0.4em;
 margin-top      : 5em;
}
.w35 {
 width           : 265px;
}
.w18 {
 width           : 143px;
}
.w6 {
 width           : 47px;
}
.wr {
 text-align      : right;
 width           : 38px;
}
.w150 {
 width           : 150px;
}
.w250 {
 width           : 250px;
}
</STYLE>
</HEAD>

<BODY>
<a name="top"></a>
<TABLE id="menu" bgcolor="silver" class="noborder"><TR>
<TD class="c"><H3>dbiCal Admin</H3></TD>
<TD class="c">
<BUTTON class="menu" id="btnconfig"     type="submit" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor'] ='default'" onClick="displayDiv('configForm');">Config</BUTTON>
<BUTTON class="menu" id="btnstatus"     type="submit" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor'] ='default'" onClick="displayDiv('statusForm');">db Status</BUTTON>
<BUTTON class="menu" id="btnfiles"      type="submit" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor'] ='default'" onClick="displayDiv('filesForm');">Files</BUTTON>
<BUTTON class="menu" id="btncompare"    type="submit" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor'] ='default'" onClick="displayDiv('compareForm');">file Compare</BUTTON>
<BUTTON class="menu" id="btnselectTest" type="submit" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor'] ='default'" onClick="displayDiv('selectTestForm');">test Select</BUTTON>
<BUTTON class="menu" id="btnview"       type="submit" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor'] ='default'" onClick="displayDiv('viewForm');">Inspect Log</BUTTON>
</TD></TR></TABLE>
<SCRIPT type="text/javascript">
var tmpl, showStyle;
tmpl = document.getElementById('menu');
showStyle = tmpl.currentStyle ? tmpl.currentStyle.display : getComputedStyle(tmpl,null).getPropertyValue('display');
function displayItem(item) {
 var pos = item.indexOf('F'), btn = 'btn' + item.substring(0, pos);
 document.getElementById(btn).style.backgroundColor = 'yellow';
 document.getElementById(item).style.display = showStyle;
}
function hideItem(item) {
 var pos = item.indexOf('F'), btn = 'btn' + item.substring(0, pos);
 document.getElementById(btn).style.backgroundColor = 'white';
 document.getElementById(item).style.display = 'none';
}
function displayDiv(item) {
 hideItem('configForm');
 hideItem('filesForm');
 hideItem('statusForm');
 hideItem('compareForm');
 hideItem('selectTestForm');
 hideItem('viewForm');
 displayItem(item);
}
function toggleDiv(item) {
 var i = document.getElementById(item);
 if(i.style.display == showStyle)
  i.style.display = 'none';
 else
  i.style.display = showStyle;
}
</SCRIPT>
<DIV name='configForm' id='configForm'>
<?php
/* ************************************************************************* */
/* ************************************************************************* */
$execStart = microtime( TRUE );
echo '<H5>'.DBICALVERSION." Admin Configuration</H5>\n";
echo "<TABLE>\n";
echo "<TR><TD colspan='3'>&nbsp;</TD></TR>\n";
echo '<TR><TD class="w35">iCal file directory</TD><TD colspan="2">'.DBICALDIRECTORY."</TD></TR>\n";
echo '<TR><TD>Cache directory</TD><TD colspan="2">'.DBICALCACHE."</TD></TR>\n";

echo "<TR><TD colspan='3'>&nbsp;</TD></TR>\n";
echo "<TR><TD>(session) Log level</TD><TD colspan='2'>";
echo "<FORM name='logchgForm' id='logchgForm' method='post' action=''>";
echo "<INPUT type='hidden' name='iCalAction' id='iCalAction' value='logLevelchg'/>\n";
echo '<SELECT name="logLevel" id="logLevel" onChange="document.forms[\'logchgForm\'].submit();">'."\n";
foreach( $loglevels as $key => $level ) {
  $str = ( $level == $logLevel ) ? "selected='selected'" : '';
  echo "<OPTION $str value='$level'>$key</OPTION>\n";
}
echo "</SELECT></FORM>\n";
echo "</TD></TR>\n";
if( 8 > $logLevel ) {
  echo '<TR><TD>Log file name create format</TD><TD colspan="2">"dbiCal_".date(\'Ymd\').".log"'."</TD></TR>\n";
  echo "<TR><TD>Current log file</TD><TD colspan='2'>$logfile</TD></TR>\n";
  echo '<TR><TD>Log lineFormat</TD><TD colspan="2">'.$conf['lineFormat']."<br /><SPAN>Timestamp, priority, class, function, line number, text</SPAN></TD></TR>\n";
}
echo '<TR><TD>Log file size</TD><TD>'.number_format((float) filesize( $logfile ), 0, '', ' ' )." bytes</TD>\n";
echo "<TD><FORM name='clearLogcfgForm' id='clearLogcfgForm' method='post' action=''>";
echo $commonVars;
echo '<INPUT type="hidden" name="formName" id="formName" value="clearLogcfgForm"/>'."\n";
echo '<INPUT name="clearLogbtn" type="submit" id="clearLogbtn" value="clear Log" />'."\n";
echo "</FORM>\n";
echo '<SCRIPT type="text/javascript">document.getElementById("clearLogcfgForm").elements[0].value=\'clearLog\';</SCRIPT>'."\n";
echo "</TD></TR>\n";

echo "<TR><TD colspan='3'>&nbsp;</TD></TR>\n";
echo "<TR><TD>Upload max file size</TD><TD colspan='2'>".number_format((float) DBICALFILEUPLOADMAXSIZE, 0, '', ' ' )." bytes</TD></TR>\n";

echo "<TR><TD>Max input time (in php.ini)</TD><TD colspan='2'>".ini_get( 'max_input_time' )." sec</TD></TR>\n";

echo "<TR><TD colspan='3'>&nbsp;</TD></TR>\n";
echo "<TR><TD>dsn, Data Source Name</TD><TD colspan='2'>".DBICALDSN."</TD></TR>\n";

echo "</TABLE>\n";
echo "</DIV>\n";
echo "<SCRIPT type='text/javascript'> document.getElementById('configForm').style.display = 'none';</SCRIPT>\n";
$execTime['display config Form'] = microtime( TRUE ) - $execStart;

/* ************************************************************************* */
/* ************************************************************************* */
$execStart = microtime( TRUE );
echo "<DIV name='statusForm' id='statusForm'>\n";
$calendars = $dbiCal->getCalendars();
if( FALSE === $calendars ) {
  if( $_log ) {
    $_log->log( 'getCalendars - databas-katastof!!!', PEAR_LOG_LOG_EMERG );
    $_log->flush();
  }
  exit();
}
elseif( !empty( $calendars )) {
  $execTime['fetch db Status data'] = microtime( TRUE ) - $execStart;
  $execStart = microtime( TRUE );
  $str = "<H5>Stored calendar files</H5>\n";
  $str .= "<SPAN>".count( $calendars )." hits</SPAN><BR />\n";
  $str .= "<SPAN>function dbiCal->getCalendars with no arguments<BR />(exec time 'fetch db Status data'+'display db Status Form' below).</SPAN>";
  $selectTest = array();
  $sort = ( isset( $_REQUEST['sort'] ) && !empty( $_REQUEST['sort'] )) ? $_REQUEST['sort'] : 'fa';
  echo dispDBcal( $str, 1, $calendars, $selectTest, array(), $sort );
}
echo "</DIV>\n";
echo "<SCRIPT type='text/javascript'> document.getElementById('statusForm').style.display = 'none';</SCRIPT>\n";
$execTime['display db Status Form'] = microtime( TRUE ) - $execStart;

/* ************************************************************************* */
/* ************************************************************************* */
$execStart = microtime( TRUE );
echo "<DIV name='filesForm' id='filesForm'>\n";
echo "<H5>Calendar files in directory '".DBICALDIRECTORY."'</H5>\n";
$dir = new DirectoryIterator( DBICALDIRECTORY );
$files = array();
foreach( $dir as $file ) {
  if( $file->isDot() || $file->isDir())                         continue;
  if( 'ics' != strtolower( substr( $file->getFilename(), -3 ))) continue; // only ics files
  if( 'filecmp.ics' == $file->getFilename())                    continue; // the compare file.. .
  $files[$file->getFilename()] = $file->getSize();
}
echo "<SPAN>Files : ".count( $files )."</SPAN>\n";
echo "<BR /><SPAN>Filename ( size ) (in db). NB filename may match, content may differ!</SPAN>\n";
$strlen1 = $strlen2 = 0;
foreach( $files as $fileName => & $fileSize ) {
  if( $strlen1 < strlen( $fileName ))
    $strlen1 = strlen( $fileName ) + 1;
  $fileSize = '('.$fileSize.')';
  if( $strlen2 < strlen( $fileSize ))
    $strlen2 = strlen( $fileSize );
}
foreach( $files as $fileName => & $fileSize ) {
  $fileSize = str_pad( $fileSize, $strlen2, '.', STR_PAD_LEFT );
  $fcnt = 0;
  foreach( $calendars as $calendar ) {
    if( $fileName == $calendar['filename'] )
      $fcnt += 1;
  }
  if( 0 < $fcnt )
    $fileSize .= " ($fcnt)";
}
$execTime['fetch Files directory data'] = microtime( TRUE ) - $execStart;
$execStart = microtime( TRUE );
ksort( $files, SORT_REGULAR );
$str = ( 20 < count( $files )) ? " size='20'" : " size='".count( $files )."'";
echo "<FORM name='formFile' id='formFile' method='post' enctype='multipart/form-data' action=''>\n";
echo $commonVars;
echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.DBICALFILEUPLOADMAXSIZE.'">';
echo "<TABLE>\n<TR><TD rowspan='9'>";
echo "<SELECT$str name='fileName' id='fileName'>\n";
foreach( $files as $fileName => $fileSize )
  echo "<OPTION value='$fileName'>".str_pad( $fileName, $strlen1, '.' ).$fileSize."</OPTION>\n";
echo "</SELECT>\n</TD>\n";
$scriptContent1 = "var x=document.forms['formFile'];x.elements[0].value='";
$scriptContent2 = "';x.submit();";
echo "<TD class='r' colspan='2'>\n<SPAN>function dbiCal->insert (exec time 'Insert' below).</SPAN></TD></TR>\n";

echo "<TR><TD><SPAN>Opt. metadata:key</SPAN></TD><TD><SPAN>Opt. metadata:value</SPAN></TD></TR>\n";

echo "<TR>";
echo '<TD><input class="w150" name="metadata[1][key]"   id="metadata[1][key]"   value="" type="text"></TD>';
echo '<TD><input class="w250" name="metadata[1][value]" id="metadata[1][value]" value="" type="text"></TD></TR>'."\n";
echo "<TR>";
echo '<TD><input class="w150" name="metadata[2][key]"   id="metadata[2][key]"   value="" type="text"></TD>';
echo '<TD><input class="w250" name="metadata[2][value]" id="metadata[2][value]" value="" type="text"></TD></TR>'."\n";
echo "<TR>";
echo '<TD><input class="w150" name="metadata[3][key]"   id="metadata[3][key]"   value="" type="text"></TD>';
echo '<TD><input class="w250" name="metadata[3][value]" id="metadata[3][value]" value="" type="text"></TD></TR>'."\n";

echo "<TR><TD colspan='2' class='r'>";
echo '<BUTTON name="Insert" type="button" onClick="'.$scriptContent1.'Insert'.$scriptContent2.'">Insert</BUTTON></TD></TR>'."\n";

echo "<TR class='bt'><TD class='bt'>\n<SPAN>Examine file content.</SPAN></TD>";
echo '<TD class="r"<BUTTON name="Inspect" type="button" onClick="'.$scriptContent1.'Inspect'.$scriptContent2.'">Inspect</BUTTON></TD></TR>'."\n";

echo "<TR class='bt'><TD>\n<SPAN>Remove file.</SPAN></TD>";
echo '<TD class="r"><BUTTON name="Insert" type="button" onClick="if(confirm(\'Remove??\')){'.$scriptContent1.'fileRemove'.$scriptContent2.'}">Remove</BUTTON></TD></TR>'."\n";

echo "<TR class='bt'><TD>\n<SPAN>Upload iCal file.</SPAN></TD>";
echo '<TD class="r"><input type="file" name="uploadfile" id="uploadfile">';
echo '<BUTTON name="Uploadbtn" id="Uploadbtn" type="button" onClick="'.$scriptContent1.'Upload'.$scriptContent2.'">Upload</BUTTON></TD></TR>'."\n";
echo "</TABLE>\n</FORM>\n";
echo "</DIV>\n";
echo "<SCRIPT type='text/javascript'> document.getElementById('filesForm').style.display = 'none';</SCRIPT>\n";
$execTime['display Files Form'] = microtime( TRUE ) - $execStart;

/* ************************************************************************* */
/* ************************************************************************* */
echo "<DIV name='compareForm' id='compareForm'>\n";
echo '<H5>Compare Result</H5>'."\n";
if( isset( $compareResult )) {
  $execStart = microtime( TRUE );
  $rows = substr_count( $compareResult, PHP_EOL );
  echo "<SPAN>Result: $rows rows. Left column : file content, right column: database content</SPAN>\n";
  if( $rows > 40 )
    $rows = 40;
  echo "<BR /><TEXTAREA readonly='readonly' rows='$rows'>$compareResult</TEXTAREA>\n";
  $execTime['display CompareResult'] = microtime( TRUE ) - $execStart;
}
echo "</DIV> \n";
echo "<SCRIPT type='text/javascript'> document.getElementById('compareForm').style.display = 'none';</SCRIPT>\n";

/* ************************************************************************* */
/* ************************************************************************* */
echo "<DIV name='selectTestForm' id='selectTestForm'>\n";
echo '<H5>View database intersection</H5>'."\n";
if( !empty( $selectTest )) {
  $execStart = microtime( TRUE );
  uksort( $selectTest, 'cmp2' );
  echo "<SPAN>function dbiCal->getCalendars WITH argument(-s) (exec time 'select Test' below).</SPAN>";
  echo "<FORM name='formSelect' id='formSelect' method='post' action=''>\n";
  echo $commonVars;
  echo "<TABLE>\n<TR><TD>key</TD><TD colspan='5'>value</TD></TR>\n";
  $rowsw = 0;
  $lablerow1 = '<TR><TD colspan="6"><SPAN class="span2">';
  $lablerow2 = "</SPAN></TD></TR>\n";
  foreach( $selectTest as $skey => $svalues ) {
    if( 'sort' == $skey )
      continue;
    if(( 0 == $rowsw ) && in_array( $skey, array( 'calendar_id', 'date', 'filename' ))) {
      echo $lablerow1.'iCal file info'.$lablerow2;
      $rowsw = 1;
    }
    elseif(( 1 == $rowsw ) && ( 'x-' == substr( strtolower( $skey ), 0, 2 ))) {
      echo $lablerow1.'iCal file X-properties'.$lablerow2;
      $rowsw = 2;
    }
    elseif(( 2 == $rowsw ) && ( 'x-' != substr( strtolower( $skey ), 0, 2 ))) {
      echo $lablerow1.'metadata'.$lablerow2;
      $rowsw = 3;
    }
    if( !in_array( $skey , array( 'date', 'filename' )))
      echo '<TR>';
    if( 'date' == $skey )
      echo "<TD class='label'>insert $skey</TD>\n<TD><SELECT name='selectTest[$skey]' id='selectTest[$skey]'>\n";
    elseif( !in_array( $skey , array( 'calendar_id', 'date', 'filename' )))
      echo "<TD class='label'>$skey</TD>\n<TD colspan='5'><SELECT name='selectTest[$skey]' id='selectTest[$skey]'>\n";
    else
      echo "<TD class='label'>$skey</TD>\n<TD><SELECT name='selectTest[$skey]' id='selectTest[$skey]'>\n";
    echo '<OPTION';
    if( !isset( $selectOptions[$skey] ))
      echo ' selected="selected"';
    echo "></OPTION>\n";
    if( !in_array( $skey , array( 'calendar_id', 'date', 'filename' ))) {
      $selstr = ( isset( $selectOptions[$skey] ) && ( '' == $selectOptions[$skey] )) ? "selected='selected'" : '';
      echo "<OPTION value='empty' $selstr>'key only'</OPTION>\n";
    }
    sort( $svalues );
    $optgroup = '';
    foreach( $svalues as $svalue ) {
      if( 'calendar_id' ==  $skey ) {
        $optcmpgroup = floor(( $svalue / 100 ));
        if( 1 > $optcmpgroup )
          $optcmpgroup = '0';
      }
      elseif( 'date' ==  $skey )
        $optcmpgroup = substr( $svalue, 0, 7 );
      else
        $optcmpgroup = substr( $svalue, 0, 1 );
      if( $optgroup != $optcmpgroup ) {
        $optgroup = $optcmpgroup;
        echo "<OPTGROUP label='$optgroup'>\n";
      }
      $selstr = ( isset( $selectOptions[$skey] ) && ( $svalue == $selectOptions[$skey] )) ? "selected='selected'" : '';
      echo "<OPTION value='$svalue' $selstr>";
      echo ( 75 < strlen( $svalue )) ? substr( $svalue, 0, 75 ).'...' : $svalue;
      echo "</OPTION>\n";
    }
    echo '</SELECT></TD>';
    if( !in_array( $skey , array( 'calendar_id', 'date' )))
      echo "</TR>\n";
  }
  echo '<TR><TD colspan="6" align="right">'."\n";
  $skeys = array_keys( $selectTest );
  foreach( $skeys as & $skey )
    $skey = "'$skey'";
  echo '<INPUT name="clearSelects" type="button" id="submit" value="clear All" onClick="var e=document.getElementById('."'formSelect'".').elements,le=e.length,x;for(x=2;x<le;x++) {if(e[x].options) clearOption(e[x].id);}"/>'."\n";
  echo '<INPUT name="submit" type="submit" id="submit" value="test select" /></TD></TR>';
  echo "</TABLE>\n";
  echo "</FORM>\n";
  echo '<script type="text/javascript">document.getElementById("formSelect").elements[0].value=\'selectTest\';</script>'."\n";
  echo '<script type="text/javascript">function clearOption(item) { var selElem = document.getElementById(item), srcLen = selElem.options.length, s; for (s=srcLen-1; s > -1; s--) selElem.options[s].selected = false;; }</script>'."\n";
  $execTime['display select Test Form'] = microtime( TRUE ) - $execStart;
}
/* result of testing selectOptions */
if( isset( $selectTestResult ) && !empty( $selectTestResult )) {
  $execStart = microtime( TRUE );
  echo $selectTestResult;
  $execTime['display select Test Result'] = microtime( TRUE ) - $execStart;
}
echo "</DIV>\n";
echo "<SCRIPT type='text/javascript'> document.getElementById('selectTestForm').style.display = 'none';</SCRIPT>\n";
/* ************************************************************************* */
/* ************************************************************************* */
?>
<DIV name='viewForm' id='viewForm'>
<H5>Inspect Log</H5>
<?php
$execStart = microtime( TRUE );
if( isset( $_REQUEST ) && isset( $_REQUEST['iCalAction'] ) && ( 'clearLog' == $_REQUEST['iCalAction'] ))
  file_put_contents( $logfile, '' );
if( is_file( $logfile )) {
  $content = file( $logfile);
  $rows = count( $content );
  if( empty( $content ))
    $content = array();
}
else {
  $content = array();
  $rows = 0;
}
$startOpRows = array();
foreach( $content as $rix => $rw ) {
  if( 0 < substr_count( $rw, str_repeat( '* ', 50 ))) {
    $stix = (100>$rix) ? 0 : ((int)($rix/100)) * 100;
    $startOpRows[$stix] = ($stix+1).' ('.($rix+1).')';
  }
}
$startRow = ( isset( $_REQUEST ) && isset( $_REQUEST['startRow'] )) ? $_REQUEST['startRow'] : 0;
$rowcount = 100;
$firstRowsw = $prevRowsw = $nextRowsw = $lastRowsw = '';
if( isset( $_REQUEST['firstRow'] )) {
  $startRow = 0;
  $firstRowsw = $prevRowsw = ' disabled = "disabled"';
  if( $rows < $rowcount ) {
    $rowcount   = $rows;
    $nextRowsw  = $lastRowsw = ' disabled = "disabled"';
  }
}
elseif( isset( $_REQUEST['prevRow'] )) {
  $startRow    -= 100;
  if( 0 > $startRow ) {
    $startRow   = 0;
    $firstRowsw = $prevRowsw = ' disabled = "disabled"';
  }
  if( $rows < ( $startRow + $rowcount + 1 )) {
    $nextRowsw = $lastRowsw = ' disabled = "disabled"';
    $rowcount  = $rows - $startRow;
  }
}
elseif( isset( $_REQUEST['nextRow'] )) {
  $startRow    += 100;
  if(( $rows - 1 ) < $startRow )
    $startRow  = ((int) (($rows - 1) / 100)) * 100;
  if( $rows < ( $startRow + $rowcount + 1 )) {
    $rowcount  = ( $rows - $startRow + 1 );
    $nextRowsw = $lastRowsw = ' disabled = "disabled"';
  }
}
elseif( isset( $_REQUEST['lastRow'] )) {
  $startRow    = ((int) (($rows - 1) / 100)) * 100;
  $rowcount    = $rows - $startRow;
  $nextRowsw   = $lastRowsw = ' disabled = "disabled"';
}
else {
  if( !isset( $_REQUEST['startRow'] ))
    $firstRowsw  = $prevRowsw = ' disabled = "disabled"';
  if( $rows < $rowcount ) {
    $rowcount  = $rows;
    $nextRowsw = $lastRowsw = ' disabled = "disabled"';
  }
}
if( !empty( $content )) {
  $content = array_slice( $content, $startRow, $rowcount );
}
echo "<FORM name='logForm' id='logForm' method='post' action=''>\n";
echo "<INPUT type='hidden' name='iCalAction' id='iCalAction' value='showLog'/>\n";
echo '<INPUT type="hidden" name="formName" id="formName" value="logForm"/>'."\n";
echo '<TABLE class="noborder"><TR><TD><SPAN>'.number_format((float) $rows, 0, '', ' ' )." rows</SPAN></TD>\n";
$colcnt = 1;
if( !empty( $startOpRows )) {
  echo "<TD class='r'><SPAN>Operation row start</SPAN>\n";
  echo '&nbsp;<SELECT name="startRow2" id="startRow2" onChange="var rs=document.getElementById(\'startRow\');rs.options[0].value=this.options[this.selectedIndex].value;rs.options[0].selected=true;document.forms[\'logForm\'].submit();">'."\n";
  foreach( $startOpRows as $stix => $opRow ) {
    $str = ( $stix == $startRow ) ? "selected='selected'" : '';
    echo "<OPTION $str value='$stix'>$opRow</OPTION>\n";
  }
  echo "</SELECT></TD>\n";
  $colcnt +=1;
}
echo '<TD class="c">';
echo '<INPUT type="submit" name="firstRow" id="firstRow" value="<<"'.$firstRowsw."/>\n";
echo '<INPUT type="submit" name="prevRow"  id="prevRow"  value="<" '.$prevRowsw."/>\n";
if( 0 < $rows ) {
  echo '&nbsp;<SELECT name="startRow" id="startRow" onChange="document.forms[\'logForm\'].submit();">'."\n";
  $x = 0;
  while( $x < $rows ) {
    $str = ( $x == $startRow ) ? "selected='selected'" : '';
    echo "<OPTION $str value='$x'>".($x+1)."</OPTION>\n";
    $x += 100;
  }
  echo "</SELECT>\n";
  $rowcount += $startRow;
  echo " - $rowcount&nbsp;\n";
}
else
  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo '<INPUT type="submit" name="nextRow"  id="nextRow"  value=">" '.$nextRowsw."/>\n";
echo '<INPUT type="submit" name="lastRow"  id="lastRow"  value=">>"'.$lastRowsw."/>\n";
echo "</TD>\n</TR>\n";
$colcnt +=1;

echo "<TR>\n<TD class='r'";
if( 2 < $colcnt )
  echo ' colspan="2"';
echo '><INPUT type="submit" name="clearLogbtn" id="clearLogbtn" value="clear Log" onClick="document.getElementById(\'logForm\').elements[0].value=\'clearLog\';document.getElementById(\'logForm\').elements[3].value=\'0\';"/>'."</TD>\n";
echo "<TD class='r'><SPAN>(session) Log level</SPAN>";
echo '<SELECT name="logLevel" id="logLevel" onChange="document.getElementById(\'logForm\').elements[0].value=\'logLevelchg\';document.forms[\'logForm\'].submit();">'."\n";
foreach( $loglevels as $key => $level ) {
  $str = ( $level == $logLevel ) ? "selected='selected'" : '';
  echo "<OPTION $str value='$level'>$key</OPTION>\n";
}
echo "</SELECT></TD>\n";
echo "</TR></TABLE></FORM>\n";
if( $rows > 40 )
  $rows = 40;
elseif( $rows < 3 )
  $rows = 3;
echo "<TEXTAREA readonly='readonly' rows='$rows'>".implode( '', $content )."</TEXTAREA>\n";
echo "</DIV>\n";
echo "<SCRIPT type='text/javascript'> document.getElementById('viewForm').style.display = 'none';</SCRIPT>\n";
$execTime['displayLog'] = microtime( TRUE ) - $execStart;
/* ************************************************************************* */
/* ************************************************************************* */
if( isset( $displayItem ) && !empty( $displayItem ))
  echo "<SCRIPT type='text/javascript'>displayDiv('$displayItem');</SCRIPT>\n";
?>
<BR />
<TABLE><TR>
<TD><H5 onClick="toggleDiv('execTimeForm')" onMouseOver="this.style['cursor']='pointer'" onMouseOut="this.style['cursor']='default'">+ Server exec. time...</H5></TD>
<TD class="r"><a class="mini" title="top" href="#top">[top]</a></TD>
</TR></TABLE>
<DIV name='execTimeForm' id='execTimeForm'>
<TABLE>
<TR><TH class="w35">operation</TH><TH class="w18">time</TH><TH>&nbsp;</TH></TR>
<?php
$execTime['Total'] = microtime( TRUE ) - $start;
foreach( $execTime as $operation => $time ) {
  $time = number_format( $time, 5 );
  if( isset( $_REQUEST ) && isset( $_REQUEST['iCalAction'] ) && ( 'clearLog' == $_REQUEST['iCalAction'] ))
    $continue = TRUE;
  elseif( $_log )
    $_log->log( str_pad( $operation.':', 15 ).$time.' sec', PEAR_LOG_NOTICE );
  echo "<TR><TD><SPAN>$operation</SPAN></TD><TD class='r'><SPAN>$time sec</SPAN></TD><TD>&nbsp;</TD></TR>\n";
}
if( $_log )
  $_log->flush();
?>
</TABLE>
</DIV>
<SCRIPT type='text/javascript'> document.getElementById('execTimeForm').style.display = 'none';</SCRIPT>
<TABLE><TR>
<TD style="width:25%"><?php echo DBICALVERSION; ?> Admin</TD>
<TD style="text-align:center;width:25%">&copy; 2011 kigkonsult</TD>
<TD style="text-align:center;width:25%"><A href="docs/LGPL.txt" target="_blank">Licence LGPL</A></TD>
<TD style="text-align:right"><A href="http://www.kigkonsult.se/contact/index.php">Contact kigkonsult.se</A></TD>
</TR></TABLE>
</BODY>
</HTML>
