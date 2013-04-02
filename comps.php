<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>Below are listed the various competitions that are in progress across the various Emperor's Hammer Groups:</p>";
$today = time();
$query = "SELECT Comp_ID, Name, Group_ID, StartDate, EndDate, Scope, Description, Awards, Admin_ID From EH_Competitions WHERE StartDate<=$today AND EndDate>=$today AND Approved=1 Order By StartDate DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>".stripslashes($values[1])."<br />\n";
  echo "Start Date: ". date("F j, Y", $values[3]) ."<br />\n";
  echo "End Date: ". date("F j, Y", $values[4])."<br />\n";
  echo "Groups involved: ".stripslashes($values[5])."<br />\n";
  echo "Awards: ".stripslashes($values[7])."<br />\n";
  echo "Description:<br />\n".stripslashes($values[6])."<br />\n";
  echo "Comp Admin: <a href=\"/profile/$values[8]\">".RankAbbrName($values[8], $values[2], 1)."</a><br />\n";
  echo "<a href=\"/compsstats.php?id=$values[0]\">Stats</a></p>\n";
  }
include_once("footer.php");
?>