<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "medaladmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "medaladmin");
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
  $query = "SELECT EH_Medals_Upgrades.MU_ID, EH_Medals_Upgrades.Name, EH_Medals.Name FROM EH_Medals_Upgrades, EH_Medals WHERE EH_Medals_Upgrades.Group_ID=$datatable AND EH_Medals_Upgrades.Medal_ID=EH_Medals.Medal_ID Order By EH_Medals.SortOrder, EH_Medals_Upgrades.Lower";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="80%"><?=stripslashes($values[2])?> - <?=stripslashes($values[1])?></td>
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
  $query = "SELECT EH_Medals.Name, EH_Medals_Upgrades.Name, EH_Medals_Upgrades.Abbr, EH_Medals_Upgrades.Lower, EH_Medals_Upgrades.Upper, EH_Medals_Upgrades.Recycles FROM EH_Medals, EH_Medals_Upgrades WHERE EH_Medals_Upgrades.MU_ID=$id AND EH_Medals.Medal_ID=EH_Medals_Upgrades.Medal_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Medal Upgrade">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name"><?=stripslashes($values[0])?> - : </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="abbr">Abbr: </label></td>
          <td><input type="text" name="abbr" id="abbr" value="<?=stripslashes($values[2])?>"></td>
        </tr>
        <tr>
          <td><label for=\"recycle\">Recycles: </label></td>
          <td>
              <input type="checkbox" name="recycle" id="recycle" value="1" <?=($values[5]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="lower">Lower: </label></td>
          <td><input type="text" name="lower" id="lower" value="<?=stripslashes($values[3])?>"></td>
        </tr>
        <tr>
          <td><label for="upper">Upper:</label></td>
          <td><input type="text" name="upper" id="upper" value="<?=stripslashes($values[4])?>"></td>
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
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['recycle'], $mysql_link);
  if($avail)
    $avail=1;
  else
    $avail=0;
  $lower = mysql_real_escape_string($_POST['lower'], $mysql_link);
  $upper = mysql_real_escape_string($_POST['upper'], $mysql_link);
  $query = "UPDATE EH_Medals_Upgrades Set Name='$name', Abbr='$abbr', Recycles='$avail', Lower='$lower', Upper='$upper' WHERE MU_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $medal = mysql_real_escape_string($_POST['medal'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['recycle'], $mysql_link);
  if($avail)
    $avail=1;
  else
    $avail=0;
  $lower = mysql_real_escape_string($_POST['lower'], $mysql_link);
  $upper = mysql_real_escape_string($_POST['upper'], $mysql_link);
  $query = "INSERT INTO EH_Medals_Upgrades (Medal_ID, Name, Abbr, Group_ID, Lower, Upper, Recycles) VALUES('$medal', '$name', '$abbr', '$group', '$lower', '$upper', 'avail')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Medals_Upgrades WHERE MU_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Upgrade deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Medals Upgrades Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Upgrades</label>
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
    <a name="adddialog" onClick="getMedalsTypes(); $('#add').show()" href="#">
        <span style="color:#6699CC;">Add New Medal Upgrade</span>
    </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Medal Upgrade">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="medal">Medal: </label></td>
        <td>
            <select name="medal" id="medal">
            </select>
        </td>
      </tr>
      <tr>
        <td><label for="name">Name of Upgrade: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="abbr">Abbr: </label></td>
        <td><input type="text" name="abbr" id="abbr"></td>
      </tr>
      <tr>
        <td><label for=\"recycle\">Recycles: </label></td>
        <td>
          <input type="checkbox" name="recycle" id="recycle" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="lower">Lower: </label></td>
        <td><input type="text" name="lower" id="lower"></td>
      </tr>
      <tr>
        <td><label for="upper">Upper:</label></td>
        <td><input type="text" name="upper" id="upper"></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input type="submit" id="Submit" name="Submit"
                 value="Submit" onClick="" />
          <input type="reset" id="Reset" name="Reset" />
          <input type="button" id="Cancel" name="Cancel" value="Cancel"
                 onClick="$('#Reset').click();$('#add').hide();" />
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editdialog" title="Edit Medal Upgrade" refreshOnShow="true">
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

  function getMedalsTypes(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#medal").empty();
	getAdminJSONdata("getMedalsByGroup", postvars,function(data){
//			$("#rtid").append('<option value="0">No Medal</option>');
			if (data != false){
				$.each(data, function(index, item){
					$("#medal").append('<option value="'+item.Medal_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
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

  function postAdd() {
    var group = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add=true&group='+group,
        success: showSuccess
    }
    $("#addForm").ajaxSubmit(options);
    $("#Cancel").click();
    return false;
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