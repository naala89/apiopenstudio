SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `uid` int(11) NOT NULL COMMENT 'uid of the owner',
  `name` varchar(255) NOT NULL COMMENT 'name of account'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `account`;
INSERT INTO `account` (`accid`, `uid`, `name`) VALUES
  (1, 1, 'Datagator'),
  (2, 9, 'Test');

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `name` varchar(255) NOT NULL COMMENT 'application name'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `application`;
INSERT INTO `application` (`appid`, `accid`, `name`) VALUES
  (1, 1, 'Datagator'),
  (2, 1, 'All'),
  (3, 1, 'Testing');

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `resource`;
INSERT INTO `resource` (`id`, `appid`, `name`, `description`, `method`, `identifier`, `meta`, `ttl`) VALUES
  (1, 2, 'User login', 'Login a user to Datagator using username/password.', 'post', 'userlogin', '{"process":{"function":"UserLogin","id":1,"username":{"function":"VarPost","id":2,"name":"username"},"password":{"function":"VarPost","id":3,"name":"password"}}}', 0),
  (2, 2, 'YAML export', 'Fetch a resource in string or YAML format.', 'get', 'resourceyaml', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"resourceYaml","id":3,"method":{"function":"varGet","id":4,"name":"method"},"noun":{"function":"varGet","id":5,"name":"noun"},"verb":{"function":"varGet","id":6,"name":"verb"}}}', 0),
  (3, 2, 'YAML import', 'Create a resource from a document or string in YAML format.', 'post', 'resourceyaml', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"resourceYaml","id":3,"resource":"resource"}}', 0),
  (4, 2, 'User login Drupal', 'Login a user to the system, using user/pass validation on an external Drupal site', 'post', 'userlogindrupal', '{"process":{"function":"loginStoreDrupal","id":1,"source":{"function":"url","id":2,"source":{"function":"concatenate","id":3,"sources":[{"function":"varPersistent","id":4,"name":"drupalUrl","operation":"fetch"},"api\\/anon\\/user\\/login"]},"method":"post","reportError":true,"normalise":true,"vars":{"username":{"function":"varPost","id":5,"name":"username"},"password":{"function":"varPost","id":6,"name":"password"}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}}', 0),
  (5, 2, 'Processors all', 'Fetch details of all procssors.', 'get', 'processorsall', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"ProcessorsAll","id":3}}', 3600),
  (6, 2, 'JSON import', 'Create a resource from a document or string in JSON format.', 'post', 'resourcejson', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"resourceJson","id":3,"yaml":{"function":"varPost","id":4,"name":"yaml"}}}', 0),
  (7, 2, 'JSON export', 'Export a resource in string or JSON format.', 'get', 'resourcejson', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"resourceJson","id":3,"method":{"function":"varGet","id":4,"name":"method"},"noun":{"function":"varGet","id":5,"name":"noun"},"verb":{"function":"varGet","id":6,"name":"verb"}}}', 0),
  (8, 2, 'Resource delete', 'Delete a resource.', 'delete', 'resourcedelete', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"resourceYaml","id":3,"method":{"function":"varGet","id":4,"name":"method"},"noun":{"function":"varGet","id":5,"name":"noun"},"verb":{"function":"varGet","id":6,"name":"verb"}}}', 0),
  (9, 2, 'Swagger import', 'Create resource/s from a Swagger document.', 'post', 'resourceswagger', '{"security":{"function":"tokenDeveloper","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"resourceSwagger","id":3,"resource":"resource"}}', 0),
  (10, 1, 'Account create', 'Create an account.', 'post', 'accountmanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorAccount","meta":{"id":3,"username":{"processor":"varPost","meta":{"id":4,"name":"username"}},"accountName":{"processor":"varPost","meta":{"id":5,"name":"accountName"}}}}}', 0),
  (11, 1, 'Account delete', 'Delete an account.', 'delete', 'accountmanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorAccount","meta":{"id":3,"accountName":{"processor":"varGet","meta":{"id":4,"name":"accountName"}}}}}', 0),
  (12, 1, 'Account fetch', 'Fetch an account.', 'get', 'accountmanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorAccount","meta":{"id":3,"accountName":{"processor":"varGet","meta":{"id":4,"name":"accountName"}}}}}', 0),
  (13, 1, 'Application create', 'Create an application.', 'post', 'applicationmanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorApplication","meta":{"id":3,"accountName":{"processor":"varPost","meta":{"id":4,"name":"accountName"}},"applicationName":{"processor":"varPost","meta":{"id":5,"name":"applicationName"}}}}}', 0),
  (14, 1, 'Application delete', 'Delete an application.', 'delete', 'applicationmanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorApplication","meta":{"id":3,"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}}}}}', 0),
  (15, 1, 'Application fetch', 'Fetch an application.', 'get', 'applicationmanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorApplication","meta":{"id":3,"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}}}}}', 0),
  (16, 1, 'User create', 'Create a user.', 'post', 'usermanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUser","meta":{"id":3,"username":{"processor":"varPost","meta":{"id":4,"name":"username"}},"active":{"processor":"varPost","meta":{"id":5,"name":"active"}},"email":{"processor":"varPost","meta":{"id":6,"name":"email"}},"honorific":{"processor":"varPost","meta":{"id":7,"name":"honorific"}},"nameFirst":{"processor":"varPost","meta":{"id":8,"name":"nameFirst"}},"nameLast":{"processor":"varPost","meta":{"id":9,"name":"nameLast"}},"company":{"processor":"varPost","meta":{"id":10,"name":"company"}}}}}', 0),
  (17, 1, 'User fetch', 'Fetch a user.', 'get', 'usermanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUser","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}}}}}', 0),
  (18, 1, 'User delete', 'Delete a user.', 'delete', 'usermanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUser","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}}}}}', 0),
  (19, 1, 'User Role create', 'Add a role for a user for an application.', 'post', 'userrolemanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varPost","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUserRole","meta":{"id":3,"username":{"processor":"varPost","meta":{"id":4,"name":"username"}},"applicationName":{"processor":"varPost","meta":{"id":5,"name":"applicationName"}},"roleName":{"processor":"varPost","meta":{"id":6,"name":"roleName"}}}}}', 0),
  (20, 1, 'User Role delete', 'Delete a role for a user and/or application.', 'delete', 'userrolemanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUserRole","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}},"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}},"roleName":{"processor":"varPost","meta":{"id":6,"name":"roleName"}}}}}', 0),
  (21, 1, 'User Role fetch', 'Fetch a role for a user on an application.', 'get', 'userrolemanage', '{"security":{"processor":"TokenSysAdmin","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"datagatorUserRole","meta":{"id":3,"username":{"processor":"varGet","meta":{"id":4,"name":"username"}},"applicationName":{"processor":"varGet","meta":{"id":5,"name":"applicationName"}},"roleName":{"processor":"varPost","meta":{"id":6,"name":"roleName"}}}}}', 0),
  (22, 2, 'User delete', 'Delete a user.', 'delete', 'usermanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUser","id":3,"username":{"function":"varGet","id":4,"name":"username"}}}', 0),
  (23, 2, 'User fetch', 'Fetch a user.', 'get', 'usermanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUser","id":3,"username":{"function":"varGet","id":4,"name":"username"}}}', 0),
  (24, 2, 'User Role create', 'Add a role for a user for an application.', 'post', 'userrolemanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorUserRole","id":3,"username":{"function":"varPost","id":4,"name":"username"},"applicationName":{"function":"varPost","id":5,"name":"applicationName"},"roleName":{"function":"varPost","id":6,"name":"roleName"}}}', 0),
  (25, 2, 'User Role delete', 'Delete a role for a user and/or application.', 'delete', 'userrolemanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUserRole","id":3,"username":{"function":"varGet","id":4,"name":"username"},"applicationName":{"function":"varGet","id":5,"name":"applicationName"},"roleName":{"function":"varPost","id":6,"name":"roleName"}}}', 0),
  (26, 2, 'User Role fetch', 'Fetch a role for a user on an application.', 'get', 'userrolemanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorUserRole","id":3,"username":{"function":"varGet","id":4,"name":"username"},"applicationName":{"function":"varGet","id":5,"name":"applicationName"},"roleName":{"function":"varPost","id":6,"name":"roleName"}}}', 0),
  (27, 2, 'Account create', 'Create an account.', 'post', 'accountmanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorAccount","id":3,"username":{"function":"varPost","id":4,"name":"username"},"accountName":{"function":"varPost","id":5,"name":"accountName"}}}', 0),
  (28, 2, 'Account delete', 'Delete an account.', 'delete', 'accountmanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorAccount","id":3,"accountName":{"function":"varGet","id":4,"name":"accountName"}}}', 0),
  (29, 2, 'Account fetch', 'Fetch an account.', 'get', 'accountmanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorAccount","id":3,"accountName":{"function":"varGet","id":4,"name":"accountName"}}}', 0),
  (30, 2, 'Application create', 'Create an application.', 'post', 'applicationmanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorApplication","id":3,"accountName":{"function":"varPost","id":4,"name":"accountName"},"applicationName":{"function":"varPost","id":5,"name":"applicationName"}}}', 0),
  (31, 2, 'Application delete', 'Delete an application.', 'delete', 'applicationmanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorApplication","id":3,"applicationName":{"function":"varGet","id":5,"name":"applicationName"}}}', 0),
  (32, 2, 'Application fetch', 'Fetch an application.', 'get', 'applicationmanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varGet","id":2,"name":"token"}},"process":{"function":"datagatorApplication","id":3,"applicationName":{"function":"varGet","id":5,"name":"applicationName"}}}', 0),
  (33, 2, 'User create', 'Create a user.', 'post', 'usermanage', '{"security":{"function":"TokenSysAdmin","id":1,"token":{"function":"varPost","id":2,"name":"token"}},"process":{"function":"datagatorUser","id":3,"username":{"function":"varPost","id":4,"name":"username"},"active":{"function":"varPost","id":5,"name":"active"},"email":{"function":"varPost","id":6,"name":"email"},"honorific":{"function":"varPost","id":7,"name":"honorific"},"nameFirst":{"function":"varPost","id":8,"name":"nameFirst"},"nameLast":{"function":"varPost","id":9,"name":"nameLast"},"company":{"function":"varPost","id":10,"name":"company"}}}', 0);

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `name` varchar(255) NOT NULL COMMENT 'title of role'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `role`;
INSERT INTO `role` (`rid`, `name`) VALUES
  (1, 'Admin'),
  (2, 'Developer'),
  (3, 'Consumer'),
  (4, 'Owner'),
  (5, 'SysAdmin');

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

