<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>There are many groups that make up the Emperor's Hammer, they vary from troops to dark jedi warriors, to planetary governors. Below is the current list of groups in the Emperor's Hammer</p>";
$query = "SELECT Name, Abbr, LongDesc, Active, Banner From EH_Groups WHERE Active!=2 Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>".stripslashes($values[0])." (".stripslashes($values[1]).")";
  if($values[3]==0)
    echo " - <font color=\"#FF0000\">Inactive</font>";
  echo "<br />\n";
  if($values[4])
    echo "<img src=\"images/".stripslashes($values[4])."\" alt=\"".stripslashes($values[0])." Banner\" /><br />";
  echo stripslashes($values[2])."</p>";
  }
include_once("footer.php");
?>