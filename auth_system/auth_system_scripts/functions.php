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

function hash_xauth()
{
	global $realPass, $postPass;
	
	$cryptPass = false;
	$saltPos = (strlen($postPass) >= strlen($realPass) ? strlen($realPass) : strlen($postPass));
	$salt = substr($realPass, $saltPos, 12);
	$hash = hash('whirlpool', $salt . $postPass);
	$cryptPass = substr($hash, 0, $saltPos) . $salt . substr($hash, $saltPos);
	
	return $cryptPass;
}

function hash_md5()
{
	global $postPass;
	
	$cryptPass = false;
	$cryptPass = md5($postPass);
	
	return $cryptPass;
}

function hash_cauth()
{
	global $realPass, $postPass;
	
	$cryptPass = false;
	if (strlen($realPass) < 32)
	{
		$cryptPass = md5($postPass);
		$rp = str_replace('0', '', $realPass);
		$cp = str_replace('0', '', $cryptPass);
		(strcasecmp($rp,$cp) == 0 ? $cryptPass = $realPass : $cryptPass = false);
	}
	else
	{
		$cryptPass = md5($postPass);
	}
	
	return $cryptPass;
}

function hash_authme()
{
	global $realPass, $postPass;
	
	$cryptPass = false;
	$ar = preg_split("/\\$/",$realPass);
	$salt = $ar[2];
	$cryptPass = '$SHA$'.$salt.'$'.hash('sha256',hash('sha256',$postPass).$salt);
	
	return $cryptPass;
}

function hash_joomla()
{
	global $realPass, $postPass;
	
	$cryptPass = false;
	$parts = explode( ':', $realPass);
	$salt = $parts[1];
	$cryptPass = md5($postPass . $salt) . ":" . $salt;
	
	return $cryptPass;
}

function hash_ipb()
{
	global $postPass, $salt;
	
	$cryptPass = false;
	$cryptPass = md5(md5($salt).md5($postPass));
	
	return $cryptPass;
}

function hash_xenforo()
{
	global $postPass, $salt;
	
	$cryptPass = false;
	$cryptPass = hash('sha256', hash('sha256', $postPass) . $salt);
	
	return $cryptPass;
}

function hash_wordpress()
{
	global $realPass, $postPass;
	
	$cryptPass = false;
	$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$count_log2 = strpos($itoa64, $realPass[3]);
	$count = 1 << $count_log2;
	$salt = substr($realPass, 4, 8);
	$input = md5($salt . $postPass, TRUE);
	do 
	{
		$input = md5($input . $postPass, TRUE);
	} 
	while (--$count);
                
	$output = substr($realPass, 0, 12);
				
	$count = 16;
	$i = 0;
	do 
	{
		$value = ord($input[$i++]);
		$cryptPass .= $itoa64[$value & 0x3f];
		if ($i < $count)
			$value |= ord($input[$i]) << 8;
		$cryptPass .= $itoa64[($value >> 6) & 0x3f];
		if ($i++ >= $count)
			break;
		if ($i < $count)
			$value |= ord($input[$i]) << 16;
		$cryptPass .= $itoa64[($value >> 12) & 0x3f];
		if ($i++ >= $count)
			break;
		$cryptPass .= $itoa64[($value >> 18) & 0x3f];
	} 
		while ($i < $count);
				
	$cryptPass = $output . $cryptPass;

	return $cryptPass;
}

function hash_vbulletin()
{
	global $postPass, $salt;

	$cryptPass = false;
	$cryptPass = md5(md5($postPass) . $salt);

	return $cryptPass;
}
?>