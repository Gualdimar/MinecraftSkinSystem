<?php
if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');

// Конфигурация подключения к базе данных
$db_host		= 'localhost'; // Ip-адрес базы данных
$db_port		=  3306; // Порт базы данных
$db_user		= 'test'; // Пользователь базы данных
$db_pass		= 'test'; // Пароль базы данных

// Конфигурация базы данных для плагинов AuthMe, xAuth, CAuth и сайтав/cms/форумов Joomla, IPB, XenForo, WordPress
/*
$db_database - имя базы данных, значение по умолчанию:
AuthMe = 'authme'
xAuth = отсутствует (указывается вручную)
CAuth = 'cauth'
Joomla,IPB,XenForo,WordPress,vBulletin,DLE - отсутствует (указывается вручную)
*/
$db_database	= '_xf';

/*
$db_table - таблица базы данных, значение по умолчанию:
AuthMe = 'authme'
xAuth = 'accounts'
CAuth = 'users'
Joomla = 'префикс_users' - пример 'y3wbm_users', где "y3wbm_" - префикс. Примечание префикс может отсутствовать - пример 'users'
IPB = 'members'
XenForo = 'префикс_user' - пример 'xf_user', где "xf_" - префикс. Примечание префикс может отсутствовать - пример 'user'
WordPress = 'префикс_users' - пример 'wp_users', где "wp_" - префикс. Примечание префикс может отсутствовать - пример 'users'
vBulletin = 'префикс_user' - пример 'bb_user', где "bb_" - префикс. Примечание префикс может отсутствовать - пример 'user'
DLE = 'префикс_user' - пример 'dle_users', где "dle_" - префикс. Примечание префикс может отсутствовать - пример 'users'
*/
$db_table       = 'xf_user';

/*
$db_columnId - уникальный идентификатор, значение по умолчанию
AuthMe = 'id'
xAuth = 'id'
CAuth = 'id'
Joomla = 'id'
IPB = 'member_id'
XenForo = 'user_id'
WordPress = 'id'
vBulletin = 'userid'
DLE = 'user_id'
*/
$db_columnId  = 'user_id';

/*
$db_columnUser - колонка логина, значение по умолчанию:
AuthMe = 'username'
xAuth = 'playername'
CAuth = 'login'
Joomla = 'name'
PB = 'name'
XenForo = 'username'
WordPress = 'user_login'
vBulletin = 'username'
DLE = 'name'
*/
$db_columnUser  = 'username';

/*
$db_columnPass - колонка пароля, значение по умолчанию:
AuthMe = 'password'
xAuth = 'password'
CAuth = 'password'
Joomla = 'password'
IPB = 'members_pass_hash'
XenForo = 'data'
WordPress = 'user_pass'
vBulletin = 'password'
DLE = 'password'
*/
$db_columnPass  = 'data';

// ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ ТОЛЬКО ДЛЯ IPB и XenForo

// Настраивается только для XenForo 'префикс_user_authenticate' - пример 'xf_user_authenticate', где "xf_" - префикс. Примечание префикс может отсутствовать - пример 'user_authenticate'
$db_tableOther = 'xf_user_authenticate';

// Настраивается для IPB и vBulletin
// IPB - members_pass_salt
//vBulletin - salt
$db_columnOther = 'members_pass_salt';

// НАСТРОЙКИ АВТОРИЗАЦИИ ЧЕРЕЗ ЛАУНЧЕР

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