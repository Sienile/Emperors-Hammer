<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "compapproval");
$groupsaccess = AccessGroups($_SESSION['EHID'], "compapproval");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="10%">Name</td>
      <td width="70%">Description</td>
      <td width="10%">Approve</td>
      <td width="10%">Deny</td>
    </tr>
    <?php
  $query = "SELECT Comp_ID, Name, Description, Awards, Scope, Admin_ID, Group_ID, StartDate FROM EH_Competitions WHERE Approved=0 AND Group_ID=$datatable Order By StartDate";
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
      <td width="10%"><?=$values[1]?></td>
      <td width="70%"><p>Description: <?=stripslashes(nl2br($values[2]))?></p>
      <p>Awards: <?=stripslashes(nl2br($values[3]))?></p>
      <p>Scope: <?=stripslashes(nl2br($values[4]))?></p>
      <p>Admin: <?=RankAbbrName($values[5], $values[6], 0)?></p>
      <p>Starting: <?=date("m/d/Y", $values[7])?></p></td>
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
  $query = "SELECT Admin_ID, Name, Group_ID FROM EH_Competitions WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $for = RankAbbrName($values[0], $values[2], 0);
    $forid=$values[0];
    $name=stripslashes($values[1]);
    }
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$forid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $formail = stripslashes($values[0]);
    }
  $recipient = "$for <$formail>";
  $subject = "Competition Request: $name Denied";
  $body .= "Your request for competition: $name, has been Denied for the following Reason:\n";
  $body .= stripslashes($reason);
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  $query = "DELETE FROM EH_Competitions WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Competition Denied successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "SELECT Admin_ID, Name, Group_ID FROM EH_Competitions WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $for = RankAbbrName($values[0], $values[2], 0);
    $forid=$values[0];
    $name=stripslashes($values[1]);
    }
  $query = "UPDATE EH_Competitions Set Approved=1 WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "SELECT Email FROM EH_Members WHERE Member_ID=$forid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $formail=stripslashes($values[0]);
    }
  $recipient .="$for <$formail>";
  $subject = "Competition: $name Approved!";
  $body .= "$name Competition has been approved.\n";
  $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
  $headers .= "From: $postmaster\n";
  $headers .= "X-Mailer: PHP\n"; // mailer
  $headers .= "Return-Path: $postmaster\n";  // Return path for errors
  //Mail it!
  $grade = mail($recipient, $subject, $body, $headers);
  if($result)
    echo "<p>Competition Approved successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Competition Approval</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to see pending Competitions</label>
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

  <div id="editArea" title="Deny Competition">
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