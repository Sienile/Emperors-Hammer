<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
?>
<h1>Emperor's Hammer Database Import Scripts</h1>
<?
if($_GET['db']=="hf") {
  $groupid=6;
  echo "<p>Beginning Hammer's Fist Data import<br>";
  $newmysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db("emperors_fist", $newmysql_link);

  //Begin Import: HF_Operations
  $query = "SELECT Operation_ID, Name, StartDate, EndDate, Admin_ID, Description FROM HF_Operations";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $start = mysql_real_escape_string($values[2]);
    $end = mysql_real_escape_string($values[3]);
    $desc = mysql_real_escape_string($values[5]);
    if($values[4]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $admin=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Competitions (Name, Admin_ID, Group_ID, StartDate, EndDate, Description) VALUES ('$name', '$admin', '$groupid', '$start', '$end', '$desc')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Operation', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Operations Transferred.<br>";
  //End Import HF_Operations
/*
  //Begin Import: HF_Operations_Participants
  $query = "SELECT Operation_ID, Member_ID, Score FROM HF_Operations_Participants";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Operation' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $comp=$values1[0];
      }
    $score = $values[2];
    $query1 = "INSERT INTO EH_Competitions_Participants (Comp_ID, Member_ID, Score) VALUES ('$comp', '$mem', '$score')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Operations_Participants Transferred.<br>";
  //End Import HF_Operations_Participants
*/
  }
if($_GET['db']=="tc") {
  echo "<p>Beginning TIE Corps Data import</p>\n";
  $newmysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db("emperors_members", $newmysql_link);
  $groupid=2;

  //Begin Import: TC_bugs
  $query = "SELECT Bug_ID, Bug_BattleID, BUG_PIN, BUG_Date, BUG_Content, BUG_Status, BUG_DateProcessed, BUG_Type FROM TC_bugs";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[7]==1) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $battle=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $status = mysql_real_escape_string($values[5]);
      if($status==1)
        $status=0;
      else
        $status=1;
      $update = mysql_real_escape_string($values[6]);
      $query1 = "INSERT INTO EH_Battles_Bugs (Battle_ID, Poster_ID, Date_Added, Description, Status) VALUES ('$battle', '$member', '$date', '$info', '$status')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($values[7]==2) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $status = mysql_real_escape_string($values[5]);
      if($status==1)
        $status=0;
      else
        $status=2;
      $update = mysql_real_escape_string($values[6]);
      $query1 = "INSERT INTO EH_Patches_Bugs (Patch_ID, Member_ID, DateReported, Description, Status) VALUES ('$patch', '$member', '$date', '$info', '$status')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_bugs Transferred.<br>";
  //End Import TC_bugs

  //Begin Import: TC_reviews
  $query = "SELECT R_ID, R_BattleID, R_PIN, R_Date, R_Content, R_Type, R_Rating FROM TC_reviews";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[5]==1) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $battle=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $rate = mysql_real_escape_string($values[6]);
      $query1 = "INSERT INTO EH_Battles_Reviews (Battle_ID, Poster_ID, Date_Added, Description, Status, Rating) VALUES ('$battle', '$member', '$date', '$info', '1', '$rate')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($values[5]==2) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $query1 = "INSERT INTO EH_Patches_Reviews (Patch_ID, Member_ID, DatePosted, Review) VALUES ('$patch', '$member', '$date', '$info')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_reviews Transferred.<br>";
  //End Import TC_reviews

  echo "<p>Items to Fix:<br>\n";
  echo "Fix Positions<br>\n";
  echo "Fixe EH_Ships<br>\n";
  echo "Fixe Categories for Training<br>\n";
  echo "</p>\n";
  }
?>
Fix All Medals in Sort Order, remove duplicates<br>
Fix All Access for all Positions<br>
<a href="import.php?db=hf">Import Hammer's Fist Database</a><br>
<a href="import.php?db=tc">Import TIE Corps Database</a><br>
<?
include_once("footer.php");
?>