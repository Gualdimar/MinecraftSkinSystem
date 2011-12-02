<?php

if(!defined('INCLUDE_CHECK')) die('У вас не прав запускать файл на выполнение');

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
	global $realPass, $postPass, $salt;
	
	$cryptPass = false;
	$cryptPass = md5(md5($salt).md5($postPass));
	
	return $cryptPass;
}

function hash_xenforo()
{
	global $realPass, $postPass, $salt;
	
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

function uploadHandle($max_file_size = 5, $upload_dir = '.')  
    {  
		global $dir_skins, $dir_cloaks, $username;
        $error = null;  
        $info  = null;  
        $max_file_size *= 1024;
		//$username = $_SESSION['playername'].".png";
		if($_POST['Mod'] == "1") { $upload_dir = $dir_skins; }
		if($_POST['Mod'] == "2") { $upload_dir = $dir_cloaks; }
		
        if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK)  
        {  
            // проверяем расширение файла  
			if (($_FILES['userfile']['type'] == "image/png")  || ($_FILES['userfile']['type'] == "image/x-png"))
            {  
                // проверяем размер файла  
                if ($_FILES['userfile']['size'] < $max_file_size)  
                {  
					// проверяем разрешение файла
					$ImageSize = getimagesize($_FILES['userfile']['tmp_name']); 
					if($ImageSize['0'] == 64 && $ImageSize['1'] == 32) {
						
						$destination = $upload_dir .'/' .$username.".png";  
      					if (move_uploaded_file($_FILES['userfile']['tmp_name'], $destination))
							$info = 'Файл успешно загружен.';
							else  
							$error = 'Не удалось загрузить файл.';
							
					}   
					else  
						$error = 'Разрешение файла отличается от  допустимого 64pх на 32px.';
                }   
                else  
                    $error = 'Размер файла больше допустимого.';  
            }   
            else  
                $error = 'Разрешена загрузка только изображений в формате "png".';  
        }   
        else { 

            // массив ошибок  
            $error_values = array( 

                UPLOAD_ERR_INI_SIZE   => 'Размер файла больше разрешенного директивой upload_max_filesize в php.ini',  
                UPLOAD_ERR_FORM_SIZE  => 'Размер файла превышает указанное значение в MAX_FILE_SIZE',                            
                UPLOAD_ERR_PARTIAL    => 'Файл был загружен только частично',   
                UPLOAD_ERR_NO_FILE    => 'Не был выбран файл для загрузки',   
                UPLOAD_ERR_NO_TMP_DIR => 'Не найдена папка для временных файлов',   
                UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла на диск' 

                                  );  
      
            $error_code = $_FILES['userfile']['error'];  
      
            if (!empty($error_values[$error_code]))  
                $error = $error_values[$error_code];  
            else  
                $error = 'Случилось что-то непонятное';  
			}
        return array('info' => $info, 'error' => $error);  
    }
	
// функции постраничного вывода из файла
function get_page( $page, $start ){
  global $text, $len, $chars_per_page, $pagebreak;
  if( $page < 1 || $start >= $len ) return "";
  $end = $start + $chars_per_page;
  if( $end >= $len ) $end = $len;
  else{
    $end = strpos( $text, $pagebreak, $end );
    if( $end === false ) $end = $len;
    }
  if( $page == 1 ) return @substr( $text, $start, $end-$start );
  return get_page( $page-1, $end );
  }
 
function get_pages_count(){
  global $text, $len, $chars_per_page, $pagebreak;
  $pages = 1;
  $pos = 0;
  while( ( $pos = @strpos( $text, $pagebreak, $pos+$chars_per_page ) ) !== false )
    $pages++;
  return $pages;
  }
 
function paginate(){
  global $this_url, $text, $len, $chars_per_page;
 
  if( $len <= $chars_per_page ){
    echo $text;
    return;
    }
 
  $page = isset($_GET['page']) ? $_GET['page'] : 1;
  $pages = get_pages_count();
  if( $page < 1 ) $page = 1;
  elseif( $page > $pages ) $page = $pages;
 
  echo get_page( $page, 0 );
  echo '<br /><br /><hr /><br />';
  echo '<p style="text-align:center;font-size:14px;"/>';
  for( $i = 1; $i <= $pages; $i++ ){
    if( $i == $page ) echo ' <b>'.$i.'</b>';
    else echo ' <a href="'.$this_url.'?page='.$i.'">'.$i.'</a>';
    }
  }

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
?>