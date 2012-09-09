<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<h2 align=\"center\">Welcome to the Emperor's Hammer Battle Center</h2>
<p>Here at the Emperor's Hammer Battle Center, you will find everything from statistical information about all of the Emperor's Hammer custom battles, with a variety of features available to members of the Emperor's Hammer to tools on creating your own missions!</p>
<p>All of our custom battles have been organized into categories, per platform and subgroup of the Emperor's Hammer. This Battle Center currently contains over <b>4,000 custom-made missions</b> by members of the Emperor's Hammer Strike Fleet!
<p>More information about new battles, battle submission, cheating policy, how to play battles, and other rules and procedures related to using custom missions and battles can be found at the <a href=\"http://tac.emperorshammer.org\" target=\"_blank\">Tactical Office</a>.
<p><b><font color=\"yellow\">IMPORTANT</font></b><br>
If you wish to play XWA battles, be sure to check for a 'patch*.txt' file to determine if a patch is needed. If so, you can download the patch(es) from the Science Office website.<br>Science Office Patch Archive: <a href=\"http://sco.tiranto.com/sco/patch.html\" target=\"_NEW\">click here</a><br>Super XP Installer: <a href=\"http://sco.tiranto.com/sco/files/xwa/XPInstaller.zip\">click here</a><br><i>note: in the newer battles, the patch information is located within the readme.txt file</i></p>\n";
echo "<b><font color=\"red\">Emperor's Hammer Mission Categories:</font></b><br />\n";
$query = "SELECT Platform_ID, Name, Abbr From EH_Platforms Order By Name";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $query2 = "SELECT Battle_ID From EH_Battles WHERE Platform_ID=$values[0]";
  $result2 = mysql_query($query2, $mysql_link);
  $rows3 = mysql_num_rows($result2);
  if($rows3)
    echo "<p>$values[1] ($values[2]) Battles By Platform:<br />\n";
  $query1 = "SELECT BC_ID, Name, Abbr From EH_Battles_Categories Order By SortOrder";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "SELECT Battle_ID From EH_Battles WHERE BC_ID=$values1[0] AND Platform_ID=$values[0] And Status=1";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      echo "<a href=\"battlescat.php?cat=$values1[0]&amp;plt=$values[0]\"><abbr title=\"".stripslashes($values[1])."\">".stripslashes($values[2])."</abbr>-<abbr title=\"".stripslashes($values1[1])."\">".stripslashes($values1[2])."</abbr> ($rows2 ";
      if($values1[0]==11)
        echo "Mission";
      else
        echo "Battle";
      if($rows2>1)
        echo "s";
      echo ")</a><br />\n";
      }
    }
  if($rows3)
    echo "</p>";
  }
echo"<br />
<br />
<b><font color=\"yellow\">Important:</font></b>
<ul>
  <li>In TIE Fighter, pilots are given points for hitting something with their lasers. To prevent pilots from endlessly firing lasers at friendly ships, the scores for TIE Fighter are recalculated before they are processed, the so-called \"laserless scoring\". Laserless scoring applies to all high scores and competitions. <font color=\"#EEEEEE\">Laser-less Score = Total Score - (Laser Hits * 3)</font></li>
  <li>In order to fly the Emperor's Hammer custom missions for TIE Fighter, X-wing vs. TIE Fighter, Balance of Power and X-wing Alliance, you need to have the <font color=\"#EEEEEE\">Emperor's Hammer Battle Launcher</font> installed to decrypt the missions. The Battle Launcher is only available to members of the Emperor's Hammer. <a href=\"http://www.emperorshammer.org/tc/downloads/EHBL-win.zip\">Download the EHBL here</a>.</li>
</ul>
<p>The Emperor's Hammer Battle Center, Mission Compendium, FCHG list, Pilot kill board and Squadron citations list are maintained by the Emperor's Hammer Tactical Officer. Currently, the Tactical Officer is <a href=\"profile.php?pin=382\">HA Anahorn Dempsey</a>.</p>\n";
include_once("footer.php");
?>