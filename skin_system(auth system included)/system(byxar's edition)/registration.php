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
<?php
echo    '<h2>Регистрация</h2>';
$err = null;
include "connect.php";
    if (isset($_POST['login']))
        {
		$login = $_POST['login'];
		$pass = $_POST['passwd'];
		$repass = $_POST['repasswd'];
		$email = $_POST['email'];

            $login = addslashes(trim($login));
            $pass = addslashes(trim($pass));
            $repass = addslashes(trim($repass));
            $email = addslashes(trim($email));

        if (empty($login) || empty($pass) || empty($repass))
            {
                $err = 'Не все поля заполнены.';
            }
        
        elseif (ereg("[^0-9a-zA-Z_-]", $login, $Txt))
            {
                $err = "Логин введен не корректно.";
            }
            
        elseif (ereg("[^0-9a-zA-Z_-]", $pass, $Txt))
            {
                $err = "Пароль введен не корректно.";
            }
        
        elseif (ereg("[^0-9a-zA-Z_-]", $repass, $Txt))
            {
                $err = "Повтор пароля введен не корректно.";
            } 
        
	elseif (!validatemail($email))
            {
                $err = "E-mail введен не корректно.";
            } 
	else
            {
                $login_proverka = mysql_query("SELECT $db_columnUser FROM $db_table WHERE $db_columnUser='$login'") or ("Запрос к базе завершился ощибкой.");

	        if (mysql_num_rows($login_proverka))
            {
                $err = "Акаунт <b>".$login."</b> уже существует.";
            }
        elseif ((strlen($login) < 4) or (strlen($login) > 8)) 
        
            {
                $err = "Логин должен содержать не меньше 4 символов и не больше 8.";
            }
        elseif ((strlen($pass) < 4) or (strlen($pass) > 15)) 
        
            {
                $err = "Пароль должен содержать не меньше 4 символов и не больше 15.";
            }
            
        elseif ((strlen($repass) < 4) or (strlen($repass) > 15)) 
            {
                $err = "Повтор пароля должен содержать не меньше 4 символов и не больше 15.";
            }
        elseif ($pass != $repass)
            {
                $err = "Пароли не совпадают.";
            }  
        elseif (!chk_crypt($_POST['captcha']))
            {
		$err = "Каптча введена не верно!";
	        }
        else
            {
                $cp = encryptPassword($pass);
		if(!empty($email))
		{
		mysql_query("INSERT INTO $db_table ($db_columnUser,$db_columnPass,$db_columnEmail,$db_columnRegDate) VALUES('$login','$cp','$email',NOW())") or die ("Запрос к базе завершился ощибкой.");
		}
		else
		{
		mysql_query("INSERT INTO $db_table ($db_columnUser,$db_columnPass,$db_columnRegDate) VALUES('$login','$cp',NOW())") or die ("Запрос к базе завершился ощибкой.");
		}
		$info = "Аккаунт <b>".$login."</b> успешно зарегестрирован. Вы будете перенаправлены на главную страницу через 5 секунд.";
		echo '<br /><p class="ok">'.$info.'<br /></p>';
		echo "<meta http-equiv='refresh'; content='5; url=index.php'> ";
		
            }    
        }     
    }
if(!empty($err))
{
echo '<br /><p class="err">'.$err.'<br /></p>';
}
?>

<form action="registration.php" method="post">
								<p><br />Логин:<br /><input type=text name=login class="field" /><br /></p>
								<p><br />Пароль:<br /><input  type=password name=passwd class="field" /><br /></p>
								<p><br />Повторите пароль:<br /><input  type=password name=repasswd class="field" /><br /></p>
								<p><br />E-mail(опционально, для востановления пароля):<br /><input  type=text name=email class="field" /><br /></p>
								<p><br /><?php dsp_crypt(0,1); ?></p>
								<p><br />Введите каптчу:<br /><input  type=text name=captcha class="field" /><br /></p>
								<p><input type="submit" name="submit" value="Зарегистрироваться" class="regbutton" /><br /></p>
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