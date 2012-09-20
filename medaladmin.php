<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "medaladmin");
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
  $query = "SELECT Medal_ID, Name, SortOrder, MG_ID FROM EH_Medals Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="60%"><?
if($values[3]!=0) {
  $query1 = "SELECT Name FROM EH_Medals_Groups WHERE MG_ID=$values[3]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    echo stripslashes($values1[0])." - ".stripslashes($values[1]);
    }
}
else{
  echo stripslashes($values[1]);
}
?></td>
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
    <?php }  ?>
      </tr>
    <?php 
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['up']) {
  $id = mysql_real_escape_string($_GET['up'], $mysql_link);
  $query = "select Medal_ID, SortOrder From EH_Medals Where Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select Medal_ID From EH_Medals Where SortOrder=$newso";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Medals Set SortOrder=$newso Where Medal_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Medals Set SortOrder=$curso Where Medal_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Medal moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $query = "select Medal_ID, SortOrder From EH_Medals Where Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select Medal_ID From EH_Medals Where SortOrder=$newso";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Medals Set SortOrder=$newso Where Medal_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Medals Set SortOrder=$curso Where Medal_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Medal moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Medal_ID, Name, Abbr, MG_ID, MT_ID, Group_ID, Image, Active, ShowOnID FROM EH_Medals WHERE Medal_ID=$id";
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
          <td><label for="mgid">Medal Group: </label></td>
          <td>
    <select name="mgid" id="mgid" >
      <option value="0"<? if($values[3]==0) echo " selected=\"selected\"";?>>No Medal Group</option>
    <?php
    $query1 = "SELECT MG_ID, Name FROM EH_Medals_Groups WHERE Group_ID=$values[5] Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[3])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="mtid">Medal Type: </label></td>
          <td>
    <select name="mtid" id="mgid" >
    <?php
    $query1 = "SELECT MT_ID, Name FROM EH_Medals_Types Order By Name";
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
          <td><label for="group">Group: </label></td>
          <td>
    <select name="group" id="group" >
    <?php
    $query1 = "SELECT Group_ID, Name FROM EH_Groups Order By Group_ID";
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
          <td><label for="img">Image: </label></td>
          <td><input type="text" name="img" id="img" value="<?=stripslashes($values[6])?>"></td>
        </tr>
        <tr>
          <td><label for="active">Available: </label></td>
          <td>
              <input type="checkbox" name="active" id="active" value="1" <?=($values[7]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="show">Show on ID Line: </label></td>
          <td>
              <input type="checkbox" name="show" id="show" value="1" <?=($values[8]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
      </table>
    </form>
<?
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $mgid = mysql_real_escape_string($_POST['mgid'], $mysql_link);
  $mtid = mysql_real_escape_string($_POST['mtid'], $mysql_link);
  $group = mysql_real_escape_string($_POST['group'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $active = mysql_real_escape_string($_POST['active'], $mysql_link);
  if($active)
    $active=1;
  else
    $active=0;
  $show = mysql_real_escape_string($_POST['show'], $mysql_link);
  if($show)
    $show=1;
  else
    $show=0;
  $query = "UPDATE EH_Medals Set Name='$name', Abbr='$abbr', MG_ID='$mgid', MT_ID='$mtid', Group_ID='$group', Image='$img', Active='$active', ShowOnID='$show' WHERE Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $mgid = mysql_real_escape_string($_POST['mgid'], $mysql_link);
  $mtid = mysql_real_escape_string($_POST['mtid'], $mysql_link);
  $group = mysql_real_escape_string($_POST['group'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $active = mysql_real_escape_string($_POST['active'], $mysql_link);
  if($active)
    $active=1;
  else
    $active=0;
  $show = mysql_real_escape_string($_POST['show'], $mysql_link);
  if($show)
    $show=1;
  else
    $show=0;
  $query = "SELECT MAX(SortOrder) FROM EH_Medals";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Medals (Name, Abbr, MG_ID, MT_ID, Group_ID, Image, Active, ShowOnID, SortOrder) VALUES('$name', '$abbr', '$mgid', '$mtid', '$group', '$img', '$active', '$show', '$so')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Medals WHERE Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Medals Set SortOrder=SortOrder-1 WHERE SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Medals_Complete WHERE Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Medals_Upgrades WHERE Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Medals WHERE Medal_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Medal deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Medal Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Medal</span>
      </a>
  </p>
  <div id="add-form" title="Add New Medal">
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
        <td><label for="mgid">Medal Group: </label></td>
        <td>
    <select name="mgid" id="mgid">
      <option value="0">No Medal Group</option>
    <?php
    $query1 = "SELECT MG_ID, Name FROM EH_Medals_Groups Order By Name";
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
        <td><label for="mtid">Medal Type: </label></td>
        <td>
    <select name="mtid" id="mgid" >
    <?php
    $query1 = "SELECT MT_ID, Name FROM EH_Medals_Types Order By Name";
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
        <td><label for="group">Group: </label></td>
        <td>
    <select name="group" id="group" >
    <?php
    $query1 = "SELECT Group_ID, Name FROM EH_Groups Order By Group_ID";
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
        <td><label for="active">Available: </label></td>
        <td>
          <input type="checkbox" name="active" id="active" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="show">Show on ID Line: </label></td>
        <td>
          <input type="checkbox" name="show" id="show" value="1">
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Medal">
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