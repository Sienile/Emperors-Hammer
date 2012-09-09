<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "serveradmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if($_GET['datatable']) {
    ?>
  <table>
    <tr>
      <td width="80%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
<?php
  $query = "SELECT Server_ID, Name FROM EH_Servers Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="80%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a id="edit" onclick="getEditForm(<?=$values[0]?>)"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Server_ID, Name, ServerType, Address, Port, Password, Notes, URL FROM EH_Servers WHERE Server_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Server">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="type">Type: </label></td>
          <td><select name="type" id="type">
            <option value="0"<? if($values[2]==0) echo " selected=\"selected\""; ?>>Communications</option>
            <option value="1"<? if($values[2]==1) echo " selected=\"selected\""; ?>>Gaming</option>
          </select></td>
        </tr>
        <tr>
          <td><label for="addr">Address: </label></td>
          <td><input type="text" name="addr" id="addr" value="<?=stripslashes($values[3])?>"></td>
        </tr>
        <tr>
          <td><label for="port">Port: </label></td>
          <td><input type="text" name="port" id="port" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="pw">Password: </label></td>
          <td><input type="text" name="pw" id="pw" value="<?=stripslashes($values[5])?>"></td>
        </tr>
        <tr>
          <td><label for="notes">Notes: </label></td>
          <td><textarea name="notes" id="notes" style="width:400px; height:120px"><?=stripslashes($values[6])?></textarea></td>
        </tr>
        <tr>
          <td><label for="url">URL: </label></td>
          <td><input type="text" name="url" id="url" value="<?=stripslashes($values[7])?>"></td>
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
  $type = mysql_real_escape_string($_POST['type'], $mysql_link);
  $addr = mysql_real_escape_string($_POST['addr'], $mysql_link);
  $port = mysql_real_escape_string($_POST['port'], $mysql_link);
  $pw = mysql_real_escape_string($_POST['pw'], $mysql_link);
  $notes = mysql_real_escape_string($_POST['notes'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $query = "UPDATE EH_Servers Set Name='$name', ServerType='$type', Address='$addr', Port='$port', Password='$pw', Notes='$notes', URL='$url' WHERE Server_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $type = mysql_real_escape_string($_POST['type'], $mysql_link);
  $addr = mysql_real_escape_string($_POST['addr'], $mysql_link);
  $port = mysql_real_escape_string($_POST['port'], $mysql_link);
  $pw = mysql_real_escape_string($_POST['pw'], $mysql_link);
  $notes = mysql_real_escape_string($_POST['notes'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $query = "INSERT INTO EH_Servers (Name, ServerType, Address, Port, Password, Notes, URL) VALUES('$name', '$type', '$addr', '$port', '$pw', '$notes', '$url')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Servers WHERE Server_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Server deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Server Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a name="adddialog" onclick="$('#add').show()">
          <span style="color:#6699CC;">Add New Server</span>
      </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Server">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="type">Type: </label></td>
        <td><select name="type" id="type">
          <option value="0">Communications</option>
          <option value="1">Gaming</option>
        </select></td>
      </tr>
      <tr>
        <td><label for="addr">Address: </label></td>
        <td><input type="text" name="addr" id="addr"></td>
      </tr>
      <tr>
        <td><label for="port">Port: </label></td>
        <td><input type="text" name="port" id="port"></td>
      </tr>
      <tr>
        <td><label for="pw">Password: </label></td>
        <td><input type="text" name="pw" id="pw"></td>
      </tr>
      <tr>
        <td><label for="notes">Notes: </label></td>
        <td><textarea name="notes" id="notes" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="url">URL: </label></td>
        <td><input type="text" name="url" id="url"></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input type="submit" id="Submit" name="Submit" value="Submit" />
          <input type="reset" id="Reset" name="Reset" value="Reset" />
          <input type="button" id="Cancel" name="Cancel" value="Cancel"
                 onClick="$('#Reset').click();$('#add').hide();" />
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editdialog" title="Edit Server" >
 
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
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id,{},showSuccess,'html');
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function postAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add=true',
        success: showSuccess
    }
    $("#addForm").ajaxSubmit(options);
    $("#Cancel").click();
    return false;
  }

  function postEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true',
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getDataTable() {
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=true",{},function(data){
        $("#response").html(data);
    },'html');
  }
  $(document).ready(getDataTable);
</script>
 <?php
  include_once("footer.php");
  }
?>