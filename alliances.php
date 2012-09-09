<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>Since there are other Star Wars clubs out there, the Emperor's Hammer has established relationships with some of them at various levels.</p>";
echo "<p>Allies are listed below:</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"20%\">Name</td>";
echo "    <td width=\"60%\">Description</td>";
echo "    <td width=\"20%\">Image</td>";
echo "  </tr>\n";
$query = "SELECT Name, Abbr, Description, SiteURL, Banner From EH_Alliances Where Status=1 Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"20%\">".stripslashes($values[0])." (".stripslashes($values[1]).")</td>";
  echo "    <td width=\"60%\">".stripslashes($values[2])."</td>";
  echo "    <td width=\"20%\"><a href=\"".stripslashes($values[3])."\"><img src=\"".stripslashes($values[2])."\" alt=\"".stripslashes($values[0])." Image\" /></a></td>";
  echo "  </tr>\n";
  }
echo "</table>";
echo "<p>Neutral clubs are listed below:</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"20%\">Name</td>";
echo "    <td width=\"60%\">Description</td>";
echo "    <td width=\"20%\">Image</td>";
echo "  </tr>\n";
$query = "SELECT Name, Abbr, Description, SiteURL, Banner From EH_Alliances Where Status=2 Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"20%\">".stripslashes($values[0])." (".stripslashes($values[1]).")</td>";
  echo "    <td width=\"60%\">".stripslashes($values[2])."</td>";
  echo "    <td width=\"20%\"><a href=\"".stripslashes($values[3])."\"><img src=\"".stripslashes($values[2])."\" alt=\"".stripslashes($values[0])." Image\" /></a></td>";
  echo "  </tr>\n";
  }
echo "</table>";
echo "<p>Enemies are listed below:</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"20%\">Name</td>";
echo "    <td width=\"60%\">Description</td>";
echo "    <td width=\"20%\">Image</td>";
echo "  </tr>\n";
$query = "SELECT Name, Abbr, Description, SiteURL, Banner From EH_Alliances Where Status=3 Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"20%\">".stripslashes($values[0])." (".stripslashes($values[1]).")</td>";
  echo "    <td width=\"60%\">".stripslashes($values[2])."</td>";
  echo "    <td width=\"20%\"><a href=\"".stripslashes($values[3])."\"><img src=\"".stripslashes($values[2])."\" alt=\"".stripslashes($values[0])." Image\" /></a></td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>