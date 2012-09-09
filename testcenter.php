<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Test Center";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if($_SESSION['EHID']) {
  echo "<p><a href=\"menu.php\">Return to the Main Administration Menu</a></p>\n";
  echo "<p>Saved/InProgress Tests:<br />\n";
  $count=0;
  $query = "SELECT Training_ID, Name FROM EH_Training";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i = 1; $i <= $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT TEC_ID FROM EH_Training_Exams_Complete Where Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID']." AND Status=1";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $count++;
      echo "<a href=\"test.php?id=$values[0]\">".stripslashes($values[1])."</a><br />\n";
      }
    }
  if($count==0)
    echo "No Courses Currently In Progress.";
  echo "</p>\n";
  echo "<p>Submited/Awaiting Grading:<br />\n";
  $count=0;
  $query = "SELECT Training_ID, Name FROM EH_Training";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i = 1; $i <= $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT TEC_ID FROM EH_Training_Exams_Complete Where Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID']." AND Status=2";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $count++;
      echo "<a href=\"viewtest.php?id=$values[0]\">".stripslashes($values[1])."</a><br />\n";
      }
    }
  if($count==0)
    echo "No Courses Currently Submited/Awaiting Grading.";
  echo "</p>\n";
  echo "<p>Past Submitted Courses:<br />\n";
  $count=0;
  $query = "SELECT Training_ID, Name FROM EH_Training";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i = 1; $i <= $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT TEC_ID FROM EH_Training_Exams_Complete Where Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID']." AND Status=3";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $count++;
      echo "<a href=\"viewtest.php?id=$values[0]\">".stripslashes($values[1])."</a><br />\n";
      }
    }
  if($count==0)
    echo "No Past Courses in the database that the database records exist for.";
  echo "</p>\n";
  }
else {
  echo "<p>You need to be logged in to see the Testing Center and it's various displays</p>\n";
  }
include_once("footer.php"); ?>