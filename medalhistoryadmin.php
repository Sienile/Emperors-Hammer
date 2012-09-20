<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "certadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "certadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  $acad = mysql_real_escape_string($_GET['acad'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="80%">Medal</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT EH_Medals_Complete.MC_ID, EH_Medals.Name FROM EH_Medals_Complete, EH_Medals WHERE EH_Medals.Medal_ID=EH_Medals_Complete.Medal_ID AND EH_Medals_Complete.Member_ID=$datatable AND EH_Medals_Complete.Group_ID=$acad AND EH_Medals_Complete.Status=1 ORDER By EH_Medals.SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="80%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a href="#" id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php
    } // End for loop
    ?>
  </table>
<?php
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT MC_ID, DateAwarded, Reason, Medal_ID FROM EH_Medals_Complete WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT Name FROM EH_Medals WHERE Medal_ID=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $name=$values1[0];
      }
    ?>
    <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="date"><?=$name?>: Date Awarded: </label></td>
            <?
            $date = date("m/d/Y", $values[1]);
            ?>
          <td>
              <div id="date_edit"></div>
              <input type="hidden" name="date" id="date" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="reason"><?=$name?>: Reason: </label></td>
          <td><textarea name="reason" id="reason" style="width:400px; height:120px"><?=stripslashes($values[2])?></textarea></td>
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
  $reason = mysql_real_escape_string($_POST['reason'], $mysql_link);
  $query = "UPDATE EH_Medals_Complete Set DateAwarded='$date', Reason='$reason' WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $member = mysql_real_escape_string($_GET['member'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $medal = mysql_real_escape_string($_POST['selMedal'], $mysql_link);
  $qty = mysql_real_escape_string($_POST['qty'], $mysql_link);
  $date = mysql_real_escape_string($_POST['datea'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $reason = mysql_real_escape_string($_POST['reason'], $mysql_link);
  for($i=0; $i<$qty; $i++) {
    $query = "INSERT INTO EH_Medals_Complete
                (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status)
                VALUES('$member', '$medal', '".$_SESSION['EHID']."', '$group', '$date', '$reason', '1')";
    $result = mysql_query($query, $mysql_link);
    }
  if($result)
    echo "<p>Record inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Medals_Complete WHERE MC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Medals History Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selAcad">Select the Group to modify their Awards</label>
  <select name="selAcad" id="selAcad" onChange="getTraining();getDataTable()">
    <option value="0">No Group</option>
  <?php
  $ga = implode (" OR Group_ID=", $groupsaccess);
  $query = "SELECT Group_ID, Name FROM EH_Groups";
  if($ga) {
    $query .=" WHERE Group_ID=$ga";
    }
  $query.=" Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
  }
?>
  </select><br>
    <label for="selGroup">Select the Person to modify their Medals History</label>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
    <option value="0">No Member</option>
  <?php $ga = implode (" OR Group_ID=", $groupsaccess);
  $query = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])." ($values[0])</option>\n";
  }
?>
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Record</span>
    </a>
  </p>
  <div id="add-form" title="Add New Record">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="selMedal">Medal: </label></td>
        <td>
        <select name="selMedal" id="selMedal">
        </select>
        </td>
      </tr>
      <tr>
        <td><label for="datea">Date Awarded: </label></td>
        <td>
            <div id="date_add"></div>
            <input type="hidden" name="datea" id="datea" />
        </td>
      </tr>
      <tr>
        <td><label for="reason">Reason: </label></td>
        <td><textarea name="reason" id="reason" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="qty">Quantity: </label></td>
        <td><input type="text" name="qty" id="qty" value="1" /></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Record">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  function getTraining(){
	var group = $("#selAcad option:selected").val();
	var postvars = {"id":group}
	$("#selMedal").empty();
	$("#selMedal").append('<option value="0">No Medal</option>');
	getAdminJSONdata("getMedalsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#selMedal").append('<option value="'+item.Medal_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

  $(function() {
    $("#date_add").datepicker({altField: '#datea'});
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
  
  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function postAdd() {
    var group = $("#selAcad option:selected").val();
    var member = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add=true&group='+group+'&member='+member,
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
    var group = $("#selAcad option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable="+id+"&acad="+group,{},function(data){
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