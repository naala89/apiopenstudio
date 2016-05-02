-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2016 at 04:59 PM
-- Server version: 5.5.48-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `datagato_api`
--

--
-- Truncate table before insert `account`
--

TRUNCATE TABLE `account`;
--
-- Dumping data for table `account`
--

INSERT INTO `account` (`accid`, `uid`, `name`) VALUES
(1, 1, ''Datagator'');

--
-- Truncate table before insert `application`
--

TRUNCATE TABLE `application`;
--
-- Dumping data for table `application`
--

INSERT INTO `application` (`appid`, `accid`, `name`) VALUES
(1, 1, ''System''),
(2, 1, ''All'');

--
-- Truncate table before insert `blacklist`
--

TRUNCATE TABLE `blacklist`;
--
-- Truncate table before insert `external_user`
--

TRUNCATE TABLE `external_user`;
--
-- Truncate table before insert `log`
--

TRUNCATE TABLE `log`;
--
-- Truncate table before insert `resource`
--

TRUNCATE TABLE `resource`;
--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`id`, `appid`, `method`, `identifier`, `meta`, `ttl`) VALUES
(54, 1, ''post'', ''systemcache'', ''A"process":{"processor":"systemCache","meta":{"id":3,"operation":{"processor":"varUri","meta":{"id":4,"index":0}}}}}'', 0),
(55, 2, ''post'', ''userlogin'', ''{"process":{"processor":"UserLogin","meta":{"id":1,"username":{"processor":"VarPost","meta":{"id":2,"name":"username"}},"password":{"processor":"VarPost","meta":{"id":3,"name":"password"}}}}}'', 300),
(56, 2, ''get'', ''resourceyaml'', ''{"security":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}'', 300),
(57, 2, ''post'', ''resourceyaml'', ''{"security":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3}}}'', 300),
(58, 2, ''delete'', ''resourceyaml'', ''{"security":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}'', 300),
(59, 2, ''post'', ''userlogindrupal'', ''{"process":{"processor":"loginStoreDrupal","meta":{"id":1,"source":{"processor":"inputUrl","meta":{"id":2,"source":{"processor":"concatenate","meta":{"id":3,"sources":[{"processor":"varStore","meta":{"id":4,"name":"drupalUrl","operation":"fetch"}},"api\\/anon\\/user\\/login"]}},"method":"post","0":{"username":{"processor":"varPost","meta":{"id":5,"name":"username"}},"password":{"processor":"varPost","meta":{"id":6,"name":"password"}}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}}}}'', 0),
(60, 2, ''get'', ''processorsall'', ''{"process":{"processor":"ProcessorsAll","meta":{"id":3}},"security":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}}}'', 3600),
(61, 2, ''post'', ''resourcejson'', ''{"process":{"processor":"resourceJson","meta":{"id":3}}}'', 300),
(62, 2, ''get'', ''resourcejson'', ''{"process":{"processor":"resourceJson","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}'', 300),
(63, 2, ''delete'', ''resourcejson'', ''{"process":{"processor":"resourceJson","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}'', 300);

--
-- Truncate table before insert `role`
--

TRUNCATE TABLE `role`;
--
-- Dumping data for table `role`
--

INSERT INTO `role` (`rid`, `name`) VALUES
(1, ''sys-admin''),
(2, ''admin''),
(3, ''developer''),
(4, ''consumer'');

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;

--
-- Truncate table before insert `user_role`
--

TRUNCATE TABLE `user_role`;

--
-- Truncate table before insert `vars`
--

TRUNCATE TABLE `vars`;