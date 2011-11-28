<?php
define('INCLUDE_CHECK',true);

require 'functions.php';
require 'config.php'; 

if ($login == 'on')
{
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
	$_POST['submit'] = false;
	
	if($_POST['submit']=='Войти')
	{
		$err = array();
		
		if ($crypt == 'hash_md5' || $crypt == 'hash_authme' || $crypt == 'hash_xauth' || $crypt == 'hash_cauth' || $crypt == 'hash_joomla' || $crypt == 'hash_ipb' || $crypt == 'hash_xenforo' || $crypt == 'hash_wordpress')
		{
		
			if(!$_POST['username'] || !$_POST['password'])
			$err[] = 'Все поля должны быть заполнены!';
		
			if ((!preg_match('#^[A-Za-z0-9_-]+$#i', $_POST['username'])) || (!preg_match('#^[A-Za-z0-9_-]+$#i', $_POST['password'])))  // Проверка логина и пароля на допустимые символы
			{
				$err[] = 'Разрешены только цифры, латинские буквы и символы подчеркивание и дефис!';
			}
			else
			{
		
				if(!count($err))
				$_POST['username'] = mysql_real_escape_string($_POST['username']);
				$_POST['password'] = mysql_real_escape_string($_POST['password']);
				$_POST['rememberMe'] = (int)$_POST['rememberMe'];
				$postPass = $_POST['password'];
			
				switch ($crypt)
				{
					case 'hash_md5':
					case 'hash_authme':
					case 'hash_xauth':
					case 'hash_cauth':
					case 'hash_joomla':
					case 'hash_wordpress':
						$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass FROM $db_table WHERE $db_columnUser='{$_POST['username']}'"));
						$realPass = $row[$db_columnPass];
					break;

					case 'hash_ipb':
						$row = mysql_fetch_assoc(mysql_query("SELECT $db_columnId,$db_columnUser,$db_columnPass,$db_columnSalt FROM $db_table WHERE $db_columnUser='{$_POST['username']}'"));
						$realPass = $row[$db_columnPass];
						$salt = $row[$db_columnSalt];
					break;
					
					case 'hash_xenforo':
						$row = mysql_fetch_assoc(mysql_query("SELECT $db_table.$db_columnId,$db_table.$db_columnUser,$db_tableOther.$db_columnId,$db_tableOther.$db_columnPass FROM $db_table, $db_tableOther WHERE $db_table.$db_columnId = $db_tableOther.$db_columnId AND $db_table.$db_columnUser='{$_POST['username']}'"));
						$realPass = substr($row[$db_columnPass],22,64);
						$salt = substr($row[$db_columnPass],105,64);
					break;
				}

				if($realPass)
				{
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
						$err[]= 'Неправильный пароль.';
					}
				}
				else
				{
					$err[] = 'Такого пользователя не существует.';
				}
			
			}
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
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Minecraft</title>
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
						<!-- Скрипт мониторинга сервера -->
						<?php
						$arr = query($server, $portmc);
						
						if ($arr == false)
						{
							$fp = @fsockopen($server, $port, $errno, $errstr, 0.1);
							
							if ($fp) 
							{
								echo '<div class="pechkaon"></div>';
								echo '<br />Версия: '.$version;
								echo '<br />Сервер: '.$server.':'.$port;
								fclose($fp);
							} 
							else
							{
								echo '<div class="pechkaoff"></div>';
								echo '<p><br />Сервер отключен!<br /></p>';
							}
						}
						else
						{
							echo '<div class="pechkaon"></div>';
							echo '<br />Версия: '.$version;
							echo '<br />Сервер: '.$server.':'.$port;
							echo '<br />Игроков: '.$arr['playerCount'].'/'.$arr['maxPlayers'];
							echo '<br />Задержка: '.round($arr['latency']);
							echo '<br /><br />Игроки онлайн: ';
						

							echo '<a href="#" onclick="return swch(2);" id="block2_swch">[+]</a><br /><br />
							<div id="block2" style="display : none;">';

							$first = true;
							
							foreach ($arr['playerList'] as $value)
							{
							
								if (empty($value))
								{
								echo '<b />Пусто, все ушли на фронт';
								}
								else
							
								if (!$first) print ', ';
								$first = false;
								echo $value;
							}
							echo '</div>';
						}	
						?>
						<div class='clear'></div>
						</div>
					</div>
					<div class='clear'></div>
					<div class="bg-bl"></div>
					<div class="bg-br"></div>
					<div class="bg-bc"></div>
					<div class='clear'></div>
				</div>

				<div class="column">
					<div class="bg-tl"></div>
					<div class="bg-tr"></div>
					<div class='clear'></div>
					<div class="bg-cl">
						<div class="content">
							<?php
							if ($login == 'on') // Загрузка скинов с авторизацией пользователя
							{	

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
								<p><br /><input name="rememberMe" id="rememberMe" type="checkbox"  value="1" /> &nbsp;Запомнить меня<br /></p>
								<p><input type="submit" name="submit" value="Войти" class="button" /><br /></p></form>';
								}
								else
								{
									$username = $_SESSION['playername'];
									
									if($_SESSION['id'])
									{
										echo '<h1>Здравствуйте, '.$username.'!</h1><h2> Добро пожаловать на страницу смены скинов!</h2><br /><br />';
										
										if(!empty($_POST['upload_submit'])) 
										{      
											$message = uploadHandle();
											
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
										<p><input type="submit" value="Закачать" name="upload_submit" class="button" /><br /><br /></p></form>';
										
										if ($skinpreview == '3d') // Вывод 3d просмотра загруженного скина (java).
										{
											echo '<applet code="skinpreviewapplet.AppletLauncher" archive="./template/js/skin3d.jar" codebase="." width="160" height="160">
											<param name="url" value="http://'.$url.$dir.$dir_skins.$username.'.png" /></applet>';
										}
										
										if ($skinpreview == '2d') // Вывод 2d просмотра загруженного скина (php).
										{ 
										
											if ( !file_exists($dir_skins.$username.'.png'))
											{
												$skinpath = $dir_skins.'default.png';
											}
											else
											{
												$skinpath = $dir_skins.$username.'.png';
											}
											echo '<img src="skin2d.php?skinpath='.$skinpath.'" />';
										}

										echo '<p><input type="button" onclick="location.href=\'?logoff\'" value="Выход" class="button"/></p>';
									}
								}
							}
							
							if ($login == 'off') // Загрузка скинов без авторизации пользователя
							{	
							
								if(empty($_POST['username']))
								{
									$_POST['username'] = false;
								}
								
								$username = $_POST['username'];
								
								if(!empty($_POST['upload_submit'])) 
								{      
									$message = uploadHandle();
									
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
								<p><br />Логин:<br /><input type="text" name="username" id="username" class="field" value="Имя в игре" onFocus="this.value=\'\'" /><br /></p>
								<br /><input type="file" name="userfile" />
								<p><br /><select name="Mod" class="field">
								<option value=1>Скин</option>
								<option value=2>Плащ</option>	
								<br /></p></select>
								<p><input type="submit" value="Закачать" name="upload_submit" class="button" /><br /><br /></p></form>';
								
								if ($skinpreview == '3d') // Вывод 3d просмотра загруженного скина (java).
										{
											echo '<applet code="skinpreviewapplet.AppletLauncher" archive="./template/js/skin3d.jar" codebase="." width="160" height="160">
											<param name="url" value="http://'.$url.$dir.$dir_skins.$username.'.png" /></applet>';
										}
										if ($skinpreview == '2d') // Вывод 2d просмотра загруженного скина (php).
										{ 
										
											if ( !file_exists($dir_skins.$username.'.png'))
											{
												$skinpath = $dir_skins.'default.png';
											}
											else
											{
												$skinpath = $dir_skins.$username.'.png';
											}
											echo '<img src="skin2d.php?skinpath='.$skinpath.'" />';
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
				?>
			</div>
		</div>
	</div>
	<div class='clear'></div>
	<div class='spacer64px'></div>
</div>
<div id='footer'><!-- Подвал сайта -->
</div>
</body>
</html>