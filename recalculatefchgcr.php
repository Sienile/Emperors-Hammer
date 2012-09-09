<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "fchgadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
?>
<p>Emperor's Hammer FCHG/Combat Rating Points Recalculation</p>
<p><a href="menu.php">Return to the administration menu</a></p>
<?
$query = "SELECT Member_ID, Name From EH_Members WHERE Member_ID=382 OR Member_ID=683 Order By Member_ID";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $pts = 0;
  $cr = 0;
  //Every Mission Flown
  $query1 = "SELECT SUM(NumMissions) FROM EH_Battles WHERE Battle_ID IN ( SELECT DISTINCT Battle_ID FROM EH_Battles_Complete WHERE Member_ID =$values[0] AND STATUS =1)";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0];
    }
  //Mission High Score = 2pts
  $query1 = "SELECT Count(Mission_ID) From EH_Battles_Missions WHERE HS_Holder=$values[0]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0]*2;
    }
  //Battle High Score = 2ptsxnum missions
  $query1 = "SELECT NumMissions From EH_Battles WHERE HS_Holder=$values[0] AND NumMissions>1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0]*2;
    }
  //IS-BW = 1 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$values[0] AND Medal_ID=130";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0];
    }
  //IS-SW = 3 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$values[0] AND Medal_ID=132";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += 3*$values1[0];
    }
  //IS-GW = 5 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$values[0] AND Medal_ID=134";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += 5*$values1[0];
    }
  //IS-PW = 10 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$values[0] AND Medal_ID=136";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += 10*$values1[0];
    }
  //LoC = 1 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$values[0] AND Medal_ID=137";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $cr +=$values1[0];
    $pts += $values1[0];
    }
  //DFW = 5 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$values[0] AND Medal_ID=138";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $cr +=$values1[0]*5;
    }
  if($pts) {
    $query1 = "SELECT EMSA_ID FROM EH_Members_Special_Areas WHERE SA_ID=1 AND Member_ID=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "Update EH_Members_Special_Areas Set Value=$pts WHERE EMSA_ID=$values1[0]";
      $result2 = mysql_query($query2, $mysql_link);
      }
    else {
      $query2 = "INSERT INTO EH_Members_Special_Areas  (Member_ID, SA_ID, Value) Values('$values[0]', '1', '$pts')";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  if($cr) {
    $query1 = "SELECT EMSA_ID FROM EH_Members_Special_Areas WHERE SA_ID=2 AND Member_ID=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "Update EH_Members_Special_Areas Set Value=$cr WHERE EMSA_ID=$values1[0]";
      $result2 = mysql_query($query2, $mysql_link);
      }
    else {
      $query2 = "INSERT INTO EH_Members_Special_Areas  (Member_ID, SA_ID, Value) Values('$values[0]', '2', '$cr')";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  }
echo "<p>Recalculation Complete.</p>";
?>
<?
include_once("footer.php");
?>