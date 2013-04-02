<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$query = "SELECT Meeting_ID, Name, MeetTimeDesc FROM EH_Meetings Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows==0) {
  echo "<p>There are currently no weekly meetings scheduled.</p>";
  }
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>Meeting: ".stripslashes($values[1])."<br />";
  echo "Occurs on:".stripslashes($values[2])."<br>
  <a href=\"/meetinglogs.php?id=$values[0]\">Logs</a></p>";
  }
include_once("footer.php");
?>