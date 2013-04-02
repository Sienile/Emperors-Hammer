<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
echo "<p>Emperor's Hammer Command Staff Reports</p>\n";
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$query = "select Position_ID, Name, Abbr From EH_Positions Where isCS=1 AND Group_ID=1 Order By CSOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<a href=\"/posreports.php?id=$values[0]\">".stripslashes($values[1])." Reports</a>";
  $query1 = "select Report_ID From EH_Reports Where Position_ID=$values[0]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1)
    echo " ($rows1)";
  echo "<br />\n";
  }
echo "<a href=\"/posreports.php?id=68\">Owner/Founder Reports</a><br />\n";
$query = "select Group_ID, Name From EH_Groups WHERE Active=1";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>$values[1] Command Staff Reports<br />\n";
  $query1 = "select Position_ID, Name, Abbr From EH_Positions Where isCS=1 AND Group_ID=$values[0] Order By CSOrder";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    echo "<a href=\"/posreports.php?id=$values1[0]\">".stripslashes($values1[1])." Reports</a>";
    $query2 = "select Report_ID From EH_Reports Where Position_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2)
      echo " ($rows2)";
    echo "<br />\n";
    }
  echo "</p>\n";
  }
include_once("footer.php");
?>