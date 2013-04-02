<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$log = mysql_real_escape_string($_GET['log'], $mysql_link);
echo "<p><a href=\"/meetings.php\">Return to Meetings</a></p>\n";
if($log) {
  $query = "SELECT ML_ID, Name, DateofLog, Log FROM EH_Meetings_Logs WHERE ML_ID=$log";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows==0) {
    echo "<p>An error occured.</p>";
    }
  else {
    $values = mysql_fetch_row($result);
    echo "<p>".stripslashes($values[1])."<br>\n";
    echo "From: ".date("F j, Y", $values[2])."<br>\n";
    $log = nl2br($values[3]);
    echo stripslashes($log)."</p>";
    }
  }
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
echo "<p>Welcome to the Emperor's Hammer Meeting Logs archive. Below you can select which meeting you'd like to view the log from.</p>\n";
$query = "SELECT ML_ID, Name, DateofLog FROM EH_Meetings_Logs WHERE Meeting_ID=$id Order By DateofLog DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows==0) {
  echo "<p>There are currently no logs stored for this meeting.</p>";
  }
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>Log from: ".date("F j, Y", $values[2])." <a href=\"/meetinglogs.php?id=$id&amp;log=$values[0]\">".stripslashes($values[1])."</a></p>";
  }
include_once("footer.php");
?>