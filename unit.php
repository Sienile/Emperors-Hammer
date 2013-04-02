<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(isset($_GET['view']))
  $view = mysql_real_escape_string($_GET['view'], $mysql_link);
else
  $view=0;
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
Unit($id, $view);
echo "<a href=\"/search.php\">Search the Roster</a> | <a href=\"/login.php\">Administration</a> | ";
if($view)
  echo "<a href=\"/unit.php?id=$id&amp;view=0\">View None</a>";
else
  echo "<a href=\"/unit.php?id=$id&amp;view=1\">View All</a>";
include_once("footer.php");
?>