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
'hash_vbulletin' 	- интеграция с vBulletin
'hash_dle' 	- интеграция с DLE
*/
$crypt = 'hash_md5';

// Директории для загрузки скинов/плащей
$dir_skins = 'upload/skins/';
$dir_cloaks = 'upload/cloaks/';

// Вывод текста из файла
$text = file_get_contents( 'pages/test.txt' ); // месторасположение загружаемого файла
$text = preg_replace( '#(^.*?<body[^>]*>)|(</body>.*$)#i', '', $text ); // обрезка <body> включительно и конец, оставляем только текст (для html страниц)
$len = strlen( $text );
$pagebreak = '<!-- pagebreak -->'; // метка для разделения страниц
$this_url = $_SERVER['PHP_SELF'];
$chars_per_page = 10;

$url = $_SERVER['HTTP_HOST'];
$dir = preg_replace('~\index.*$~', '', $_SERVER['REQUEST_URI']);
?>