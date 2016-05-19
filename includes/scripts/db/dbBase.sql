SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL COMMENT 'user id',
  `rid` int(10) unsigned NOT NULL COMMENT 'role id',
  `appid` int(10) unsigned NOT NULL COMMENT 'application id'
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

TRUNCATE TABLE `user_role`;
INSERT INTO `user_role` (`id`, `uid`, `rid`, `appid`) VALUES
  (1, 1, 1, 1),
  (2, 1, 2, 1),
  (3, 1, 3, 1),
  (4, 1, 1, 3),
  (5, 1, 2, 3),
  (6, 1, 3, 3),
  (21, 9, 2, 4),
  (22, 9, 3, 4),
  (23, 2, 3, 5),
  (24, 2, 2, 5),
  (25, 3, 2, 6),
  (26, 3, 3, 6),
  (27, 4, 3, 7),
  (28, 4, 2, 7),
  (29, 5, 2, 8),
  (30, 5, 3, 8),
  (31, 6, 3, 9),
  (32, 6, 2, 9),
  (33, 7, 2, 10),
  (34, 7, 3, 10),
  (35, 8, 3, 11),
  (36, 8, 2, 11),
  (37, 1, 3, 12),
  (38, 1, 2, 12),
  (40, 1, 5, 1),
  (43, 1, 2, 2);


ALTER TABLE `user_role`
ADD PRIMARY KEY (`id`);


ALTER TABLE `user_role`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=44;