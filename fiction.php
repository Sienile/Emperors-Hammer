<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
echo "  <tr>\n";
echo "    <td width=\"40%\"><a href=\"/fiction.php?sort=0\">Title</a></td>";
echo "    <td width=\"40%\"><a href=\"/fiction.php?sort=1\">Author</a></td>";
echo "    <td width=\"20%\"><a href=\"/fiction.php?sort=2\">Date</a></td>";
echo "  </tr>\n";
$query = "SELECT EH_Fiction.Fiction_ID, EH_Members.Member_ID, EH_Members.Name, EH_Fiction.Title, EH_Fiction.DatePosted From EH_Fiction, EH_Members WHERE EH_Fiction.Approved=1 AND EH_Fiction.Member_ID=EH_Members.Member_ID";
if($_GET['sort']==0) {
  $query.=" Order By EH_Fiction.Title";
  }
elseif($_GET['sort']==1) {
  $query.=" Order By EH_Members.Name";
  }
else {
  $query.=" Order By EH_Fiction.DatePosted";
  }
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "  <tr>\n";
  echo "    <td width=\"40%\"><a href=\"/story.php?id=$values[0]\">".stripslashes($values[3])."</a></td>";
  echo "    <td width=\"40%\"><a href=\"/profile/$values[1]\">".stripslashes($values[2])."</a></td>";
  echo "    <td width=\"20%\">".date("M j, Y", $values[4])."</td>";
  echo "  </tr>\n";
  }
echo "</table>";
include_once("footer.php");
?>