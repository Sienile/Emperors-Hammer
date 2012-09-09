<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>The Emperor's Hammer Command Staff are the primary people in charge of various activities in the EH. Below is the current list of officers holding the various positions.</p>";
$query = "SELECT Position_ID, Name, Abbr, Description, Banner, isCS, CSOrder From EH_Positions WHERE (isCS=1 OR isCS=2 OR isCS=3) AND Group_ID=1 Order By isCS, CSOrder, SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>";
  if($values[5]==1)
    echo "CS";
  elseif($values[5]==2)
    echo "CA";
  elseif($values[5]==3)
    echo "IA";
  echo "-".stripslashes($values[6])." ";
  echo stripslashes($values[1])." (".stripslashes($values[2]).")<br />\n";
  if($values[4])
    echo "Banner Here<br />\n";
  if($values[3])
    echo stripslashes($values[3])."<br />\n";
  echo "Current ".stripslashes($values[2]).": ";
  $pos=MembersPosition($values[0], 1, "<br />\n");
  if($pos)
    echo $pos;
  else
    echo "TBA";
  echo "<br>\n";
  if($values[4])
    echo "<img src=\"$values[4]\" alt=\"$values[1] Banner\"><br />\n";
  if($values[3])
    echo stripslashes($values[3]);
  echo "</p>\n";
  }
include_once("footer.php");
?>