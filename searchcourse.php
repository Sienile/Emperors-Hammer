<?
session_start();
include_once("config.php");
include_once("functions.php");
$textbox=$button=true;
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if(isset($_POST['name'])) {
  $val = mysql_real_escape_string($_POST['name'], $mysql_link);
  echo "<p>Your search for ".$val. " revealed the following results:<br />\n";
  $query = "SELECT Training_ID, Name From EH_Training WHERE Name LIKE '%". $val ."%' OR Description LIKE '%".$valu."%' Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows==0)
    echo "No matches found";
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "<a href=\"/course.php?id=$values[0]\">".stripslashes($values[1])."</a><br />\n";
    }
  echo "</p>";
  echo "<p>&nbsp;</p>\n";
  }
echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">\n";
echo "<p><label for=\"name\">Enter the term that you're searching for in a course:</label></p>";
echo "<input type=\"text\" name=\"name\" id=\"name\" /></p>\n";
echo "<p><button type=\"submit\" name=\"Submit\">Submit</button>";
echo "<button name=\"reset\" type=\"reset\">Reset</button></p>\n";
echo "</form>\n";
include_once("footer.php");
?>