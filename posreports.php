<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query1 = "SELECT Position_ID, Name From EH_Positions Where Position_ID=$id";
$result1 = mysql_query($query1, $mysql_link);
$values1 = mysql_fetch_row($result1);
$name = stripslashes($values1[1]);
echo "$name's Reports<br />\n";
$query = "SELECT Report_ID, Name, ReportDate, ReportNum FROM EH_Reports Where Position_ID=$id Order By ReportDate DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<a href=\"report.php?id=$values[0]\">".stripslashes($values[1])." - ".stripslashes($values[3])."</a> - Posted on ".date("F j, Y", $values[2])."<br />\n";
  }
include_once("footer.php");
?>