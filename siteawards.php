<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>These are site awards that the Emperor's Hammer has received over the years.</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"40%\">Name/Description</td>";
echo "    <td width=\"20%\">Reason</td>";
echo "    <td width=\"20%\">Image</td>";
echo "    <td width=\"20%\">Date</td>";
echo "  </tr>\n";
$query = "SELECT Name, Description, Reason, DateAwarded, Banner, Link From EH_Site_Awards Order By DateAwarded, Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"40%\">".stripslashes($values[0])." - ".stripslashes($values[1])."</td>";
  echo "    <td width=\"20%\">".stripslashes($values[2])."</td>";
  echo "    <td width=\"20%\"><a href=\"".stripslashes($values[5])."\"><img src=\"".stripslashes($values[4])."\" alt=\"".stripslashes($values[0])." Image\" /></a></td>";
  echo "    <td width=\"20%\">".date("M j, Y", $values[3])."</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>