<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$pin=mysql_real_escape_string($_GET['pin']);
echo "<a href=\"/menu.php\">Return to the Administration Menu</a><br />\n";
if($pin)
  echo "PIN: $pin new FCHG Points: ".CalculateFCHG($pin) ."<br />\n";
echo "<form method=\"GET\" action=\"".$_SERVER['PHP_SELF']."\">\n";
echo " <label for=\"pin\" >PIN</label>: ";
echo " <input type=\"textbox\" name=\"pin\" id=\"pin\" size=\"4\" /><br />\n";
echo "<button type=\"submit\" name=\"submit\">Recalculate</button><button name=\"reset\" type=\"reset\">Reset</button>\n";
echo "</form>\n";
include("footer.php");
?>