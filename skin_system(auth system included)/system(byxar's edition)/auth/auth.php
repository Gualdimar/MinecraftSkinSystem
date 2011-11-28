<?php
define('INCLUDE_CHECK',true);
require_once '../functions.php';
include ("../connect.php");
$login=$_POST['user'];
$postPass=$_POST['password'];
$ver=$_POST['version'];

		if(getGameInfo('launcher') == $ver){

			$result = mysql_query("SELECT $db_columnPass FROM $db_table WHERE $db_columnUser='$login'") or die ("Запрос к базе завершился ошибкой."); //извлекаем из базы все данные о пользователе с введенным логином
    			$myrow = mysql_fetch_array($result);

			$realPass = $myrow[$db_columnPass];

    			if (checkPass($realPass,$postPass)) 
				{
					$sessid = generateSessionId();
					$gamebuild=getGameInfo('build');

					mysql_query("UPDATE $db_table SET $db_columnSesId='$sessid' WHERE $db_columnUser = '$login'") or die ("Запрос к базе завершился ошибкой.");

					$dlticket = md5($login);
					echo $gamebuild.':'.$dlticket.':'.$login.':'.$sessid.':';
				}
			else {
				echo "Bad login";
				}
		}
		else{
			echo 'Old version';
			}
?>