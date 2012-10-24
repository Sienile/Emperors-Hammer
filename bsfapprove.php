<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "bsfapprove");
$groupsaccess = AccessGroups($_SESSION['EHID'], "bsfapprove");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  $query = "SELECT Complete_ID, Battle_ID, Filename, Member_ID, Scores, TACStatus, Rec_ID FROM EH_Battles_Complete WHERE Status=0 Order By Date_Completed";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
if($rows) {
  ?>
  <table>
    <tr>
      <td width="10%">For</td>
      <td width="10%">From</td>
      <td width="10%">Battle</td>
      <td width="10%">Reason</td>
      <td width="10%">Scores</td>
      <td width="30%">File</td>
      <td width="10%">Approve</td>
      <td width="10%">Deny</td>
    </tr>
    <?php
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
    <tr>
      <td width="10%" style="vertical-align:top;"><a href="profile.php?pin=<?=$values[3]?>" target="_blank"><? echo RankAbbrName($values[3], PriGroup($values[3]),1); ?></a></td>
      <td width="10%" style="vertical-align:top;"><? echo RankAbbrName($values[6], PriGroup($values[6]),1); ?></td>
      <td width="10%" style="vertical-align:top;"><a href="battle.php?id=<?=$values[1]?>" target="_blank"><?=BattleName($values[1], 1); ?></a></td>
      <td width="10%" style="vertical-align:top;"><?
    $body="";
    $errorbin = decbin($values[5]);
    $errorbinrev = strrev($errorbin);
    for($q=0; $q<8; $q++) {
      if($errorbinrev[$q]==1) {
        switch($q) {
          case 0:
          $body.="File Size incorrect<br>\n";
          break;
          case 1:
          $body.="Skill Check wrong<br>\n";
          break;
          case 2:
          $body.="Mission Scores don't add up<br>\n";
          break;
          case 3:
          $body.="Mission Count incorrect<br>\n";
          break;
          case 4:
          $body.="Total Score equals 0<br>\n";
          break;
          case 5:
          $body.="Possible resubmission of pilot file<br>\n";
          break;
          case 6:
          $body.="Possible new High Score<br>\n";
          break;
          case 7:
          $body.="Game Error<br>\n";
          break;
          }
        }
      }
     echo $body;
?></td>
      <td width="10%" style="vertical-align:top;"><?
$scores = explode(";", $values[4]);
echo "Battle Score: $scores[0]<br>\n";
for($q=1; $q<count($scores); $q++)
  echo "Mission $q: $scores[$q]<br>\n";
?></td>
      <td width="30%" style="vertical-align:top;"><a href="plts/<?=$values[2];?>">Download Pilot File</a></td>
      <td width="10%" style="vertical-align:top;"><a href="#" id="del" onclick="getDelForm(<? echo $values[0]?>)"><span style="color:#6699CC;">Approve</span></a></td>
      <td width="10%" style="vertical-align:top;"><a href="#" id="edit" onclick="getEditForm(<? echo $values[0]?>);"><span style="color:#6699CC;">Deny</span></a></td>
    </tr>
    <?php
    } // End for loop
    ?>
  </table>
<?php
    }
  else { 
    echo "No BSFs to approve";
     }
  } // end if $_GET['datatable']
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="denyreason">Reason to Deny: </label></td>
          <td><textarea name="denyreason" id="denyreason" style="width:400px; height:120px"></textarea></td>
        </tr>
      </table>
    </form>
