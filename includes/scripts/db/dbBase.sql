SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
`accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `name` varchar(255) NOT NULL COMMENT 'name of account'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `account`;
INSERT INTO `account` (`accid`, `uid`, `name`) VALUES
(1, 10, 'Datagator'),
(2, 10, 'Test');

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
`appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `name` varchar(255) NOT NULL COMMENT 'application name'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `application`;
INSERT INTO `application` (`appid`, `accid`, `name`) VALUES
(1, 1, 'System'),
(2, 1, 'All'),
(3, 2, 'Alpha_test');

DROP TABLE IF EXISTS `blacklist`;
CREATE TABLE IF NOT EXISTS `blacklist` (
`id` int(10) unsigned NOT NULL,
  `appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `min_ip` varchar(32) NOT NULL,
  `max_ip` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

TRUNCATE TABLE `blacklist`;
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

TRUNCATE TABLE `external_user`;
DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
`id` int(10) unsigned NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(256) DEFAULT NULL,
  `ip` varchar(11) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `text` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

TRUNCATE TABLE `log`;
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `resource`;
INSERT INTO `resource` (`id`, `appid`, `name`, `description`, `method`, `identifier`, `meta`, `ttl`) VALUES
(1, 2, 'User login', 'Login a user to Datagator using username/password.', 'post', 'userlogin', '{"process":{"processor":"UserLogin","meta":{"id":1,"username":{"processor":"VarPost","meta":{"id":2,"name":"username"}},"password":{"processor":"VarPost","meta":{"id":3,"name":"password"}}}}}', 0),
(2, 2, 'YAML export', 'Fetch a resource in string or YAML format.', 'get', 'resourceyaml', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 0),
(3, 2, 'YAML import', 'Create a resource from a document or string in YAML format.', 'post', 'resourceyaml', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"resource":"resource"}}}', 0),
(4, 2, 'User login Drupal', 'Login a user to the system, using user/pass validation on an external Drupal site', 'post', 'userlogindrupal', '{"process":{"processor":"loginStoreDrupal","meta":{"id":1,"source":{"processor":"inputUrl","meta":{"id":2,"source":{"processor":"concatenate","meta":{"id":3,"sources":[{"processor":"varStore","meta":{"id":4,"name":"drupalUrl","operation":"fetch"}},"api\\/anon\\/user\\/login"]}},"method":"post","vars":{"username":{"processor":"varPost","meta":{"id":5,"name":"username"}},"password":{"processor":"varPost","meta":{"id":6,"name":"password"}}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}}}}', 0),
(5, 2, 'Processors all', 'Fetch details of all procssors.', 'get', 'processorsall', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"ProcessorsAll","meta":{"id":3}}}', 3600),
(6, 2, 'JSON import', 'Create a resource from a document or string in JSON format.', 'post', 'resourcejson', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceJson","meta":{"id":3,"yaml":{"processor":"varGet","meta":{"id":4,"name":"yaml"}}}}}', 0),
(7, 2, 'JSON export', 'Export a resource in string or JSON format.', 'get', 'resourcejson', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceJson","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 0),
(8, 2, 'Resource delete', 'Delete a resource.', 'delete', 'resourcedelete', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 0),
(9, 2, 'Swagger import', 'Create resource/s from a Swagger document.', 'post', 'resourceswagger', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceSwagger","meta":{"id":3,"resource":"resource"}}}', 0),
(10, 1, 'Account create', 'Create an account.', 'post', 'accountmanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorAccount","meta":{"id":3,"username":{"processor":"varPost","meta":{"id":4,"name":"username"}},"accountName":{"processor":"varPost","meta":{"id":5,"name":"accountName"}}}}}', 0),
(11, 1, 'Account delete', 'Delete an account.', 'delete', 'accountmanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorAccount","meta":{"id":3,"accountName":{"processor":"varGet","meta":{"id":4,"name":"accountName"}}}}}', 0),
(12, 1, 'Account fetch', 'Fetch an account.', 'get', 'accountmanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorAccount","meta":{"id":3,"accountName":{"processor":"varGet","meta":{"id":4,"name":"accountName"}}}}}', 0),
(13, 1, 'Application create', 'Create an application.', 'post', 'applicationmanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorApplication","meta":{"id":3,"accountName":{"processor":"varPost","meta":{"id":4,"name":"accountName"}},"applicationName":{"processor":"varPost","meta":{"id":5,"name":"applicationName"}}}}}', 0),
(14, 1, 'Application delete', 'Delete an application.', 'delete', 'applicationmanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorApplication","meta":{"id":3,"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}}}}}', 0),
(15, 1, 'Application fetch', 'Fetch an application.', 'get', 'applicationmanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorApplication","meta":{"id":3,"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}}}}}', 0),
(16, 1, 'User create', 'Create a user.', 'post', 'usermanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUser","meta":{"id":3,"username":{"processor":"varPost","meta":{"id":4,"name":"username"}},"active":{"processor":"varPost","meta":{"id":5,"name":"active"}},"email":{"processor":"varPost","meta":{"id":6,"name":"email"}},"honorific":{"processor":"varPost","meta":{"id":7,"name":"honorific"}},"nameFirst":{"processor":"varPost","meta":{"id":8,"name":"nameFirst"}},"nameLast":{"processor":"varPost","meta":{"id":9,"name":"nameLast"}},"company":{"processor":"varPost","meta":{"id":10,"name":"company"}}}}}', 0),
(17, 1, 'User fetch', 'Fetch a user.', 'get', 'usermanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUser","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}}}}}', 0),
(18, 1, 'User delete', 'Delete a user.', 'delete', 'usermanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUser","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}}}}}', 0),
(86, 1, 'User Role create', 'Add a role for a user for an application.', 'post', 'userrolemanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUserRole","meta":{"id":3,"username":{"processor":"varPost","meta":{"id":4,"name":"username"}},"applicationName":{"processor":"varPost","meta":{"id":5,"name":"applicationName"}},"roleName":{"processor":"varPost","meta":{"id":6,"name":"roleName"}}}}}', 0),
(87, 1, 'User Role delete', 'Delete a role for a user on an application.', 'get', 'userrolemanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUserRole","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}},"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}},"roleName":{"processor":"varPost","meta":{"id":6,"name":"roleName"}}}}}', 0),
(88, 1, 'User Role fetch', 'Fetch a role for a user and/or application.', 'delete', 'userrolemanage', '{"security":{"processor":"tokenAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUserRole","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}},"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}},"roleName":{"processor":"varPost","meta":{"id":6,"name":"roleName"}}}}}', 0);

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
`rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `name` varchar(255) NOT NULL COMMENT 'title of role'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `role`;
INSERT INTO `role` (`rid`, `name`) VALUES
(1, 'Admin'),
(2, 'Developer'),
(3, 'Consumer');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `user`;
INSERT INTO `user` (`uid`, `active`, `username`, `salt`, `hash`, `token`, `token_ttl`, `email`, `honorific`, `name_first`, `name_last`, `company`, `website`, `address_street`, `address_suburb`, `address_city`, `address_state`, `address_postcode`, `phone_mobile`, `phone_work`) VALUES
(1, 1, 'john', '…•›#¼J5¼>ôpé2', 'afb1c00e97f5b8503197cb61ffecebb5', 'da6852e62c404efa567027449fcd0937', '2016-05-13 08:12:45', 'john@naala.com.au', 'Mr', 'John', 'Avery', 'Naala Pty Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'leon', NULL, NULL, NULL, NULL, 'leon@naala.com.au', 'Mr', 'Leon', 'Kelly', 'IAG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'nathan', NULL, NULL, NULL, NULL, '', 'Mr', 'Nathan', '', 'IAG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 'sverre', NULL, NULL, NULL, NULL, '', 'Mr', 'Sverre', 'Kvaala', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 'frank', NULL, NULL, NULL, NULL, '', 'Mr', 'Frank', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 1, 'dave', NULL, NULL, NULL, NULL, '', 'Mr', 'Dave', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 'salman', NULL, NULL, NULL, NULL, '', 'Mr', 'Salman', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 1, 'mike', NULL, NULL, NULL, NULL, '', 'Mr', 'Mike', 'G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
`id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `appid` int(10) unsigned NOT NULL COMMENT 'application id'
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `user_role`;
INSERT INTO `user_role` (`id`, `uid`, `rid`, `appid`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 1, 3),
(5, 1, 2, 3),
(6, 1, 3, 3),
(7, 2, 2, 3),
(8, 2, 3, 3),
(9, 3, 3, 3),
(10, 3, 2, 3),
(11, 4, 2, 3),
(12, 4, 3, 3),
(13, 5, 3, 3),
(14, 5, 2, 3),
(15, 6, 2, 3),
(16, 6, 3, 3),
(17, 7, 3, 3),
(18, 7, 2, 3),
(19, 8, 2, 3),
(20, 8, 3, 3);

DROP TABLE IF EXISTS `vars`;
CREATE TABLE IF NOT EXISTS `vars` (
`id` int(11) unsigned NOT NULL COMMENT 'id of the var',
  `appid` int(11) unsigned NOT NULL COMMENT 'client id',
  `name` varchar(256) NOT NULL COMMENT 'name of the var',
  `val` varchar(256) DEFAULT NULL COMMENT 'value of the var'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

TRUNCATE TABLE `vars`;

ALTER TABLE `account`
 ADD PRIMARY KEY (`accid`);

ALTER TABLE `application`
 ADD PRIMARY KEY (`appid`);

ALTER TABLE `blacklist`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `external_user`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `log`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `resource`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `role`
 ADD PRIMARY KEY (`rid`);

ALTER TABLE `user`
 ADD PRIMARY KEY (`uid`);

ALTER TABLE `user_role`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `vars`
 ADD PRIMARY KEY (`id`);


ALTER TABLE `account`
MODIFY `accid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'account id',AUTO_INCREMENT=3;
ALTER TABLE `application`
MODIFY `appid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'application id',AUTO_INCREMENT=4;
ALTER TABLE `blacklist`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `external_user`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `log`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `resource`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'resource id',AUTO_INCREMENT=89;
ALTER TABLE `role`
MODIFY `rid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'role id',AUTO_INCREMENT=4;
ALTER TABLE `user`
MODIFY `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user id',AUTO_INCREMENT=9;
ALTER TABLE `user_role`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
ALTER TABLE `vars`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of the var';