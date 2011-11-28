<?php
if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');

// Конфигурация подключения к базе данных
$db_host		= 'localhost'; // Ip-адрес базы данных
$db_port		=  '3306'; // Порт базы данных
$db_user		= 'root'; // Пользователь базы данных
$db_pass		= 'root'; // Пароль базы данных

/*
$db_database - имя базы данных с аккаунтами
*/
$db_database	= 'auth';

/*
$db_table - таблица базы данных
*/
$db_table       = 'accounts';

/*
$db_columnUser - колонка логина, значение по умолчанию для плагинов:
AuthMe = username, xAuth = playername, CAuth = login
*/
$db_columnUser  = 'username';

/*
$db_columnPass - колонка пароля
*/
$db_columnPass  = 'password';

/*
$db_columnSesId - колонка id сессии
*/
$db_columnSesId = 'session';

/*
$db_columnServer - колонка id сервера
*/
$db_columnServer = 'server';

/*
$db_GameDatatable - имя базы данных с информацией о версиях
*/
$db_GameDatatable = 'data';

/*
НЕ МЕНЯТЬ
*/
$db_Propertycolumn = 'property';
$db_Valuecolumn = 'value';


$link = @mysql_connect($db_host.':'.$db_port,$db_user,$db_pass) or die('Невозможно установить соединение с базой данных!');

mysql_select_db($db_database,$link);
mysql_query("SET names UTF8");
?>