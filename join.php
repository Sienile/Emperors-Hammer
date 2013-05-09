<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
$name = mysql_real_escape_string($_POST['name'], $mysql_link);
if(isset($name) && $name!="" && $_POST['age']==0) {
  include_once($_SERVER['DOCUMENT_ROOT'] . "/securimage/securimage.php");
  $securimage = new Securimage();
  if ($securimage->check($_POST['captcha_code']) == false)
    die('The code you entered was incorrect.  Go back and try again.');
  $email = mysql_real_escape_string($_POST['email'], $mysql_link);
  $pw = mysql_real_escape_string($_POST['pw'], $mysql_link);
  $pwh = hash("sha512", $pw);
  $comments = mysql_real_escape_string($_POST['comments'], $mysql_link);
  $recruiter = mysql_real_escape_string($_POST['recruiter'], $mysql_link);
  $query = "INSERT INTO EH_Members (Name, Email, UserPassword) Values ('$name', '$email', '$pwh')";
  $result = mysql_query($query, $mysql_link);
  $newpin = mysql_insert_id($mysql_link);
  $recipient = "$name <$email>";
  $subject = "Welcome to the Emperor's Hammer!";
  $body .= "$name, Welcome to the Emperor's Hammer. You can find information below about the groups you have joined.\n";
  $body .= "To login to the Emperor's Hammer site located at ".$site_host.", you will need both your pin and password\n";
  $body .= "Your pin # is: ".$newpin."\n";
  $body .= "Your requested password is: ".$pw."\n\n";
  $body .= "You can also login to the message boards located at http://www.emperorshammer.org/messageboard with the following login information:\n";
  $body .= "Username: ".mysql_real_escape_string($_POST['username'], $mysql_link)."\n";
  $body .= "Password: $pw\n\n";
  $time=time();
  foreach($_POST['Groups'] as $group) {
    $query = "SELECT Group_ID, Name, Abbr, GroupJoinContact, JoinMailBlurb FROM EH_Groups WHERE Group_ID=$group";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $groupsarr[]=stripslashes($values[1]);
      $groupname=stripslashes($values[1]);
      $groupabbr=stripslashes($values[2]);
      $groupjoinpos=$values[3];
      $groupjoinmailblurb=stripslashes($values[4]);
      }
    $query = "INSERT INTO EH_Members_Groups (Member_ID, Group_ID, Active, JoinDate) Values ('$newpin', '$group', 1, '$time')";
    $result = mysql_query($query, $mysql_link);
    $body .="Group Information for: $groupname ($groupabbr)\n";
    $body .="$groupjoinmailblurb\n";
    $query = "SELECT Rank_ID, Name, Abbr FROM EH_Ranks WHERE Group_ID=$group";
    if($group==3)
      $query .=" AND RT_ID=".$_POST['dborder'];
    $query.=" ORDER By SortOrder LIMIT 1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $rankid=$values[0];
      $rankname=stripslashes($values[1]);
      $rankabbr=stripslashes($values[2]);
      }
    $query = "INSERT INTO EH_Members_Ranks (Rank_ID, Member_ID, Group_ID, PromotionDate) Values ('$rankid', '$newpin', '$group', '$time')";
    $result = mysql_query($query, $mysql_link);
    $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$newpin', '$group', 1, '0-".$rankid."', 'Induction Rank', '$time')";
    $result = mysql_query($query, $mysql_link);
    $body .="Your Group Rank is: $rankname ($rankabbr)\n";

    $query = "SELECT Unit_ID, Name FROM EH_Units WHERE Group_ID=$group AND UT_ID=1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $unitid=$values[0];
      $unitname=stripslashes($values[1]);
      }
    $query = "INSERT INTO EH_Members_Units (Unit_ID, Member_ID, Group_ID, UnitDate) Values ('$unitid', '$newpin', '$group', '$time')";
    $result = mysql_query($query, $mysql_link);
    $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$newpin', '$group', 2, '0-".$unitid."', 'Induction Unit', '$time')";
    $result = mysql_query($query, $mysql_link);
    $body .="Your Group Unit is: $unitname\n";

    $query = "SELECT Position_ID, Name, Abbr FROM EH_Positions WHERE Group_ID=$group AND SortOrder=1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $posid=$values[0];
      $posname=stripslashes($values[1]);
      $posabbr=stripslashes($values[2]);
      }
    $query = "INSERT INTO EH_Members_Positions (Position_ID, Member_ID, Group_ID, PositionDate, isGroupPrimary) Values ('$posid', '$newpin', '$group', '$time', 1)";
    $result = mysql_query($query, $mysql_link);
    $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$newpin', '$group', 3, '0-".$posid."', 'Induction Position', '$time')";
    $result = mysql_query($query, $mysql_link);
    $body .="Your Group Position is: $posname ($posabbr)\n";

    $query = "SELECT Position_ID FROM EH_Positions WHERE Group_ID=$group AND (Position_ID=$groupjoinpos OR SortOrder=(SELECT MAX(SortOrder) FROM EH_Positions WHERE Group_ID=$group) OR SortOrder=(SELECT MAX(SortOrder) FROM EH_Positions WHERE Group_ID=$group)-1)";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    for($i=0; $i<$rows; $i++) {
      $values = mysql_fetch_row($result);
      $query1 = "SELECT EH_Members.Name, EH_Members.Email FROM EH_Members, EH_Members_Positions WHERE EH_Members_Positions.Group_ID=$group AND EH_Members_Positions.Position_ID=$values[0] AND EH_Members_Positions.Member_ID=EH_Members.Member_ID";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      for($j=0; $j<$rows1; $j++) {
        $values1 = mysql_fetch_row($result1);
        $cotos.="$values1[0] <$values1[1]>, ";
        }
      }
    $body .="\n";
    }
  $body .= "\n\nThis message was generated as an automatic e-mail.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $mailit = mail($recipient, $subject, $body, $headers);
  $group=1;
  $query = "SELECT Position_ID FROM EH_Positions WHERE Group_ID=$group AND (Position_ID=$groupjoinpos OR Position_ID=51 OR Position_ID=60 OR SortOrder=(SELECT MAX(SortOrder) FROM EH_Positions WHERE Group_ID=$group) OR SortOrder=(SELECT MAX(SortOrder) FROM EH_Positions WHERE Group_ID=$group)-1)";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT EH_Members.Name, EH_Members.Email FROM EH_Members, EH_Members_Positions WHERE EH_Members_Positions.Group_ID=$group AND EH_Members_Positions.Position_ID=$values[0] AND EH_Members_Positions.Member_ID=EH_Members.Member_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j=0; $j<$rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $cotos.=stripslashes($values1[0])." <$values1[1]>, ";
      }
    }
