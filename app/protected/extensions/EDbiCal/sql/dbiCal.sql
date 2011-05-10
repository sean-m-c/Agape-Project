--
-- dbiCal
-- ver 3.0rc
--
-- copyright (c) 2011 Kjell-Inge Gustafsson kigkonsult
-- www.kigkonsult.se/iCalcreator/dbiCal/index.php
-- ical@kigkonsult.se
-- updated 20110219
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
--

DROP TABLE IF EXISTS xprop;
DROP TABLE IF EXISTS rexrule;
DROP TABLE IF EXISTS rdate;
DROP TABLE IF EXISTS pfreebusy;
DROP TABLE IF EXISTS exdate;
DROP TABLE IF EXISTS attach;
DROP TABLE IF EXISTS mtext;

DROP TABLE IF EXISTS alarm;
DROP TABLE IF EXISTS freebusy;
DROP TABLE IF EXISTS journal;
DROP TABLE IF EXISTS todo;
DROP TABLE IF EXISTS event;
DROP TABLE IF EXISTS stddlght;
DROP TABLE IF EXISTS timezone;
DROP TABLE IF EXISTS parameter;
DROP TABLE IF EXISTS metadata;
DROP TABLE IF EXISTS calendar;

CREATE TABLE IF NOT EXISTS calendar (
  calendar_id              bigint(20)   NOT NULL auto_increment,
  calendar_create_date     datetime,
  calendar_version         varchar(50)  default NULL COMMENT 'calendar version',
  calendar_calscale        varchar(50)  default NULL COMMENT 'calendar calscale',
  calendar_method          varchar(50)  default NULL COMMENT 'calendar method',
  calendar_unique_id       varchar(255) default NULL COMMENT 'calendar unique id',
  calendar_filename        varchar(255) default NULL COMMENT 'calendar file name',
  calendar_filesize        bigint       default 0    COMMENT 'calendar file size',
  calendar_cnt_metadata    smallint     default 0,
  calendar_cnt_xprop       smallint     default 0,
  calendar_cnt_timezone    smallint     default 0,
  calendar_cnt_event       smallint     default 0,
  calendar_cnt_journal     smallint     default 0,
  calendar_cnt_freebusy    smallint     default 0,
  calendar_cnt_todo        smallint     default 0,
  PRIMARY KEY  (calendar_id),
  KEY calendar_filename (calendar_filename(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS metadata (
  metadata_id              bigint(20)   NOT NULL auto_increment,
  metadata_owner_id        bigint(20)   NOT NULL COMMENT 'link to calendar',
  metadata_key             varchar(255) NOT NULL,
  metadata_text            text         NOT NULL,
  PRIMARY KEY  (metadata_id),
  KEY metadata_owner_id (metadata_owner_id),
  KEY metadata_key      (metadata_key(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS parameter (
  parameter_id             bigint(20)   NOT NULL auto_increment,
  parameter_owner_id       bigint(20)   NOT NULL COMMENT 'link to owner',
  parameter_ownertype      char(14)     NOT NULL COMMENT 'owner',
  parameter_property       char(14)     NOT NULL COMMENT 'property name',
  parameter_key            varchar(255) NOT NULL,
  parameter_value          varchar(255) NOT NULL,
  PRIMARY KEY  (parameter_id),
  KEY parameter_owner_id (parameter_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS timezone (
  timezone_id              bigint(20)   NOT NULL auto_increment,
  timezone_owner_id        bigint(20)   NOT NULL COMMENT 'link to owner',
  timezone_ono             smallint     COMMENT 'component order number',
  timezone_tzid            varchar(255) NOT NULL,
  timezone_tzurl           varchar(255) NOT NULL,
  timezone_last_modified   datetime     default NULL COMMENT 'datetime UTC',
  timezone_cnt_xprop       smallint     default 0,
  timezone_cnt_stddlght    smallint     default 0,
  PRIMARY KEY  (timezone_id),
  KEY timezone_owner_id (timezone_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS stddlght (
  stddlght_id              bigint(20)   NOT NULL auto_increment,
  stddlght_owner_id        bigint(20)   NOT NULL COMMENT 'link to owner',
  stddlght_type            char(8)      NOT NULL COMMENT 'standard/daylight',
  stddlght_ono             smallint     COMMENT 'component order number',
  stddlght_startdatetime   datetime     default NULL COMMENT 'local datetime',
  stddlght_tzoffsetfrom    smallint     NOT NULL COMMENT 'utc-offset from',
  stddlght_tzoffsetto      smallint     NOT NULL COMMENT 'utc-offset to',
  stddlght_cnt_mtext       smallint     default 0,
  stddlght_cnt_rdate       smallint     default 0,
  stddlght_cnt_rrule       smallint     default 0,
  stddlght_cnt_xprop       smallint     default 0,
  PRIMARY KEY  (stddlght_id),
  KEY stddlght_owner_id (stddlght_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS event (
  event_id                bigint(20)   NOT NULL auto_increment,
  event_owner_id          bigint(20)   NOT NULL COMMENT 'link to calendar',
  event_ono               smallint     COMMENT 'component order number',
  event_uid               varchar(255) NOT NULL COMMENT 'unique id',
  event_dtstamp           datetime     NOT NULL COMMENT 'datetime UTC',
  event_startdatetime     datetime     default NULL COMMENT 'when the calendar component begins',
  event_startdatetimeutc  bool         default NULL COMMENT 'if true(1), datetime UTC',
  event_startdate         date         default NULL COMMENT 'when the calendar component begins',
  event_enddatetime       datetime     default NULL COMMENT 'when the calendar component ends',
  event_enddatetimeutc    bool         default NULL COMMENT 'if true(1), datetime UTC',
  event_enddate           date         default NULL COMMENT 'when the calendar component ends',
  event_duration          varchar(255) default NULL COMMENT 'a positive duration of time',
  event_summary           varchar(255) default NULL COMMENT 'subject for the calendar component',
  event_description       text         default NULL COMMENT 'a more complete description',
  event_geo               varchar(255) default NULL COMMENT 'the global position for the activity',
  event_location          varchar(255) default NULL COMMENT 'the intended venue for the activity',
  event_organizer         varchar(255) default NULL COMMENT 'organizer for a calendar component',
  event_class             varchar(50)  default NULL COMMENT 'access classification',
  event_transp            varchar(50)  default NULL COMMENT 'transparent or not to busy time searches',
  event_status            varchar(50)  default NULL COMMENT 'the overall status or confirmation',
  event_url               varchar(255) default NULL COMMENT 'where to find (more) info',
  event_priority          tinyint      default NULL COMMENT 'relative priority,1-9; 1-4 high, 5 medium.. .',
  event_recurrence_id_dt  datetime     default NULL COMMENT 'to identify a specific instance',
  event_recurrence_idutc  bool         default NULL COMMENT 'if true(1), datetime UTC',
  event_recurrence_id     date         default NULL COMMENT 'to identify a specific instance',
  event_sequence          smallint     default NULL COMMENT 'the revision sequence number',
  event_created           datetime     default NULL COMMENT 'datetime UTC',
  event_last_modified     datetime     default NULL COMMENT 'datetime UTC',
  event_cnt_mtext         smallint     default 0,
  event_cnt_attach        smallint     default 0,
  event_cnt_exdate        smallint     default 0,
  event_cnt_exrule        smallint     default 0,
  event_cnt_rdate         smallint     default 0,
  event_cnt_rrule         smallint     default 0,
  event_cnt_xprop         smallint     default 0,
  event_cnt_alarm         smallint     default 0,
  PRIMARY KEY  (event_id),
  KEY event_uid (event_uid),
  KEY event_owner_id (event_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS todo (
  todo_id                 bigint(20)   NOT NULL auto_increment,
  todo_owner_id           bigint(20)   NOT NULL COMMENT 'link to calendar',
  todo_ono                smallint     COMMENT 'component order number',
  todo_uid                varchar(255) NOT NULL COMMENT 'unique id',
  todo_dtstamp            datetime     NOT NULL COMMENT 'datetime UTC',
  todo_startdatetime      datetime     default NULL COMMENT 'when the calendar component begins',
  todo_startdatetimeutc   bool         default NULL COMMENT 'if true(1), datetime UTC',
  todo_startdate          date         default NULL COMMENT 'when the calendar component begins',
  todo_duedatetime        datetime     default NULL COMMENT 'expected completion',
  todo_duedatetimeutc     bool         default NULL COMMENT 'if true(1), datetime UTC',
  todo_duedate            date         default NULL COMMENT 'expected completion',
  todo_duration           varchar(255) default NULL COMMENT 'a positive duration of time',
  todo_completed          datetime     default NULL COMMENT 'datetime UTC',
  todo_summary            varchar(255) default NULL COMMENT 'subject for the calendar component',
  todo_description        text         default NULL COMMENT 'a more complete description',
  todo_geo                varchar(255) default NULL COMMENT 'the global position for the activity',
  todo_location           varchar(255) default NULL COMMENT 'the intended venue for the activity',
  todo_organizer          varchar(255) default NULL COMMENT 'organizer for a calendar component',
  todo_class              varchar(50)  default NULL COMMENT 'access classification',
  todo_status             varchar(50)  default NULL COMMENT 'the overall status or confirmation',
  todo_url                varchar(255) default NULL COMMENT 'where to find (more) info',
  todo_percent_complete   tinyint      default NULL COMMENT 'the percent completion',
  todo_priority           tinyint      default NULL COMMENT 'relative priority,1-9; 1-4 high, 5 medium.. .',
  todo_recurrence_id_dt   datetime     default NULL COMMENT 'to identify a specific instance',
  todo_recurrence_idutc   bool         default NULL COMMENT 'if true(1), datetime UTC',
  todo_recurrence_id      date         default NULL COMMENT 'to identify a specific instance',
  todo_sequence           smallint     default NULL COMMENT 'the revision sequence number',
  todo_created            datetime     default NULL COMMENT 'datetime UTC',
  todo_last_modified      datetime     default NULL COMMENT 'datetime UTC',
  todo_cnt_mtext          smallint     default 0,
  todo_cnt_attach         smallint     default 0,
  todo_cnt_exdate         smallint     default 0,
  todo_cnt_exrule         smallint     default 0,
  todo_cnt_rdate          smallint     default 0,
  todo_cnt_rrule          smallint     default 0,
  todo_cnt_xprop          smallint     default 0,
  todo_cnt_alarm          smallint     default 0,
  PRIMARY KEY  (todo_id),
  KEY todo_uid (todo_uid),
  KEY todo_owner_id (todo_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS journal (
  journal_id                bigint(20)   NOT NULL auto_increment,
  journal_owner_id          bigint(20)   NOT NULL COMMENT 'link to calendar',
  journal_ono               smallint     COMMENT 'component order number',
  journal_uid               varchar(255) NOT NULL COMMENT 'unique id',
  journal_dtstamp           datetime     NOT NULL COMMENT 'datetime UTC',
  journal_startdatetime     datetime     default NULL COMMENT 'when the calendar component begins',
  journal_startdatetimeutc  bool         default NULL COMMENT 'if true(1), datetime UTC',
  journal_startdate         date         default NULL COMMENT 'when the calendar component begins',
  journal_summary           varchar(255) default NULL COMMENT 'subject for the calendar component',
  journal_organizer         varchar(255) default NULL COMMENT 'organizer for a calendar component',
  journal_class             varchar(50)  default NULL COMMENT 'access classification',
  journal_url               varchar(255) default NULL COMMENT 'where to find (more) info',
  journal_recurrence_id_dt  datetime     default NULL COMMENT 'to identify a specific instance',
  journal_recurrence_idutc  bool         default NULL COMMENT 'if true(1), datetime UTC',
  journal_recurrence_id     date         default NULL COMMENT 'to identify a specific instance',
  journal_sequence          smallint     default NULL COMMENT 'the revision sequence number',
  journal_status            varchar(50)  default NULL COMMENT 'the overall status or confirmation',
  journal_created           datetime     default NULL COMMENT 'datetime UTC',
  journal_last_modified     datetime     default NULL COMMENT 'datetime UTC',
  journal_cnt_mtext         smallint     default 0,
  journal_cnt_attach        smallint     default 0,
  journal_cnt_exdate        smallint     default 0,
  journal_cnt_exrule        smallint     default 0,
  journal_cnt_rdate         smallint     default 0,
  journal_cnt_rrule         smallint     default 0,
  journal_cnt_xprop         smallint     default 0,
  PRIMARY KEY  (journal_id),
  KEY journal_uid (journal_uid),
  KEY journal_owner_id (journal_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS freebusy (
  freebusy_id             bigint(20)   NOT NULL auto_increment,
  freebusy_owner_id       bigint(20)   NOT NULL COMMENT 'link to owner',
  freebusy_ono            smallint     COMMENT 'component order number',
  freebusy_uid            varchar(255) NOT NULL COMMENT 'unique id',
  freebusy_dtstamp        datetime     NOT NULL COMMENT 'datetime UTC',
  freebusy_contact        varchar(255) default NULL COMMENT 'the intended venue for the activity',
  freebusy_startdatetime  datetime     default NULL COMMENT 'when the calendar component begins',
  freebusy_enddatetime    datetime     default NULL COMMENT 'when the calendar component ends',
  freebusy_duration       varchar(255) default NULL COMMENT 'a positive duration of time',
  freebusy_organizer      varchar(255) default NULL COMMENT 'organizer for a calendar component',
  freebusy_url            varchar(255) default NULL COMMENT 'where to find (more) info',
  freebusy_cnt_mtext      smallint     default 0,
  freebusy_cnt_freebusy   smallint     default 0,
  freebusy_cnt_xprop      smallint     default 0,
  PRIMARY KEY  (freebusy_id),
  KEY freebusy_owner_id (freebusy_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS alarm (
  alarm_id                bigint(20)   NOT NULL auto_increment,
  alarm_owner_id          bigint(20)   NOT NULL COMMENT 'link to owner',
  alarm_ownertype         char(8)      NOT NULL,
  alarm_ono               smallint     COMMENT 'component order number',
  alarm_action            varchar(255) default NULL COMMENT 'the action to be invoked when triggered',
  alarm_description       text         default NULL COMMENT 'a more complete description',
  alarm_duration          varchar(255) default NULL COMMENT 'a positive duration of time',
  alarm_repeat            smallint     default NULL COMMENT 'to be repeated, after the initial trigger',
  alarm_summary           varchar(255) default NULL COMMENT 'subject for the calendar component',
  alarm_trigger_datetime  datetime     default NULL COMMENT 'datetime UTC',
  alarm_trigger           varchar(255) default NULL COMMENT 'a positive duration of time',
  alarm_cnt_attach        smallint     default 0,
  alarm_cnt_mtext         smallint     default 0,
  alarm_cnt_xprop         smallint     default 0,
  PRIMARY KEY  (alarm_id),
  KEY alarm_owner_id (alarm_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS mtext (
  mtext_id                bigint(20)   NOT NULL auto_increment,
  mtext_owner_id          bigint(20)   NOT NULL COMMENT 'link to owner',
  mtext_ownertype         char(8)      NOT NULL,
  mtext_name              varchar(14)  NOT NULL COMMENT 'property name',
  mtext_mtext             varchar(255) NOT NULL COMMENT 'property content',
  PRIMARY KEY  (mtext_id),
  KEY mtext_owner_id (mtext_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS attach (
  attach_id              bigint(20)   NOT NULL auto_increment,
  attach_owner_id        bigint(20)   NOT NULL COMMENT 'link to owner',
  attach_ownertype       char(8)      NOT NULL,
  attach_attach_uri      varchar(255) default NULL COMMENT 'a URI type of reference',
  attach_attach_bin      mediumblob   default NULL COMMENT 'a character encoding of inline binary data',
  PRIMARY KEY  (attach_id),
  KEY attach_owner_id (attach_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS exdate (
  exdate_id              bigint(20)   NOT NULL auto_increment,
  exdate_owner_id        bigint(20)   NOT NULL COMMENT 'link to owner',
  exdate_ownertype       char(8)      NOT NULL,
  exdate_order           tinyint unsigned          COMMENT 'exception order number within component',
  exdate_sequence        tinyint unsigned          COMMENT 'sequence number of exception date/time',
  exdate_datetime        datetime     default NULL COMMENT 'datetime exception',
  exdate_datetimeutc     bool         default NULL COMMENT 'if true(1), datetime UTC',
  exdate_date            date         default NULL COMMENT 'date exception',
  PRIMARY KEY  (exdate_id),
  KEY exdate_owner_id (exdate_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS pfreebusy (
  pfreebusy_id            bigint(20)  NOT NULL auto_increment,
  pfreebusy_owner_id      bigint(20)  NOT NULL     COMMENT 'link to owner',
  pfreebusy_order         tinyint unsigned         COMMENT 'freebusy order number within component',
  pfreebusy_pfreebusytype char(255)   NOT NULL,
  pfreebusy_sequence      tinyint unsigned         COMMENT 'period sequence no within order',
  pfreebusy_startdatetime datetime    NOT NULL     COMMENT 'start datetime UTC',
  pfreebusy_enddatetime   datetime    default NULL COMMENT 'end period datetime UTC period',
  pfreebusy_periodduration varchar(255) default NULL COMMENT 'a positive duration of time',
  PRIMARY KEY  (pfreebusy_id),
  KEY pfreebusy_owner_id (pfreebusy_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS rdate (
  rdate_id               bigint(20)   NOT NULL auto_increment,
  rdate_owner_id         bigint(20)   NOT NULL COMMENT 'link to owner',
  rdate_ownertype        char(8)      NOT NULL,
  rdate_order            tinyint unsigned          COMMENT 'exception order number within component',
  rdate_sequence         tinyint unsigned          COMMENT 'sequence number within order',
  rdate_startdatetime    datetime     default NULL COMMENT 'start datetime exception',
  rdate_startdatetimeutc bool         default NULL COMMENT 'if true(1), datetime UTC',
  rdate_startdate        date         default NULL COMMENT 'start date exception',
  rdate_enddatetime      datetime     default NULL COMMENT 'end datetime exception period',
  rdate_enddatetimeutc   bool         default NULL COMMENT 'if true(1), datetime UTC',
  rdate_periodduration   varchar(255) default NULL COMMENT 'a positive duration of time',
  PRIMARY KEY  (rdate_id),
  KEY rdate_owner_id (rdate_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS rexrule (
  rexrule_id              bigint(20)   NOT NULL auto_increment,
  rexrule_owner_id        bigint(20)   NOT NULL COMMENT 'link to owner',
  rexrule_ownertype       char(8)      NOT NULL,
  rexrule_type            varchar(6)   NOT NULL COMMENT 'rrule/exrule',
  rexrule_freq            varchar(50)  default NULL COMMENT 'SECONDLY/MINUTELY/HOURLY/DAILY/WEEKLY/MONTHLY/YEARLY',
  rexrule_count           int(8)       default NULL COMMENT 'number of occurencies, count OR until',
  rexrule_until_datetime  datetime     default NULL COMMENT 'occurs until <datetime> (UTC)',
  rexrule_until_date      date         default NULL COMMENT 'occurs until <date>',
  rexrule_interval        int(8)       default NULL COMMENT 'interval between occurencies',
  rexrule_bysecond        varchar(255) default NULL COMMENT 'comma separated list of seconds',
  rexrule_byminute        varchar(255) default NULL COMMENT 'comma separated list of minutes',
  rexrule_byhour          varchar(255) default NULL COMMENT 'comma separated list of hours',
  rexrule_byday           varchar(255) default NULL COMMENT 'comma separated list of days',
  rexrule_bymonthday      varchar(255) default NULL COMMENT 'comma separated list of monthdays',
  rexrule_byyearday       varchar(255) default NULL COMMENT 'comma separated list of yeardays',
  rexrule_byweekno        varchar(255) default NULL COMMENT 'comma separated list of weeknos',
  rexrule_bymonth         varchar(255) default NULL COMMENT 'comma separated list of months',
  rexrule_bysetpos        varchar(255) default NULL COMMENT 'comma separated list of.. .',
  rexrule_wkst            char(2)      default NULL COMMENT 'weekday',
  PRIMARY KEY  (rexrule_id),
  KEY rexrule_owner_id (rexrule_owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS xprop (
  xprop_id                bigint(20)   NOT NULL auto_increment,
  xprop_owner_id          bigint(20)   NOT NULL COMMENT 'link to owner',
  xprop_ownertype         char(8)      NOT NULL,
  xprop_key               varchar(255) NOT NULL,
  xprop_text              text         NOT NULL COMMENT 'non-processing information',
  PRIMARY KEY  (xprop_id),
  KEY xprop_owner_id  (xprop_owner_id),
  KEY xprop_ownertype (xprop_ownertype),
  KEY xprop_key       (xprop_key(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

