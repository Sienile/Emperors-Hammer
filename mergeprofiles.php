<?
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
?>
<p>Merge Profiles</p>
<p><a href="menu.php">Return to the administration menu</a></p>
<p>
<?
if($_GET['merge']) {
  $query1 = "SELECT To_ID FROM EH_Merged_Profiles WHERE From_ID=".$_SESSION['EHID'];
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $to = $values1[0];
    }
  if($rows1) {
    echo "<p>Confirm that you're willing to merge your profile into PIN $to</p>";
    echo "<p><a href=\"mergeprofiles.php?mergeyes=yes\">Confirmed</a> | <a href=\"mergeprofiles.php?mergeno=$to\">No, I don't want to merge!</a></p>\n";
    }
  else {
    echo "There appears to be a problem, suggest you contact the IO";
    }
  }
elseif($_GET['mergeyes']) {
  $query1 = "SELECT To_ID, MP_ID FROM EH_Merged_Profiles WHERE From_ID=".$_SESSION['EHID'];
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $to = $values1[0];
    $emp=$values1[1];
    }
  echo "<p>Your profile is being merged into PIN $to. Please <a href=\"logout.php\">logout</a>, and login using PIN $to.</p>\n<p>Please note: You might have duplicate entries for Skills, Platforms, and Chat Information, please update these as necessary.</p>";
  $query1 = "UPDATE EH_Merged_Profiles Set Approved=1 WHERE MP_ID=$emp";
  $result1 = mysql_query($query1, $mysql_link);
  $from = $_SESSION['EHID'];
  $query1 = "DELETE FROM EH_Members WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "UPDATE EH_Members_ChatProfile Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Groups Set Member_ID=$to, isPrimary=0 WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_History Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_INPR Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Items Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Platforms Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Positions Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Ranks Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Skills Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Special_Areas Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Uniforms Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Members_Units Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Articles Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles Set Creator_1=$to WHERE Creator_1=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles Set Creator_2=$to WHERE Creator_2=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles Set Creator_3=$to WHERE Creator_3=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles Set Creator_4=$to WHERE Creator_4=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles Set HS_Holder=$to WHERE HS_Holder=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles_Bugs Set Poster_ID=$to WHERE Poster_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles_Complete Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles_Missions Set HS_Holder=$to WHERE HS_Holder=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Battles_Reviews Set Poster_ID=$to WHERE Poster_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Benefactors Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Competitions Set Admin_ID=$to WHERE Admin_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Competitions_Participants Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_ConvertInfo Set NewValue=$to WHERE NewValue=$from And `Table`='Member'";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Fiction Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Heroes Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_History Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Images Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Links_Comments Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Medals_Complete Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Medals_Complete Set Awarder_ID=$to WHERE Awarder_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_News Set Poster_ID=$to WHERE Poster_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Patches_Bugs Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Patches_Reviews Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Promotion_Recs Set For_ID=$to WHERE For_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Promotion_Recs Set From_ID=$to WHERE From_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Reports Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Training Set Grader=$to WHERE Grader=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Training_Complete Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  $query1 = "UPDATE EH_Training_Exams_Complete Set Member_ID=$to WHERE Member_ID=$from";
  $result1 = mysql_query($query1, $mysql_link);
  if(mysql_errno($mysql_link))
    echo $query1;
  }
elseif($_GET['mergeno']) {
  echo "<p>We are removing the request to merge your profile into PIN #. Please return to the menu using the link above</p>";
  $query1 = "DELETE FROM EH_Merged_Profiles WHERE To_ID='".$_GET['mergeno']."' AND From_ID='".$_SESSION['EHID']."'";
  $result1 = mysql_query($query1, $mysql_link);
  }
elseif($_POST['confirm']) {
  $query1 = "SELECT To_ID, MP_ID FROM EH_Merged_Profiles WHERE (From_ID=".$_POST['confirm']." OR To_ID=".$_POST['confirm'] ." OR From_ID=".$_SESSION['EHID']." OR To_ID=".$_SESSION['EHID'].") AND Approved=0";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1==0) {
    echo "<p>Confirm that you're wanting to merge PIN <a href=\"profile.php?pin=".$_POST['confirm']."\">".$_POST['confirm']."</a> into your existing PIN of ".$_SESSION['EHID'] ."</p>";
    echo "<p><a href=\"mergeprofiles.php?confirmyes=".$_POST['confirm']."\">Confirmed</a> | <a href=\"menu.php\">No, I want to return to the menu</a></p>\n";
    }
  else {
    echo "<p>This profile is already in a request status, Please either have them merge or remove this request, prior to merging.</p>";
    }
  }
elseif($_GET['confirmyes']) {
  echo "<p>An e-mail is being sent to PIN ".$_GET['confirmyes'].", to have them confirm this request.<p>";
  $query1 = "INSERT INTO EH_Merged_Profiles (To_ID, From_ID, Approved) VALUES('".$_SESSION['EHID']."', '".$_GET['confirmyes']."', 0)";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "SELECT Name, Email FROM EH_Members WHERE Member_ID=".$_GET['confirmyes'];
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    $name=stripslashes($values1[0]);
    $email=stripslashes($values1[1]);
    }
  $recipient = "$name <$email>";
  $subject = "Merge Profiles Request to PIN #".$_SESSION['EHID'];
  $body = "PIN #".$_SESSION['EHID']. " has requested to merege your profile PIN #". $_GET['confirmyes']. " into theirs. To acknowledge this request, sign into the EH Site and accept or deny this request.";
  $body .= "\n\nThis message was generated as an automatic e-mail, if you were not aware of this request contact the Security Officer.";
  $headers = "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  }
else {
?>
<p>Please input the pin of the profile you'd like to merge with your current profile PIN of <?=$_SESSION['EHID']?></p>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type="text" name="confirm" id="confirm" size="5"><br>
<input type="submit" id="Submit" name="Submit" value="Submit" />
<input type="reset" id="Reset" name="Reset" value="Reset" />
</form>
<?
}
include_once("footer.php");
?>