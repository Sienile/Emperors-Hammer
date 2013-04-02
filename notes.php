<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Course Notes";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Training_ID, Name FROM EH_Training WHERE Training_ID=$id";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
echo "<p>Course Notes for: <a href=\"/course.php?id=$values[0]\">".stripslashes($values[1])."</a></p>\n";
if($_SESSION['EHID']) {
$query = "SELECT Training_ID, Name, Abbr, Description, Min_Training_ID, Min_Rank_ID, Min_Pos_ID, Min_Time, MinPoints, MaxPoints, NotesFile, Ribbon, Grader, TAc_ID FROM EH_Training WHERE Training_ID=$id";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
$acad = stripslashes($values[13]);
if($values[4]) {
  $query1 = "SELECT Training_ID, Name FROM EH_Training WHERE Training_ID=$values[4]";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $mincert=$values1[0];
  $reqs++;
  }
if($values[5]) {
  $query1 = "SELECT Rank_ID, Name, Group_ID FROM EH_Ranks WHERE Rank_ID=$values[5]";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $minrank = $values1[0];
  $reqs++;
  }
if($values[6]) {
  $query1 = "SELECT Position_ID, Name, Group_ID FROM EH_Positions WHERE Position_ID=$values[6]";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $minpos = $values1[0];
  $reqs++;
  }
if($values[7]) {
  $mintime=$values[7];
  }
  $allow=true;
  $error="";
  $query1 = "SELECT Group_ID FROM EH_Training_Academies WHERE TAc_ID=$acad";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $group = $values1[0];
  if($minrank) {
    $query1 = "SELECT Rank_ID FROM EH_Members_Ranks WHERE Member_ID=".$_SESSION['EHID']." AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $rank=$values1[0];
      }
    else {
      $rank=0;
      }
    $query1 = "SELECT Rank_ID, Name, SortOrder FROM EH_Ranks Where Rank_ID=$rank";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $rso=$values1[2];
      }
    else
      $rso=0;
    $query1 = "SELECT Rank_ID, Name, SortOrder FROM EH_Ranks Where Rank_ID=$minrank AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      if($values1[2]>=$rso) {
        $allow=false;
        $error .= "Minimum Rank required: ".stripslashes($values1[1]).".<br />\n";
        }
      }
    }
  if($minpos) {
    $query1 = "SELECT Position_ID FROM EH_Members_Positions WHERE Member_ID=".$_SESSION['EHID']." AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j = 1; $j <= $rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $positions[]=$values1[0];
      }
    if(is_array($positions))
      $pos = implode(" OR Position_ID=", $positions);
    else
      $pos=0;
    $poscount = 0;
    $query1 = "SELECT SortOrder, Name FROM EH_Positions Where Position_ID=$minpos AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $values1 = mysql_fetch_row($result1);
    $minso= $values1[0];
    $minname = $values1[1];
    $query1 = "SELECT SortOrder FROM EH_Positions WHERE Position_ID=$pos AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j = 1; $j <= $rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      if($values1[0]>=$minso)
        $poscount++;
      }
    if($poscount==0) {
      $allow=false;
      $error .="Minimum Position Required: ".stripslashes($minname).".<br />\n";
      }
    }
  if($mincert) {
    $query1 = "SELECT CT_ID FROM EH_Training_Complete WHERE Training_ID=$mincert AND Member_ID=".$_SESSION['EHID'];
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if(!$rows1) {
      $allow=false;
      $query1 = "SELECT Training_ID, Name FROM EH_Training WHERE Training_ID=$mincert";
      $result1 = mysql_query($query1, $mysql_link);
      $values1 = mysql_fetch_row($result1);
      $error .= "Minimum Training Certification required: <a href=\"/course.php?$values1[0]\">".stripslashes($values1[1])."</a>.<br />\n";
      }
    }
  $query1 = "SELECT Min(JoinDate) FROM EH_Members_Groups WHERE Member_ID=".$_SESSION['EHID'];
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $join=$values1[0];
  if($mintime && ((time()-$join)<($mintime*30*24*60*60))) {
    $error.="Mimimum time in the Emperor's Hammer required: $mintime months(s).<br />\n";
    $allow=false;
    }
  $query1 = "SELECT CT_ID FROM EH_Training_Complete WHERE Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID'];
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $allow=false;
    $error .= "You have already completed this course. To view your answers, if stored, click <font color=\"#cc1000\">&gt;<a href=\"/viewtest.php?id=$values[0]\">HERE</a>&lt;</font>.<br />\n";
    }
  $query1 = "SELECT Status FROM EH_Training_Exams_Complete WHERE Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID'];
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  $values1 = mysql_fetch_row($result1);
  if($values1[0]==2) {
    $allow=false;
    $error.="You have already submited this course and is awaiting grading. To view your answers click <font color=\"#cc1000\">&gt;<a href=\"/viewtest.php?id=$values[0]\">HERE</a>&lt;</font>.<br />\n";
    }
  if($allow)
    echo "<a href=\"/test.php?id=$values[0]\">Take Test</a>.";
  else
    echo "The following errors need to be resolved before taking this test:<br />\n$error";
  }
echo "<p>Sections:<br />\n";
$query = "SELECT TN_ID, SectionName FROM EH_Training_Notes WHERE Training_ID=$id Order By SortOrder";
$result = mysql_query($query,$mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<a href=\"#$values[0]\">".stripslashes($values[1])."</a><br />\n";
  }
echo "</p>\n<hr>\n<br />\n";
$query = "SELECT TN_ID, SectionName, SectionText FROM EH_Training_Notes WHERE Training_ID=$id Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p align=\"center\"><b><a name=\"$values[0]\">".stripslashes($values[1])."</a></b></p>\n";
  echo "<p>".stripslashes($values[2])."</p>\n";
}
include_once("footer.php"); ?>