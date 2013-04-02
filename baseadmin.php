<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "baseadmin");
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
  $query = "SELECT Base_ID, Name FROM EH_Bases Order By Name";
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
  $query = "SELECT Base_ID, Name, BT_ID, Types, Link, Mission, Notes, Master_ID FROM EH_Bases WHERE Base_ID=$id";
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
          <td><label for="btid">Base Type: </label></td>
          <td>
    <select name="btid" id="btid" >
    <?php
    $query1 = "SELECT BT_ID, Name FROM EH_Bases_Types Order By GroupLevel, SortOrder";
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
          <td><label for="stid1">Ship Type: </label></td>
          <td>
    <select name="stid1" id="stid1" >
      <option value="0"<? if($ships[0]==0 || $ships[0]=="") echo " selected=\"selected\""; ?>>None</option>
    <?php
    $ships = explode(";", $values[3]);
    $query1 = "SELECT Ship_ID, Name FROM EH_Ships Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$ships[0])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="stid2">Ship Type (Second Type): </label></td>
          <td>
    <select name="stid2" id="stid2" >
      <option value="0"<? if($ships[1]==0 || $ships[1]=="") echo " selected=\"selected\""; ?>>None</option>
    <?php
    $query1 = "SELECT Ship_ID, Name FROM EH_Ships Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$ships[1])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="stid3">Ship Type (Third Type): </label></td>
          <td>
    <select name="stid3" id="stid3" >
      <option value="0"<? if($ships[2]==0 || $ships[2]=="") echo " selected=\"selected\""; ?>>None</option>
    <?php
    $query1 = "SELECT Ship_ID, Name FROM EH_Ships Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$ships[2])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="link">Link: </label></td>
          <td><textarea name="link" id="link" style="width:400px; height:120px"><?=stripslashes($values[4])?></textarea></td>
        </tr>
        <tr>
          <td><label for="mission">Mission: </label></td>
          <td><textarea name="mission" id="mission" style="width:400px; height:120px"><?=stripslashes($values[5])?></textarea></td>
        </tr>
        <tr>
          <td><label for="notes">Notes: </label></td>
          <td><textarea name="notes" id="notes" style="width:400px; height:120px"><?=stripslashes($values[6])?></textarea></td>
        </tr>
        <tr>
          <td><label for="master">Master Base: </label></td>
          <td>
    <select name="master" id="master" >
      <option value="0"<? if($values[7]==0) echo " selected=\"selected\""; ?>>None</option>
    <?php
    $query1 = "SELECT Base_ID, Name FROM EH_Bases Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[7])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
      </table>
    </form>
<?
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $btid = mysql_real_escape_string($_POST['btid'], $mysql_link);
  $stid1 = mysql_real_escape_string($_POST['stid1'], $mysql_link);
  $stid2 = mysql_real_escape_string($_POST['stid2'], $mysql_link);
  $stid3 = mysql_real_escape_string($_POST['stid3'], $mysql_link);
  $link = mysql_real_escape_string($_POST['link'], $mysql_link);
  $mission = mysql_real_escape_string($_POST['mission'], $mysql_link);
  $notes = mysql_real_escape_string($_POST['notes'], $mysql_link);
  $master = mysql_real_escape_string($_POST['master'], $mysql_link);
  $type = $stid1;
  if($stid2)
    $type.=";$stid2";
  if($stid3)
    $type.=";$stid3";
  $query = "UPDATE EH_Bases Set Name='$name', BT_ID='$btid', Types='$type', Link='$link', Mission='$mission', Notes='$notes', Master_ID='$master' WHERE Base_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $btid = mysql_real_escape_string($_POST['btid'], $mysql_link);
  $stid1 = mysql_real_escape_string($_POST['stid1'], $mysql_link);
  $stid2 = mysql_real_escape_string($_POST['stid2'], $mysql_link);
  $stid3 = mysql_real_escape_string($_POST['stid3'], $mysql_link);
  $link = mysql_real_escape_string($_POST['link'], $mysql_link);
  $mission = mysql_real_escape_string($_POST['mission'], $mysql_link);
  $notes = mysql_real_escape_string($_POST['notes'], $mysql_link);
  $master = mysql_real_escape_string($_POST['master'], $mysql_link);
  $type = $stid1;
  if($stid2)
    $type.=";$stid2";
  if($stid3)
    $type.=";$stid3";
  $query = "INSERT INTO EH_Bases (Name, BT_ID, Types, Link, Mission, Notes, Master_ID) VALUES('$name', '$btid', '$type', '$link', '$mission', '$notes', '$master')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Bases WHERE Base_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Base deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Base Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Base</span>
      </a>
  </p>
  <div id="response"></div>
  <div id="add-form" title="Add New Base">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="btid">Base Type: </label></td>
        <td>
    <select name="btid" id="btid" >
    <?php
    $query1 = "SELECT BT_ID, Name FROM EH_Bases_Types Order By GroupLevel, SortOrder";
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
        <td><label for="stid1">Ship Type: </label></td>
        <td>
    <select name="stid1" id="stid1" >
      <option value="0">None</option>
    <?php
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
        <td><label for="stid2">Ship Type (Second Type): </label></td>
        <td>
    <select name="stid2" id="stid2" >
      <option value="0">None</option>
    <?php
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
        <td><label for="stid3">Ship Type (Third Type): </label></td>
        <td>
    <select name="stid3" id="stid3" >
      <option value="0">None</option>
    <?php
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
        <td><label for="link">Link: </label></td>
        <td><textarea name="link" id="link" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="mission">Mission: </label></td>
        <td><textarea name="mission" id="mission" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="notes">Notes: </label></td>
        <td><textarea name="notes" id="notes" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="master">Master Base: </label></td>
        <td>
    <select name="master" id="master" >
      <option value="0">None</option>
    <?php
    $query1 = "SELECT Base_ID, Name FROM EH_Bases Order By Name";
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

  <div id="editArea" title="Edit Academy">
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
        modal: true,
        width: 500,
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
        modal: true,
        width: 500,
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