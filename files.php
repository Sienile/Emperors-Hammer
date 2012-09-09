<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$query = "SELECT Group_ID, Name From EH_Groups Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
$space="&nbsp;&nbsp;";
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p>".stripslashes($values[1])." Files<br />";
  $count=0;
  $query1 = "SELECT FC_ID, Name From EH_Files_Categories WHERE Group_ID=$values[0] Order By SortOrder";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  $count+=$rows1;
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    echo $space.stripslashes($values1[1]).":<br />";
    $query2 = "SELECT Name, Filename, Description, DateAdded From EH_Files WHERE FC_ID=$values1[0] Order By DateAdded";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    $count+=$rows2;
    if($rows2==0)
      echo $space.$space."No files in this category<br />";
    for($k=0; $k<$rows2; $k++) {
      $values2 = mysql_fetch_row($result2);
      echo $space.$space."<a href=\"$values2[1]\">".stripslashes($values2[0])."</a><br />\n";
      echo $space.$space.$space.stripslashes($values2[2])."<br />\n";
      if($values2[3])
        echo $space.$space.$space."Date Added: ".date("M j, Y", $values2[3])."<br />\n";
      }
    }
  if($count==0)
    echo "No Files or File Categories currently available";
  echo "</p>\n";
  }
include_once("footer.php");
?>