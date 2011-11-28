SET FOREIGN_KEY_CHECKS=0;
--
-- Структура таблицы `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `playername` varchar(255) NOT NULL,
  `password` char(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `registerdate` datetime DEFAULT NULL,
  `registerip` varchar(39) DEFAULT NULL,
  `lastlogindate` datetime DEFAULT NULL,
  `lastloginip` varchar(39) DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;