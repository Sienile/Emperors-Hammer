<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Course Graduates";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Training_ID, Name, MaxPoints, TAc_ID FROM EH_Training Where Training_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  $query1 = "SELECT Group_ID FROM EH_Training_Academies Where TAc_ID=$values[3]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $group = $values1[0];
    }
  echo "<p>Graduates for ".stripslashes($values[1]).":<br />\n";
  $query1 = "SELECT EH_Training_Complete.DateComplete, EH_Training_Complete.Score, EH_Members.Member_ID, EH_Members.Name FROM EH_Training_Complete, EH_Members Where EH_Training_Complete.Training_ID=$values[0] AND EH_Members.Member_ID=EH_Training_Complete.Member_ID Order By EH_Training_Complete.DateComplete DESC, EH_Members.Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    if(!isinGroup($group, $values1[2]))
      $group  = PriGroup($values1[2]);
    echo "<a href=\"/profile/$values1[2]\">".RankAbbrName($values1[2], $group, 1)."</a>";
    if($values1[0])
      echo " completed on ".date("F j, Y", $values1[0]);
    if($values1[1])
      echo " with a score of $values1[1] out of $values[2]";
    echo "<br />\n";
    }
  echo "</p>\n";
  }
include_once("footer.php"); ?>