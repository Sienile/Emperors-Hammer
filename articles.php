<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>These are article mentions that the Emperor's Hammer has received over the years.</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"25%\">Publication</td>";
echo "    <td width=\"25%\">Name</td>";
echo "    <td width=\"25%\">Image</td>";
echo "    <td width=\"25%\">Date</td>";
echo "  </tr>\n";
$query = "SELECT Publication, Member_ID, Name, Image, Link, DateReceived From EH_Articles Order By DateReceived, Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"25%\">".stripslashes($values[0])."</td>";
  echo "    <td width=\"25%\">";
  if($values[1])
    echo "<a href=\"profile.php?pin=$values[1]\">";
  echo stripslashes($values[2]);
  if($values[1])
    echo "</a>";
  echo "</td>";
  echo "    <td width=\"25%\"><a href=\"$values[4]\">".stripslashes($values[3])."</a></td>";
  echo "    <td width=\"25%\">".date("M j, Y", $values[5])."</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>