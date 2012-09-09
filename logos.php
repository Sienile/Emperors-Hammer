<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>These are various logos that the EH has used.</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"20%\">Name</td>";
echo "    <td width=\"60%\">Image</td>";
echo "    <td width=\"20%\">Date</td>";
echo "  </tr>\n";
$query = "SELECT Images_ID, Name, DateSubmitted From EH_Images WHERE IC_ID=2 Order By DateSubmitted";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"20%\">".stripslashes($values[1])."</td>";
  echo "    <td width=\"60%\"><img src=\"image.php?id=$values[0]\" alt=\"".stripslashes($values[1])."\" /></td>";
  echo "    <td width=\"20%\">".date("M j, Y", $values[2])."</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>