<?
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if($_GET['datatable']) {
  ?>
  <table>
    <tr>
      <td width="80%">Title</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT Fiction_ID, Title FROM EH_Fiction WHERE Member_ID=".$_SESSION['EHID']." Order By DatePosted";
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
  $query = "SELECT Fiction_ID, Title, Body FROM EH_Fiction WHERE Fiction_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="title">Title:</label></td>
          <td><input type="text" name="title" id="title" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="resetdate">Reset Date:</label></td>
          <td>
              <input type="checkbox" name="resetdate" id="resetdate" value="1">
          </td>
        </tr>
        <tr>
          <td><label for="body">Body:</label></td>
          <td><textarea name="body" id="body" style="width:510px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $title = mysql_real_escape_string($_POST['title'], $mysql_link);
  $body = mysql_real_escape_string($_POST['body'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['resetdate'], $mysql_link);
  if($avail)
    $date=time();
  else
    $date=0;
  $query = "UPDATE EH_Fiction Set Title='$title', Body='$body'";
  if($date)
    $query.=", DatePosted=$date";
   $query.=" WHERE Fiction_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($title)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $title = mysql_real_escape_string($_POST['title'], $mysql_link);
  $body = mysql_real_escape_string($_POST['abody'], $mysql_link);
  $date = time();
  $mem = $_SESSION['EHID'];
  if(strip_tags($body)==$body)
    $body=nl2br($body);
  $query = "INSERT INTO EH_Fiction (Member_ID, Title, Body, DatePosted, Approved) VALUES('$mem', '$title', '$body', '$date', '1')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($title)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Fiction WHERE Fiction_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Story deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Fiction Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
    <a onClick="$('#add-form').dialog('open')" href="#">
        <span style="color:#6699CC;">Add New Story</span>
    </a>
  </p>
  <div id="add-form" title="Add New Story">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="title">Title:</label></td>
        <td><input type="text" name="title" id="title"></td>
      </tr>
      <tr>
        <td><label for="abody">Body:</label></td>
        <td><textarea name="abody" id="abody" style="width:510px; height:120px"></textarea></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Story">
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
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id+"&group="+groupId,{},showSuccess,'html');
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
  $(function() {
    $("#add-form").dialog({
        autoOpen: false,
        width: 600,
        modal: true,
        open: function () {
          $('#abody').ckeditor();
          },
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
          var editor = CKEDITOR.instances['abody'];
          if(editor) {
            editor.destroy(true);
            }
          document.forms["addForm"].reset();
          }
      });
      $("#editArea").dialog({
        autoOpen: false,
        width: 600,
        modal: true,
        open: function () {
          $('#body').ckeditor();
          },
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
          var editor = CKEDITOR.instances['body'];
          if(editor) {
            editor.destroy(true);
            }
            document.forms["editForm"].reset();
            }
        });
  });

  $(document).ready(getDataTable);
  </script>
  <?php
  include_once("footer.php");
  }
?>