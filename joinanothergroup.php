<?
session_start();
include_once("config.php");
include_once("functions.php");
if(!$_SESSION['EHID'])
  Redirect("logon.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
if($_POST['Groups']) {
?>
<p>Join another Emperor's Hammer Group</p>
<p><a href="menu.php">Return to the administration menu</a></p>
<?
  $query = "SELECT Member_ID, Name, Email FROM EH_Members WHERE Member_ID=".$_SESSION['EHID'];
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $name=stripslashes($values[1]);
    $email=$values[2];
    $newpin=$values[0];
    }
  $recipient = "$name <$email>";
  $subject = "Welcome to the Emperor's Hammer!";
  $body .= "$name, Welcome to the Emperor's Hammer. You can find information below about the groups you have joined.\n";
  $body .= "Since you're already a member, you can login to the site at ".$site_host.", with your existing pin of $newpin.\n";
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
        $cotos.=stripslashes($values1[0])." <$values1[1]>, ";
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
  $cotos = substr($cotos, 0, strlen($cotos)-2);
  $cotos = explode(", ", $cotos);
  $cotos = array_unique($cotos);
  $cotos = implode(", ", $cotos);
  $recipient = $cotos;
  $subject = "Emperor's Hammer: New Group Member!";
  $body = "The following person joined some new groups within the Emperor's Hammer.\n";
  $body .= "Name: $name\n";
  $body .= "Pin: $newpin\n";
  $body .= "E-mail: $email\n";
  $body .= "Comments: $comments\n";;
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
?>
<p>You should be contacted shortly with regards to group information.</p>
<?
}
else {
?>
<p>Join another Emperor's Hammer Group</p>
<p><a href="menu.php">Return to the administration menu</a></p>
<p>You are currently a member of the following Groups:<br>
<?php
$query = "SELECT EH_Groups.Name, EH_Groups.Group_ID FROM EH_Groups, EH_Members_Groups WHERE EH_Groups.Group_ID=EH_Members_Groups.Group_ID AND EH_Members_Groups.Member_ID=".$_SESSION['EHID']." Order By EH_Groups.Group_ID";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $groups[]=$values[1];
  echo stripslashes($values[0])."<br />\n";
  }
?>
</p>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<p>Please select which groups you would like to join:</p>
<?
$groups = implode(" AND Group_ID!=", $groups);
$query = "SELECT Group_ID, Name, ShortDesc, Abbr FROM EH_Groups WHERE (Group_ID!=$groups) And Active=1 Order By Group_ID";
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
?>
<br />
Upon review of your information you will be contacted by the respective training officers for the groups you selected.</p>
<p><button  type="submit" name="Submit">Submit</button>
<button name="reset" type="reset">Reset</button></p>
</form>
<?
}
include_once("footer.php");
?>