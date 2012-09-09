<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Training Course";
include("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT TAc_ID, Name, Description, Leader, Deputy, Trainers FROM EH_Training_Academies WHERE TAc_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  echo "<p><b>".stripslashes($values[1])."</b></p>";
  echo "<p>".stripslashes($values[2])."</p>";
  }
include_once("footer.php"); ?>