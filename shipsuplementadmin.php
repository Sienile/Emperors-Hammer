<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "shipadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if($_GET['datatable']) {
    ?>
  <table>
    <tr>
      <td width="60%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
      <td width="10%">Move Up</td>
      <td width="10%">Move Down</td>
    </tr>
<?php
  $query = "SELECT SS_ID, Name, SortOrder FROM EH_Ships_Supplement Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="60%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a id="edit" onclick="getEditForm(<?=$values[0]?>)"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
    <?php if($i>0) { ?>
          <td width="10%"><a id="up" onclick="moveUp(<?=$values[0]?>)"><span style="color:#6699CC;">Move Up</span></a></td>
    <?php }else{ ?>
          <td width="10%">Move Up</td>
    <?php }if($i+1<$rows){ ?>
          <td width="10%"><a id="down" onclick="moveDown(<?=$values[0]?>)"><span style="color:#6699CC;">Move Down</span></a></td>
    <?php } else { ?>
          <td width="10%">Move Down</td>
    <?php } ?>
      </tr>
    <?php
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['up']) {
  $id = mysql_real_escape_string($_GET['up'], $mysql_link);
  $query = "select SS_ID, SortOrder From EH_Ships_Supplement Where SS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select SS_ID From EH_Ships_Supplement Where SortOrder=$newso";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Ships_Supplement Set SortOrder=$newso Where SS_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Ships_Supplement Set SortOrder=$curso Where SS_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Supplement moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $query = "select SS_ID, SortOrder From EH_Ships_Supplement Where SS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select SS_ID From EH_Ships_Supplement Where SortOrder=$newso";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Ships_Supplement Set SortOrder=$newso Where SS_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Ships_Supplement Set SortOrder=$curso Where SS_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Supplement moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT SS_ID, Name FROM EH_Ships_Supplement WHERE SS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Supplement">
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
  $query = "UPDATE EH_Ships_Supplement Set Name='$name' WHERE SS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Ships_Supplement";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Ships_Supplement (Name, SortOrder) VALUES('$name', '$so')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Ships_Supplement WHERE SS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Ships_Supplement Set SortOrder=SortOrder-1 WHERE SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Ships_Supplement WHERE SS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Supplement deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Ship Supplement Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a name="adddialog" onclick="$('#add').show()">
          <span style="color:#6699CC;">Add New Supplement</span>
      </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Supplement">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
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

  <div id="editdialog" title="Edit Supplement" >
 
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

  function moveUp(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?up="+id,{},showSuccess,'html');
  }

  function moveDown(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?down="+id,{},showSuccess,'html');
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