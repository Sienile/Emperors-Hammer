<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Academy Graduates";
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
echo "<p>Select the course to see the graduates from the list below:</p>\n";
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Training_ID, Name FROM EH_Training Where Available=1 AND TAc_ID=$id Order By TC_ID, SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  $query1 = "SELECT CT_ID FROM EH_Training_Complete Where Training_ID=$values[0]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    echo "<p><a href=\"/coursegrads.php?id=$values[0]\">".stripslashes($values[1])."</a> [$rows1 graduate";
    if($rows1>1)
      echo "s";
    echo "]</p>\n";
    }
  }
include_once("footer.php"); ?>