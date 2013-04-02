<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "compspersonadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "compspersonadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="80%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT Comp_ID, Name, Approved FROM EH_Competitions WHERE Group_ID=$datatable AND Admin_ID=".$_SESSION['EHID']." Order By StartDate";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[2]) {
      $color="#00FF00";
      $status="Approved";
      }
    else {
      $color="#FF0000";
      $status="Not Approved";
      }
    ?>
      <tr>
        <td width="80%"><span style="color:<?=$color?>"><?=stripslashes($values[1])?> - <?=$status?></span></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a href="#" id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php
    } // End for loop
    ?>
  </table>
<?php
  } // end if $_GET['datatable']
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Comp_ID, Name, Admin_ID, StartDate, EndDate, Scope, Awards, Description FROM EH_Competitions WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="startdate">Start Date: </label></td>
            <?
            $date = date("m/d/Y", $values[3]);
            ?>
          <td>
              <div id="startdate_edit"></div>
              <input type="hidden" name="startdate" id="startdate" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="enddate">End Date: </label></td>
            <?
            $date = date("m/d/Y", $values[4]);
            ?>
          <td>
              <div id="enddate_edit"></div>
              <input type="hidden" name="enddate" id="enddate" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="scope">Scope: </label></td>
          <td><textarea name="scope" id="scope" style="width:400px; height:120px"><?=stripslashes($values[5])?></textarea></td>
        </tr>
        <tr>
          <td><label for="awards">Awards: </label></td>
          <td><textarea name="awards" id="awards" style="width:400px; height:120px"><?=stripslashes($values[6])?></textarea></td>
        </tr>
        <tr>
          <td><label for="desc">Comp Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[7])?></textarea></td>
        </tr>
      </table>
    </form>

<script type="text/javascript">
$(function() {
    $("#startdate_edit").datepicker(
        {dateFormat: "mm/dd/yy" ,
         defaultDate: new Date("<?=date("M d, Y",$values[3])?>"),
         altField: "#startdate"
        }
    );
    $("#enddate_edit").datepicker(
        {dateFormat: "mm/dd/yy" ,
         defaultDate: new Date("<?=date("M d, Y",$values[4])?>"),
         altField: "#enddate"
        }
    );
});
</script>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $startdate = mysql_real_escape_string($_POST['startdate'], $mysql_link);
  $startdate = explode("/", $startdate);
  $startdate = mktime(0, 0, 0, $startdate[0], $startdate[1], $startdate[2]);
  $enddate = mysql_real_escape_string($_POST['enddate'], $mysql_link);
  $enddate = explode("/", $enddate);
  $enddate = mktime(0, 0, 0, $enddate[0], $enddate[1], $enddate[2]);
  $scope = mysql_real_escape_string($_POST['scope'], $mysql_link);
  $awards = mysql_real_escape_string($_POST['awards'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $query = "UPDATE EH_Competitions Set Name='$name', StartDate='$startdate', EndDate='$enddate', Scope='$scope', Awards='$awards', Description='$desc' WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $admin = mysql_real_escape_string($_SESSION['EHID'], $mysql_link);
  $startdate = mysql_real_escape_string($_POST['startdatea'], $mysql_link);
  $startdate = explode("/", $startdate);
  $startdate = mktime(0, 0, 0, $startdate[0], $startdate[1], $startdate[2]);
  $enddate = mysql_real_escape_string($_POST['enddatea'], $mysql_link);
  $enddate = explode("/", $enddate);
  $enddate = mktime(0, 0, 0, $enddate[0], $enddate[1], $enddate[2]);
  $scope = mysql_real_escape_string($_POST['scope'], $mysql_link);
  $awards = mysql_real_escape_string($_POST['awards'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  if($group) {
    $query = "INSERT INTO EH_Competitions
                (Name, Admin_ID, StartDate, EndDate, Scope, Awards, Description, Group_ID, Approved)
                VALUES('$name', '$admin', '$startdate', '$enddate', '$scope', '$awards', '$desc', '$group', '0')";
    $result = mysql_query($query, $mysql_link);
    $query = "SELECT EH_Members.Member_ID, EH_Members.Email FROM EH_Members, EH_Members_Positions, EH_Groups WHERE EH_Groups.Group_ID=EH_Members_Positions.Group_ID AND EH_Members_Positions.Member_ID=EH_Members.Member_ID AND EH_Members_Positions.Position_ID=EH_Groups.CompAdmin AND EH_Groups.Group_ID=$group";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $for=RankAbbrName($values[0], $group, 0);
      $formail=$values[1];
      }
    $recipient = "$for <$formail>";
    $subject = "Competition Request Submitted";
    $body .= "A request has been submitted for a new competition($name) in the ".GroupName($group)." Group. Please login to the site to approve it.\n";
    $body .= stripslashes($reason);
    $body .= "\n\nThis message was generated as an automatic e-mail after consideration of the request.";
    $headers .= "From: $postmaster\n";
    $headers .= "X-Mailer: PHP\n"; // mailer
    $headers .= "Return-Path: $postmaster\n";  // Return path for errors
    //Mail it!
    $grade = mail($recipient, $subject, $body, $headers);
    if($result)
      echo "<p>".stripslashes($name)." submitted sucessfully for approval.</p>\n";
    else
      echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
    }
  else {
    echo "<p>No Group Selected</p>";
    }
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Competitions WHERE Comp_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Competition deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer - Manage your Competitions Administration Center</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify your competitions</label>
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
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Submit New Competition</span>
    </a>
  </p>
  <div id="add-form" title="Add New Competition">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="startdatea">Start Date: </label></td>
          <?
          $date = date("m/d/Y", $values[3]);
          ?>
        <td>
              <div id="startdate_add"></div>
              <input type="hidden" name="startdatea" id="startdatea" />
        </td>
      </tr>
      <tr>
        <td><label for="enddatea">End Date: </label></td>
          <?
          $date = date("m/d/Y", $values[4]);
          ?>
        <td>
            <div id="enddate_add"></div>
            <input type="hidden" name="enddatea" id="enddatea" />
        </td>
      </tr>
      <tr>
        <td><label for="scope">Scope: </label></td>
        <td><textarea name="scope" id="scope" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="awards">Awards: </label></td>
        <td><textarea name="awards" id="awards" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="desc">Comp Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Competition">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  $(function() {
    $("#startdate_add").datepicker({altField: '#startdatea'});
  });

  $(function() {
    $("#enddate_add").datepicker({altField: '#enddatea'});
  });

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
  
  function moveUp(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?up="+id+"&group="+groupId,{},showSuccess,'html');
  }

  function moveDown(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?down="+id+"&group="+groupId,{},showSuccess,'html');
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function postAdd() {
    var group = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add=true&group='+group,
        success: showSuccess
    }
    $("#addForm").ajaxSubmit(options);
    return false;
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
    $("#add-form").dialog({
        autoOpen: false,
        width: 550,
        modal: true,
        buttons: {
          "Submit": function() {
            postAdd();
            $( this ).dialog( "close" );
            },
          Cancel: function() {
            $( this ).dialog( "close" );
            }
          },
        close: function() {
          document.forms["addForm"].reset();
          }
      });

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