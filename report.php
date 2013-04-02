<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Report_ID, Name, ReportDate, ReportNum, Report, Unit_ID, Position_ID, Member_ID, Group_ID FROM EH_Reports Where Report_ID=$id";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
echo "<p>".stripslashes($values[1])." - ".stripslashes($values[3])."<br />\n";
$query1 = "SELECT Position_ID, Name, isCS From EH_Positions Where Position_ID=$values[6]";
$result1 = mysql_query($query1, $mysql_link);
$values1 = mysql_fetch_row($result1);
if($values1[2])
  echo "For: <a href=\"/posreports.php?id=$values1[0]\">".stripslashes($values1[1])."</a><br />\n";
else {
  $name = UnitType($values[5]);;
  echo "For: <a href=\"/unit.php?id=$values2[0]\">".stripslashes($name)."'s</a><br />";
  }
if($values[7]) {
  echo "Posted By: <a href=\"/profile/$values[7]\">". stripslashes(RankAbbrName($values[7], $values[8], 1))."</a><br />\n";
  }
echo "Posted On: ". date("F j, Y", $values[2]) ."<br />\n<br />\n";
echo stripslashes($values[4])."</p>";
include_once("footer.php");
?>