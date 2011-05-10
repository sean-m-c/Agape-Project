-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2010 at 04:47 PM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `agape`
--

-- --------------------------------------------------------

--
-- Table structure for table `authassignment`
--

CREATE TABLE IF NOT EXISTS `authassignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authassignment`
--

INSERT INTO `authassignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
('appadmin', '1', NULL, 'N;'),
('volunteer', '4', NULL, 'N;'),
('cpadmin', '1', NULL, 'N;'),
('super', '1', NULL, 'N;'),
('volunteer', '1', NULL, 'N;');

-- --------------------------------------------------------

--
-- Table structure for table `authitem`
--

CREATE TABLE IF NOT EXISTS `authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authitem`
--

INSERT INTO `authitem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
('Authority', 2, NULL, NULL, NULL),
('appadmin', 2, 'An admin who can approve projects, etc; typically member of group reviewing application.', 'return Yii::app()->user->appadmin;', ''),
('super', 2, 'User who can create other admins', 'return Yii::app()->user->super;', ''),
('volunteer', 2, 'User who can join community partners, create project, and use their own profiles', 'return Yii::app()->user->volunteer;', ''),
('SiteIndex', 0, NULL, NULL, NULL),
('SiteError', 0, NULL, NULL, NULL),
('SiteContact', 0, NULL, NULL, NULL),
('SiteLogin', 0, NULL, NULL, NULL),
('SiteLogout', 0, NULL, NULL, NULL),
('UserAdministrating', 1, NULL, NULL, NULL),
('UserCreate', 0, NULL, NULL, NULL),
('UserView', 0, NULL, NULL, NULL),
('UserUpdate', 0, NULL, NULL, NULL),
('UserDelete', 0, NULL, NULL, NULL),
('UserIndex', 0, NULL, NULL, NULL),
('UserAdmin', 0, NULL, NULL, NULL),
('UserOwnAdministrating', 1, 'Users administrating their own profile (update, delete) for themselves', 'return Yii::app()->user->id==$_GET[''id''];', ''),
('Community_partnerPartners', 0, NULL, NULL, NULL),
('Community_partnerAdd', 0, NULL, NULL, NULL),
('Community_partnerView', 0, NULL, NULL, NULL),
('Community_partnerCreate', 0, NULL, NULL, NULL),
('Community_partnerUpdate', 0, NULL, NULL, NULL),
('Community_partnerDelete', 0, NULL, NULL, NULL),
('Community_partnerIndex', 0, NULL, NULL, NULL),
('Community_partnerAdmin', 0, NULL, NULL, NULL),
('Community_partnerRemove', 0, NULL, NULL, NULL),
('OwnProjectPartners', 1, 'Lets a person view their own projects and partners', '', ''),
('SiteAdvSearch', 0, NULL, NULL, NULL),
('cpadmin', 2, 'User who is in a community partner, can approve projects for cp and users.', ' 	return Yii::app()->user->cpadmin;', '');

-- --------------------------------------------------------

--
-- Table structure for table `authitemchild`
--

CREATE TABLE IF NOT EXISTS `authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authitemchild`
--

INSERT INTO `authitemchild` (`parent`, `child`) VALUES
('OwnProjectPartners', 'Community_partnerAdd'),
('OwnProjectPartners', 'Community_partnerCreate'),
('OwnProjectPartners', 'Community_partnerIndex'),
('OwnProjectPartners', 'Community_partnerPartners'),
('OwnProjectPartners', 'Community_partnerRemove'),
('OwnProjectPartners', 'Community_partnerView'),
('UserAdministrating', 'UserAdmin'),
('UserAdministrating', 'UserDelete'),
('UserAdministrating', 'UserIndex'),
('UserAdministrating', 'UserUpdate'),
('UserAdministrating', 'UserView'),
('UserOwnAdministrating', 'SiteAdvSearch'),
('UserOwnAdministrating', 'UserCreate'),
('UserOwnAdministrating', 'UserDelete'),
('UserOwnAdministrating', 'UserUpdate'),
('UserOwnAdministrating', 'UserView'),
('volunteer', 'OwnProjectPartners'),
('volunteer', 'UserOwnAdministrating');

