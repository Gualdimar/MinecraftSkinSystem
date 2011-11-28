<?php
    defined('_JEXEC') or die('Ай-яй-яй, сюда нельзя!');
function db_mysql_check_xss () {
	$url = html_entity_decode(urldecode($_SERVER['QUERY_STRING']));
	if ($url) {
		if ((strpos($url, '<') !== false) ||
			(strpos($url, '>') !== false) ||
			(strpos($url, '"') !== false) ||
			(strpos($url, '\'') !== false) ||
			(strpos($url, './') !== false) ||
			(strpos($url, '../') !== false) ||
			(strpos($url, '--') !== false) ||
			(strpos($url, '.php') !== false)
		   )
		{
			die("Попытка взлома!");
		}
	}
	$url = html_entity_decode(urldecode($_SERVER['REQUEST_URI']));
	if ($url) {
		if ((strpos($url, '<') !== false) ||
			(strpos($url, '>') !== false) ||
			(strpos($url, '"') !== false) ||
			(strpos($url, '\'') !== false)
		   )
		{
			die("Попытка взлома!");
		}
	}

}


function office_secure($check_string)
{
    $ret_string = $check_string;
    $ret_string = htmlspecialchars ($ret_string);
    $ret_string = strip_tags ($ret_string);
    $ret_string = trim ($ret_string);
    $ret_string = str_replace ('\\l', '', $ret_string);
    $ret_string = str_replace (' ', '', $ret_string);
    $ret_string  = str_replace("'", "", $ret_string );
    $ret_string  = str_replace("\"", "",$ret_string );
    $ret_string  = str_replace("--", "",$ret_string );
    $ret_string  = str_replace("#", "",$ret_string );
    $ret_string  = str_replace("$", "",$ret_string );
    $ret_string  = str_replace("%", "",$ret_string );
    $ret_string  = str_replace("^", "",$ret_string );
    $ret_string  = str_replace("&", "",$ret_string );
    $ret_string  = str_replace("(", "",$ret_string );
    $ret_string  = str_replace(")", "",$ret_string );
    $ret_string  = str_replace("=", "",$ret_string );
    $ret_string  = str_replace("+", "",$ret_string );
    $ret_string  = str_replace("%00", "",$ret_string );
    $ret_string  = str_replace(";", "",$ret_string );
    $ret_string  = str_replace(":", "",$ret_string );
    $ret_string  = str_replace("|", "",$ret_string );
    $ret_string  = str_replace("<", "",$ret_string );
    $ret_string  = str_replace(">", "",$ret_string );
    $ret_string  = str_replace("~", "",$ret_string );
    $ret_string  = str_replace("`", "",$ret_string );
    $ret_string  = str_replace("%20and%20", "",$ret_string );
    $ret_string = stripslashes ($ret_string);
    return $ret_string;
}

 function check_sql_inject() 
  { 
    $badchars = array("--","truncate","tbl_","exec",	";","'","*","/"," \ ","drop",
    	"select","update","delete","where", "-1", "-2", "-3","-4", "-5", "-6", "-7", "-8", "-9"); 
    foreach($_POST as $value) 
    { 
	    foreach($badchars as $bad) 
	    {
		    if(strstr(strtolower($value),$bad)<>FALSE) 
		    {
		    	die('Использованы недопустимые символы, <a href="'.$_SERVER['PHP_SELF'].'">вернитесь назад</a>');
		    }
		}
    } 
  } 

check_sql_inject(); 
db_mysql_check_xss ();
?>