<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$type = mysql_real_escape_string($_GET['type'], $mysql_link);
echo "<p>These are various servers the Emperor's Hammer utilizes for";
if($type==0)
  echo "communication";
else
  echo "gaming";
echo ".</p>";
$query = "SELECT Name, Address, Port, Password, Notes, URL From EH_Servers WHERE ServerType=$type Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<p><i>".stripslashes($values[0])."</i><br>\n\n";
  echo "Address: ".stripslashes($values[1]);
  if($values[2])
    echo ":".stripslashes($values[2]);
  echo "<br>\n";
  if($_SESSION['EHID'] && $values[3])
    echo "Password: ".stripslashes($values[3])."<br>\n";
  if($values[5])
    echo "URL for Server Software: ".stripslashes($values[5])."<br>\n";
  echo "Notes: ".stripslashes($values[4])."</p>\n";
  }
include_once("footer.php");
?>