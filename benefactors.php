<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<center><img src=\"/images/benefact.gif\" alt=\"EH Benefactors\" /></center>\n";
echo "<p>The Emperor's Hammer would like to thank the following people for their contributions over the years:</p>";
echo "<p>William P. Call - The Founder<br />\n<br />\nAnd the following others:</p>\n";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"60%\">Name</td>";
echo "    <td width=\"20%\">Amount</td>";
echo "    <td width=\"20%\">Date</td>";
echo "  </tr>\n";
$query = "SELECT Member_ID, Name, Amount, DateGiven From EH_Benefactors Order By DateGiven, Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"60%\">";
  if($values[0])
    echo "<a href=\"/profile/$values[0]\">";
  echo stripslashes($values[1]);
  if($values[0])
    echo "</a>";
  echo "</td>";
  echo "    <td width=\"20%\">".stripslashes($values[2])."</td>";
  echo "    <td width=\"20%\">".date("M j, Y", $values[3])."</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>