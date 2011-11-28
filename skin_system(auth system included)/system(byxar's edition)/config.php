<?php
if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');

// Режим загрузки скинов
$login = 'on'; // 'on' - загрузка скина с предварительной авторизацией пользователя, 'off' - загрузка скина без авторизации пользователя

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

//Мыло на которое будут отправляться сообщения обратной связи
$to = "webmaster@example.com";
//Префикс темы сообщения обратной связи
$psubject = '[Сообщение через контактную форму] : ';

//Мыло от которого будет отправлено сообщение востановления пароля
$from = "do_not_reply@example.com";

$url = $_SERVER['HTTP_HOST'];
$dir = preg_replace('~\index.*$~', '', $_SERVER['REQUEST_URI']);
?>