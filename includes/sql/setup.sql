SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client` int(10) unsigned NOT NULL,
  `resource` varchar(64) NOT NULL,
  `meta` varchar(4096) NOT NULL,
  `ttl` int(10) unsigned NOT NULL DEFAULT '300',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

INSERT INTO `resource` (`id`, `client`, `resource`, `meta`, `ttl`) VALUES
  (1, 999, 'user/login', '{"type":"input","meta":{"url":"https:\/\/swellnet.com.au\/api\/anon\/user\/login"}}', 0);
