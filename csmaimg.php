<?
include_once("config.php");
include_once("functions.php");
session_start();
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Type, ImageData FROM EH_Training_Images WHERE TI_ID=$id";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
header("Content-type: $values[0]");
echo $values[1];
?>