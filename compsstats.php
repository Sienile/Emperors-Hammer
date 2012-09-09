<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$id = mysql_real_escape_string($_GET['id']);
$query = "SELECT Comp_ID, Name, Group_ID From EH_Competitions WHERE Comp_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  echo "Results from: $values[1]<br />\n";
  $query1 = "SELECT Member_ID, Unit_ID, DateParticipated, Comments, Score From EH_Competitions_Participants WHERE Comp_ID=$values[0] Order By Score DESC, DateParticipated";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo RankAbbrName($values1[0], $values[2], 1) ."";
    if($values1[1])
     echo " from: ".UnitType($values1[1]);
    if($values1[4])
      echo " scored: ".stripslashes($values1[4]);
    if($values1[2])
      echo " on ".date("F j, Y", $values1[2]);
    if($values1[3])
     echo " (".stripslashes($values1[3]).")";
    }
  }
else {
  echo "Invalid Selection";
  }
include_once("footer.php");
?>