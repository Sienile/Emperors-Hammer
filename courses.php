<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Training Categories";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if($_GET['cat'])
  $cat = mysql_real_escape_string($_GET['cat'], $mysql_link);
else
  $cat=0;
if($_GET['id'])
  $acad = mysql_real_escape_string($_GET['id'], $mysql_link);
else
  $acad=0;
$query = "SELECT TC_ID, Name, Description FROM EH_Training_Categories WHERE TC_ID=$cat";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
$name = $values[1];
echo "<p><b>".stripslashes($values[1])."</b><br>\n";
echo stripslashes($values[2])."</p>\n";

$query = "SELECT TC_ID, Name, Description FROM EH_Training_Categories WHERE Active=1 AND Master_ID=$values[0] AND TCa_ID=$acad Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows)
  echo "<p>Sub Categories:</p>\n";
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p><a href=\"/courses.php?cat=$values[0]&id=$acad\">".stripslashes($values[1])."</a><br>\n";
  echo stripslashes($values[2])."</p>\n";
  }
echo "<p>Courses in ".stripslashes($name).":</p>\n";
$query = "SELECT Training_ID, Name, Description FROM EH_Training WHERE TC_ID=$cat AND Available=1 Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p><a href=\"/course.php?id=$values[0]\">".stripslashes($values[1])."</a>";
  if($_SESSION['EHID']) {
    $query1 = "SELECT CT_ID FROM EH_Training_Complete WHERE Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID'];
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1)
      echo " - <font color=\"#FF0000\">Course Completed</font>";
    else {
      $query1 = "SELECT Status FROM EH_Training_Exams_Complete WHERE Training_ID=$values[0] AND Member_ID=".$_SESSION['EHID'];
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      $values1 = mysql_fetch_row($result1);
      if($values1[0]==1)
        echo " - <font color=\"#00FF00\">Course In Progress</font>";
      if($values1[0]==2)
        echo " - <font color=\"#FFFF00\">Course Awaiting Grading</font>";
      }
    }
  echo "<br>\n";
  echo stripslashes($values[2])."</p>\n";
  }
include("footer.php"); ?>