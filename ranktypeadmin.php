<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "rankadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "rankadmin");
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
  $query = "SELECT RT_ID, Name FROM EH_Ranks_Types WHERE Group_ID=$datatable Order By Name";
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
  } // end if $_GET['datatable']
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT RT_ID, Name FROM EH_Ranks_Types WHERE RT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Rank Type">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
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
  $query = "UPDATE EH_Ranks_Types Set Name='$name' WHERE RT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $query = "INSERT INTO EH_Ranks_Types (Name, Group_ID) VALUES('$name', '$group')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Ranks_Types WHERE RT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Rank Type deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Rank Types Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Rank Types</label>
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
    <a name="adddialog" onClick="$('#add').show()" href="#">
        <span style="color:#6699CC;">Add New Rank Type</span>
    </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Rank Type">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
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

  <div id="editdialog" title="Edit Rank Type" refreshOnShow="true">
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