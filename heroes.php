<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>These are some of the people that have been recognized as key personell in the history of the Emperor's Hammer as its heroes.</p>";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"25%\">Name</td>";
echo "    <td width=\"75%\">Reason</td>";
echo "  </tr>\n";
$query = "SELECT Name, Member_ID, Reason From EH_Heroes Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"25%\">";
  if($values[0])
    echo "<a href=\"profile.php?pin=$values[0]\">";
  echo stripslashes($values[1]);
  if($values[0])
    echo "</a>";
  echo "</td>";
  echo "    <td width=\"75%\">".stripslashes($values[2])."</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>