<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Training Exam";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Training_ID, Name FROM EH_Training WHERE Training_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  $values = mysql_fetch_row($result);
  $name = RankAbbrName($_SESSION['EHID'], PriGroup($_SESSION['EHID']), 1);
  echo "<p>Welcome $name, to the <a href=\"/course.php?id=$values[0]\">".stripslashes($values[1])."</a> Course Exam.</p>\n";
  echo "<form action=\"test1.php\" method=\"POST\">\n";
  echo "  <input type=\"hidden\" name=\"examid\" value=\"$values[0]\" />\n";
  echo "  <input type=\"hidden\" name=\"userid\" value=\"".$_SESSION['EHID']."\" />\n";
  $query1 = "SELECT TE_ID, Question, Type, Choices, Points FROM EH_Training_Exams WHERE Training_ID=$values[0] Order By SortOrder, TE_ID";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j = 1; $j <= $rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "SELECT Answer FROM EH_Training_Exams_Complete WHERE Member_ID=".$_SESSION['EHID']." AND TE_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      $answer = stripslashes($values2[0]);
      }
    else
      $answer = "";
    echo "<p>$j) ".stripslashes($values1[1])." [".stripslashes($values1[4])."]<br />\n";
    if($values1[2]==2) {
      echo "<input type=\"text\" name=\"$values1[0]\" value=\"$answer\" />";
      }
    elseif($values1[2]==1) {
      echo "<textarea name=\"$values1[0]\" style=\"width:400px\">$answer</textarea>";
      }
    elseif($values1[2]==0) {
      $choices = explode(",", $values1[3]);
      for($q=0; $q<count($choices); $q++) {
        echo "<input type=\"radio\" id=\"$values1[0]$q\" name=\"$values1[0]\"";
        if($choices[$q]==$answer)
          echo " checked=\"checked\"";
        echo " value=\"".stripslashes($choices[$q])."\" type=\"radio\" />";
        echo "<label for=\"$values1[0]$q\">".chr($q+65).") ".stripslashes($choices[$q])."</label><br />\n";
        }
      }
    echo "</p>\n";
    }
  echo "<p><input type=\"submit\" name=\"Submit\" value=\"Submit for Grading\" /><input type=\"submit\" name=\"Save\" value=\"Save for Later\" />";
  echo "<input  name=\"reset\" type=\"reset\" value=\"Reset\" /></p>\n";
  echo "</form>\n";
  }
include_once("footer.php");
?>