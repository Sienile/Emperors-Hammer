<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$name = UnitType($id);
echo "<a href=\"/unit.php?id=$values1[0]\">".$name."'s</a> Unit Reports<br />";
$query = "SELECT Report_ID, Name, ReportDate, ReportNum FROM EH_Reports Where Unit_ID=$id Order By ReportDate DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<a href=\"/report.php?id=$values[0]\">".stripslashes($values[1])." - ".stripslashes($values[3])."</a> - Posted on ".date("F j, Y", $values[2])."<br />\n";
  }
include_once("footer.php");
?>