<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<h2 align=\"center\">Welcome to the Emperor's Hammer Battle Center</h2>\n";
if($_GET['cat'] && $_GET['plt']) {
  $cat = mysql_real_escape_string($_GET['cat'], $mysql_link);
  $plt = mysql_real_escape_string($_GET['plt'], $mysql_link);
  $query = "SELECT Name, Abbr From EH_Platforms WHERE Platform_ID=$plt";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $pltname=stripslashes($values[0]);
    $pltabbr=stripslashes($values[1]);
    }
  $query = "SELECT Name, Abbr From EH_Battles_Categories WHERE BC_ID=$cat";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $catname=stripslashes($values[0]);
    $catabbr=stripslashes($values[1]);
    }
  echo "<p>Welcome to the $catname's $pltname battles! Select a battle from below to find the information for it.</p>";
  $query = "SELECT Battle_ID, BattleNumber, Name From EH_Battles WHERE BC_ID=$cat AND Platform_ID=$plt AND Status=1 Order By BattleNumber";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "<a href=\"battle.php?id=$values[0]\">".BattleName($values[0], 1)."</a><br />\n";
    }
  }
include_once("footer.php");
?>