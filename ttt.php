<?
header("Content-Type: application/xml; charset=ISO-8859-1");
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
$id = mysql_real_escape_string($_GET['pin'], $mysql_link);
mysql_select_db($db_name, $mysql_link);
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
echo "<ttt pin=\"$id\">\n";
$query = "select Name FROM EH_Members WHERE Member_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  echo "  <name>".stripslashes($values[0])."</name>\n";
  //Loop over Groups - Rank, Position, Unit
  $query1 = "select EH_Groups.Group_ID, EH_Groups.Name, EH_Groups.Abbr FROM EH_Members_Groups, EH_Groups WHERE EH_Members_Groups.Group_ID=EH_Groups.Group_ID AND EH_Members_Groups.Member_ID=$id Order By EH_Groups.Group_ID";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    echo "  <group id=\"".stripslashes($values1[0])."\" name=\"".stripslashes($values1[1])."\" abbr=\"".stripslashes($values1[2])."\">\n";
    echo "    <positions>\n";
    $query2 = "select EH_Positions.Position_ID, EH_Positions.Name, EH_Positions.Abbr FROM EH_Members_Positions, EH_Positions WHERE EH_Members_Positions.Position_ID=EH_Positions.Position_ID AND EH_Members_Positions.Member_ID=$id AND EH_Members_Positions.Group_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    for($k=0; $k<$rows2; $k++) {
      $values2 = mysql_fetch_row($result2);
      echo "      <position id=\"".stripslashes($values2[0])."\" name=\"".stripslashes($values2[1])."\" abbr=\"".stripslashes($values2[2])."\" />\n";
      }
    echo "    </positions>\n";
    $query2 = "select EH_Ranks.Rank_ID, EH_Ranks.Name, EH_Ranks.Abbr FROM EH_Members_Ranks, EH_Ranks WHERE EH_Members_Ranks.Rank_ID=EH_Ranks.Rank_ID AND EH_Members_Ranks.Member_ID=$id AND EH_Members_Ranks.Group_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      echo "    <rank id=\"".stripslashes($values2[0])."\" name=\"".stripslashes($values2[1])."\" abbr=\"".stripslashes($values2[2])."\" />\n";
      }
    $query2 = "select EH_Units.Unit_ID FROM EH_Members_Units, EH_Units WHERE EH_Members_Units.Unit_ID=EH_Units.Unit_ID AND EH_Members_Units.Member_ID=$id AND EH_Members_Units.Group_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      echo "    <unit id=\"".stripslashes($values2[0])."\" name=\"".UnitType($values2[0])."\" />\n";
      }
    echo "  </group>";
    }
  $query1 = "select Value FROM EH_Members_Special_Areas WHERE Member_ID=$id AND SA_ID=1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "select FCHG_ID, Name From EH_FCHG Where Points<=$values1[0] ORDER By Points DESC LIMIT 1";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      echo "  <fchgid>$values2[0]</fchgid>\n";
      echo "  <fchgname>$values2[1]</fchgname>\n";
      }
    }
  $query1 = "select Value FROM EH_Members_Special_Areas WHERE Member_ID=$id AND SA_ID=2";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "select CR_ID, Name From EH_Combat_Ratings Where Points<=$values1[0] ORDER By Points DESC LIMIT 1";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      echo "  <crid>$values2[0]</crid>\n";
      echo "  <crname>$values2[1]</crname>\n";
      }
    }
  echo "  <medals>\n";
  $query1 = "select Medal_ID, Name, Abbr, MG_ID FROM EH_Medals Order By Group_ID, SortOrder";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "select COUNT(MC_ID) FROM EH_Medals_Complete WHERE Member_ID=$id AND Medal_ID=$values1[0] AND Status=1";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      $query3 = "select Name, Abbr From EH_Medals_Groups Where MG_ID=$values1[3]";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      if($rows3) {
        $values3 = mysql_fetch_row($result3);
        $name=stripslashes($values3[0])." ";
        $abbr=stripslashes($values3[1])."-";
        }
      echo "    <medal id=\"".stripslashes($values1[0])."\" name=\"".$name.stripslashes($values1[1])."\" abbr=\"".$abbr.stripslashes($values1[2])."\" count=\"$values2[0]\" />\n";
      }
    }
  echo "  </medals>\n";
  }
echo "</ttt>\n";
?>
