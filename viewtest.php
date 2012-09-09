<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Training Exam";
include("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Training_ID, Name, MaxPoints FROM EH_Training WHERE Training_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $totalscore=0;
  $values = mysql_fetch_row($result);
  $name = RankAbbrName($_SESSION['EHID'], PriGroup($_SESSION['EHID']), 1);
  echo "<p>Welcome $name, to the <a href=\"course.php?id=$values[0]\">".stripslashes($values[1])."</a> Course Test viewer.</p>\n";
  $query1 = "SELECT TE_ID, Question, Type, Choices, Points FROM EH_Training_Exams WHERE Training_ID=$values[0] Order By SortOrder, TE_ID";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j = 1; $j <= $rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "SELECT Answer, Score, Status FROM EH_Training_Exams_Complete WHERE Member_ID=".$_SESSION['EHID']." AND TE_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      $answer = stripslashes($values2[0]);
      $score = stripslashes($values2[1]);
      $totalscore+=$score;
      $status = $values2[2];
      }
    else
      $answer = "";
    echo "<p>$j) ".stripslashes($values1[1])." [".stripslashes($values1[4])."]<br />\n";
    echo stripslashes($answer)."<br />\n";
    if($score) {
      echo "Score: $score/".stripslashes($values1[4]);
      }
    echo "</p>\n";
    }
  if($status==2)
    echo "Test Awaiting Grading.";
  else
    echo "Test Graded! Final score $totalscore out of ".stripslashes($values[2]);
  }
include_once("footer.php");
?>