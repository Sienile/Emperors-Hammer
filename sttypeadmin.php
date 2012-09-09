<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "sttadmin");
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
  $query = "SELECT SSType_ID, Name FROM EH_SSType Order By Name";
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
  $query = "SELECT SSType_ID, Name, Cert_ID, Image FROM EH_SSType WHERE SSType_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Stormtrooper Type">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="cert">Required Certificate: </label></td>
          <td>
    <select name="cert" id="cert" >
      <option value="0"<? if($values[2]==0) echo " selected=\"selected\""; ?>>No Required Certificate</option>
    <?php
    $query1 = "SELECT Training_ID, Name FROM EH_Training WHERE TAc_ID=5 Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[2])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="img">Image: </label></td>
          <td><input type="text" name="img" id="img" value="<?=stripslashes($values[3])?>"></td>
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
  $cert = mysql_real_escape_string($_POST['cert'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $query = "UPDATE EH_SSType Set Name='$name', Cert_ID='$cert', Image='$img' WHERE SSType_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $cert = mysql_real_escape_string($_POST['cert'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $query = "INSERT INTO EH_SSType (Name, Cert_ID, Image) VALUES('$name', '$cert', '$img')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Members_Special_Areas WHERE Value=$id AND SA_ID=3";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_SSType WHERE SSType_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Stormtrooper Type deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Stormtrooper Type Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a name="adddialog" onclick="$('#add').show()">
          <span style="color:#6699CC;">Add New Stormtrooper Type</span>
      </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Stormtrooper Type">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name"></td>
        </tr>
        <tr>
          <td><label for="cert">Required Certificate: </label></td>
          <td>
    <select name="cert" id="cert" >
      <option value="0">No Required Certificate</option>
    <?php
    $query1 = "SELECT Training_ID, Name FROM EH_Training WHERE TAc_ID=5 Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="img">Image: </label></td>
          <td><input type="text" name="img" id="img"></td>
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

  <div id="editdialog" title="Edit Stormtrooper Type" >
 
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