<?php
/**
 * Created by PhpStorm.
 * User: Constantin Krayushkin
 * Date: 14.01.20
 * Time: 11:50
 */
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
// локальная бд
define("DB_SERVER", "localhost");
// линукс
define("DB_USER", "host16973_remote");
// винда
//define("DB_USER", "host16973_rem");
define("DB_PASS", "");
define("DB_NAME", "");

// локальная бд firebird
//define("DB_SERVER_FB", "");
define("DB_USER_FB", "sysdba");
define("DB_PASS_FB", "masterkey");
define("DB_NAME_FB", "c:\ProgramData\Vzljot\Vzljot Sp Db Firebird\VZLJOTSP.FDB");


//удаленная бд "https://mysql.--.ru"
define("DB_SERVER_RM", "1.2.1.2");
define("DB_USER_RM", "host16973_r");
////////////

define("ADM_LOG1", "kka");
define("ADM_LOG2", "knm");
define("ADM_LOG3", "yau");
define("ADM_PASS", "1234765");

define("FTP_SRV", "ftp7.--.ru");
define("FTP_PATH", "host16973.--.pro/htdocs/www/uploads");
define("FTP_USER", "host1697773_kd");
define("FTP_PASS", "Hd169ro");

function connect_to_db ()
{
	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
/* проверка соединения */
	if (mysqli_connect_errno()) {
		printf("Не удалось подключиться: %s\n", mysqli_connect_error());
		exit();
	}
/* изменение набора символов на utf8 */
	if (!mysqli_set_charset($link, "utf8")) {
		printf("Ошибка при загрузке набора символов utf8: %s\n", mysqli_error($link));
	}
	//else {
	//    printf("Текущий набор символов: %s\n", mysqli_character_set_name($link));
	//}
	return $link;
}

// не удается подключиться, не хватает библиотек по ходу
// подключение к бд firebird
/*function connect_to_db_fb ()
{
	$link = ibase_connect(DB_NAME_FB, DB_USER_FB, DB_PASS_FB, "utf8");
if ($link!=true) {
		printf("Не удалось подключиться: %s\n", "");
		exit();
	}
	return $link;
}
*/
// подключение к удаленной бд
function connect_to_db_rm ()
{
	$link_rm = mysqli_connect(DB_SERVER_RM, DB_USER_RM, DB_PASS, DB_NAME);
/* проверка соединения */
	if (mysqli_connect_errno()) {
		printf("Не удалось подключиться: %s\n", mysqli_connect_error());
		exit();
	}
/* изменение набора символов на utf8 */
	if (!mysqli_set_charset($link_rm, "utf8")) {
		printf("Ошибка при загрузке набора символов utf8: %s\n", mysqli_error($link_rm));
	}
	return $link_rm;
}

?>