dbiCal v3.0
copyright (c) 2011 Kjell-Inge Gustafsson, kigkonsult
www.kigkonsult.se/dbiCal
ical@kigkonsult.se

dbiCal is a PHP database back end solution storing (multiple)
iCal calendars in a database using pear MDB2 as database API
and iCalcreator (2.8) as the calendar information API.

dbiCal may very well fit as a caldav calendar database back end.

The package supports
- insert calendar(-s) into database
- fetching information about stored calendars in the database
- fetch calendar (instance) from database
- delete calendar instance(-s) in database

dbiCal supports ALL calendar information, timezone, event, todo,
journal and freebusy components with all properties, including
x-properties. Empty properties are not supported. Multiple
instances of a calendar (with separate insert datetimes) are
supported. Also metadata, ex. user/calendar references or
user/calendar preferences, may be stored for each calendar instance.


Using the PHP PEAR MDB2 package, dbiCal supports the following RDBMS:
MySQL/MySQLi, PostgreSQL, Oracle, Frontbase (unmaintained), Querysim,
Interbase/Firebird, MSSQL, SQLite. Depending on the underlying RDBMS
(and MDB2), transactions are supported.

The PHP PEAR LOG usage is optional.


dbiCal is also shipped with a simple web admin page, offering ability
to upload, insert, examine (file), inspect (db), removal and compare
of calendar files and db content.


dbIcal is implemented as a 'public' class file, dbiCal.class.php and
'inner' calendar/component/property/table DAOs (data access objects)
doing the hard work. iCalcreator is used as calendar (file) API.

Developing environment is PHP 5.3.3, mysqli driver and MySQL 5.1.47.
Please notify back if using other database (engines).


To get a proper understanding of iCal, calendar, components and
properties, explore the RFC2445, download from
'http://www.kigkonsult.se/downloads#rfc2445'.


DEPENDENCIES
============

iCalcreator-2.8
'http://www.kigkonsult.se/downloads/index.php#icalcreator'.

MDB2
'http://pear.php.net'

LOG, optional
'http://pear.php.net'


FILES
=====

cache                 directory, used when comparing disc and db files
calendars             directory, calendar file storage (proposal)
docs/GPL.txt          licence
docs/README.txt       this file
includes              directory, dbiCal, DAO class files and images
sql                   directory, (MySQL) sql create script
dbiCal_admin.php      dbiCal web admin page


INSTALL
=======

Unpack to any directory
- add this directory to your include-path
- or unpack to your application-(include)-directory

Add
"require_once [directory/][dbiCal/includes/]]dbIcal.class.php;"
to your php-script. Please examine the enclosed 'dbiCal_admin.php'
scripts for using example.


Download iCalcreator package and place iCalcreator class files
in the same directory as dbiCal or your application-(include)-directory.
Add
"require_once [directory/][dbiCal/includes/]]iCalcreator.class.php;"
to your php-script. Please examine the enclosed 'dbiCal_admin.php'
script for using example.


In order to create a proper MDB2 DSN (data source name), please
examine
'http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php'.
As defined in 'dbiCal_admin.php':
'mysqli://dbiCal:dbiCal@localhost/dbiCal2'
<php db backend>://<userid>:<passwd>@<host>/<database>


If setting up and configure logging, please examine
'http://www.indelible.org/php/Log/guide.html' for details and the
enclosed 'dbiCal_admin.php' script for using example.


Place 'dbiCal_admin.php' under your web server document root,
edit the 'init and defines' and 'PHP includes' sections (especially
directories and log settings) and open the page in a web browser.


How to create a MySQL database:
Open a MySQL database interface like phpMyAdmin and create a new
database, make note of database server name/IP, database name, user
and password. Select the new database, import and execute the
dbiCal.sql (from the sql directory). The character set in the sql
script (default UTF8) may need to be altered to fit.


DESCRIPTION
===========

- - - - - - - - - - - -
To create a new dbiCal object
- - - - - - - - - - - -

dbiCal( db_conn [, log] )

db_comm = pear MDB2 connection object
log     = pear Log object


- - - - - - - - - - - -

The dbiCal class offer four API methods:

- - - - - - - - - - - -


dbiCal->getCalendars( [(array) selectOptions] )

selectOptions = associative array (
           'calendar_id'           => (int)    value
         , 'filename'              => (string) value,
         , 'date'                  => (string) date (YYYY-mm-dd)
       *[, x-property (string) key => (string) value / empty ]
       *[, metadata (string)   key => (string) value / empty ] )

