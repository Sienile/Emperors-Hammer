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
echo "<p>Select a category to choose courses from:</p>\n";
$acad=0;
if($_GET['id']) {
  $acad = mysql_real_escape_string($_GET['id'], $mysql_link);
  }
$query = "SELECT TC_ID, Name, Description FROM EH_Training_Categories WHERE Active=1 AND Master_ID=0 AND TCa_ID=$acad Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p><a href=\"/courses.php?cat=$values[0]&id=$acad\">".stripslashes($values[1])."</a><br>\n";
  echo stripslashes($values[2])."</p>\n";
  }
include_once("footer.php"); ?>