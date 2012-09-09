<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$pin = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT EH_Members.Member_ID, EH_Members.Name, EH_Fiction.Title, EH_Fiction.Body, EH_Fiction.DatePosted FROM EH_Fiction, EH_Members WHERE EH_Fiction.Fiction_ID=$pin AND EH_Fiction.Member_ID=EH_Members.Member_ID";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  echo "<h2>".stripslashes($values[2])."</h2>\n";
  echo "<h5>Written by: <a href=\"profile.php?pin=$values[0]\">".stripslashes($values[1])."</a> and posted on: ".date("M j, Y", $values[4])."</h5>\n";
  echo stripslashes($values[3]);
  }
else {
  echo "<p>The page you were looking for does not exist</p>";
  }
include_once("footer.php");
?>