-- --------------------------------------------------------

--
-- Table structure for table `community_partner`
--

CREATE TABLE IF NOT EXISTS `community_partner` (
  `community_partner_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agency_name` varchar(45) NOT NULL,
  `pc_first_name` varchar(20) NOT NULL,
  `pc_last_name` varchar(30) NOT NULL,
  `pc_email` varchar(45) DEFAULT NULL,
  `pc_phone_number` int(11) NOT NULL,
  `pc_url` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`community_partner_oid`),
  UNIQUE KEY `community_partner_oid` (`community_partner_oid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `community_partner`
--

INSERT INTO `community_partner` (`community_partner_oid`, `agency_name`, `pc_first_name`, `pc_last_name`, `pc_email`, `pc_phone_number`, `pc_url`) VALUES
(1, 'Community Partner Alpha', 'Alvin', 'Angle', 'alvinangle@domain.com', 2147483647, 'communitypartneralpha.com'),
(2, 'Community Partner Beta', 'Billy', 'Bob', 'billybob@domain.com', 2147483647, 'communitypartnerbeta.com'),
(3, 'Community Partner Charlie', 'Charlie', 'Chow', 'charliechow@domain.com', 2147483647, 'communitypartnercharlie.com'),
(4, 'Community Partner Delta', 'David', 'Doe', 'daviddoe@domain.com', 2147483647, 'communitypartnerdelta.com'),
(5, 'Community Partner Echo', 'Edgar', 'Egolf', 'edgaregolf@domain.com', 2147483647, 'communitypartnerecho.com'),
(6, 'Friendship Community', 'Brian', 'Nejmeh', '', 2147483647, '');

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

CREATE TABLE IF NOT EXISTS `goal` (
  `goal_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_fk` bigint(20) unsigned NOT NULL,
  `goal_description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`goal_oid`),
  KEY `issue_fk` (`issue_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `goal`
--


-- --------------------------------------------------------

--
-- Table structure for table `involved`
--

CREATE TABLE IF NOT EXISTS `involved` (
  `involved_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_fk` bigint(20) unsigned NOT NULL,
  `community_partner_fk` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`involved_oid`),
  UNIQUE KEY `involved_oid` (`involved_oid`),
  KEY `involved_community_partner_fk` (`community_partner_fk`),
  KEY `involved_user_fk` (`user_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `involved`
--

INSERT INTO `involved` (`involved_oid`, `user_fk`, `community_partner_fk`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(5, 5, 5),
(11, 4, 2),
(12, 4, 1),
(13, 4, 2),
(16, 5, 1),
(17, 1, 2),
(18, 1, 1),
(19, 1, 1),
(20, 1, 1),
(21, 1, 1),
(22, 1, 2),
(23, 1, 3),
(24, 1, 4),
(25, 1, 5),
(26, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE IF NOT EXISTS `issue` (
  `issue_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_type_fk` bigint(20) unsigned NOT NULL,
  `project_fk` bigint(20) unsigned NOT NULL,
  `is_major` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`issue_oid`),
  KEY `issue_type_fk` (`issue_type_fk`),
  KEY `project_fk` (`project_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `issue`
--


-- --------------------------------------------------------

--
-- Table structure for table `issue_type`
--

CREATE TABLE IF NOT EXISTS `issue_type` (
  `issue_type_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`issue_type_oid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `issue_type`
--


-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `project_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_name` varchar(45) NOT NULL,
  `project_description` varchar(1000) DEFAULT NULL,
  `start_date` varchar(10) DEFAULT NULL,
  `end_date` varchar(10) DEFAULT NULL,
  `geographic` tinyint(4) DEFAULT NULL,
  `project_address_line_1` varchar(50) DEFAULT NULL,
  `project_address_line_2` varchar(50) DEFAULT NULL,
  `project_city` varchar(40) DEFAULT NULL,
  `project_state` char(2) DEFAULT NULL,
  `project_zip` char(5) DEFAULT NULL,
  `volunteer_lead_name` varchar(50) DEFAULT NULL,
  `volunteer_lead_email` varchar(35) DEFAULT NULL,
  `volunteer_lead_phone` varchar(20) DEFAULT NULL,
  `credit_bearing` tinyint(4) DEFAULT NULL,
  `prep_work` tinyint(4) DEFAULT NULL,
  `prep_work_help` tinyint(4) DEFAULT NULL,
  `measure_results` tinyint(4) DEFAULT NULL,
  `indoor_outdoor` tinyint(4) DEFAULT NULL,
  `contingency_description` varchar(500) DEFAULT NULL,
  `rain_description` varchar(500) DEFAULT NULL,
  `concurrent_projects` tinyint(4) DEFAULT NULL,
  `concurrent_projects_impact` varchar(400) DEFAULT NULL,
  `rmp` tinyint(4) DEFAULT NULL,
  `emergency_contact` varchar(50) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `volunteer_count` int(11) DEFAULT NULL,
  `minimum_age` int(11) DEFAULT NULL,
  `apparel` varchar(250) DEFAULT NULL,
  `food_provided` tinyint(1) DEFAULT NULL,
  `food_provider` varchar(30) DEFAULT NULL,
  `restroom` tinyint(1) DEFAULT NULL,
  `alt_restroom` varchar(100) DEFAULT NULL,
  `handicap_friendly` tinyint(1) DEFAULT NULL,
  `preregistration` varchar(250) DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `parking_instructions` varchar(50) DEFAULT NULL,
  `community_partner_fk` bigint(20) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '4',
  `user_fk` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`project_oid`),
  UNIQUE KEY `project_oid` (`project_oid`),
  KEY `project_user_fk` (`user_fk`),
  KEY `project_community_partner_fk` (`community_partner_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_oid`, `project_name`, `project_description`, `start_date`, `end_date`, `geographic`, `project_address_line_1`, `project_address_line_2`, `project_city`, `project_state`, `project_zip`, `volunteer_lead_name`, `volunteer_lead_email`, `volunteer_lead_phone`, `credit_bearing`, `prep_work`, `prep_work_help`, `measure_results`, `indoor_outdoor`, `contingency_description`, `rain_description`, `concurrent_projects`, `concurrent_projects_impact`, `rmp`, `emergency_contact`, `emergency_phone`, `volunteer_count`, `minimum_age`, `apparel`, `food_provided`, `food_provider`, `restroom`, `alt_restroom`, `handicap_friendly`, `preregistration`, `arrival_time`, `parking_instructions`, `community_partner_fk`, `status`, `user_fk`) VALUES
(0, 'Another project', 'This is a project that should have a status of 6', '', '', NULL, 'One Red Lane Dr.', '', 'Loudonville', 'NY', '12211', '', '', '', NULL, NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', '', NULL, NULL, '', NULL, '', NULL, '', NULL, '', '0000-00-00 00:00:00', '', 1, 4, 1),
(1, 'Project Alpha', 'A project for testing', '', '', NULL, 'Messiah College Box 5308', 'One College Ave.', 'Grantham', 'PA', '17027', '', '', '', 1, NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', '', NULL, NULL, '', NULL, '', NULL, '', NULL, '', '0000-00-00 00:00:00', '', 1, 3, 2),
(2, 'Project Beta', 'A project dedicated to serving Messiah College''s COSC333 class.', '', '', NULL, '12 village dr', '', 'barnegat', 'NJ', '08005', 'Douglas Coiner', 'dcoiner@domain.com', '', 1, NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', '', NULL, NULL, '', NULL, '', NULL, '', NULL, '', '0000-00-00 00:00:00', '', 3, 2, 2),
(3, 'new project', 'Chris Miller searches for project', '', '', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', '', NULL, NULL, '', NULL, '', NULL, '', NULL, '', '0000-00-00 00:00:00', '', 1, 4, 5),
(4, 'Sprint 2 Demo', '', '03/25/2010', '03/26/2010', NULL, '', '', '', '', '', '', '', '', 0, NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', '', NULL, NULL, '', NULL, '', NULL, '', NULL, '', '0000-00-00 00:00:00', '', 5, 0, 1),
(6, 'Sprint 3 Demo', '', '', '', NULL, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, '', '', NULL, '', NULL, '', '', NULL, NULL, '', NULL, '', NULL, '', NULL, '', '0000-00-00 00:00:00', '', 3, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `strategy`
--

CREATE TABLE IF NOT EXISTS `strategy` (
  `strategy_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `goal_fk` bigint(20) unsigned NOT NULL,
  `strategy_description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`strategy_oid`),
  KEY `goal_fk` (`goal_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `strategy`
--


-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `task_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `strategy_fk` bigint(20) unsigned NOT NULL,
  `task_description` varchar(500) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`task_oid`),
  KEY `strategy_fk` (`strategy_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `task`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(40) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `address_line_1` varchar(50) DEFAULT NULL,
  `address_line_2` varchar(50) DEFAULT NULL,
  `city` varchar(40) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` char(5) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `login_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_cpadmin` tinyint(1) NOT NULL DEFAULT '0',
  `is_super` tinyint(1) NOT NULL DEFAULT '0',
  `is_volunteer` tinyint(1) NOT NULL DEFAULT '0',
  `is_appadmin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_oid`),
  UNIQUE KEY `user_oid` (`user_oid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_oid`, `first_name`, `last_name`, `middle_initial`, `address_line_1`, `address_line_2`, `city`, `state`, `zip`, `phone`, `email`, `password`, `login_enabled`, `is_cpadmin`, `is_super`, `is_volunteer`, `is_appadmin`) VALUES
(1, 'Rick', 'Lima', 'M', '12 Village Dr.', NULL, 'Barnegat', 'NJ', '08005', '6097092873', 'rl1238@messiah.edu', '642b33ad72ecaa2f69f48f415fce5ff6', 1, 1, 1, 1, 1),
(2, 'Sean', 'Clark', 'A', '32 Waterway Blvd.', NULL, 'Oxford', 'CA', '29387', '1022938447', 'sc1254@messiah.edu', '642b33ad72ecaa2f69f48f415fce5ff6', 1, 1, 1, 0, 0),
(3, 'Chris', 'Miller', 'C', '933 Bridgewater St.', NULL, 'Countrytown', 'OH', '84332', '9944833327', 'cm1457@messiah.edu', '642b33ad72ecaa2f69f48f415fce5ff6', 1, 0, 1, 1, 0),
(4, 'Brian', 'Nejmeh', 'V', '5943 Marathon Lane', NULL, 'New York', 'NY', '68543', '9384569543', 'bnejmeh@messiah.edu', '642b33ad72ecaa2f69f48f415fce5ff6', 1, 1, 0, 1, 0),
(5, 'Scott', 'Weaver', 'L', '43 Network Dr.', NULL, 'Data', 'TX', '85432', '4564339677', 'sweaver@messiah.edu', '642b33ad72ecaa2f69f48f415fce5ff6', 1, 0, 0, 1, 0),
(14, 'New', 'User', '', '', '', '', '', '', '', 'newuser@domain.com', '642b33ad72ecaa2f69f48f415fce5ff6', 1, 0, 0, 1, 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `involved`
--
ALTER TABLE `involved`
  ADD CONSTRAINT `involved_community_partner_fk` FOREIGN KEY (`community_partner_fk`) REFERENCES `community_partner` (`community_partner_oid`),
  ADD CONSTRAINT `involved_user_fk` FOREIGN KEY (`user_fk`) REFERENCES `user` (`user_oid`);

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_community_partner_fk` FOREIGN KEY (`community_partner_fk`) REFERENCES `community_partner` (`community_partner_oid`),
  ADD CONSTRAINT `project_user_fk` FOREIGN KEY (`user_fk`) REFERENCES `user` (`user_oid`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
