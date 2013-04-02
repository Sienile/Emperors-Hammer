<?
session_start();
include_once("config.php");
include_once("functions.php");
if(!isset($mysql_link)) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if(isset($_SESSION['EHID']))
  Redirect("menu.php");
if(isset($_POST['pin'])) {
    $id = mysql_real_escape_string($_POST['pin'], $mysql_link);
    $hash_value = hash("sha512", $_POST['pw']);
    $query = "select Member_ID From EH_Members WHERE Member_ID=$id AND UserPassword='$hash_value'";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
        if ($SO->checkAccess($id) > 0){
            $error = "By order of the Security Office, your access to this system has been restricted";   
        }else{
            $_SESSION['EHID'] = $id;
            $SO->addIP("Successful Login", true);
            Redirect("menu.php");
        }
    }else{
        $error= "<p>Error logging in. Try again</p>\n";
    }
}
include_once("nav.php");
echo "<p>Emperor's Hammer Administration Login</p>\n";
if(isset($error))
  echo $error;
echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name=\"myLogin\">\n";
echo " <label for=\"pin\" >PIN</label>: ";
echo " <input type=\"textbox\" name=\"pin\" id=\"pin\" size=\"4\" /><br />\n";
echo " <label for=\"pw\" >Password</label>: ";
echo " <input type=\"password\" name=\"pw\" id=\"pw\" /><br />\n";
echo "<button type=\"submit\" name=\"submit\">Login</button><button name=\"reset\" type=\"reset\">Reset</button>\n";
echo "</form>\n";
echo "<p><a href=\"/resetpassword.php\">Password Reset</a></p>";
//echo "<p>Site login disabled for backup</p>\n";
include_once("footer.php");
?>