TRUNCATE TABLE `user`;
INSERT INTO `user` (`uid`, `active`, `username`, `salt`, `hash`, `token`, `token_ttl`, `email`, `honorific`, `name_first`, `name_last`, `company`, `website`, `address_street`, `address_suburb`, `address_city`, `address_state`, `address_postcode`, `phone_mobile`, `phone_work`) VALUES
  (1, 1, 'john', '…•›#¼J5¼>ôpé2', 'afb1c00e97f5b8503197cb61ffecebb5', '77203f614f9d1dadb0bbfa0444837a4b', '2016-07-31 00:26:31', 'john@naala.com.au', 'Mr', 'John', 'Avery', 'Naala Pty Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (2, 1, 'tester', 'º	K_ VÐÕÎ˜d\rù', 'ce45c312b0d34dd876bd039133510718', '9e3e5346db262b5aba9d4eb38d2646ec', '2016-07-31 05:59:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `appid` int(10) unsigned NOT NULL COMMENT 'application id'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `user_role`;
INSERT INTO `user_role` (`id`, `uid`, `rid`, `appid`) VALUES
  (1, 1, 1, 1),
  (2, 1, 2, 1),
  (3, 1, 3, 1),
  (4, 1, 4, 1),
  (5, 1, 5, 1),
  (6, 1, 2, 2),
  (7, 2, 2, 3),
  (8, 2, 3, 3);

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
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'resource id',AUTO_INCREMENT=34;
ALTER TABLE `role`
MODIFY `rid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'role id',AUTO_INCREMENT=6;
ALTER TABLE `user`
MODIFY `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user id',AUTO_INCREMENT=3;
ALTER TABLE `user_role`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
ALTER TABLE `vars`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of the var';