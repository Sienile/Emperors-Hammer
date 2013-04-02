<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Training Course";
include("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Training_ID, Name, Abbr, Description, Min_Training_ID, Min_Rank_ID, Min_Pos_ID, Min_Time, MinPoints, MaxPoints, NotesFile, Ribbon, Grader, TAc_ID FROM EH_Training WHERE Training_ID=$id";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
$acad = stripslashes($values[13]);
echo "<p><b>".stripslashes($values[1])."</b></p>\n";
echo "<p>Course Description:<br />\n";
echo stripslashes($values[3])."</p>\n";
echo "<p>Course Pre-Requisites:<br />\n";
echo "<ul>\n";
$reqs = 0;
if($values[4]) {
  $query1 = "SELECT Training_ID, Name FROM EH_Training WHERE Training_ID=$values[4]";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  echo "  <li>Minimum Certification: <a href=\"/course.php?id=$values1[0]\">".stripslashes($values1[1])."</a></li>\n";
  $mincert=$values1[0];
  $reqs++;
  }
if($values[5]) {
  $query1 = "SELECT Rank_ID, Name, Group_ID FROM EH_Ranks WHERE Rank_ID=$values[5]";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $minrank = $values1[0];
  echo "  <li>Minimum Rank: ".stripslashes($values1[1])." (".GroupName($values1[2]).")</li>\n";
  $reqs++;
  }
if($values[6]) {
  $query1 = "SELECT Position_ID, Name, Group_ID FROM EH_Positions WHERE Position_ID=$values[6]";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $minpos = $values1[0];
  echo "  <li>Minimum Position: ".stripslashes($values1[1])." (".GroupName($values1[2]).")</li>\n";
  $reqs++;
  }
if($values[7]) {
  $mintime=$values[7];
  echo "  <li>Minimum Time in the Emperor's Hammer: $values[7] month";
  if($values[7]>1)
    echo "s";
  echo "</li>\n";
  }
if($reqs==0)
  echo "  <li>No Pre-Requisites for this course.</li>\n";
echo "</ul></p>\n";
echo "<p>Course Structure:<br />\n";
echo "<ul>\n";
echo "  <li>Max Points possible: ".stripslashes($values[9])."</li>\n";
echo "  <li>Min Points needed: ".stripslashes($values[8])."</li>\n";
if($values[12])
  echo "  <li>".Professor($values[13], $values[12])."</li>\n";
echo "</ul></p>\n";
echo "<p>Course Notes:<br />\n";
$query1 = "SELECT TN_ID FROM EH_Training_Notes WHERE Training_ID=$values[0]";
$result1 = mysql_query($query1, $mysql_link);
$rows1 = mysql_num_rows($result1);
if($rows1) {
  echo "To view the online version of the course materials click <font color=\"#cc1000\">&gt;<a href=\"/notes.php?id=$values[0]\">HERE</a>&lt;</font>";
  }
else {
  echo "All course materials can be downloaded from <font color=\"#cc1000\">&gt;<a href=\"$values[10]\">HERE</a>&lt;</font>\n";
  }
echo "</p>\n";
echo "<p>Course Rewards:<br />\n";
echo "<ul>\n";
echo "  <li>$values[8] points passing, and adding to your ID Line";
$query1 = "SELECT Score, TAT_ID, Award_ID FROM EH_Training_Awards WHERE Training_ID=$values[0] Order By Score";
$result1 = mysql_query($query1, $mysql_link);
$rows1 = mysql_num_rows($result1);
for($j = 1; $j <= $rows1; $j++) {
  $values1 = mysql_fetch_row($result1);
  echo "  <li>$values1[0] points: ";
  if($values1[1]==1) { // Medal
    $query2 = "SELECT Name FROM EH_Medals WHERE Medal_ID=$values1[2]";
    $result2 = mysql_query($query2, $mysql_link);
    $values2 = mysql_fetch_row($result2);
    echo " ".stripslashes($values2[0])." Medal";
    }
  elseif($values1[1]==2) {
    $query2 = "SELECT Name FROM EH_Ranks WHERE Rank_ID=$values1[2]";
    $result2 = mysql_query($query2, $mysql_link);
    $values2 = mysql_fetch_row($result2);
    echo " ".stripslashes($values2[0])." Rank";
    }
  echo "</li>\n";
  }
echo "</ul>\n";
echo "</p>\n";
echo "<p>Course Test:<br />\n";
if($_SESSION['EHID']) {
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
else {
  echo "You need to be logged in to take the test.";
  }
echo "</p>\n";
echo "<p><a href=\"/coursegrads.php?id=$values[0]\">Course Graduates</a></p>\n";
include_once("footer.php"); ?>