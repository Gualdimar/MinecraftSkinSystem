<?php
define('INCLUDE_CHECK',true);
require 'config.php';
require 'functions.php';
include ('connect.php');
$login=$_POST['user'];
$postPass=$_POST['password'];
$ver=$_POST['version'];

		if(getGameInfo('launcher') == $ver){
				


				switch ($crypt)
				{
					case 'hash_md5':
					case 'hash_authme':
					case 'hash_xauth':
					case 'hash_cauth':
					case 'hash_joomla':
					case 'hash_wordpress':
						$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass FROM $db_table WHERE $db_columnUser='$login'"));
						$realPass = $row[$db_columnPass];
					break;

					case 'hash_ipb':
						$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass,$db_columnSalt FROM $db_table WHERE $db_columnUser='$login'"));
						$realPass = $row[$db_columnPass];
						$salt = $row[$db_columnSalt];
					break;
					
					case 'hash_xenforo':
						$row = mysql_fetch_assoc(mysql_query("SELECT $db_table.$db_columnId,$db_table.$db_columnUser,$db_tableOther.$db_columnId,$db_tableOther.$db_columnPass FROM $db_table, $db_tableOther WHERE $db_table.$db_columnId = $db_tableOther.$db_columnId AND $db_table.$db_columnUser='$login'"));
						$realPass = substr($row[$db_columnPass],22,64);
						$salt = substr($row[$db_columnPass],105,64);
					break;
					
					default:
						echo "Wrong hash method. Check config.php";
					break;
				}

    			if ($realPass) 
				{
					$checkPass = $crypt();
					
					if(strcmp($realPass,$checkPass) == 0)
					{
						$sessid = generateSessionId();
						$gamebuild=getGameInfo('build');
						mysql_query("UPDATE $db_table SET $db_columnSesId='$sessid' WHERE $db_columnUser = '$login'") or die ("Запрос к базе завершился ошибкой.");
						$dlticket = md5($login);
						echo $gamebuild.':'.$dlticket.':'.$login.':'.$sessid.':';
					}
					else
					{
						echo "Bad login";
					}
				}
			else {
				echo "Bad login";
				}
		}
		else{
			echo 'Old version';
			}
?>