An empty array or no array at all selects all calendars in the database.

Any set 'calendar_id' overrides all other criteria.

Multiple criteria are combined by the AND condition,
(* multiple x-property select criteria  are combined by the OR
condition as well as metadata criteria).

Any x-property and/or metadata key set and empty value selects
matching calendar with the specific key set (regardless of value).

X-property and metadata key uses database type VARCHAR(255)
and value TEXT (in MySQL 2+(2^16)).

Returns an array of data for existing calendar instances in the
database matching the criteria in array selectOptions,
each matching calendar is presented by
an associative sub-array(
    'calendar_id'           => (int)    value
  , 'filename'              => (string) value,
  , 'filesize'              => (int)    value,
  , 'create_date'           => (string) db insert date value
 [, (string) x-property key => (string) value ]
 [, 'metadata'              => array( *[(string) key=>(string) value )]
)

Default sort order is descending 'calendar_id' (also array key),
ex. when searching on 'filenamn' and multiple instances occur,
higest 'calendar_id' (=latest insert) is placed "first" in array.

Returns an emtpy array if no matching criteria
Returns FALSE if database error occurs.

- - - - - - - - - - - -


dbiCal->delete( (array) selectOptions )

selectOptions, see above.

Deletes all calendars that matches criteria in selectOptions,
an empty array or no array at all, deletes all.

Returns FALSE if database error occurs, otherwise always TRUE,
even if no delete is done.

 - - - - - - - - - - -


dbiCal->insert( (object) calendar [, (array) metadata ] )

calendar = iCalcreator calendar object
metadata = arbitrary (string) key / (string) value associative array,
ex. user/calendar reference or user/calendar preferences

metadata key uses database type VARCHAR(255) and value TEXT
(in MySQL 2+(2^16)).

Returns calendar database id (success) or FALSE (database error).

 - - - - - - - - - - -


dbiCal->select( (int) calendar_id [, unique_id=FALSE ] )

Selects calendar with argument database id

Returns iCalcreator calendar object (success) or FALSE (not found
or database error).

Setting argument 'unique_id' will override any existent (stored)
calendar unique_id (if set), please examine
'http://www.kigkonsult.se/iCalcreator/docs/using.html#Unique_id'.

 - - - - - - - - - - -


The web admin page presents a menu with six tabs:

'config'       shows dbiCal admin configuration
               offer ability to change (session) log level

'db status'    show inserted file instance(-s) in the database
               and offer ability to
                remove   calendar instance from database
                compare  disc file and database calendar instance (contents)
                download calendar instance as file from database

'Files'        display files in the 'calendars' directory
               and offer ability to
                insert  calendar file (instance) into database
                inspect calendar file in the directory
                remove  calendar file from the directory
                upload  calendar file into the directory

'file compare' shows result from a 'compare' operation

'test Select'  offer ability for 'ad hoc' database searches
               and displays the result
               offer the same functionality for matching results as
               the 'db status' tab

'Inspect Log'  display (opt.) log file
               offer the ability to examine and clear the log file
               and change (session) log level

 - - - - - - - - - - -


For creating (used as dbiCal input) and utilize (dbiCal output)
iCalcreator objects, please examine
'http://www.kigkonsult.se/iCalcreator/docs/using.html'
for use and coding issues.
Also useful are RFC2445 (Internet Calendaring and Scheduling Core
Object Specification (iCalendar)
and RFC2446 (iCalendar Transport-Independent Interoperability
Protocol (iTIP) Scheduling Events, BusyTime, To-dos and Journal
Entries)
or RFC5545 (Internet Calendaring and Scheduling Core Object
Specification (iCalendar), Obsoletes: RFC2445).


SUPPORT
=======

The main support channel is using iCalcreator Sourceforge forum.

Use www.kigkonsult.se/contact/index.php page for queries,
improvement/development issues or professional support and
development. Please note that paid support or consulting service
has the highest priority.

Our services are available for support and designing and developing
iCalcreator, dbiCal etc. customizations, adaptations and other
PHP/MySQL solutions with a special focus on software utility and
reliability, supported through our iterative acquire/design/transition
process modell.


COPYRIGHT & LICENCE
===================

COPYRIGHT

dbiCal v3.0
copyright (c) 2011 Kjell-Inge Gustafsson, kigkonsult
www.kigkonsult.se/dbiCal
ical@kigkonsult.se

LICENCE

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
