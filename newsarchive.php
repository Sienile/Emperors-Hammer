<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$start=mysql_real_escape_string($_POST['start'], $mysql_link);
if($start) {
  $startdate = $start;
  $enddate = mktime(0,0,0,date("n", $start)+1, 1,date("Y", $start));
  $query = "select News_ID, Topic, Poster, Poster_ID, DatePosted, Body from EH_News where DatePosted>=$startdate AND DatePosted<=$enddate Order By DatePosted DESC";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=1; $i<=$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "<p><b>".stripslashes($values[1])."</b><br />\n";
    echo "Posted on: ". date("F j, Y", $values[4]) ."<br>\n";
    if($values[3])
      echo "Posted By: <a href=\"/profile/$values[3]\">".stripslashes($values[2])."</a><br />\n";
    else
      echo "Posted By: ".stripslashes($values[2])."<br />\n";
    echo stripslashes($values[5])."<br />\n</p>\n";
    }
  if(!$stories) {
    echo "<p>No news stories available in the selected time period</p>\n";
    }
  }//Display
echo "<form method=\"post\" action=\"newsarchive.php\">\n";
echo "<select name=\"start\">\n";
$monthnum = date("n");
$year = date("Y");
$time = mktime(0, 0, 0, $monnum, 1, $year);
$lastdate = mktime(0, 0, 0, 12, 1, 1995);
while($time>=$lastdate) {
  echo "  <option value=\"$time\">".date("F", $time)." ".date("Y", $time)."</option>";
  $prevmon = date("n", $time)-1;
  $prevyear = date("Y", $time);
  $time = mktime(0, 0, 0, $prevmon, 1, $prevyear);
  }
echo "</select><br>\n";
echo "<button type=\"submit\" name=\"submit\">Search Archive</button><button name=\"reset\" type=\"reset\">Reset</button>\n";
echo "</form>\n";
include("footer.php");
?>