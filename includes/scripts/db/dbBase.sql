-- phpMyAdmin SQL Dump
-- version 4.2.9.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 16, 2016 at 01:27 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `datagator`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `uid` int(11) NOT NULL COMMENT 'uid of the owner',
  `name` varchar(255) NOT NULL COMMENT 'name of account'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `account`
--

TRUNCATE TABLE `account`;
--
-- Dumping data for table `account`
--

INSERT INTO `account` (`accid`, `uid`, `name`) VALUES
  (1, 1, 'Datagator');

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `name` varchar(255) NOT NULL COMMENT 'application name'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `application`
--

TRUNCATE TABLE `application`;
--
-- Dumping data for table `application`
--

INSERT INTO `application` (`appid`, `accid`, `name`) VALUES
  (1, 1, 'Datagator'),
  (2, 1, 'Common'),
  (3, 1, 'Testing');

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

DROP TABLE IF EXISTS `blacklist`;
CREATE TABLE IF NOT EXISTS `blacklist` (
  `id` int(10) unsigned NOT NULL,
  `appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `min_ip` varchar(32) NOT NULL,
  `max_ip` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `blacklist`
--

TRUNCATE TABLE `blacklist`;
-- --------------------------------------------------------

--
-- Table structure for table `external_user`
--

DROP TABLE IF EXISTS `external_user`;
CREATE TABLE IF NOT EXISTS `external_user` (
  `id` int(10) unsigned NOT NULL,
  `appid` int(10) unsigned NOT NULL COMMENT 'appplication id',
  `external_id` varchar(255) DEFAULT NULL COMMENT 'user id in external entity',
  `external_entity` varchar(255) NOT NULL COMMENT 'name of the external entity',
  `data_field_1` varchar(255) DEFAULT NULL,
  `data_field_2` varchar(255) DEFAULT NULL,
  `data_field_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `external_user`
--

TRUNCATE TABLE `external_user`;
-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(256) DEFAULT NULL,
  `ip` varchar(11) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `text` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `log`
--

TRUNCATE TABLE `log`;
-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL COMMENT 'resource id',
  `appid` int(10) unsigned DEFAULT NULL COMMENT 'client id',
  `name` varchar(256) NOT NULL COMMENT 'name of the resource',
  `description` varchar(2048) NOT NULL COMMENT 'description of the resource',
  `method` text NOT NULL COMMENT 'form delivery method',
  `identifier` varchar(64) NOT NULL COMMENT 'identifier of the api call',
  `meta` varchar(16384) NOT NULL COMMENT 'all of the actions taken by the call',
  `ttl` int(10) unsigned NOT NULL DEFAULT '300' COMMENT 'time to cache the results (seconds)'
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `resource`
--

TRUNCATE TABLE `resource`;
--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`id`, `appid`, `name`, `description`, `method`, `identifier`, `meta`, `ttl`) VALUES
  (1, 1, 'Account create', 'Create an account.', 'post', 'account', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorAccount","id":4,"username":{"function":"varPost","id":5,"name":"username"},"accountName":{"function":"varPost","id":7,"name":"accountName"}}}', 0),
  (2, 1, 'Account delete', 'Delete an account.', 'delete', 'account', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorAccount","id":4,"accountName":{"function":"varGet","id":5,"name":"accountName"}}}', 0),
  (3, 1, 'Account fetch', 'Fetch an account.', 'get', 'account', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorAccount","id":4,"accountName":{"function":"varGet","id":5,"name":"accountName"}}}', 0),
  (4, 1, 'Application create', 'Create an application.', 'post', 'application', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorApplication","id":4,"accountName":{"function":"varPost","id":5,"name":"accountName"},"applicationName":{"function":"varPost","id":7,"name":"applicationName"}}}', 0),
  (5, 1, 'Application delete', 'Delete an application.', 'delete', 'applicaton', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorApplication","id":4,"applicationName":{"function":"varGet","id":5,"name":"applicationName"}}}', 0),
  (6, 1, 'Application fetch', 'Fetch an application.', 'get', 'application', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorApplication","id":4,"applicationName":{"function":"varGet","id":5,"name":"applicationName"}}}', 0),
  (7, 1, 'User create', 'Create a user.', 'post', 'user', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorUser","id":3,"username":{"function":"varPost","id":4,"name":"username"},"active":{"function":"varPost","id":5,"name":"active"},"email":{"function":"varPost","id":6,"name":"email"},"honorific":{"function":"varPost","id":7,"name":"honorific"},"nameFirst":{"function":"varPost","id":8,"name":"nameFirst"},"nameLast":{"function":"varPost","id":9,"name":"nameLast"},"company":{"function":"varPost","id":10,"name":"company"}}}', 0),
  (8, 1, 'User delete', 'Delete a user.', 'delete', 'user', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUser","id":3,"username":{"function":"varGet","id":4,"name":"username"}}}', 0),
  (9, 1, 'User fetch', 'Fetch a user.', 'get', 'user', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUser","id":3,"username":{"function":"varGet","id":4,"name":"username"}}}', 0),
  (10, 1, 'User Role create', 'Add a role for a user for an application.', 'post', 'userrole', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorUserRole","id":3,"username":{"function":"varPost","id":4,"name":"username"},"applicationName":{"function":"varPost","id":5,"name":"applicationName"},"roleName":{"function":"varPost","id":6,"name":"roleName"}}}', 0),
  (11, 1, 'User Role delete', 'Delete a role for a user and/or application.', 'delete', 'userrole', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUserRole","id":3,"username":{"function":"varGet","id":4,"name":"username"},"applicationName":{"function":"varGet","id":5,"name":"applicationName"},"roleName":{"function":"varPost","id":6,"name":"roleName"}}}', 0),
  (12, 1, 'User Role fetch', 'Fetch a role for a user on an application.', 'get', 'userrole', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUserRole","id":3,"username":{"function":"varGet","id":4,"name":"username"},"applicationName":{"function":"varGet","id":5,"name":"applicationName"},"roleName":{"function":"varPost","id":6,"name":"roleName"}}}', 0),
  (13, 2, 'Processors all', 'Fetch details of all procssors.', 'get', 'processors/all', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"ProcessorsAll","id":3}}', 3600),
  (14, 2, 'Resource delete', 'Delete a resource.', 'delete', 'resource', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varRequest","id":2,"name":"token"}},"process":{"function":"resourceYaml","id":3,"method":{"function":"varRequest","id":4,"name":"method"},"uri":{"function":"varRequest","id":5,"name":"uri"}}}', 0),
  (15, 2, 'Resource export JSON', 'Export a resource in string or JSON format.', 'get', 'resource/json', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"resourceJson","id":3,"method":{"function":"varGet","id":4,"name":"method"},"noun":{"function":"varGet","id":5,"name":"noun"},"verb":{"function":"varGet","id":6,"name":"verb"}}}', 0),
  (16, 2, 'Resource export YAML', 'Fetch a resource in string or YAML format.', 'get', 'resource/yaml', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"resourceYaml","id":3,"method":{"function":"varGet","id":4,"name":"method"},"noun":{"function":"varGet","id":5,"name":"noun"},"verb":{"function":"varGet","id":6,"name":"verb"}}}', 0),
  (17, 2, 'Resource import JSON', 'Create a resource from a document or string in JSON format.', 'post', 'resource/json', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"resourceJson","id":3,"yaml":{"function":"varPost","id":4,"name":"yaml"}}}', 0),
  (18, 2, 'Resource import Swagger', 'Create resource/s from a Swagger document.', 'post', 'resource/swagger', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"resourceSwagger","id":3,"resource":"resource"}}', 0),
  (19, 2, 'Resource import YAML', 'Create a resource from a document or string in YAML format.', 'post', 'resource/yaml', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varPost","id":2,"name":{"function":"literal","id":3,"value":"token"}}},"process":{"function":"resourceYaml","id":4,"resource":{"function":"literal","id":3,"value":"resource"}}}', 0),
  (20, 2, 'User login', 'Login a user to Datagator using username/password.', 'post', 'user/login', '{"process":{"function":"UserLogin","id":1,"username":{"function":"VarPost","id":2,"name":"username"},"password":{"function":"VarPost","id":3,"name":"password"}}}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `name` varchar(255) NOT NULL COMMENT 'title of role'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `role`
--

TRUNCATE TABLE `role`;
--
-- Dumping data for table `role`
--

INSERT INTO `role` (`rid`, `name`) VALUES
  (1, 'Admin'),
  (2, 'Developer'),
  (3, 'Consumer'),
  (4, 'Owner'),
  (5, 'SysAdmin');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `username` varchar(256) NOT NULL,
  `salt` varchar(16) DEFAULT NULL COMMENT 'Salt for the password hash',
  `hash` varchar(32) DEFAULT NULL COMMENT 'pasword hash',
  `token` varchar(32) DEFAULT NULL COMMENT 'temporary access token',
  `token_ttl` timestamp NULL DEFAULT NULL COMMENT 'end of life for token',
  `email` varchar(255) DEFAULT NULL,
  `honorific` varchar(16) DEFAULT NULL COMMENT 'person''s title',
  `name_first` varchar(255) DEFAULT NULL COMMENT 'first name',
  `name_last` varchar(255) DEFAULT NULL COMMENT 'last name',
  `company` varchar(255) DEFAULT NULL COMMENT 'company name',
  `website` varchar(255) DEFAULT NULL COMMENT 'URL',
  `address_street` varchar(255) DEFAULT NULL COMMENT 'number and street name',
  `address_suburb` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `address_state` varchar(255) DEFAULT NULL,
  `address_postcode` varchar(8) DEFAULT NULL,
  `phone_mobile` int(11) DEFAULT NULL,
  `phone_work` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `active`, `username`, `salt`, `hash`, `token`, `token_ttl`, `email`, `honorific`, `name_first`, `name_last`, `company`, `website`, `address_street`, `address_suburb`, `address_city`, `address_state`, `address_postcode`, `phone_mobile`, `phone_work`) VALUES
  (1, 1, 'john', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (2, 1, 'tester', 'ÐKdó“##V™''„\Z‰“;†', 'a1d45363e9041a93fef109feb57c76f3', '2359132eec3a6545c49f011dd5dd3a41', '2016-10-14 21:05:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `appid` int(10) unsigned NOT NULL COMMENT 'application id'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `user_role`
--

TRUNCATE TABLE `user_role`;
--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `uid`, `rid`, `appid`) VALUES
  (1, 1, 1, 1),
  (2, 1, 2, 1),
  (3, 1, 3, 1),
  (4, 1, 4, 1),
  (5, 1, 5, 1),
  (6, 1, 2, 2),
  (7, 2, 2, 3),
  (8, 2, 3, 3);

-- --------------------------------------------------------

--
-- Table structure for table `vars`
--

DROP TABLE IF EXISTS `vars`;
CREATE TABLE IF NOT EXISTS `vars` (
  `id` int(11) unsigned NOT NULL COMMENT 'id of the var',
  `appid` int(11) unsigned NOT NULL COMMENT 'client id',
  `name` varchar(256) NOT NULL COMMENT 'name of the var',
  `val` varchar(256) DEFAULT NULL COMMENT 'value of the var'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `vars`
--

TRUNCATE TABLE `vars`;
--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`accid`);

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`appid`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `external_user`
--
ALTER TABLE `external_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`rid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vars`
--
ALTER TABLE `vars`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `accid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'account id',AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `appid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'application id',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `external_user`
--
ALTER TABLE `external_user`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `resource`
--
ALTER TABLE `resource`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'resource id',AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `rid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'role id',AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user id',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `vars`
--
ALTER TABLE `vars`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of the var';