<?php
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $reason = mysql_real_escape_string($_POST['denyreason'], $mysql_link);
  $query = "SELECT Complete_ID, Battle_ID, Filename, Member_ID, Scores, TACStatus, Rec_ID FROM EH_Battles_Complete WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $for = RankAbbrName($values[3], PriGroup($values[3]), 0);
    $from = RankAbbrName($values[6], PriGroup($values[6]), 0);
    $fromid=$values[6];
    $battlename = BattleName($values[1], 0);
    }
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$fromid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $frommail = $values[0];
    }
  $recipient = "$from <$frommail>, Tactical Officer <tac@emperorshammer.org>";
  $subject = "BSF for $for Denied";
  $body .= "Your BSF for battle: $battlename for $for has been Denied for the following Reason:\n";
  $body .= $reason;
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "DELETE FROM EH_Battles_Complete WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>BSF Denied successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
?>
  <form id="approveForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="scores">Scores (Battle score first line, each mission follows): </label></td>
          <td><textarea name="denyreason" id="denyreason" style="width:400px; height:120px"><?
  $query = "SELECT Complete_ID, Scores FROM EH_Battles_Complete WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $scores = explode(";", $values[1]);
    echo "$scores[0]\n";
    for($q=1; $q<count($scores); $q++) {
      echo "$scores[$q]";
      if($q+1<count($scores))
        echo "\n";
      }
    }
?></textarea></td>
        </tr>
      </table>
    </form>
<?php
  }
