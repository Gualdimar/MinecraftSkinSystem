<?php
$cryptinstall="./crypt/cryptographp.fct.php";
include $cryptinstall; 
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
    <h2>Востановление пароля</h2>
<?php
$err = null;
if($_POST['submit']=='Отправить')
	{
		if(!$_POST['login'] || !$_POST['email'])
		{
			$err = "Не все поля заполнены.";
		}
		elseif (ereg("[^0-9a-zA-Z_-]", $_POST['login'], $Txt))
            	{
                $err = "Логин введен не корректно.";
            	} 
		elseif (!validatemail($_POST['email']))
            	{
                $err = "E-mail введен не корректно.";  
            	} 
		else
		{
			if (!chk_crypt($_POST['captcha']))
            		{
			$err = "Каптча введена не верно!";
	       		 }
			else
			{
			$login = $_POST['login'];
			$email = $_POST['email'];
			include ("connect.php");
			$result    = mysql_query("SELECT * FROM $db_table WHERE $db_columnUser='$login'") or die ("Запрос к базе завершился ощибкой.");
			$myrow    = mysql_fetch_array($result);
			if    (empty($myrow[$db_columnId]) or $myrow[$db_columnId]=='') 
			{
				$err = "Акаунт <b>".$login."</b> не существует или введен неправильный e-mail.";
			}
			else
			{
				if    (empty($myrow[$db_columnEmail]) or $myrow[$db_columnEmail]=='') 
				{
					$err = "У акаунта <b>".$login."</b> не установлен e-mail для востановления пароля.";
				}
				else
				{
					if    ($myrow[$db_columnEmail]==$email)
					{
						$datenow = date('YmdHis');
						$new_password = md5($datenow);
						$new_password = substr($new_password,    2, 6);
						$new_password_sh =    encryptPassword($new_password);
						mysql_query("UPDATE $db_table SET    $db_columnPass='$new_password_sh' WHERE $db_columnUser='$login'") or die ("Запрос к базе завершился ощибкой.");
						$message = "Здравствуйте, ".$login."! \nВаш новый пароль: ".$new_password." \nВы сможете войти на сайт используя его. После входа желательно его сменить.";
						mail($email,    "Восстановление пароля", $message, "From: $from \r\n");
						$info = 'На Ваш e-mail отправлено письмо с паролем. Вы будете перенаправлены на главную страницу через 5 секунд.';
						echo '<br /><p class="ok">'.$info.'<br /></p>';
						echo "<meta http-equiv='refresh'; content='5; url=index.php'> "; 
					}
					else
					{
						$err = "Акаунт <b>".$login."</b> не существует или введен неправильный e-mail.";
					}
				}
			}}
		}
	}
if(!empty($err))
{
echo '<br /><p class="err">'.$err.'<br /></p>';
}
?>
    <form action="send_pass.php" method="post">
								<p><br />Логин:<br /><input type=text name=login class="field" /><br /></p>
								<p><br />E-mail:<br /><input type=text name=email class="field" /><br /></p>
								<p><br /><?php dsp_crypt(0,1); ?></p>
								<p><br />Введите каптчу:<br /><input  type=text name=captcha class="field" /><br /></p>
								<p><input type="submit" name="submit" value="Отправить" class="regbutton" /><br /></p>
								<p><input type="button" onclick="location.href='index.php'" value="На главную" class="regbutton" /><br /></p>
</form>



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