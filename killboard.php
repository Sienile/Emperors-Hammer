<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Kill Board";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$month=date("m");
$year=date("Y");
if(is_string($_POST['month']) && strlen($_POST['month'])>0)
  $month=mysql_real_escape_string($_POST['month'], $mysql_link);
if($month>12)
  $month = date("m");
if($month < 1)
  $month = date("m");
if(is_string($_POST['year']) && strlen($_POST['year'])>0)
  $year=mysql_real_escape_string($_POST['year'], $mysql_link);
if($year>date("Y"))
  $year = date("Y");
if($year < 1)
  $year = date("Y");
$startdate = mktime(0,0,0,$month,1, $year);
$enddate = mktime(0,0,0,$month+1,1, $year);
echo "<p>Welcome to the killboard! See who the hottest pilot for the month starting on: ". date("F Y", $startdate).". This board shows the number of missions flown by members in a particular month.</p>";
$query = "SELECT EH_Battles_Complete.Member_ID, SUM(EH_Battles.NumMissions) As Score FROM EH_Battles_Complete, EH_Battles WHERE EH_Battles_Complete.Battle_ID=EH_Battles.Battle_ID AND EH_Battles_Complete.Date_Completed>=$startdate AND EH_Battles_Complete.Date_Completed<=$enddate AND EH_Battles_Complete.Status=1 Group By Member_ID Order by Score DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "$i) <a href=\"profile.php?pin=$values[0]\">".RankAbbrName($values[0], PriGroup($values[0]), 1)."</a> - $values[1]<br />\n";
  }
echo "<p>Annual Totals are:<br />\n";
$startdate = mktime(0,0,0,1,1, $year);
$enddate = mktime(0,0,0,1,1, $year+1);
$query = "SELECT EH_Battles_Complete.Member_ID, SUM(EH_Battles.NumMissions) As Score FROM EH_Battles_Complete, EH_Battles WHERE EH_Battles_Complete.Battle_ID=EH_Battles.Battle_ID AND EH_Battles_Complete.Date_Completed>=$startdate AND EH_Battles_Complete.Date_Completed<=$enddate AND EH_Battles_Complete.Status=1 Group By Member_ID Order by Score DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "$i) <a href=\"profile.php?pin=$values[0]\">".RankAbbrName($values[0], PriGroup($values[0]), 1)."</a> - $values[1]<br />\n";
  }
echo "</p>\n";
echo "<form method=\"post\" action=\"killboard.php\">\n";
echo "Select: Month: <select name=\"month\">\n";
echo "<option value=\"1\">January</option>\n";
echo "<option value=\"2\">February</option>\n";
echo "<option value=\"3\">March</option>\n";
echo "<option value=\"4\">April</option>\n";
echo "<option value=\"5\">May</option>\n";
echo "<option value=\"6\">June</option>\n";
echo "<option value=\"7\">July</option>\n";
echo "<option value=\"8\">August</option>\n";
echo "<option value=\"9\">September</option>\n";
echo "<option value=\"10\">October</option>\n";
echo "<option value=\"11\">November</option>\n";
echo "<option value=\"12\">December</option>\n";
echo "</select> | ";
echo "Year: <select name=\"year\">\n";
for($i=0; $i<10; $i++) {
  $date = date("Y")-$i;
  echo "<option value=\"$date\">$date</option>\n";
  }
echo "</select>";
echo "<button type=\"submit\" name=\"submit\">Submit</button>\n";
echo "</form>\n";
include_once("footer.php"); ?>