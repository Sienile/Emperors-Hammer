<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$id = mysql_real_escape_string($_GET['unit'], $mysql_link);
$name = UnitType($id);
$subunits = SubUnitList($id);
echo "<a href=\"/unit.php?id=$values1[0]\">".$name."'s</a> Training Certificates<br />";
$units = implode(" OR Unit_ID=", $subunits);
$member = array();
$query = "SELECT Member_ID FROM EH_Members_Units WHERE Unit_ID=$units";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $member[]=$values[0];
  }
$indentspace = "&nbsp;&nbsp;&nbsp;";
$unitmembers = count($member);
$query = "SELECT Training_ID, Name From EH_Training Order By TAc_ID, TC_ID, SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $memlist = implode(" OR Member_ID=", $member);
  $query1 = "SELECT CT_ID From EH_Training_Complete WHERE Training_ID=$values[0] AND (Member_ID=$memlist) Group By Member_ID";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1>$unitmembers/2) {
    $percent = round($unitmembers/$rows1*100, 1);
    echo $indentspace."<a href=\"/training/course.php?id=$values[0]\">".stripslashes($values[1])."</a> ($percent%)<br>\n";
    }
}
include_once("footer.php");
?>