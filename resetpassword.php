<?
session_start();
include_once("config.php");
include_once("functions.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if(isset($_POST['pin'])) {
  $id = mysql_real_escape_string($_POST['pin'], $mysql_link);
  $SO->addIP("Password Reset, for E-mail: $id");
  $query = "select Member_ID, Email, Name From EH_Members WHERE Email='$id'";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $pin = $values[0];
    $email = stripslashes($values[1]);
    $name = stripslashes($values[2]);
    srand(time());
    $pool = "ABCDEFGHIJKLMNOPQRSTUZWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "1234567890";
    $pool .="!@#$%^&*()_-+=[]{};:<>,./?|`~";
    $pw="";
    for($l=0; $l<10; $l++) {
      $pw .= substr($pool, (rand()%(strlen($pool))), 1);
      }
    $hash_value = hash("sha512", $pw);
    $query1 = "UPDATE EH_Members Set UserPassword='$hash_value' WHERE Member_ID=$pin";
    $result1 = mysql_query($query1, $mysql_link);
    $error = "<p>New password sent.</p>\n";
    $recipient = "$name <$email>";
    $subject = "EH Password Reset";
    $body = "Your password was reset to: $pw\n";
    $body .= "Your pin is: $pin";
    $body .= "\n\nThis message was generated as an automatic e-mail, if you did not request your password to be reset contact the Security Officer.";
    $grade = mail($recipient, $subject, $body, $headers);
    storeEmail($recipient, '', '', $subject, $body);
    }
  if($rows==0) {
    $error = "<p>E-mail address not found.</p>\n";
    }
  }
include_once("nav.php");
echo "<p>Emperor's Hammer Password Reset</p>\n";
if($error)
  echo $error;
echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name=\"myLogin\">\n";
echo " <label for=\"pin\" >E-Mail Address</label>: ";
echo " <input type=\"textbox\" name=\"pin\" id=\"pin\"><br />\n";
echo "<button type=\"submit\" name=\"submit\">Reset Passsword</button><button name=\"reset\" type=\"reset\">Reset Form</button>\n";
echo "</form>\n";
echo "<p><a href=\"/login.php\">Return to the login page</a></p>";
include_once("footer.php");
?>