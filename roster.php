<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$id = mysql_real_escape_string($_GET['group'], $mysql_link);
if(isset($_GET['view']))
  $view = mysql_real_escape_string($_GET['view'], $mysql_link);
else
  $view=0;
$query = "SELECT Group_ID, Name FROM EH_Groups WHERE Group_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  echo "<h2>".stripslashes($values[1])."'s Roster</h2>\n";
  echo "<p align=\"right\">Roster Counts Active Members: ";
  $query = "SELECT EH_Members_Units.Member_ID FROM EH_Members_Units, EH_Units, EH_Units_Types WHERE EH_Members_Units.Group_ID=$id AND EH_Members_Units.Unit_ID=EH_Units.Unit_ID AND EH_Units.UT_ID=EH_Units_Types.UT_ID AND EH_Units_Types.UT_ID!=2 AND EH_Units_Types.UT_ID!=1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  echo $rows;
  echo "</p>";
  echo "<p align=\"right\">Roster Counts Active+Training Members: ";
  $query = "SELECT EH_Members_Units.Member_ID FROM EH_Members_Units, EH_Units, EH_Units_Types WHERE EH_Members_Units.Group_ID=$id AND EH_Members_Units.Unit_ID=EH_Units.Unit_ID AND EH_Units.UT_ID=EH_Units_Types.UT_ID AND EH_Units_Types.UT_ID!=2";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  echo $rows;
  echo "</p>";
  echo "<p align=\"right\">Roster Counts including Reserves: ";
  $query = "SELECT Member_ID FROM EH_Members_Groups WHERE Group_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  echo $rows;
  echo "</p>";
  Roster($id, 0,0,$view);
  echo "<a href=\"/search.php\">Search the Roster</a> | <a href=\"/login.php\">Administration</a> | ";
  if ($view)
    echo "<a href=\"/roster.php?view=0&amp;group=$id\">View None</a>";
  else
    echo "<a href=\"/roster.php?view=1&amp;group=$id\">View All</a>";
  }
else {
  echo "<p>The page you were looking for does not exist</p>";
  }
include_once("footer.php");
?>