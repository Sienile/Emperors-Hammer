<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "allianceadmin");
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
  $query = "SELECT Alliance_ID, Name FROM EH_Alliances Order By Name";
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
  $query = "SELECT Alliance_ID, Name, Abbr, Description, SiteURL, Banner, Status FROM EH_Alliances WHERE Alliance_ID=$id";
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
          <td><label for="abbr">Abbr: </label></td>
          <td><input type="text" name="abbr" id="abbr" value="<?=stripslashes($values[2])?>"></td>
        </tr>
        <tr>
          <td><label for="desc">Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[3])?></textarea></td>
        </tr>
        <tr>
          <td><label for="url">URL: </label></td>
          <td><input type="text" name="url" id="url" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="banner">Banner: </label></td>
          <td><input type="text" name="banner" id="banner" value="<?=stripslashes($values[5])?>"></td>
        </tr>
        <tr>
          <td><label for="status">Status: </label></td>
          <td><select name="status" id="status">
            <option value="1"<? if($values[6]==1) echo " selected=\"selected\""; ?>>Ally</option>
            <option value="2"<? if($values[6]==2) echo " selected=\"selected\""; ?>>Neutral</option>
            <option value="3"<? if($values[6]==3) echo " selected=\"selected\""; ?>>Enemy</option>
          </select></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $status = mysql_real_escape_string($_POST['status'], $mysql_link);
  $query = "UPDATE EH_Alliances Set Name='$name', Abbr='$abbr', Description='$desc', SiteURL='$url', Banner='$banner', Status='$status' WHERE Alliance_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $status = mysql_real_escape_string($_POST['status'], $mysql_link);
  $query = "INSERT INTO EH_Alliances (Name, Abbr, Description, SiteURL, Banner, Status) VALUES('$name', '$abbr', '$desc', '$url', '$banner', '$status')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Alliances WHERE Alliance_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Alliance deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Alliance Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Alliance</span>
      </a>
  </p>
  <div id="add-form" title="Add New Alliance">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="abbr">Abbr: </label></td>
        <td><input type="text" name="abbr" id="abbr"></td>
      </tr>
      <tr>
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="url">URL: </label></td>
        <td><input type="text" name="url" id="url"></td>
      </tr>
      <tr>
        <td><label for="banner">Banner: </label></td>
        <td><input type="text" name="banner" id="banner"></td>
      </tr>
      <tr>
        <td><label for="status">Status: </label></td>
        <td><select name="status" id="status">
          <option value="1">Ally</option>
          <option value="2">Neutral</option>
          <option value="3">Enemy</option>
        </select></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Alliance">
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
    return false;
  }

  function postEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true',
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    return false;
  }

  function getDataTable() {
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=true",{},function(data){
        $("#response").html(data);
    },'html');
  }
  $(document).ready(getDataTable);

  $(function() {
    $("#add-form").dialog({
        autoOpen: false,
        width: 650,
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
        width: 650,
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