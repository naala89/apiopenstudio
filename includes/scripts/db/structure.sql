SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `name` varchar(255) NOT NULL COMMENT 'name of account'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `accid` int(10) unsigned NOT NULL COMMENT 'account id',
  `name` varchar(255) NOT NULL COMMENT 'application name'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `blacklist`;
CREATE TABLE IF NOT EXISTS `blacklist` (
  `id` int(10) unsigned NOT NULL,
  `appid` int(10) unsigned NOT NULL COMMENT 'application id',
  `min_ip` varchar(32) NOT NULL,
  `max_ip` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(256) DEFAULT NULL,
  `ip` varchar(11) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `text` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `name` varchar(255) NOT NULL COMMENT 'title of role'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `username` varchar(256) NOT NULL,
  `salt` varchar(16) DEFAULT NULL COMMENT 'Salt for the password hash',
  `hash` varchar(32) DEFAULT NULL COMMENT 'pasword hash',
  `token` varchar(32) DEFAULT NULL COMMENT 'temporary access token',
  `token_ttl` timestamp NULL DEFAULT NULL COMMENT 'end of life for token',
  `email` varchar(255) NOT NULL,
  `honorific` varchar(16) NOT NULL COMMENT 'person''s title',
  `name_first` varchar(255) NOT NULL COMMENT 'first name',
  `name_last` varchar(255) NOT NULL COMMENT 'last name',
  `company` varchar(255) DEFAULT NULL COMMENT 'company name',
  `website` varchar(255) DEFAULT NULL COMMENT 'URL',
  `address_street` varchar(255) DEFAULT NULL COMMENT 'number and street name',
  `address_suburb` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `address_state` varchar(255) DEFAULT NULL,
  `address_postcode` varchar(8) DEFAULT NULL,
  `phone_mobile` int(11) DEFAULT NULL,
  `phone_work` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `appid` int(10) unsigned NOT NULL COMMENT 'application id'
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `vars`;
CREATE TABLE IF NOT EXISTS `vars` (
  `id` int(11) unsigned NOT NULL COMMENT 'id of the var',
  `appid` int(11) unsigned NOT NULL COMMENT 'client id',
  `name` varchar(256) NOT NULL COMMENT 'name of the var',
  `val` varchar(256) DEFAULT NULL COMMENT 'value of the var'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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
MODIFY `accid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'account id',AUTO_INCREMENT=4;
ALTER TABLE `application`
MODIFY `appid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'application id',AUTO_INCREMENT=5;
ALTER TABLE `blacklist`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `external_user`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `log`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `resource`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'resource id',AUTO_INCREMENT=77;
ALTER TABLE `role`
MODIFY `rid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'role id',AUTO_INCREMENT=5;
ALTER TABLE `user`
MODIFY `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user id',AUTO_INCREMENT=12;
ALTER TABLE `user_role`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
ALTER TABLE `vars`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of the var';