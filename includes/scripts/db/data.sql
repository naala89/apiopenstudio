-- phpMyAdmin SQL Dump
-- version 4.2.9.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 21, 2016 at 06:11 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `datagator`
--

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`accid`, `uid`, `name`) VALUES
(1, 1, 'Datagator');

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`appid`, `accid`, `name`) VALUES
(1, 1, 'System'),
(2, 1, 'All');

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`appid`, `method`, `identifier`, `meta`, `ttl`) VALUES
(1, 'post', 'systemcache', 'A"process":{"processor":"systemCache","meta":{"id":3,"operation":{"processor":"varUri","meta":{"id":4,"index":0}}}}}', 0),
(2, 'post', 'userlogin', '{"process":{"processor":"UserLogin","meta":{"id":1,"username":{"processor":"VarPost","meta":{"id":2,"name":"username"}},"password":{"processor":"VarPost","meta":{"id":3,"name":"password"}}}}}', 300),
(2, 'get', 'resourceyaml', '{"validation":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 300),
(2, 'post', 'resourceyaml', '{"validation":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3}}}', 300),
(2, 'delete', 'resourceyaml', '{"validation":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceYaml","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 300),
(2, 'post', 'userlogindrupal', '{"process":{"processor":"loginStoreDrupal","meta":{"id":1,"source":{"processor":"inputUrl","meta":{"id":2,"source":{"processor":"concatenate","meta":{"id":3,"sources":[{"processor":"varStore","meta":{"id":4,"name":"drupalUrl","operation":"fetch"}},"api\\/anon\\/user\\/login"]}},"method":"post","0":{"username":{"processor":"varPost","meta":{"id":5,"name":"username"}},"password":{"processor":"varPost","meta":{"id":6,"name":"password"}}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}}}}', 0),
(2, 'get', 'processorsall', '{"process":{"processor":"ProcessorsAll","meta":{"id":3}},"validation":{"processor":"TokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}}}', 3600),
(2, 'post', 'resourcejson', '{"process":{"processor":"resourceJson","meta":{"id":3}}}', 300),
(2, 'get', 'resourcejson', '{"process":{"processor":"resourceJson","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 300),
(2, 'delete', 'resourcejson', '{"process":{"processor":"resourceJson","meta":{"id":3,"method":{"processor":"varGet","meta":{"id":4,"name":"method"}},"noun":{"processor":"varGet","meta":{"id":5,"name":"noun"}},"verb":{"processor":"varGet","meta":{"id":6,"name":"verb"}}}}}', 300);

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`rid`, `name`) VALUES
(1, 'sys-admin'),
(2, 'admin'),
(3, 'developer'),
(4, 'consumer');


