--
-- Структура таблицы `data`
--

DROP TABLE IF EXISTS `data`;
CREATE TABLE `data` (
  `property` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  UNIQUE KEY `property` (`property`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `data`
--

INSERT INTO `data` (`property`, `value`) VALUES
('latest-game-build', '10746'),
('launcher-version', '13');
-- --------------------------------------------------------