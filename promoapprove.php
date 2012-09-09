<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "promoapprove");
$groupsaccess = AccessGroups($_SESSION['EHID'], "promoapprove");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
<p>Red highlights: Award Requests<br>
Green highlights: General Recommendation</p>
  <table>
    <tr>
      <td width="10%">For</td>
      <td width="10%">From</td>
      <td width="60%">Reason</td>
      <td width="10%">Approve</td>
      <td width="10%">Deny</td>
    </tr>
    <?php
  $query = "SELECT PR_ID, For_ID, From_ID, Type, Reason, Group_ID FROM EH_Promotion_Recs WHERE Group_ID=$datatable Order By PR_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[3]==0)
      $color="#00FF00";
    elseif($values[3]==1)
      $color="#FF0000";
    ?>
    <tr>
      <td width="10%"><span style="color:<?=$color?>;"><?=RankAbbrName($values[1], $values[5], 1);?></span></td>
      <td width="10%"><?=RankAbbrName($values[2], $values[5], 1);?></td>
      <td width="60%"><span style="color:<?=$color?>;"><?=stripslashes($values[4])?></span></td>
      <td width="10%"><a href="#" id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Approve</span></a></td>
      <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Deny</span></a></td>
    </tr>
    <?php
    } // End for loop
    ?>
  </table>
<?php
  } // end if $_GET['datatable']
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
?>
<div id="editDiv" class="ajaxForm" title="Deny">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="denyreason">Reason to Deny: </label></td>
          <td><textarea name="denyreason" id="denyreason" style="width:400px; height:120px"></textarea></td>
        </tr>
        <tr>
          <td colspan="2" align="center">
            <input type="submit" id="Submit" name="Submit" value="Submit" />
            <input type="reset" id="Reset" name="Reset" />
            <input type="button" id="Cancel" name="Cancel" value="Cancel"
                 onClick="$('#Reset').click();destroyForm();" />
            </td>
        </tr>
      </table>
    </form>
</div>
<?php
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $reason = mysql_real_escape_string($_POST['denyreason'], $mysql_link);
  $query = "SELECT From_ID, For_ID, Group_ID FROM EH_Promotion_Recs WHERE PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $for = RankAbbrName($values[1], $values[2], 0);
    $from = RankAbbrName($values[0], $values[2], 0);
    $fromid=$values[0];
    }
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$fromid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $frommail = stripslashes($values[0]);
    }
  $recipient = "$from <$frommail>";
  $subject = "Promotion Request for $for Denied";
  $body .= "Your request to promote $for has been Denied for the following Reason:\n";
  $body .= stripslashes($reason);
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "DELETE FROM EH_Promotion_Recs WHERE PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Request Denied successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "SELECT For_ID, From_ID, Group_ID, Reason FROM EH_Promotion_Recs WHERE PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $forid=$values[0];
    $fromid=$values[1];
    $group = $values[2];
    $reason = stripslashes($values[3]);
    }
  $query = "SELECT Rank_ID FROM EH_Members_Ranks WHERE Member_ID=$forid AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $currankid=$values[0];
    }
  $query = "SELECT SortOrder, RT_ID FROM EH_Ranks WHERE Rank_ID=$currankid AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $curso=$values[0];
    $rtid=$values[1];
    }
  $query = "SELECT Rank_ID FROM EH_Ranks WHERE Group_ID=$group AND SortOrder>$curso AND RT_ID=$rtid ORDER By SortOrder LIMIT 1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $newrankid=$values[0];
    }
  //Update Rank
  $time = time();
  $query = "UPDATE EH_Members_Ranks Set Rank_ID=$newrankid, PromotionDate=$time WHERE Member_ID=$forid AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  //Insert into History
  $query = "INSERT INTO EH_Members_History(Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES('$forid', '$group', 1, '$currankid-$newrankid', '$reason', '$time')";
  $result = mysql_query($query, $mysql_link);
  $query = "SELECT Email, Name FROM EH_Members WHERE Member_ID=$fromid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $frommail=stripslashes($values[0]);
    $from = stripslashes($values[1]);
    }
  $query = "SELECT Email, Name FROM EH_Members WHERE Member_ID=$forid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $formail=stripslashes($values[0]);
    $for = stripslashes($values[1]);
    }
  $recipient = "$from <$frommail>";
  $recipient .=", $for <$formail>";
  $recipient .=", ".CoC($forid, $group);
  $subject = "$for has been promoted!";
  $body .= "$for has now been promoted for the following reason:\n";
  $body .= stripslashes($reason);
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "DELETE FROM EH_Promotion_Recs WHERE PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Promotion Granted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Promotion Approval</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Promotion Requests</label>
    <?php $ga = implode (" OR Group_ID=", $groupsaccess); ?>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
    <option value="0">No Group</option>
  <?php
  $query = "SELECT Group_ID, Name FROM EH_Groups";
  if($ga) {
    $query .=" WHERE Group_ID=$ga";
    }
  $query.=" Order By Group_ID";
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

  <div id="editdialog" title="Deny" refreshOnShow="true">
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  function getEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id,{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("editDiv");
        $("#editArea").show();
    },'html');
  }
  
  function destroyForm(){
      $("#editArea").hide('fast',function(){
        $("#editArea").remove();
        getDataTable();
      });
  }

  function del(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id+"&group="+groupId,{},showSuccess,'html');
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
    destroyForm();
    return false;
  }
  
  function getDataTable() {
    var id = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable="+id,{},function(data){
        $("#response").html(data);
    },'html');
  }

  </script>
  <?php
  include_once("footer.php");
  }
?>