<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$group=mysql_real_escape_string($_GET['group']);
$pin=mysql_real_escape_string($_GET['pin']);
echo CoC($pin, $group);
include("footer.php");
?>