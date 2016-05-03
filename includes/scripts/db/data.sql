SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

TRUNCATE TABLE `account`;
INSERT INTO `account` (`accid`, `uid`, `name`) VALUES
(1, 1, 'Datagator'),
(2, 10, 'Test');

TRUNCATE TABLE `application`;
INSERT INTO `application` (`appid`, `accid`, `name`) VALUES
(1, 1, 'System'),
(2, 1, 'All'),
(3, 2, 'Beta test');

TRUNCATE TABLE `blacklist`;
TRUNCATE TABLE `external_user`;
TRUNCATE TABLE `log`;
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
(9, 2, 'Swagger import', 'Create resource/s from a Swagger document.', 'post', 'resourceswagger', '{"security":{"processor":"tokenDeveloper","meta":{"id":1,"token":{"processor":"varGet","meta":{"id":2,"name":"token"}}}},"process":{"processor":"resourceSwagger","meta":{"id":3,"resource":"resource"}}}', 0);

TRUNCATE TABLE `role`;
INSERT INTO `role` (`rid`, `name`) VALUES
(1, 'sys-admin'),
(2, 'admin'),
(3, 'developer'),
(4, 'consumer');

TRUNCATE TABLE `user`;
INSERT INTO `user` (`uid`, `active`, `username`, `salt`, `hash`, `token`, `token_ttl`, `email`, `honorific`, `name_first`, `name_last`, `company`, `website`, `address_street`, `address_suburb`, `address_city`, `address_state`, `address_postcode`, `phone_mobile`, `phone_work`) VALUES
(1, 1, 'john', '…•›#¼J5¼>ôpé2', 'afb1c00e97f5b8503197cb61ffecebb5', '9cc402d502aeefbf516f1e3d510bd324', '2016-05-03 23:24:22', 'john@naala.com.au', 'Mr', 'John', 'Avery', 'Naala Pty Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, 'leon', NULL, NULL, NULL, NULL, 'leon@naala.com.au', 'Mr', 'Leon', 'Kelly', 'IAG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 'nathan', NULL, NULL, NULL, NULL, '', 'Mr', 'Nathan', '', 'IAG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 1, 'sverre', NULL, NULL, NULL, NULL, '', 'Mr', 'Sverre', 'Kvaala', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 1, 'frank', NULL, NULL, NULL, NULL, '', 'Mr', 'Frank', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 1, 'dave', NULL, NULL, NULL, NULL, '', 'Mr', 'Dave', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 1, 'salman', NULL, NULL, NULL, NULL, '', 'Mr', 'Salman', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 1, 'mike', NULL, NULL, NULL, NULL, '', 'Mr', 'Mike', 'G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

TRUNCATE TABLE `user_role`;
INSERT INTO `user_role` (`id`, `uid`, `rid`, `appid`) VALUES
(1, 1, 3, 2),
(2, 1, 4, 2),
(3, 1, 2, 4),
(4, 1, 3, 4),
(5, 1, 4, 4),
(6, 2, 3, 4),
(7, 2, 4, 4),
(8, 3, 3, 4),
(9, 3, 4, 4),
(10, 4, 3, 4),
(11, 4, 4, 4),
(13, 5, 3, 4),
(14, 5, 4, 4),
(15, 6, 3, 4),
(16, 6, 4, 4),
(17, 7, 3, 4),
(18, 7, 4, 4),
(19, 8, 3, 4),
(20, 8, 4, 4);

TRUNCATE TABLE `vars`;