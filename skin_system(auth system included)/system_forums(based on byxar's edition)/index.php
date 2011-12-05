<?php
define('INCLUDE_CHECK',true);
define( '_JEXEC', 1 );
include "def.php";

require 'functions.php';
require 'config.php'; 

	require 'connect.php';
	
	session_name('Login');
	session_set_cookie_params(2*7*24*60*60);
	session_start();

	if(empty($_SESSION['id']))
	{
		$_SESSION['id'] = false;
	}
	
	if($_SESSION['id'] && !isset($_COOKIE['Remember']) && !$_SESSION['rememberMe'])
	{
		$_SESSION = array();
		session_destroy();
	}
 
	if(isset($_GET['logoff']))
	{
		$_SESSION = array();
		session_destroy();
		header("Location: index.php");
		exit;
	}
	
	if(empty($_POST['submit']))
	{
		$_POST['submit'] = false;
	}
	
	if($_POST['submit']=='Войти')
	{
		$err = array();

		if ($crypt == 'hash_md5' || $crypt == 'hash_authme' || $crypt == 'hash_xauth' || $crypt == 'hash_cauth' || $crypt == 'hash_joomla' || $crypt == 'hash_ipb' || $crypt == 'hash_xenforo' || $crypt == 'hash_wordpress' || $crypt == 'hash_vbulletin' || $crypt == 'hash_dle' || $crypt == 'hash_drupal')
		{
			$ip=getenv("HTTP_X_FORWARDED_FOR");
			if (empty($ip) || $ip=='unknown') 
			{
				$ip=getenv("REMOTE_ADDR"); 
			}
		
			mysql_query ("DELETE FROM $db_ErrorLogtable WHERE UNIX_TIMESTAMP() -    UNIX_TIMESTAMP($db_Datecolumn) > 900");
			$result = mysql_query("SELECT $db_Numcolumn FROM $db_ErrorLogtable WHERE    $db_Ipcolumn='$ip'");
			$myrow = mysql_fetch_array($result);
			if ($myrow[$db_Numcolumn] > 2) {
			$err[] = 'Вы ошиблись 3 раза. Ваша активность заблокирована на 15 минут.';
			}    
			else{
		
	
			if(!$_POST['username'] || !$_POST['password'])
			$err[] = 'Все поля должны быть заполнены!';
			
			if ((!preg_match('#^[A-Za-z0-9]+$#i', $_POST['username'])) || (!preg_match('#^[A-Za-z0-9]+$#i', $_POST['password'])))  // Проверка логина и пароля на допустимые символы
			{
				$err[] = 'Разрешены только цифры и латинские буквы!';
			}
			else
			{
		
				if(!count($err))
				$_POST['username'] = mysql_real_escape_string($_POST['username']);
				$_POST['password'] = mysql_real_escape_string($_POST['password']);
				$_POST['rememberMe'] = (int)$_POST['rememberMe'];
				$postPass = $_POST['password'];

				if ($crypt == 'hash_md5' || $crypt == 'hash_authme' || $crypt == 'hash_xauth' || $crypt == 'hash_cauth' || $crypt == 'hash_joomla' || $crypt == 'hash_wordpress' || $crypt == 'hash_dle' || $crypt == 'hash_drupal')
				{
					$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass FROM $db_table WHERE $db_columnUser='{$_POST['username']}'"));
					$realPass = $row[$db_columnPass];
				}

				if ($crypt == 'hash_ipb' || $crypt == 'hash_vbulletin')
				{
					$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass,$db_columnSalt FROM $db_table WHERE $db_columnUser='{$_POST['username']}'"));
					$realPass = $row[$db_columnPass];
					$salt = $row[$db_columnSalt];
				}
					
				if ($crypt == 'hash_xenforo')
				{
					$row = mysql_fetch_assoc(mysql_query("SELECT $db_table.$db_columnId,$db_table.$db_columnUser,$db_tableOther.$db_columnId,$db_tableOther.$db_columnPass FROM $db_table, $db_tableOther WHERE $db_table.$db_columnId = $db_tableOther.$db_columnId AND $db_table.$db_columnUser='{$_POST['username']}'"));
					$realPass = substr($row[$db_columnPass],22,64);
					$salt = substr($row[$db_columnPass],105,64);
				}

				if ($crypt == 'hash_vbulletin')
				{
					$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass,$db_columnSalt FROM $db_table WHERE $db_columnUser='{$_POST['username']}'"));
					$realPass = $row[$db_columnPass];
					$salt = $row[$db_columnSalt];
				}

					$checkPass = $crypt();
					
					if(strcmp($realPass,$checkPass) == 0)
					{
						$_SESSION['playername']=$row[$db_columnUser];
						$_SESSION['id'] = $row[$db_columnId];
						$_SESSION['rememberMe'] = $_POST['rememberMe'];
						setcookie('Remember',$_POST['rememberMe']);
					}
					else
					{
						$select = mysql_query ("SELECT $db_Ipcolumn FROM $db_ErrorLogtable WHERE    $db_Ipcolumn='$ip'") or die ("Запрос к базе завершился ощибкой.");
						$tmp = mysql_fetch_row ($select);
						if ($ip == $tmp[0]) 
						{
						$result52 = mysql_query("SELECT $db_Numcolumn FROM $db_ErrorLogtable WHERE    $db_Ipcolumn='$ip'") or die ("Запрос к базе завершился 	ощибкой.");
						$myrow52 = mysql_fetch_array($result52);          
						$col = $myrow52[0] + 1; 
	            				mysql_query ("UPDATE $db_ErrorLogtable SET $db_Numcolumn=$col,$db_Datecolumn=NOW() WHERE    $db_Ipcolumn='$ip'") or die ("Запрос к базе завершился ощибкой.");
		            			}          
						else {
						mysql_query ("INSERT INTO $db_ErrorLogtable ($db_Ipcolumn,$db_Datecolumn,$db_Numcolumn) VALUES    ('$ip',NOW(),'1')") or die ("Запрос к базе 	завершился ощибкой.");
						}
						$result = mysql_query("SELECT $db_Numcolumn FROM $db_ErrorLogtable WHERE    $db_Ipcolumn='$ip'") or die ("Запрос к базе завершился ощибкой.");
						$myrow = mysql_fetch_array($result);
						if ($myrow[$db_Numcolumn] == 3) {
						$err[] = 'Вы ошиблись 3 раза. Ваша активность заблокирована на 15 минут.';
						}    
						else{
						$err[]='Неправильный логин или пароль.';
						}
					}
			}}
		}
		else
		{
		$err[]='Неправильно указан метод шифрования проверте настройки в файле config.php.';
		}

		if($err)
		$_SESSION['msg']['login-err'] = implode('<br />',$err);
		header("Location: index.php");
		exit;
	}

if(isset($_GET['cloakpath']))
{
	$path = $_GET[cloakpath];
	unlink($path);
	header("Location: index.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Minecraft Skin System</title>
<link rel="stylesheet" type="text/css" href="template/css/style.css" media="screen" />
<script type="text/javascript">
function swch(block_id) {
  if (document.getElementById('block'+block_id).style.display=='block') {
    document.getElementById('block'+block_id).style.display = 'none';
    document.getElementById('block'+block_id+'_swch').innerHTML = '[+]';
  } else {
    document.getElementById('block'+block_id).style.display = 'block';
    document.getElementById('block'+block_id+'_swch').innerHTML = '[-]';
  }
  return false;
}
</script>
</head>
<body>
<div id='wrapper'>
<div id='top'></div>
	<!-- Шапка сайта -->
	<div id='header'>
		<div class='spacer1px'></div>
		<!-- Логотип сайта -->
		<a href="<?php echo 'http://'.$_SERVER['HTTP_HOST'] ?>" id="logo"></a>
	</div>
	<div id='container'>
		<div id='main'>
			<!-- Правая колонка сайта -->
			<div id='right'>
				<div class="column">
					<div class="bg-tl"></div>
					<div class="bg-tr"></div>
					<div class='clear'></div>
					<div class="bg-cl">
						<div class="content">
							<?php
								if(!$_SESSION['id'])
								{
								
									if(empty($_SESSION['msg']['login-err']))
									{
										$_SESSION['msg']['login-err'] = false;
									}
									
									if($_SESSION['msg']['login-err'])
									{
										echo '<p class="err">'.$_SESSION['msg']['login-err'].'<br /></p>';
										unset($_SESSION['msg']['login-err']);
									}
							
								echo '<form action="" method="post" enctype="multipart/form-data">
								<p><br />Логин:<br /><input type="text" name="username" id="username" class="field" /><br /></p>
								<p><br />Пароль:<br /><input type="password" name="password" id="password" class="field" /><br /></p>
								<p><br /><input name="rememberMe" id="rememberMe" type="checkbox"  value="1" /> &nbsp;Запомнить меня&nbsp;&nbsp;<br /></p>
								<p><input type="submit" name="submit" value="Войти" class="button" /><input type="button" onclick="location.href=\'download.php\'" value="Загрузки" class="regbutton2"/><br /></p>';
								}
								else
								{
									$username = $_SESSION['playername'];
									
									if($_SESSION['id'])
									{
										echo '<h1>Здравствуйте, '.$username.'!</h1><h2> Добро пожаловать на страницу смены скинов!</h2>';
										echo'<input type="button" onclick="location.href=\'download.php\'" value="Загрузки" class="dlbutton" />';
										
										if(!empty($_POST['upload_submit'])) 
										{      
											$message = uploadHandle(5, $upload_dir);
											
											if (!empty($message['error']))
											{
												echo '<p class="err">'.$message['error'].'<br /></p>';
											}
											
											if (!empty($message['info']))
											{
												echo '<p class="ok">'.$message['info'].'<br /></p>';
											}
										}
								
										echo '<form action="" method="post" enctype="multipart/form-data">
										<br /><input type="file" name="userfile" />
										<p><br /><select name="Mod" class="field">
										<option value=1>Скин</option>
										<option value=2>Плащ</option>	
										<br /></p></select>
										<p><input type="submit" value="Загрузить" name="upload_submit" class="button" /><br /><br /></p></form>';

										echo '<h1> Скин:</h1><br />';
									
											if ( !file_exists($dir_skins.$username.'.png'))
											{
												$skinpath = $dir_skins.'default.png';
											}
											else
											{
												$skinpath = $dir_skins.$username.'.png';
											}
											echo '<img src="skin2d.php?skinpath='.$skinpath.'" /><br /><br />';
											echo '<img src="http://'.$url.$dir.$skinpath.'"  /><br /><br />';

											echo '<h1> Плащ:</h1>';

											if ( !file_exists($dir_cloaks.$username.'.png'))
											{
												echo 'Плащ не загружен.<br /><br />';
											}
											else
											{
												$cloakpath = $dir_cloaks.$username.'.png';
												echo '<p><input type="button" onclick="location.href=\'index.php?cloakpath='.$cloakpath.'\'" value="Удалить" class="button"/></p><br />';
												echo '<img src="http://'.$url.$dir.$dir_cloaks.$username.'.png" />';										
											}

										echo '<p><input type="button" onclick="location.href=\'?logoff\'" value="Выход" class="button"/></p>';
									}
								}
							?>
						</div>
					</div>
					<div class='clear'></div>
					<div class="bg-bl"></div>
					<div class="bg-br"></div>
					<div class="bg-bc"></div>
					<div class='clear'></div>
				</div>

			
				
			</div>
			<!-- Левая колонка сайта-->
			<div id='left'>
				<!-- Вывод текста из файла -->
				<?php
				paginate();
											if ( !file_exists($dir_skins.$username.'.png'))
											{
												$skinpath = $dir_skins.'default.png';
											}
											else
											{
												$skinpath = $dir_skins.$username.'.png';
											}
											echo '<br/><br/><applet code="skinpreviewapplet.AppletLauncher" archive="./template/js/skinpreview.jar" codebase="." width="320" height="320">
											<param name="url" value="http://'.$url.$dir.$skinpath.'" /></applet><br /><br />'; 
				?>
			</div>
		</div>
	</div>
	<div class='clear'></div>
	<div class='spacer64px'></div>
</div>
<div id='footer' align="center"><!-- Подвал сайта -->
<br />
Original code by z0z1ch. Edited by byxar.
</div>
</body>
</html>