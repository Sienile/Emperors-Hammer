<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "unitadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "unitadmin");
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
  $query = "SELECT Unit_ID, Name FROM EH_Units WHERE Group_ID=$datatable Order By Name";
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
  $query = "SELECT Unit_ID, Name, UT_ID, Master_ID, Active, Base_ID, SiteURL, MessageBoard, Banner, Motto, Nickname, MissionRoll, Group_ID FROM EH_Units WHERE Unit_ID=$id";
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
          <td><label for="utid">Unit Type: </label></td>
          <td>
    <select name="utid" id="utid" >
    <?php
    $query1 = "SELECT UT_ID, Name FROM EH_Units_Types WHERE Group_ID=$values[12] OR Group_ID=0 Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[2])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="masterid">Master Unit: </label></td>
          <td>
    <select name="masterid" id="masterid" >
      <option value="0"<? if($values[3]==0) echo " selected=\"selected\""; ?>>No Master Unit</option>
    <?php
    $query1 = "SELECT Unit_ID, Name FROM EH_Units WHERE Group_ID=$values[12] AND Unit_ID!=$values[0] Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[3])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="active">Active: </label></td>
          <td>
              <input type="checkbox" name="active" id="active" value="1" <?=($values[4]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="baseid">Base: </label></td>
          <td>
    <select name="baseid" id="baseid" >
      <option value="0"<? if($values[5]==0) echo " selected=\"selected\""; ?>>No Base</option>
    <?php
    $query1 = "SELECT Base_ID, Name FROM EH_Bases WHERE BT_ID=1 Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[5])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="url">URL: </label></td>
          <td><input type="text" name="url" id="url" value="<?=stripslashes($values[6])?>"></td>
        </tr>
        <tr>
          <td><label for="mb">Message Board: </label></td>
          <td><input type="text" name="mb" id="mb" value="<?=stripslashes($values[7])?>"></td>
        </tr>
        <tr>
          <td><label for="banner">Banner: </label></td>
          <td><input type="text" name="banner" id="banner" value="<?=stripslashes($values[8])?>"></td>
        </tr>
        <tr>
          <td><label for="motto">Motto: </label></td>
          <td><input type="text" name="motto" id="motto" value="<?=stripslashes($values[9])?>"></td>
        </tr>
        <tr>
          <td><label for="nickname">Nickname: </label></td>
          <td><input type="text" name="nickname" id="nickname" value="<?=stripslashes($values[10])?>"></td>
        </tr>
        <tr>
          <td><label for="roll">Mission Roll: </label></td>
          <td><input type="text" name="roll" id="roll" value="<?=stripslashes($values[11])?>"></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $utid = mysql_real_escape_string($_POST['utid'], $mysql_link);
  $masterid = mysql_real_escape_string($_POST['masterid'], $mysql_link);
  $active = mysql_real_escape_string($_POST['active'], $mysql_link);
  if($active)
    $active=1;
  else
    $active=0;
  $baseid = mysql_real_escape_string($_POST['baseid'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $mb = mysql_real_escape_string($_POST['mb'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $motto = mysql_real_escape_string($_POST['motto'], $mysql_link);
  $nickname = mysql_real_escape_string($_POST['nickname'], $mysql_link);
  $roll = mysql_real_escape_string($_POST['roll'], $mysql_link);
  $query = "UPDATE EH_Units Set Name='$name', UT_ID='$utid', Master_ID='$masterid', Active='$active', Base_ID='$baseid', SiteURL='$url', MessageBoard='$mb', Banner='$banner', Motto='$motto', Nickname='$nickname', MissionRoll='$roll' WHERE Unit_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $utid = mysql_real_escape_string($_POST['utid'], $mysql_link);
  $masterid = mysql_real_escape_string($_POST['masterid'], $mysql_link);
  $active = mysql_real_escape_string($_POST['active'], $mysql_link);
  if($active)
    $active=1;
  else
    $active=0;
  $baseid = mysql_real_escape_string($_POST['baseid'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $mb = mysql_real_escape_string($_POST['mb'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $motto = mysql_real_escape_string($_POST['motto'], $mysql_link);
  $nickname = mysql_real_escape_string($_POST['nickname'], $mysql_link);
  $roll = mysql_real_escape_string($_POST['roll'], $mysql_link);
  $query = "INSERT INTO EH_Units
                (Name, UT_ID, Master_ID, Active, Group_ID, Base_ID, SiteURL, MessageBoard, Banner, Motto, Nickname, MissionRoll)
                VALUES('$name', '$utid', '$masterid', '$active', '$group', '$baseid', '$url', '$mb', '$banner', '$motto', '$nickname', '$roll')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Units WHERE Unit_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Unit deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Unit Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Units</label>
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
    <a name="adddialog" onClick="getUnitTypes(); getUnits(); $('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Unit</span>
    </a>
  </p>
  <div id="add-form" title="Add New Unit">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="utid">Unit Type: </label></td>
        <td>
    <select name="utid" id="utid" >
    </select></td>
      </tr>
      <tr>
        <td><label for="masterid">Master Unit: </label></td>
        <td>
    <select name="masterid" id="masterid" >
    </select></td>
      </tr>
      <tr>
        <td><label for="active">Active: </label></td>
        <td>
          <input type="checkbox" name="active" id="active" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="baseid">Base: </label></td>
        <td>
    <select name="baseid" id="baseid" >
      <option value="0">No Base</option>
    <?php
    $query1 = "SELECT Base_ID, Name FROM EH_Bases WHERE BT_ID=1 Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
      </tr>
      <tr>
        <td><label for="url">URL: </label></td>
        <td><input type="text" name="url" id="url"></td>
      </tr>
      <tr>
        <td><label for="mb">Message Board: </label></td>
        <td><input type="text" name="mb" id="mb"></td>
      </tr>
      <tr>
        <td><label for="banner">Banner: </label></td>
        <td><input type="text" name="banner" id="banner"></td>
      </tr>
      <tr>
        <td><label for="motto">Motto: </label></td>
        <td><input type="text" name="motto" id="motto"></td>
      </tr>
      <tr>
        <td><label for="nickname">Nickname: </label></td>
        <td><input type="text" name="nickname" id="nickname"></td>
      </tr>
      <tr>
        <td><label for="roll">Mission Roll: </label></td>
        <td><input type="text" name="roll" id="roll"></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Unit">
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
  
  function getUnits(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#masterid").empty();
	$("#masterid").append('<option value="0">No master unit</option>');
	getAdminJSONdata("getUnitsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#masterid").append('<option value="'+item.Unit_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }
  
  function getUnitTypes(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#utid").empty();
	$("#utid").append('<option value="0">No unit type</option>');
	getAdminJSONdata("getUnitTypesByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#utid").append('<option value="'+item.UT_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

  function del(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id+"&group="+groupId,{},showSuccess,'html');
  }
  
  function moveUp(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?up="+id+"&group="+groupId,{},showSuccess,'html');
  }

  function moveDown(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?down="+id+"&group="+groupId,{},showSuccess,'html');
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
    return false;
  }
  
  function postEdit() {
  var group = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true&group='+group,
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    return false;
  }
  
  function getDataTable() {
    var id = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable="+id,{},function(data){
        $("#response").html(data);
    },'html');
  }

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