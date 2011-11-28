 <?php
if(!defined('INCLUDE_CHECK')) die('У вас нет прав на выполнение данного файла!');

function generateSessionId(){
    // generate rand num
    srand(time());
    $randNum = rand(1000000000, 2147483647).rand(1000000000, 2147483647).rand(0,9);
    return $randNum;
}

function getGameInfo($type){
    include ("connect.php");
    switch($type){
	case 'build':
		$query = mysql_query("SELECT * FROM $db_GameDatatable WHERE $db_Propertycolumn = 'latest-game-build'") or die ("Запрос к базе завершился ошибкой.");    
	    	$resource = mysql_fetch_array($query);
	    	return $resource[$db_Valuecolumn];
	break;

	case 'launcher':
		$query = mysql_query("SELECT * FROM $db_GameDatatable WHERE $db_Propertycolumn = 'launcher-version'") or die ("Запрос к базе завершился ошибкой."); 
	    	$resource = mysql_fetch_array($query);
	    	return $resource[$db_Valuecolumn];
	break;

	}
}

function checkPass($realPass,$password){
			if (strlen($realPass) == 32)
			{
				$cp = md5($password);
			}
			else {
				if(strpos($realPass,'$SHA$') !== false)
				{
					$ar = preg_split("/\\$/",$realPass);
					$salt = $ar[2];
					$cp = '$SHA$'.$salt.'$'.hash('sha256',hash('sha256',$password).$salt);
				}
				else
				{
					$saltPos = (strlen($password) >= strlen($realPass) ? strlen($realPass) : strlen($password));
					$salt = substr($realPass, $saltPos, 12);
					$hash = hash('whirlpool', $salt . $password);
					$cp = substr($hash, 0, $saltPos) . $salt . substr($hash, $saltPos);
				}
			}
			
			if ($realPass==$cp) {
				return true;
			}
			else {
				return false;
			}
}
?>