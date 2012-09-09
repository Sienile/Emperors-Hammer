<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "examgradeadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "examgradeadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="90%">Name</td>
      <td width="10%">Grade</td>
    </tr>
    <?php
  $query = "SELECT Training_ID, Name FROM EH_Training WHERE TAc_ID=$datatable";
  if(!has_access($_SESSION['EHID'], "acadadmin",true))
    $query .= " AND Grader=".$_SESSION['EHID'];
  $query .=" Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT Group_ID FROM EH_Training_Academies WHERE TAc_ID=$datatable";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $group=$values1[0];
      }
    $query1 = "SELECT Training_ID, Member_ID, DateSubmitted FROM EH_Training_Exams_Complete WHERE Training_ID=$values[0] AND Status=2 Group By Member_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j=0; $j<$rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
    ?>
      <tr>
        <td width="90%"><?=stripslashes($values[1])?>: Submitted By: <?=RankAbbrName($values1[1], $group, 1)?>, Submitted On: <?=date("F j, Y", $values1[2])?></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values1[0]?>, <?=$values1[1]?>);"><span style="color:#6699CC;">Grade</span></a></td>
      </tr>
    <?php
      }
    } // End for loop
    ?>
  </table>
<?php
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link); //Training ID
  $member = mysql_real_escape_string($_GET['mem'], $mysql_link); //Member
  $group = mysql_real_escape_string($_GET['group'], $mysql_link); //Group
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
    <input type="hidden" name="member" value="<?=$member?>" />
    <input type="hidden" name="group" value="<?=$group?>" />
      <table>
<?
  $query = "SELECT TE_ID, Question, Answer, Points FROM EH_Training_Exams WHERE Training_ID=$id Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=1; $i<=$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
        <tr>
          <td><label for="score<?=$values[0]?>">Question <?=$i?> (<?=stripslashes($values[3])?>): <?=stripslashes($values[1])?></label></td>
          <td>Official Answer: <?=stripslashes(nl2br($values[2]))?></td>
        </tr>
        <tr>
<?
  $query1 = "SELECT Answer FROM EH_Training_Exams_Complete WHERE Training_ID=$id AND Member_ID=$member AND TE_ID=$values[0]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $ans=$values1[0];
  }
?>
          <td>Their Answer: <font color="#22B14C"><?=stripslashes(nl2br($ans))?></font></td>
	<script type="text/javascript">

	$(function() {

		$("#slider<?=$values[0]?>").slider({

<?
if($ans==$values[2]) {
?>
			value:<?=$values[3]?>,
<?
} else {
?>
			value:0,
<?
}
?>
			min: 0,

			max: <?=$values[3]?>,

			step: 1,

			slide: function(event, ui) {

				$("#score<?=$values[0]?>").val(ui.value);

			}

		});

		$("#score<?=$values[0]?>").val($("#slider<?=$values[0]?>").slider("value"));

	});

	</script>


          <td><label for="score<?=$values[0]?>">Score: 

<input type="text" id="score<?=$values[0]?>" name="score<?=$values[0]?>" style="border:0; color:#FFFFFF; background:#000000; font-weight:bold;" /><div id="slider<?=$values[0]?>"></div>

</td>
        </tr>
<?

    }
?>
        <tr>
          <td><label for="comments">Comments: </label></td>
          <td><textarea name="comments" id="comments" style="width:400px; height:120px"></textarea></td>
        </tr>
      </table>
    </form>
