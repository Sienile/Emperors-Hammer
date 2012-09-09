<?
session_start();
include_once("config.php");
include_once("functions.php");
$page="Training Exam";
include("nav.php");
if(!$mysql_link) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }
if($_POST['Submit'])
  $status=2;
else
  $status=1;
$test = mysql_real_escape_string($_POST['examid'], $mysql_link);
$user = mysql_real_escape_string($_POST['userid'], $mysql_link);
$query = "SELECT Name, TAc_ID, Grader FROM EH_Training WHERE Training_ID=$test";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
$grader = $values[2];
$testname = stripslashes($values[0]);
$acadid=$values[1];
$body = "$testname Test submission\n";
$query = "SELECT TE_ID, Question FROM EH_Training_Exams WHERE Training_ID=$test Order By SortOrder, TE_ID";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i = 1; $i <= $rows; $i++) {
  $values = mysql_fetch_row($result);
  $ans = mysql_real_escape_string($_POST[$values[0]], $mysql_link);
  $body.="The  Q) ".stripslashes($values[1])."\n";
  $body.="Your A) ".stripslashes($ans)."\n";
  $time=time();
  $query1 = "SELECT TEC_ID FROM EH_Training_Exams_Complete WHERE TE_ID=$values[0] AND Member_ID=$user";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "Update EH_Training_Exams_Complete Set Answer='$ans', Status= $status, DateSubmitted='$time' WHERE TEC_ID=$values1[0]";
    $result2 = mysql_query($query2, $mysql_link);
    }
  else {
    $query2 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status, DateSubmitted) Values ('$user', '$test', $values[0], '$ans', '$status', '$time')";
    $result2 = mysql_query($query2, $mysql_link);
    }
  }
if($status==1)
  echo "Test has been Saved for completion at a later date. Go to the test link for the course to finish and submit the test.";
elseif($status==2) {
  $username = RankAbbrName($user, PriGroup($user), 0);
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$grader";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $recipient = RankAbbrName($grader, PriGroup($grader))." <".stripslashes($values[0]).">";
  $recipient .="EH Training Officer <to@emperorshammer.org>";
  $query = "SELECT EH_Members.Member_ID, EH_Members.Email FROM EH_Training_Academies, EH_Members, EH_Members_Positions WHERE (EH_Training_Academies.Leader=EH_Members_Positions.Position_ID OR EH_Training_Academies.Deputy=EH_Members_Positions.Position_ID) AND EH_Members.Member_ID=EH_Members_Positions.Member_ID AND EH_Training_Academies.TAc_ID=$acadid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i = 1; $i <= $rows; $i++) {
    $values = mysql_fetch_row($result);
    $recipient .=", ". RankAbbrName($values[0], PriGroup($values[0])) ." <".stripslashes($values[1]).">";
    }
  $subject = "EH Academy Test Submission: $testname";
  $body = "$username has submitted a test for $testname. Please login to the site to grade it.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  echo "Test has been submitted to the Staff for grading. To view your submitted test and get a copy of what you submitted, click <font color=\"#cc1000\">&gt;<a href=\"viewtest.php?id=$test\">HERE</a>&lt;</font>";
  }
include_once("footer.php");
?>