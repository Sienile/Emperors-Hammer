<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "fchgadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
?>
<p>Emperor's Hammer FCHG/Combat Rating Points Recalculation</p>
<p><a href="/menu.php">Return to the administration menu</a></p>
<?
$query = "SELECT Member_ID, Name From EH_Members WHERE Email!='' Order By Member_ID";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  CalculateFCHG($values[0]);
  }
echo "<p>Recalculation Complete.</p>";
?>
<?
include_once("footer.php");
?>