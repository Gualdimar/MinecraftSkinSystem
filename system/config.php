<?php
if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');

// Метод хеширования пароля для интеграции с различними плагинами/сайтами/cms/форумами
/*
'hash_md5' 			- md5 хеширование
'hash_authme'   	- интеграция с плагином AuthMe
'hash_cauth' 		- интеграция с плагином Cauth
'hash_xauth' 		- интеграция с плагином xAuth
'hash_joomla' 		- интеграция с Joomla (v1.6- v1.7)
'hash_ipb' 			- интеграция с IPB
'hash_xenforo' 		- интеграция с XenForo
'hash_wordpress' 	- интеграция с WordPress
*/
$crypt = 'hash_md5';

// Режим загрузки скинов
$login = 'on'; // 'on' - загрузка скина с предварительной авторизацией пользователя, 'off' - загрузка скина без авторизации пользователя


// Директории для загрузки скинов/плащей
$dir_skins = 'upload/skins/';
$dir_cloaks = 'upload/cloaks/';

// Переключение режима просмотра загруженного скина
$skinpreview = '3d'; // '3d' - режим просмотра скина в 3d (java), '2d' - режим просмотра скина в 2d (php)

// Настройки мониторинга
$version = '1.8.1'; // версия сервера
$server = '127.0.0.1'; // ip сервера
$port = 25565; // порт сервера
$portmc = 25566; // порт плагина мониторинга Minequery (http://dev.bukkit.org/server-mods/minequery/)

// Вывод текста из файла
$text = file_get_contents( 'pages/test.txt' ); // месторасположение загружаемого файла
$text = preg_replace( '#(^.*?<body[^>]*>)|(</body>.*$)#i', '', $text ); // обрезка <body> включительно и конец, оставляем только текст (для html страниц)
$len = strlen( $text );
$pagebreak = '<!-- pagebreak -->'; // метка для разделения страниц
$this_url = $_SERVER['PHP_SELF'];
$chars_per_page = 10;

// Не изменять
$url = $_SERVER['HTTP_HOST'];
$dir = preg_replace('~\index.*$~', '', $_SERVER['REQUEST_URI']);
?>