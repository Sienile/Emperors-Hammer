<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "notesadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "notesadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
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
  $query = "SELECT TN_ID, SectionName, SortOrder FROM EH_Training_Notes WHERE Training_ID=$datatable Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="60%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a href="#" id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
    <?php if($i>1){ ?>
          <td width="10%"><a id="up" onclick="moveUp(<?=$values[0]?>)"><span style="color:#6699CC;">Move Up</span></a></td>
    <?php }else{ ?>
          <td width="10%">Move Up</td>
    <?php } if($values[2]<$rows){ ?>
          <td width="10%"><a id="down" onclick="moveDown(<?=$values[0]?>)"><span style="color:#6699CC;">Move Down</span></a></td>
    <?php }else{ ?>
          <td width="10%">Move Down</td>
    <?php } ?>
      </tr>
    <?php
    } // End for loop
    ?>
  </table>
<?php
  } // end if $_GET['datatable']
  elseif($_GET['up']) {
  $id = mysql_real_escape_string($_GET['up'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select TN_ID, SortOrder From EH_Training_Notes Where TN_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select TN_ID From EH_Training_Notes Where SortOrder=$newso AND Training_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Notes Set SortOrder=$newso Where TN_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Notes Set SortOrder=$curso Where TN_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Section moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select TN_ID, SortOrder From EH_Training_Notes Where TN_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select TN_ID From EH_Training_Notes Where SortOrder=$newso AND Training_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Notes Set SortOrder=$newso Where TN_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Notes Set SortOrder=$curso Where TN_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Section moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT TN_ID, SectionName, SectionText FROM EH_Training_Notes WHERE TN_ID=$id";
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
          <td><label for="desc">Section Text: </label></td>
          <td><textarea name="desc" id="desc" style="width:550px; height:320px"><?=stripslashes($values[2])?></textarea></td>
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
  $query = "UPDATE EH_Training_Notes Set SectionName='$name', SectionText='$desc' WHERE TN_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Training_Notes WHERE Training_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Training_Notes
                (SectionName, SectionText, SortOrder, Training_ID)
                VALUES('$name', '$desc', '$so', '$group')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Training_Notes WHERE TN_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Training_Notes Set SortOrder=SortOrder-1 WHERE Training_ID=$group AND SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Training_Notes WHERE TN_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Section deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Training Notes Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selAcad">Select the Academy to modify their Training</label>
    <?php $ga = implode (" OR Group_ID=", $groupsaccess); ?>
  <select name="selAcad" id="selAcad" onChange="getTraining()">
    <option value="0">No Academy</option>
  <?php $ga = implode (" OR Group_ID=", $groupsaccess);
  $query = "SELECT TAc_ID, Name FROM EH_Training_Academies";
  if($ga) {
    $query .=" WHERE Group_ID=$ga";
    }
  $query.=" Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
  }
?>
  </select><br>
    <label for="selGroup">Select the Course to modify their Notes</label>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Section</span>
    </a>
  </p>
  <div id="add-form" title="Add New Section">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="desc">Section Text: </label></td>
        <td><textarea name="desc" id="desc" style="width:550px; height:120px"></textarea></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Section">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  function getTraining(){
	var group = $("#selAcad option:selected").val();
	var postvars = {"id":group}
	$("#selGroup").empty();
	$("#selGroup").append('<option value="0">No Course</option>');
	getAdminJSONdata("getTrainingByAcad", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#selGroup").append('<option value="'+item.Training_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }
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