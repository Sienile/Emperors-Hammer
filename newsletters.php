<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>The Emperor's Hammer and it's various Groups have produced newsletters in the past, the ones that are still available are linked below</p>\n";
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"50%\">Title</td>";
echo "    <td width=\"10%\">Date Rleased</td>";
echo "    <td width=\"20%\">File</td>";
echo "    <td width=\"20%\">Upgraded</td>";
echo "  </tr>\n";
$query = "SELECT Title, OriginalFile, PDFFile, DateReleased From EH_Newsletters Order By Group_ID, DateReleased";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"50%\">".stripslashes($values[0])."</td>";
  echo "    <td width=\"10%\">".date("m.d.y",$values[3])."</td>";
  echo "    <td width=\"20%\"><a href=\"/nls/$values[1]\">".stripslashes($values[1])."</a></td>";
  if($values[2])
    echo "    <td width=\"20%\"><a href=\"/nls/$values[2]\">".stripslashes($values[2])."</a></td>";
  else
    echo "    <td width=\"20%\">Not yet upgraded</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>