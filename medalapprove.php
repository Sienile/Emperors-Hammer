<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "medalapprove");
$groupsaccess = AccessGroups($_SESSION['EHID'], "medalapprove");
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
      <td width="10%">Medal</td>
      <td width="50%">Reason</td>
      <td width="10%">Approve</td>
      <td width="10%">Deny</td>
    </tr>
    <?php
  $query = "SELECT MC_ID, Member_ID, Awarder_ID, Status, Reason, Group_ID, Medal_ID FROM EH_Medals_Complete WHERE Group_ID=$datatable AND (Status=0 OR Status=3) Order By DateAwarded";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[3]==0)
      $color="#00FF00";
    elseif($values[3]==3)
      $color="#FF0000";
    ?>
    <tr>
      <td width="10%"><span style="color:<?=$color?>;"><?=RankAbbrName($values[1], $values[5], 1);?></span></td>
      <td width="10%"><?=RankAbbrName($values[2], $values[5], 1);?></td>
      <td width="10%"><?
  $query1 = "SELECT Name from EH_Medals WHERE Medal_ID=$values[6]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    echo stripslashes($values1[0]);
    }
?></td>
      <td width="50%"><span style="color:<?=$color?>;"><?=stripslashes($values[4])?></span></td>
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
  $query = "SELECT Awarder_ID, Member_ID, Group_ID, Medal_ID FROM EH_Medals_Complete WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $for = RankAbbrName($values[1], $values[2], 0);
    $from = RankAbbrName($values[0], $values[2], 0);
    $fromid=$values[0];
    $query1 = "SELECT Name from EH_Medals WHERE Medal_ID=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medalname = stripslashes($values1[0]);
      }
    }
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$fromid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $frommail = stripslashes($values[0]);
    }
  $recipient = "$from <$frommail>";
  $subject = "Medal Request for $for Denied";
  $body .= "Your request to award $for a $medalname has been Denied for the following Reason:\n";
  $body .= stripslashes($reason);
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "UPDATE EH_Medals_Complete Set Status=2, RejectReason='$reason' WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Request Denied successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "SELECT Member_ID, Awarder_ID, Group_ID, Medal_ID, Reason FROM EH_Medals_Complete WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $forid=$values[0];
    $fromid=$values[1];
    $group = $values[2];
    $reason = stripslashes($values[4]);
    $query1 = "SELECT Name from EH_Medals WHERE Medal_ID=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medalname = stripslashes($values1[0]);
      }
    }
  CalculateFCHG($forid);
  //Update Rank
  $time = time();
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
  $subject = "$for has been awarded a $medalname!";
  $body .= "$for has now been awarded a $medalname for the following reason:\n";
  $body .= $reason;
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "Update EH_Medals_Complete Set Status=1, DateAwarded='$time' WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Medal Awarded successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Medal Approval</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Medal Requests</label>
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

  <div id="editArea" title="Deny Medal">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  function getEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id,{},function(data){
      $("#editArea").html(data);
    },'html').complete(function() {
      $("#editArea").dialog("open");
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
  });
  </script>
  <?php
  include_once("footer.php");
  }
?>