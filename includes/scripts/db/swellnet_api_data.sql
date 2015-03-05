-- phpMyAdmin SQL Dump
-- version 4.2.9.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 21, 2015 at 06:44 AM
-- Server version: 5.6.21
-- PHP Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `swellnet_api`
--

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`username`) VALUES
('notadmin');

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `client`, `resource`, `meta`, `ttl`) VALUES
(1, 999, 'userlogin', '{"type":"loginStoreDrupal","meta":{"id":1,"source":{"type":"inputUrl","meta":{"id":2,"source":{"type":"concatenate","meta":{"id":3,"sources":[{"type":"varStore","meta":{"id":4,"var":"drupalUrl","operation":"fetch"}},"api\\/anon\\/user\\/login"]}},"method":"post","vars":{"name":{"type":"varPost","meta":{"id":5,"var":"username"}},"pass":{"type":"varPost","meta":{"id":6,"var":"password"}}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}}}', 0),
(2, 999, 'locationcountries', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":1,"standardError":true,"source":{"type":"concatenate","meta":{"id":3,"sources":[{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/location\\/country\\/all"]}},"method":"get","curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 86400),
(3, 999, 'locationregions', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":3,"sources":[{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/location\\/region\\/",{"type":"varUri","meta":{"id":5,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 86400),
(4, 999, 'locationlocations', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/location\\/locations\\/",{"type":"varUri","meta":{"id":6,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 86400),
(5, 999, 'locationforecast', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetForecast","meta":{"id":3,"weatherzoneUrl":{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"weatherzoneUrl"}},"weatherzoneLogin":{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"weatherzoneLogin"}},"weatherzonePass":{"type":"varStore","meta":{"id":6,"operation":"fetch","var":"weatherzonePass"}},"waveappUrl":{"type":"varStore","meta":{"id":7,"operation":"fetch","var":"waveappUrl"}},"waveappLogin":{"type":"varStore","meta":{"id":8,"operation":"fetch","var":"waveappLogin"}},"waveappPass":{"type":"varStore","meta":{"id":9,"operation":"fetch","var":"waveappPass"}},"drupalUrl":{"type":"concatenate","meta":{"id":10,"sources":[{"type":"varStore","meta":{"id":11,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/surfreport\\/"]}},"token":{"type":"varGet","meta":{"id":12,"var":"token"}},"location":{"type":"varUri","meta":{"id":13,"index":0}},"lat":{"type":"varGet","meta":{"id":14,"var":"lat"}},"lon":{"type":"varGet","meta":{"id":15,"var":"lon"}},"weatherStation":{"type":"varGet","meta":{"id":16,"var":"weatherStation"}},"tideStation":{"type":"varGet","meta":{"id":17,"var":"tideStation"}}}}', 3600),
(6, 999, 'articleslatest', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/article\\/latest\\/",{"type":"varUri","meta":{"id":6,"index":0}},"\\/",{"type":"varUri","meta":{"id":7,"index":1}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 300),
(7, 999, 'locationwams', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/wams\\/",{"type":"varUri","meta":{"id":6,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 3600),
(8, 999, 'swellnetsurfcam', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/surfcam\\/",{"type":"varUri","meta":{"id":6,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 86400),
(9, 999, 'swellnetimage', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"inputUrl","meta":{"id":3,"standardError":true,"method":"get","source":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/image\\/",{"type":"varUri","meta":{"id":6,"index":0}}]}},"curlOpts":{"CURLOPT_SSL_VERIFYPEER":0,"CURLOPT_FOLLOWLOCATION":1}}}', 600),
(10, 7, 'systemcache', '{"type":"systemCache","meta":{"id":1,"operation":{"type":"varUri","meta":{"id":2,"index":"0"}}}}', 0),
(11, 999, 'validatetoken', '{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}}', 0),
(12, 999, 'locationreport', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetReport","meta":{"id":3,"url":{"type":"concatenate","meta":{"id":4,"sources":[{"type":"varStore","meta":{"id":5,"operation":"fetch","var":"drupalUrl"}},"api\\/anon\\/surfreport\\/"]}}}}', 300),
(13, 999, 'appversion', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"varStore","meta":{"id":3,"operation":"fetch","var":"version"}}', 300),
(14, 999, 'imagelatest', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetImage","meta":{"id":3,"path":{"type":"varStore","meta":{"id":4,"operation":"fetch","var":"dropbox"}}}}', 300),
(15, 1, 'processorsfetch', '{"type":"processors","meta":{"id":1}}', 300),
(16, 999, 'swellnetanontoken', '{"type":"swellnetAnonToken","meta":{"id":1}}', 300),
(17, 999, 'swellnetwamvideo', '{"validation":{"type":"validateToken","meta":{"id":1,"token":{"type":"varGet","meta":{"id":2,"var":"token"}}}},"type":"swellnetWamVideo","meta":{"id":3,"drupalUrl":{"type":"varStore","meta":{"id":4,"var":"drupalUrl","operation":"fetch"}},"apiUrl":{"type":"varStore","meta":{"id":5,"var":"apiUrl","operation":"fetch"}},"uriPattern":{"type":"varStr","meta":{"id":6,"var":"api\\/anon\\/wams\\/%locationId%"}},"locationId":{"type":"varUri","meta":{"id":7,"index":0}},"videoType":{"type":"varUri","meta":{"id":8,"index":1}}}}', 300);

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`rid`, `client`, `role`) VALUES
(1, 999, 'authenticated user'),
(2, 999, 'administrator'),
(3, 999, 'super_admin');

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `client`, `external_id`, `token`, `session_name`, `session_id`, `stale_time`) VALUES
(30, 7, '', '0w968zidwGQR4waCqMXFhd8NY5-OyuIl69_y', NULL, NULL, NULL),
(37, 999, NULL, 'pGqr2RmNTpRkVa3tVlDJvtD28B8i3KQji7BbPOOA3xKQyFak', NULL, NULL, '2015-02-07 05:06:27'),
(45, 1, NULL, 'nH8yOD_NS6uVurXFWmOC4JWF1e84BfkHnkHZcgsJdchaSSDDGGHdcHRTHnadzfkv', NULL, NULL, NULL),
(89, 999, '1', 'xIczYIqSSd4bKhiR3X6BhFuXYMv5p8IZz1ne44aZ-Gk', 'SESSb19f4b5e02912917f550b6f50ce28ed3', 'VVDl-WTXIHlRcmyWPyu9SjcBsTIrTR_T-N1imOErp2I', '2015-02-22 17:30:44');

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `uid`, `rid`) VALUES
(100, 64, 1),
(101, 64, 2),
(103, 64, 3),
(175, 89, 1),
(176, 89, 2),
(177, 89, 3);

--
-- Dumping data for table `vars`
--

INSERT INTO `vars` (`id`, `client`, `name`, `val`) VALUES
(1, 999, 'waveappLogin', 'n3t'),
(2, 999, 'waveappPass', 'sw3ll'),
(3, 999, 'weatherzoneLogin', '12105-1574'),
(4, 999, 'weatherzonePass', 'rmcAw3y2'),
(5, 999, 'weatherzoneUrl', 'http://ws1.theweather.com.au/'),
(6, 999, 'waveappUrl', 'http://waveapp.swellnet.com/'),
(7, 999, 'drupalUrl', 'http://swellnetdev.prod.acquia-sites.com/'),
(8, 999, 'version', '1.0'),
(11, 999, 'dropbox', '/Library/WebServer/www/swellnet_api/html/images/wotd/'),
(12, 999, 'apiUrl', 'http://swellnet_api.local/');