elseif($_GET['del1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $order = array("\\r\\n", "\\n", "\\r");
  $scores = str_replace($order, "\n", mysql_real_escape_string($_POST['denyreason'], $mysql_link));
  $query = "SELECT Complete_ID, Battle_ID, Filename, Member_ID, Scores, TACStatus, Rec_ID FROM EH_Battles_Complete WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $forid= $values[3];
    if(isinGroup($forid, 2))
      $prigroup = 2;
    else
      $prigroup = PriGroup($forid);
    $for = RankAbbrName($values[3], $prigroup, 0);
    $from = RankAbbrName($values[6], $prigroup, 0);
    $fromid=$values[6];
    $battleid=$values[1];
    $query1 = "SELECT Battle_ID, NumMissions, Reward_Name, Highscore, HS_Holder FROM EH_Battles WHERE Battle_ID=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $battlename = BattleName($values1[0], 0);
      $battlenummissions = $values1[1];
      $battlereward = $values1[2];
      $battleHS = $values1[3];
      $battleHSpin =$values1[4];
      }
    }
  $scores = explode("\n", $scores);
  $query = "SELECT Complete_ID, Battle_ID, Filename, Member_ID, Scores, TACStatus, Rec_ID FROM EH_Battles_Complete WHERE Complete_ID!=$id AND Member_ID=$forid AND Battle_ID=$battleid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if(!$rows) {
    $pts = $battlenummissions;
    $query1 = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$forid AND SA_ID=1";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $fchgnew = $values1[0]+$pts;
      $query2 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$forid AND SA_ID=1";
      $result2 = mysql_query($query2, $mysql_link);
      }
    else {
      $query2 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) Values ('$forid', 1, $pts)";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  if($battlenummissions>1 && $scores[0]>$battleHS) {
    $pts = $battlenummissions*2;
    $query = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$forid AND SA_ID=1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $fchgnew = $values[0]+$pts;
      $query1 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$forid AND SA_ID=1";
      $result1 = mysql_query($query1, $mysql_link);
      }
    else {
      $query1 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) Values ('$forid', 1, $pts)";
      $result1 = mysql_query($query1, $mysql_link);
      }
    $query = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$battleHSpin AND SA_ID=1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $fchgnew = $values[0]-$pts;
      $query1 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$battleHSpin AND SA_ID=1";
      $result1 = mysql_query($query1, $mysql_link);
      }
    $query1 = "UPDATE EH_Battles Set Highscore=$scores[0], HS_Holder=$forid WHERE Battle_ID=$battleid";
    $result1 = mysql_query($query1, $mysql_link);
    }
  $query = "SELECT Mission_ID, Mission_Num, Highscore, HS_Holder FROM EH_Battles_Missions WHERE Battle_ID=$battleid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    for($i=0; $i<$rows; $i++) {
      $values = mysql_fetch_row($result);
      if($scores[$values[1]]>$values[2]) {
        $pts = 2;
        $query1 = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$forid AND SA_ID=1";
        $result1 = mysql_query($query1, $mysql_link);
        $rows1 = mysql_num_rows($result1);
        if($rows1) {
          $values1 = mysql_fetch_row($result1);
          $fchgnew = $values1[0]+$pts;
          $query2 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$forid AND SA_ID=1";
          $result2 = mysql_query($query2, $mysql_link);
          }
        else {
          $query2 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) Values ('$forid', 1, $pts)";
          $result2 = mysql_query($query2, $mysql_link);
          }
        $query1 = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$values[3] AND SA_ID=1";
        $result1 = mysql_query($query, $mysql_link);
        $rows1 = mysql_num_rows($result1);
        if($rows1) {
          $values1 = mysql_fetch_row($result1);
          $fchgnew = $values1[0]-$pts;
          $query2 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$values[3] AND SA_ID=1";
          $result2 = mysql_query($query2, $mysql_link);
          }
        $query2 = "UPDATE EH_Battles_Missions Set Highscore='".$scores[$values[1]]."', HS_Holder=$forid WHERE Mission_ID=$values[0]";
        $result2 = mysql_query($query2, $mysql_link);
        }
      }
    }
  else {
    for($i=1; $i<=$battlenummissions; $i++) {
      $pts = 2;
      $query1 = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$forid AND SA_ID=1";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $fchgnew = $values1[0]+$pts;
        $query2 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$forid AND SA_ID=1";
        $result2 = mysql_query($query2, $mysql_link);
        }
      else {
        $query2 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) Values ('$forid', 1, $pts)";
        $result2 = mysql_query($query2, $mysql_link);
      }
      $query2 = "INSERT INTO EH_Battles_Missions (Battle_ID, Mission_Num, Highscore, HS_Holder) Values($battleid, $i, '".$scores[$i]."','$forid')";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$fromid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $frommail = $values[0];
    }
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$forid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $formail = $values[0];
    }
  $recipient = "$from <$frommail>, $for <$formail>, Tactical Officer <tac@emperorshammer.org>";
  $recipient .= ", ".CoC($forid, $prigroup);
  $subject = "BSF for $battlename for $for Approved";
  $body .= "Your BSF for battle: $battlename for $for has been Approved.\n";
  $body .= "Battle Score: $scores[0]\n";
  for($q=1; $q<count($scores); $q++)
  $body .= "Mission $q: $scores[$q]\n";
  $body .= "\n\nThis message was generated as an automatic e-mail after verification by the Tactical Officer.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "UPDATE EH_Battles_Complete Set Status=1 WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>BSF Approved successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer BSF Approval</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>

  <div id="editArea" title="Deny BSF">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="approveArea" title="Approve BSF">
    <form id="approveForm" method="POST">
    </form>
  </div>

  <script type="text/javascript">

  function getEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id,{},function(data){
      $("#editArea").html(data);
    },'html').complete(function() {
      $("#editArea").dialog("open");
      });
  }
  
  function getDelForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id,{},function(data){
      $("#approveArea").html(data);
    },'html').complete(function() {
      $("#approveArea").dialog("open");
      });
  }

  function postDel() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?del1=true',
        success: showSuccess
    }
    $("#approveForm").ajaxSubmit(options);
    return false;
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }
  
  function postEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true',
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    return false;
  }

  function getDataTable() {
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=1",{},function(data){
        $("#response").html(data);
    },'html');
  }

  $(document).ready(getDataTable);

  $(function() {
    $("#editArea").dialog({
        autoOpen: false,
        width: 550,
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

      $("#approveArea").dialog({
        autoOpen: false,
        width: 550,
        modal: true,
        buttons: {
          "Submit": function() {
            postDel();
            $( this ).dialog( "close" );
            },
          Cancel: function() {
            $( this ).dialog( "close" );
            }
          },
          close: function() {
            document.forms["approveForm"].reset();
            }
        });
  });

  </script>
  <?php
  include_once("footer.php");
  }
?>