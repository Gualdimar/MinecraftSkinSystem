<?php
session_name('Login');
session_start();
define('INCLUDE_CHECK',true);
define( '_JEXEC', 1 );
include "def.php";
require 'functions.php';
require 'config.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Minecrfat Skin System</title>
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
$err = null;
if($_SESSION['id'])
{
	if($_POST['submit']=='Отправить')
	{
		if(!empty($_POST['pass']))
		{
			if(empty($_POST['newpass']) and empty($_POST['newrepass']) and empty($_POST['email']))
			{
				$err = "Все поля пустые!";
			}
			else
			{
				include "connect.php";

				$result = mysql_query("SELECT $db_columnPass FROM $db_table WHERE $db_columnId='{$_SESSION['id']}'") or ("Запрос к базе завершился ощибкой.");
				$myrow = mysql_fetch_assoc($result);

				$realPass = $myrow[$db_columnPass];
				$postPass = $_POST['pass'];

							if(strpos($realPass,'$SHA$') !== false)
							{
								$ar = preg_split("/\\$/",$realPass);
								$salt = $ar[2];
								$checkPass = '$SHA$'.$salt.'$'.hash('sha256',hash('sha256',$postPass).$salt);
							}
							else
							{
								$saltPos = (strlen($postPass) >= strlen($realPass) ? strlen($realPass) : strlen($postPass));
								$salt = substr($realPass, $saltPos, 12);
								$hash = hash('whirlpool', $salt . $postPass);
								$checkPass = substr($hash, 0, $saltPos) . $salt . substr($hash, $saltPos);
							}

				if(strcmp($realPass,$checkPass) == 0)
				{
					if(!empty($_POST['newpass']))
					{
						$newpass = $_POST['newpass'];
						$newrepass = $_POST['newrepass'];

						if(ereg("[^0-9a-zA-Z_-]", $newpass, $Txt))
						{
							$err = "Новый пароль введен не корректно.";
						}
						elseif(ereg("[^0-9a-zA-Z_-]", $newrepass, $Txt))
						{
							$err = "Повтор нового пароля введен не корректно.";
						}
						else
						{
							if ((strlen($newpass) < 4) or (strlen($newpass) > 15)) 
        						{
								$err = "Новый пароль должен содержать не меньше 4 символов и не больше 15.";
							}
							elseif ($newpass != $newrepass)
							{
								$err = "Пароли не совпадают.";
							}
							else
							{
								$newpass = encryptPassword($newpass);
								mysql_query ("UPDATE $db_table SET $db_columnPass='$newpass' WHERE    $db_columnId='{$_SESSION['id']}'") or die ("Запрос к базе завершился ощибкой.");
								$info = "Пароль успешно изменен.";
								echo '<br /><p class="ok">'.$info.'<br /></p>';
							}
						}
					}

					if(!empty($_POST['email']))
					{
						$email = $_POST['email'];
						if (!validatemail($email))
						{
							$err = "E-mail введен не корректно.";
						}
						else
						{
							mysql_query ("UPDATE $db_table SET $db_columnEmail='$email' WHERE    $db_columnId='{$_SESSION['id']}'") or die ("Запрос к базе завершился ощибкой.");
							$info = "E-mail успешно изменен.";
							echo '<br /><p class="ok">'.$info.'<br /></p>';
						}
					}
				}
				else
				{
					$err = "Неправильный старый пароль!";
				}
			}
		}
		else
		{
			$err = "Вы не ввели старый пароль!";
		}
	}
if(!empty($err))
{
echo '<br /><p class="err">'.$err.'<br /></p>';
}

	echo '<form action="change.php" method="post">
								<h2>Изменение пароля:</h2>
								<p><br />Новый пароль:<br /><input type=password name=newpass class="field" /><br /></p>
								<p><br />Повторите новый пароль:<br /><input type=password name=newrepass class="field" /><br /></p>
								<br /><hr/><hr/><br />
								<h2>Смена e-mail&#x27;a:</h2>
								<p><br />Новый email:<br /><input  type=text name=email class="field" /><br /></p>
								<br /><hr/><hr/>
								<p><br />Старый пароль(обязателен в обоих случаях):<br /><input  type=password name=pass class="field" /><br /></p>
								<p><input type="submit" name="submit" value="Отправить" class="regbutton" /><br /></p>
								<p><input type="button" onclick="location.href=\'index.php\'" value="На главную" class="regbutton" /><br /></p>
	</form>';
}
else
{
	$err = "Доступ только для зарегистрированных пользователей!";
	echo '<br /><p class="err">'.$err.'<br /></p>';
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
<div id='footer' align="center"><!-- Подвал сайта -->
<br />
Original code by z0z1ch. Edited by byxar.
</div>
</body>
</html>