//Begin Add to phpBB
define('IN_PHPBB', true);
define('ROOT_PATH', "messageboard.old");

if (!defined('IN_PHPBB') || !defined('ROOT_PATH')) {
exit();
}
else {
$phpEx = "php";
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : ROOT_PATH . '/';
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
$arrTime = getdate();
$unixTime = strtotime($arrTime['year'] . "-" . $arrTime['mon'] . '-' . $arrTime['mday'] . " " . $arrTime['hours'] . ":" . $arrTime['minutes'] . ":" . $arrTime['seconds']);

$user_row = array(
'username'              => $_POST['username'],
'user_password'         => phpbb_hash($_POST['pw']),
'user_email'            => $_POST['email'],
'group_id'              => (int) 2,
'user_timezone'         => (float) 0,
'user_dst'              => "0",
'user_lang'             => "en",
'user_type'             => 0,
'user_actkey'           => "",
'user_ip'               => $_SERVER['REMOTE_HOST'],
'user_regdate'          => $unixTime,
'user_inactive_reason'  => 0,
'user_inactive_time'    => 0
);

// all the information has been compiled, add the user
// tables affected: users table, profile_fields_data table, groups table, and config table.
$user_id = user_add($user_row);
}
// End Add to phpBB
  $cotos = substr($cotos, 0, strlen($cotos)-2);
  $cotos = explode(", ", $cotos);
  $cotos = array_unique($cotos);
  $cotos = implode(", ", $cotos);
  $recipient = $cotos;
  $subject = "Emperor's Hammer: New Recruit!";
  $body = "The following person joined the Emperor's Hammer.\n";
  $body .= "Name: ".stripslashes($name)."\n";
  $body .= "Pin: $newpin\n";
  $body .= "E-mail: $email\n";
  $body .= "Comments: ".stripslashes($comments)."\n";;
  $body .= "Recruiter: $recruiter\n";
  $body .= "Groups Joined:\n";
  foreach($groupsarr as $group) {
    $body.="$group\n";
    }
  $body .= "\n\nThis message was generated as an automatic e-mail.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $mailit = mail($recipient, $subject, $body, $headers);
  if($mailit)
    echo "Welcome to the Emperor's Hammer! You should shortly recieve an e-mail with information necessary to login to the site.";
  }
