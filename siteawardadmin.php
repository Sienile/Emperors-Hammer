<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "siteawardadmin");
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
  $query = "SELECT SA_ID, Name FROM EH_Site_Awards Order By Name";
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
  $query = "SELECT SA_ID, Name, Description, Reason, DateAwarded, Banner, Link FROM EH_Site_Awards WHERE SA_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Site Awards">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="desc">Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
        <tr>
          <td><label for="reason">Reason: </label></td>
          <td><textarea name="reason" id="reason" style="width:400px; height:120px"><?=stripslashes($values[3])?></textarea></td>
        </tr>
        <tr>
          <td><label for="date">Date: </label></td>
            <?
            $date = date("m/d/Y", $values[4]);
            ?>
          <td>
              <div id="date_edit"></div>
              <input type="hidden" name="date" id="date" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="banner">Banner: </label></td>
          <td><input type="text" name="banner" id="banner" value="<?=stripslashes($values[5])?>"></td>
        </tr>
        <tr>
          <td><label for="link">Link: </label></td>
          <td><input type="text" name="link" id="link" value="<?=stripslashes($values[6])?>"></td>
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
<script type="text/javascript">
$(function() {
    $("#date_edit").datepicker(
        {dateFormat: "mm/dd/yy" ,
         defaultDate: new Date("<?=date("M d, Y",$values[4])?>"),
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
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $reason = mysql_real_escape_string($_POST['reason'], $mysql_link);
  $date = mysql_real_escape_string($_POST['date'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $link = mysql_real_escape_string($_POST['link'], $mysql_link);
  $query = "UPDATE EH_Site_Awards Set Name='$name', Description='$desc', Reason='$reason', DateAwarded='$date', Banner='$banner', Link='$link' WHERE SA_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $reason = mysql_real_escape_string($_POST['reason'], $mysql_link);
  $date = mysql_real_escape_string($_POST['date'], $mysql_link);
  $date = mysql_real_escape_string($_POST['datea'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $link = mysql_real_escape_string($_POST['link'], $mysql_link);
  $query = "INSERT INTO EH_Site_Awards (Name, Description, Reason, DateAwarded, Banner, Link) VALUES('$name', '$desc', '$reason', '$date', '$banner', '$link')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Site_Awards WHERE SA_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Award deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Site Awards Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a name="adddialog" onclick="$('#add').show()">
          <span style="color:#6699CC;">Add New Site Award</span>
      </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Site Award">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="reason">Reason: </label></td>
        <td><textarea name="reason" id="reason" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="datea">Date: </label></td>
        <td>
            <div id="date_add"></div>
            <input type="hidden" name="datea" id="datea">
        </td>
      </tr>
      <tr>
        <td><label for="banner">Banner: </label></td>
        <td><input type="text" name="banner" id="banner"></td>
      </tr>
      <tr>
        <td><label for="link">Link: </label></td>
        <td><input type="text" name="link" id="link"></td>
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

  <div id="editdialog" title="Edit Site Award" >

  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  $(function() {
    $("#date_add").datepicker({altField: '#datea'});
  });

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

