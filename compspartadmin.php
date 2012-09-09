<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "compsadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "compsadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="80%">Member - Score</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT EH_Competitions_Participants.CP_ID, EH_Members.Name, EH_Competitions_Participants.Score FROM EH_Competitions_Participants, EH_Members WHERE EH_Competitions_Participants.Comp_ID=$datatable AND EH_Competitions_Participants.Member_ID=EH_Members.Member_ID Order By EH_Competitions_Participants.Score, EH_Members.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="80%"><?=stripslashes($values[1])?> - <?=$values[2]?></td>
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
  $query = "SELECT CP_ID, DateParticipated, Comments, Score, Unit_ID FROM EH_Competitions_Participants WHERE CP_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
  <form id="editForm" method="POST">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="date">Date: </label></td>
            <?
            $date = date("m/d/Y", $values[1]);
            ?>
          <td>
              <div id="date_edit"></div>
              <input type="hidden" name="date" id="date" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="unit">Unit: </label></td>
          <td>
    <select name="unit" id="unit" >
    <?php
    $query1 = "SELECT Unit_ID, Name FROM EH_Units";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[4])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>

        <tr>
          <td><label for="comments">Comments: </label></td>
          <td><textarea name="comments" id="comments" style="width:400px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
        <tr>
          <td><label for="score">Score: </label></td>
          <td><input type="text" name="score" id="score" value="<?=stripslashes($values[3])?>"></td>
        </tr>
      </table>
    </form>
<script type="text/javascript">
$(function() {
    $("#date_edit").datepicker(
        {dateFormat: "mm/dd/yy" ,
         defaultDate: new Date("<?=date("M d, Y",$values[1])?>"),
         altField: "#date"
        }
    );
});
</script>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $date = mysql_real_escape_string($_POST['date'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $unit = mysql_real_escape_string($_POST['unit'], $mysql_link);
  $comments = mysql_real_escape_string($_POST['comments'], $mysql_link);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $query = "UPDATE EH_Competitions_Participants Set Unit_ID='$unit', DateParticipated='$date', Comments='$comments', Score='$score' WHERE CP_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Participation updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link); //comp
  $date = mysql_real_escape_string($_POST['datea'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $member = mysql_real_escape_string($_POST['member'], $mysql_link);
  $unit = mysql_real_escape_string($_POST['unit'], $mysql_link);
  $comments = mysql_real_escape_string($_POST['comments'], $mysql_link);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $query = "INSERT INTO EH_Competitions_Participants
                (Comp_ID, Member_ID, Unit_ID, DateParticipated, Comments, Score)
                VALUES('$group', '$member', '$unit', '$date', '$comments', '$score')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Participation inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Competitions_Participants WHERE CP_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Participant deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Competitions Participants Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selAcad">Select the Group to modify their Competitions</label>
    <?php $ga = implode (" OR Group_ID=", $groupsaccess); ?>
  <select name="selAcad" id="selAcad" onChange="getComps()">
    <option value="0">No Group</option>
  <?php $ga = implode (" OR Group_ID=", $groupsaccess);
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
  </select><br>
    <label for="selGroup">Select the Competition to modify their Participants</label>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Participant</span>
    </a>
  </p>
  <div id="add-form" title="Add New Participant">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="member">Member: </label></td>
        <td>
    <select name="member" id="member">
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
      </tr>
      <tr>
        <td><label for="datea">Date: </label></td>
        <td>
            <div id="date_add"></div>
            <input type="hidden" name="datea" id="datea">
        </td>
      </tr>
      <tr>
        <td><label for="unit">Unit: </label></td>
        <td>
    <select name="unit" id="unit" >
    <?php
    $query1 = "SELECT Unit_ID, Name FROM EH_Units";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
      </tr>
      <tr>
        <td><label for="comments">Comments: </label></td>
        <td><textarea name="comments" id="comments" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="score">Score: </label></td>
        <td><input type="text" name="score" id="score"></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Participant">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  $(function() {
    $("#date_add").datepicker({altField: '#datea'});
  });

  function getComps(){
	var group = $("#selAcad option:selected").val();
	var postvars = {"id":group}
	$("#selGroup").empty();
	$("#selGroup").append('<option value="0">No Competition</option>');
	getAdminJSONdata("getCompsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#selGroup").append('<option value="'+item.Comp_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }
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