else {
  echo "<p align=\"center\"><img src=\"/images/EnlistmentBanner.png\" width=\"600\" height=\"400\"></p>\n";
  echo "<p align=\"left\">&nbsp;</p>\n";
  echo "<p align=\"left\">Welcome to the Emperor's Hammer Strike Fleet: A Star Wars Universe based online club. The Emperor's Hammer was founded in 1994 by Grand Admiral Ronin and is the oldest active Star Wars internet-based club. The Emperor's Hammer is structured along the lines of a traditional Imperial military organization offering a wide variety of activities for you to explore. In order to assign you to the appropriate segment of our fleet it is required that all members complete a brief informational questionnaire before being assigned to active duty within the Emperor's Hammer.</p>\n";
  echo "<p>Incoming Recruits/Applicants must be aware of and agree to follow the Emperor's Hammer Club Regulations:</p>\n";
  echo "<ul>\n";
  echo "  <li><a href=\"/page.php?page=aow\">Articles of War</a></li>\n";
  echo "  <li><a href=\"/page.php?page=bylaws\">Bylaws</a></li>\n";
  echo "  <li><a href=\"/page.php?page=coc\">Code of Conduct</a></li>\n";
  echo "  <li><a href=\"/page.php?page=copyright\">Copyrights/Disclaimers</a></li>\n";
  echo "  <li><a href=\"/page.php?page=privacy\">Privacy Policy</a></li>\n";
  echo "</ul>\n";
  echo "<p>Please fill out the questions below with your best accuracy and detailed description:<br />\n";
  echo "<br />\n";
  echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">\n";
  echo "<label for=\"name\">EH requested Character Name (Do not select traditional Star Wars names i.e. Darth Vader, Darth Maul, etc.):</label><br />\n";
  echo "<input type=\"text\" name=\"name\" id=\"name\"><br />\n";
  echo "<label for=\"username\">Message Board Username:</label><br />\n";
  echo "<input type=\"text\" name=\"username\" id=\"username\"><br />\n";
  echo "<label for=\"email\">What is your E-mail?</label><br />\n";
  echo "<input type=\"text\" name=\"email\" id=\"email\"><br />\n";
  echo "<label for=\"pw\">What password would you like to use?</label><br />\n";
  echo "<input type=\"text\" name=\"pw\" id=\"pw\"><br />\n";
  echo "<label for=\"comments\">Please enter any comments in the space provided below:</label><br>";
  echo "<textarea name=\"comments\" id=\"comments\" style=\"width:400px; height:120px\"></textarea><br />\n";
  echo "<label for=\"recruiter\">Please provide your recruiter's name(if applicable):</label><br />\n";
  echo "<input type=\"text\" name=\"recruiter\" id=\"recruiter\"><br />\n";
  echo "<label for=\"age\">Please indicate if you are over the age of 13:</label><br />\n";
  echo "<select name=\"age\" id=\"age\">\n";
  echo "  <option value=\"0\">Yes</option>\n";
  echo "  <option value=\"1\">No</option>\n";
  echo "</select>\n";
  echo "<p> The Emperor's Hammer currently has several groups where you can chose to begin your career, please chose one to begin with:</p>\n";
  $query = "SELECT Group_ID, Name, ShortDesc, Abbr From EH_Groups WHERE Active=1 Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "<input type=\"checkbox\" name=\"Groups[]\" id=\"$values[3]\" value=\"$values[0]\"/>";
    echo "<label for=\"$values[3]\">".stripslashes($values[1])."</label> &ndash; ".stripslashes($values[2]);
    if($values[0]==3) {
      echo " <label for=\"dborder\">Order</label>: <select name=\"dborder\" id=\"dborder\">\n";
      $query1 = "SELECT RT_ID, Name From EH_Ranks_Types WHERE Group_ID=$values[0]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      for($j=0; $j<$rows1; $j++) {
        $values1 = mysql_fetch_row($result1);
        echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>";
        }
      echo "</select>\n";
      }
    echo "<br />\n";
    }
  echo "<p>The Emperor's Hammer thanks you for your time on this matter. Please keep in mind that you are only allowed ONE profile for the EH. Your EH profile will include all groups that you decide to join.<br />\n";
  echo "<br />\n";
  echo "Upon review of your information you will be contacted by our <a href=\"mailto:to@emperorshammer.org\">Training Officer</a> regarding your first assignment.</p>\n";
  echo "<p>For security reasons, please input the image string into the following text-box: <img id=\"captcha\" src=\"/securimage/securimage_show.php\" alt=\"CAPTCHA Image\" /><input type=\"text\" name=\"captcha_code\" size=\"10\" maxlength=\"6\" /></p>";
  echo "<p><button  type=\"submit\" name=\"Submit\">Submit</button>";
  echo "<button name=\"reset\" type=\"reset\">Reset</button></p>\n";
  echo "</form>\n";
  }
include_once("footer.php");
?>