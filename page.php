<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$page = mysql_real_escape_string($_GET['page'], $mysql_link);
$query = "SELECT Name, Body, DateUpdated FROM EH_Pages WHERE ShortName='$page'";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  echo stripslashes($values[1]);
  echo "<p>&nbsp;</p>";
  echo "<p align=\"right\"><font size=\"1\">".stripslashes($values[0])." last updated: ". date("F j, Y", $values[2])."</font></p>";
  }
else {
  echo "<p>The page you were looking for does not exist</p>";
  }
include_once("footer.php");
?>