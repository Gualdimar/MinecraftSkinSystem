<?php
if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');

// Конфигурация подключения к базе данных
$db_host		= 'localhost'; // Ip-адрес базы данных
$db_port		=  3306; // Порт базы данных
$db_user		= 'root'; // Пользователь базы данных
$db_pass		= 'root'; // Пароль базы данных

// Конфигурация базы данных для плагинов AuthMe, xAuth, CAuth
/*
$db_database - имя базы данных, значение по умолчанию для плагинов:
AuthMe = authme, xAuth = отсутствует (указывается вручную), CAuth = cauth
*/
$db_database	= 'xauth';

/*
$db_table - таблица базы данных, значение по умолчанию для плагинов:
AuthMe = authme, xAuth = accounts, CAuth = users
*/
$db_table       = 'accounts';

/*
$db_columnId - уникальный идентификатор, значение по умолчанию для плагинов:
AuthMe = id, xAuth = id, CAuth = id
*/
$db_columnId  = 'id';

/*
$db_columnUser - колонка логина, значение по умолчанию для плагинов:
AuthMe = username, xAuth = playername, CAuth = login
*/
$db_columnUser  = 'playername';

/*
$db_columnPass - колонка пароля, значение по умолчанию для плагинов:
AuthMe = password, xAuth = password, CAuth = password
*/
$db_columnPass  = 'password';

/*
$db_columnEmail - колонка email'a
*/
$db_columnEmail  = 'email';

/*
$db_columnLastLogl - колонка даты последнего входа
*/
$db_columnLastLog  = 'lastlogindate';

/*
$db_columnRegDate - колонка даты регистрации
*/
$db_columnRegDate  = 'registerdate';

/*
$db_columnSesId - колонка id сессии
*/
$db_columnSesId = 'session';

/*
$db_columnServer - колонка id сервера
*/
$db_columnServer = 'server';

/*
Настройки таблицы для защиты от подбора паролей
*/
$db_ErrorLogtable = 'errorlogin';
$db_Ipcolumn = 'ip';
$db_Datecolumn = 'date';
$db_Numcolumn = 'num';

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