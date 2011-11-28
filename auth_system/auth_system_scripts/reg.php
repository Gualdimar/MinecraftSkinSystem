<?php
define('INCLUDE_CHECK',true);
echo    '<h2>Регистрация</h2>';
include "connect.php";
    if (isset($_POST['login']))
        {
		$login = $_POST['login'];
		$pass = $_POST['passwd'];
		$repass = $_POST['repasswd'];

            $login = addslashes(trim($login));
            $pass = addslashes(trim($pass));
            $repass = addslashes(trim($repass));

        if (empty($login) || empty($pass) || empty($repass))
            {
                echo 'Не все поля заполнены.';
            }
        
        elseif (ereg("[^0-9a-zA-Z_-]", $login, $Txt))
            {
                echo "Логин введен не корректно.";
            }
            
        elseif (ereg("[^0-9a-zA-Z_-]", $pass, $Txt))
            {
                echo "Пароль введен не корректно.";
            }
        
        elseif (ereg("[^0-9a-zA-Z_-]", $repass, $Txt))
            {
                echo "Повтор пароля введен не корректно.";
            } 
        
	else
            {
                $login_proverka = mysql_query("SELECT $db_columnUser FROM $db_table WHERE $db_columnUser='$login'") or ("Запрос к базе завершился ощибкой.");

	        if (mysql_num_rows($login_proverka))
            {
                echo "Акаунт <b>".$login."</b> уже существует.";
            }
        elseif ((strlen($login) < 4) or (strlen($login) > 8)) 
        
            {
                echo "Логин должен содержать не меньше 4 символов и не больше 8.";
            }
        elseif ((strlen($pass) < 4) or (strlen($pass) > 15)) 
        
            {
                echo "Пароль должен содержать не меньше 4 символов и не больше 15.";
            }
            
        elseif ((strlen($repass) < 4) or (strlen($repass) > 15)) 
            {
                echo "Повтор пароля должен содержать не меньше 4 символов и не больше 15.";
            }
        elseif ($pass != $repass)
            {
                echo "Пароли не совпадают.";
            }  
        else
            {
                $cp = md5($pass);
		mysql_query("INSERT INTO $db_table ($db_columnUser,$db_columnPass) VALUES('$login','$cp')") or die ("Запрос к базе завершился ощибкой.");
		echo 'Аккаунт <b>'.$login.'</b> успешно зарегестрирован.';
		
            }    
        }     
    }
?>
<form action="" method="post">
								<p><br />Логин:<br /><input type=text name=login /><br /></p>
								<p><br />Пароль:<br /><input  type=password name=passwd  /><br /></p>
								<p><br />Повторите пароль:<br /><input  type=password name=repasswd /><br /></p>
								<p><br /><input type="submit" name="submit" value="Отправить" /><br /></p>
</form>