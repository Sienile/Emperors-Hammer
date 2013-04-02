<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<h2 align=\"center\">Welcome to the Emperor's Hammer Patch Archive</h2>\n";
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
  $query = "SELECT Name From EH_Patches_Categories WHERE PC_ID=$cat";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $catname=stripslashes($values[0]);
    }
  echo "<p>Welcome to the $catname's $pltname battles! Select a patch below to find the information for it.</p>";
  $query = "SELECT Patch_ID, Name From EH_Patches WHERE PC_ID=$cat AND Platform_ID=$plt Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "<a href=\"/patch.php?id=$values[0]\">".stripslashes($values[1])."</a><br />\n";
    }
  }
echo "<p>Select a Category, and Game to find available patches.</p>\n";
$query = "SELECT PC_ID, Name From EH_Patches_Categories Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>".stripslashes($values[1])." By Platform:<br />\n";
  $query1 = "SELECT Platform_ID, Name, Abbr From EH_Platforms Order By Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "SELECT Patch_ID From EH_Patches WHERE PC_ID=$values[0] AND Platform_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      echo "<a href=\"/patcharchive.php?cat=$values[0]&amp;plt=$values1[0]\">".stripslashes($values1[1])."</a><br />\n";
      }
    }
  echo "</p>";
  }
include_once("footer.php");
?>