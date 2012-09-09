<?
$time_start=microtime(true);
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$daysback=7;
if(is_string($_GET['days']) && strlen($_GET['days'])>0)
   $daysback=mysql_real_escape_string($_GET['days'], $mysql_link);
if($daysback>356)
   $daysback = 356;
if($daysback < 1)
   $daysback=1;
$startdate = mktime(23,59,59,date("m"),date("d")-$q,  date("Y"));
$stories = 0;
for($q=1; $q<=$daysback; $q++) {
  $lastold = mktime (23,49,59,date("m"),date("d")-$q,  date("Y"));
  $query = "select News_ID, Topic, Poster, Poster_ID, DatePosted, Body from EH_News where DatePosted>=$lastold AND DatePosted<=$startdate Order By DatePosted DESC";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $stories+=$rows;
  if($rows) {
    echo "<hr />\n";
    echo "<p align=\"center\"><b>News from: " . date("F j, Y", $startdate) . "</b></p>\n";
    echo "<hr />\n";
    }
  for($i=1; $i<=$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "<p><b>".stripslashes($values[1])."</b><br />\n";
    if($values[3]) {
      echo "Posted By: <a href=\"profile.php?pin=$values[3]\">";
      if($values[2])
        echo stripslashes($values[2]);
      else
        echo RankAbbrName($values[3], PriGroup($values[3]), 1);
      echo "</a><br />\n";
      }
    else
      echo "Posted By: ".stripslashes($values[2])."<br />\n";
    echo stripslashes($values[5])."<br />\n</p>\n";
    if($i+1<=$rows)
      echo "<hr>\n";
    }
  $startdate=$lastold;
  }
if(!$stories) {
  echo "<p>No news stories available in the selected time period</p>\n";
  }
echo "<form method=\"get\" action=\"index.php\">\n";
echo "<input type=\"text\" value=\"$daysback\" name=\"days\" size=\"2\" /> days\n";
echo "</form>\n";
include("footer.php");
$timelen = microtime(true)-$time_start;
echo"Script executed in $timelen";
?>