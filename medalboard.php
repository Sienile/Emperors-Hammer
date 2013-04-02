<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if(isset($_POST['medal'])) {
  $val = mysql_real_escape_string($_POST['medal'], $mysql_link);
  $query = "SELECT Name, MG_ID, Image From EH_Medals Where Medal_ID=$val";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $name = stripslashes($values[0]);
    if($values[1]) {
      $query1 = "SELECT Name From EH_Medals_Groups WHERE MG_ID=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $name = stripslashes($values1[0]). " - ". $name;
        }
      }
      $img = stripslashes($values[2]);
    }
  echo "<p>Your search for ".$name. " revealed the following results:<br />\n";
  if($img)
    echo "<img src=\"/images/medals/$img\" alt=\"$name Image\" /><br />\n";
  $query = "SELECT EH_Medals_Complete.Member_ID, Count(EH_Medals_Complete.MC_ID) As MCount, EH_Medals_Complete.Group_ID FROM EH_Medals_Complete, EH_Members WHERE EH_Medals_Complete.Medal_ID=$val AND EH_Medals_Complete.Member_ID=EH_Members.Member_ID Group By EH_Medals_Complete.Member_ID Order By MCount DESC, EH_Members.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows==0)
    echo "No matches found";
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    if(isInGroup($values[2], $values[0]))
      $group = $values[2];
    else
      $group = PriGroup($values[0]);
    echo "<a href=\"/profile/$values[0]\">".RankAbbrName($values[0], $group, 1)."</a> x$values[1]<br />\n";
    }
  echo "</p>";
  echo "<p>&nbsp;</p>\n";
  }
echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">\n";
echo "<p>To see a Listing</p>";
echo "<p><label for=\"medal\">Medal:</label> ";
echo "<select name=\"medal\" id=\"medal\">\n";
$query = "SELECT Medal_ID, Name, Group_ID, MG_ID From EH_Medals Order By Group_ID, SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <option value=\"$values[0]\">";
  if($values[3]) {
    $query1 = "SELECT Name From EH_Medals_Groups WHERE MG_ID=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      echo stripslashes($values1[0])." - ";
      }
    }
  echo stripslashes($values[1])." (".GroupName($values[2]).")</option>\n";
  }
echo "</select></p>\n";
echo "<p><button type=\"submit\" name=\"Submit\">Submit</button>";
echo "<button name=\"reset\" type=\"reset\">Reset</button></p>\n";
echo "</form>\n";
include_once("footer.php");
?>