<?php
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $member = mysql_real_escape_string($_POST['member'], $mysql_link);
  $group = mysql_real_escape_string($_POST['group'], $mysql_link);
  $comments = $_POST['comments'];
  $score =0;
  $query = "SELECT TE_ID, Question, Answer, Points FROM EH_Training_Exams WHERE Training_ID=$id Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=1; $i<=$rows; $i++) {
    $values = mysql_fetch_row($result);
    $value = mysql_real_escape_string($_POST['score'.$values[0]], $mysql_link);
    $score +=$value;
    $query1 = "UPDATE EH_Training_Exams_Complete Set Score=$value WHERE TE_ID=$values[0] AND Member_ID=$member";
    $result1 = mysql_query($query1, $mysql_link);
    }
  $passed=false;
  $query = "SELECT Name, Abbr, MinPoints, TAc_ID, Grader FROM EH_Training WHERE Training_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $certname = $values[0];
    $certabbr = $values[1];
    $certminpts = $values[2];
    $acad = $values[3];
    $grader = $_SESSION['EHID'];
    }
  $query = "SELECT Rank_ID FROM EH_Members_Ranks WHERE Member_ID=$member AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $rank=0;
  if($rows) {
    $values = mysql_fetch_row($result);
    $rank = $values[0];
    }
  $query = "SELECT Name, Email FROM EH_Members WHERE Member_ID=$member";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $name = $values[0];
    $email = $values[1];
    }
  if($score>=$certminpts)
    $passed = true;
  if($passed) {
    $time = time();
    //Find highest Medal
    $query = "SELECT Award_ID FROM EH_Training_Awards WHERE Training_ID=$id AND TAT_ID=1 AND Score <=$score Order By Score DESC Limit 1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $medalid = $values[0];
      }
    if($medalid) {
      $query = "SELECT Name FROM EH_Medals WHERE Medal_ID=$medalid";
      $result = mysql_query($query, $mysql_link);
      $rows = mysql_num_rows($result);
      if($rows) {
        $values = mysql_fetch_row($result);
        $medalname = $values[0];
        }
      $query = "INSERT INTO EH_Medals_Complete (Medal_ID, Member_ID, Awarder_ID, DateAwarded, Reason) VALUES ($medalid, $member, $id, '$time', 'Completion of the $certname Course')";
      $result = mysql_query($query, $mysql_link);
      }
    //Find And award highest rank if applicable
    $query = "SELECT Award_ID FROM EH_Training_Awards WHERE Training_ID=$id AND TAT_ID=2 AND Score <=$score Order By Score DESC Limit 1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $promoid = $values[0];
      }
    if($promoid) {
      $query = "SELECT Name, SortOrder FROM EH_Ranks WHERE Ranks_ID=$promoid";
      $result = mysql_query($query, $mysql_link);
      $rows = mysql_num_rows($result);
      if($rows) {
        $values = mysql_fetch_row($result);
        $promoname = $values[0];
        $promosortorder = $values[1];
        }
      if($rank) {
        $query = "SELECT Name, SortOrder, Abbr FROM EH_Ranks WHERE Rank_ID=$rank";
        $result = mysql_query($query, $mysql_link);
        $rows = mysql_num_rows($result);
        if($rows) {
          $values = mysql_fetch_row($result);
          $currankname = $values[0];
          $curranksortorder = $values[1];
          $currankabbr = $values[2];
          }
        }
      if($rank && $curranksortorder<$promosortorder) {
        $promoted=true;
        $query = "UPDATE EH_Members_Ranks Set Rank_ID=$promoid WHERE Member_ID=$member AND Group_ID=$group";
        $result = mysql_query($query, $mysql_link);
        $query = "INSERT INTO EH_Members_History(Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES('$member', '$group', 1, '$rank-$promoid', 'Completion of $certname Course', '$time')";
        $result = mysql_query($query, $mysql_link);
        }
      }
    $query1 = "UPDATE EH_Training_Exams_Complete Set Status=3 WHERE Training_ID=$id AND Member_ID=$member";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Training_Complete (Training_ID, Member_ID, DateComplete, Score) VALUES ($id, $member, $time, $score)";
    $result1 = mysql_query($query1, $mysql_link);
    //E-mail User/CO's about completion and Rank/Medal Awards
    $to = $email;
    $to .=", ".CoC($member, PriGroup($member));
    $to .=", EH Training Officer <to@emperorshammer.org>";
    $query3 = "SELECT Email FROM EH_Members WHERE Member_ID=$grader";
    $result3 = mysql_query($query3, $mysql_link);
    $values3 = mysql_fetch_row($result3);
    $to .= ", ".RankAbbrName($grader, PriGroup($grader), 0)." <".stripslashes($values3[0]).">";
    $query3 = "SELECT EH_Members.Member_ID, EH_Members.Email FROM EH_Training_Academies, EH_Members, EH_Members_Positions WHERE (EH_Training_Academies.Leader=EH_Members_Positions.Position_ID OR EH_Training_Academies.Deputy=EH_Members_Positions.Position_ID) AND EH_Members.Member_ID=EH_Members_Positions.Member_ID AND EH_Training_Academies.TAc_ID=$acad";
    $result3 = mysql_query($query3, $mysql_link);
    $rows3 = mysql_num_rows($result3);
    for($k = 1; $k <= $rows3; $k++) {
      $values3 = mysql_fetch_row($result3);
      $to .=", ". RankAbbrName($values3[0], PriGroup($values3[0]), 0) ." <".stripslashes($values3[1]).">";
      }
    $Subject = "$name has completed: $certname Course!";
    $Body = "Greetings, $name,\n";
    $Body .= "You have passed the course of $certname with a score of $score. You can now add the $certabbr to your ID Line.\n";
    if($promoted)
      $Body .= "You have also earned the rank of $promoname for the completion of this course.\n";
    if($medalid)
      $Body .= "You have also earned a $medalname for the completion of this course.\n";
    if($comments)
      $Body .= "Grader Comments: $comments\n";
    $Body .= "Sent by: Emperor's Hammer Auto-Responder\n";
    $Body .= "Graded By: ".RankAbbrName($grader, PriGroup($grader), 0)."\n";
    $headers .= "From: $postmaster\n";
    $headers .= "X-Mailer: PHP\n"; // mailer
    //Send!
    $mail = mail($to, $Subject, $Body, $headers);
    }
  else { //Failed
    $query1 = "UPDATE EH_Training_Exams_Complete Set Status=0 WHERE Training_ID=$id AND Member_ID=$member";
    $result1 = mysql_query($query1, $mysql_link);
    $to = $email;
    $to .=", EH Training Officer <to@emperorshammer.org>";
    $Subject = "Emperor's Hammer Training: $certname Course Failed!";
    $Body = "Greetings, $name,\n";
    $Body .= "Sadly, you have failed the $certname course. You can attempt it again if you would like to try.\n";
    $Body .= "Grader Comments: $comments\n";
    $Body .= "Sent by: Emperor's Hammer Auto-Responder\n";
    $Body .= "Graded By: ".RankAbbrName($grader, PriGroup($grader), 0)."\n";
    $headers .= "From: $postmaster\n";
    $headers .= "X-Mailer: PHP\n"; // mailer
    //Send!
    $mail = mail($to, $Subject, $Body, $headers);
    }
  if($result)
    echo "<p>Exam graded!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Exam Grade Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Academy to modify their Training</label>
    <?php $ga = implode (" OR Group_ID=", $groupsaccess); ?>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
    <option value="0">No Academy</option>
  <?php
  $query = "SELECT TAc_ID, Name FROM EH_Training_Academies";
  if($ga) {
    $query .=" WHERE Group_ID=$ga";
    }
  $query.=" Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
  }
?>
  </select>
  </form>

  <div id="message" style="color: green;"></div>
  <div id="response"></div>

  <div id="editArea" title="Grade Exam">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>


  <script type="text/javascript">

  function getEditForm(id, mem) {
  var group = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id+"&mem="+mem+"&group="+group,{},function(data){
        $("#editArea").html(data);
    },'html').complete(function() {
      $("#editArea").dialog("open");
    });
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function postEdit() {
  var group = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true&group='+group,
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    return false;
  }
  
  function getDataTable() {
    var id = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable="+id,{},function(data){
        $("#response").html(data);
    },'html');
  }

  $(function() {
      $("#editArea").dialog({
        autoOpen: false,
        width: 850,
        modal: true,
        buttons: {
          "Submit": function() {
            postEdit();
            $( this ).dialog( "close" );
            },
          Cancel: function() {
            $( this ).dialog( "close" );
            }
          },
          close: function() {
            document.forms["editForm"].reset();
            }
        });
  });
  </script>
  <?php
  include_once("footer.php");
  }
?>