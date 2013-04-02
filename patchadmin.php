<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "patchadmin");
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
  $query = "SELECT Patch_ID, Name FROM EH_Patches Order By Name";
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
  $query = "SELECT Patch_ID, Name, Description, Filename, Ship_ID, PC_ID, Platform_ID, Creator, Image FROM EH_Patches WHERE Patch_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
    <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Name:</label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="desc">Description:</label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
        <tr>
          <td><label for="creator">Creator:</label></td>
          <td><textarea name="creator" id="creator" style="width:400px; height:120px"><?=stripslashes($values[7])?></textarea></td>
        </tr>
        <tr>
          <td><label for="file">Filename:</label></td>
          <td><input type="text" name="file" id="file" value="<?=stripslashes($values[3])?>"></td>
        </tr>
        <tr>
          <td><label for="img">Image:</label></td>
          <td><input type="text" name="img" id="img" value="<?=stripslashes($values[8])?>"></td>
        </tr>
        <tr>
          <td><label for="ship">Ship:</label></td>
          <td>
    <select name="ship" id="ship">
      <option value="0"<? if($values[4]==0) echo " selected=\"selected\"";?>>Not applicable</option>
    <?php
    $query1 = "SELECT Ship_ID, Name FROM EH_Ships Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[4])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="pcid">Patch Category:</label></td>
          <td>
    <select name="pcid" id="pcid">
    <?php
    $query1 = "SELECT PC_ID, Name FROM EH_Patches_Categories Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[5])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="plt">Platform:</label></td>
          <td>
    <select name="plt" id="plt">
    <?php
    $query1 = "SELECT Platform_ID, Name FROM EH_Platforms Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[6])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
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
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $creator = mysql_real_escape_string($_POST['creator'], $mysql_link);
  $file = mysql_real_escape_string($_POST['file'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $ship = mysql_real_escape_string($_POST['ship'], $mysql_link);
  $pcid = mysql_real_escape_string($_POST['pcid'], $mysql_link);
  $plt = mysql_real_escape_string($_POST['plt'], $mysql_link);
  $now = time();
  $query = "UPDATE EH_Patches Set Ship_ID='$ship', Name='$name', Filename='$file', PC_ID='$pcid', Platform_ID='$plt', Creator='$creator', UpdatedDate='$now', Image='$img', Description='$desc' WHERE Patch_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $creator = mysql_real_escape_string($_POST['creator'], $mysql_link);
  $file = mysql_real_escape_string($_POST['file'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $ship = mysql_real_escape_string($_POST['ship'], $mysql_link);
  $pcid = mysql_real_escape_string($_POST['pcid'], $mysql_link);
  $plt = mysql_real_escape_string($_POST['plt'], $mysql_link);
  $now = time();
  $person = $_SESSION['EHID'];
  $query = "INSERT INTO EH_Patches (Ship_ID, Name, Filename, PC_ID, Platform_ID, Creator, ReleasedDate, UpdatedDate, Image, Description) VALUES('$ship', '$name', '$file', '$pcid', '$plt', '$creator', '$now', '$now', '$img', '$desc')";
  $result = mysql_query($query, $mysql_link);
  $id = mysql_insert_id($mysql_link);
  $poster = RankAbbrName($person, PriGroup($person), 1);
  $query1 = "SELECT Name FROM EH_Platforms Where Platform_ID=$plt";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pltname = stripslashes($values1[0]);
    }
  $query1 = "SELECT Name FROM EH_Patches_Categories Where PC_ID=$pcid";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pcname = stripslashes($values1[0]);
    }
  $topic = "New Patch Released! $pltname $pcname $name";
  $body = "A new patch has been added to the patch archive! $pltname $pcname $name has been added! You can get it in the patch archive, or download it <a href=\"$file\">here</a>.";
  $query = "INSERT INTO EH_News (Group_ID, Topic, Poster, Poster_ID, DatePosted, Body) VALUES(1, '$topic', '$poster', '$person', '$now', '$body')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Patches WHERE Patch_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Patches_Bugs WHERE Patch_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Patches_Reviews WHERE Patch_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Patches WHERE Patch_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Patch deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Patch Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Patch</span>
      </a>
  </p>
  <div id="add-form" title="Add New Patch">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name:</label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="desc">Description:</label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="creator">Creator:</label></td>
        <td><textarea name="creator" id="creator" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="file">Filename:</label></td>
        <td><input type="text" name="file" id="file"></td>
      </tr>
      <tr>
        <td><label for="img">Image:</label></td>
        <td><input type="text" name="img" id="img"></td>
      </tr>
      <tr>
        <td><label for="ship">Ship:</label></td>
        <td>
    <select name="ship" id="ship">
      <option value="0">Not applicable</option>
    <?
    $query1 = "SELECT Ship_ID, Name FROM EH_Ships Order By Name";
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
        <td><label for="pcid">Patch Category:</label></td>
        <td>
    <select name="pcid" id="pcid">
    <?
    $query1 = "SELECT PC_ID, Name FROM EH_Patches_Categories Order By SortOrder";
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
        <td><label for="plt">Platform:</label></td>
        <td>
    <select name="plt" id="plt">
    <?php
    $query1 = "SELECT Platform_ID, Name FROM EH_Platforms Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Patch">
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