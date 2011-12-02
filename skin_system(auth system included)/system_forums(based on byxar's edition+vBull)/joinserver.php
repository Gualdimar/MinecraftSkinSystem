<?php
define('INCLUDE_CHECK',true);
include ("connect.php");

$sessionid = mysql_real_escape_string($_GET['sessionId']);
$user = mysql_real_escape_string($_GET['user']);
$serverid = mysql_real_escape_string($_GET['serverId']);

$result = mysql_query("Select $db_columnUser From $db_table Where $db_columnSesId='$sessionid' And $db_columnUser='$user' And $db_columnServer='$serverid'") or die ("Запрос к базе завершился ошибкой.");

if(mysql_num_rows($result) == 1){
    echo "OK";
} else {

$result = mysql_query("Update $db_table SET $db_columnServer='$serverid' Where $db_columnSesId='$sessionid' And $db_columnUser='$user'") or die ("Запрос к базе завершился ощибкой.");

    if(mysql_affected_rows() == 1){
        echo "OK";
    } else {
        echo "Bad login";
    }
}
?>