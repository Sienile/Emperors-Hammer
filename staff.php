<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Staff";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$query = "SELECT TAc_ID, Name, Leader, Deputy, Trainers, Group_ID FROM EH_Training_Academies Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p><b>".stripslashes($values[1])." Staff</b><br>\n";
  if($values[2]) {
    echo PositionName($values[2], "").":<br />\n";
    echo MembersPosition($values[2], $values[5], "<br />\n");
    }
  if($values[3]) {
    echo PositionName($values[3], "").":<br />\n";
    echo MembersPosition($values[3], $values[5], "<br />\n");
    }
  if($values[4]) {
    echo PositionName($values[4], "").":<br />\n";
    echo MembersPosition($values[4], $values[5], "<br />\n");
    }
  echo "</p>\n";
  }
include_once("footer.php"); ?>