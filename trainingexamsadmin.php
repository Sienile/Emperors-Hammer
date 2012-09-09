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
      <td width="60%">Question</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
      <td width="10%">Move Up</td>
      <td width="10%">Move Down</td>
    </tr>
    <?php
  $query = "SELECT TE_ID, Question, SortOrder FROM EH_Training_Exams WHERE Training_ID=$datatable Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=1; $i<=$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="60%"><?=$i?>.) <?=stripslashes($values[1])?></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a href="#" id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
    <?php if($i>1){ ?>
          <td width="10%"><a id="up" onclick="moveUp(<?=$values[0]?>)"><span style="color:#6699CC;">Move Up</span></a></td>
    <?php }else{ ?>
          <td width="10%">Move Up</td>
    <?php } if($i+1<$rows){ ?>
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
  $query = "select TE_ID, SortOrder From EH_Training_Exams Where TE_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select TE_ID From EH_Training_Exams Where SortOrder=$newso AND Training_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Exams Set SortOrder=$newso Where TE_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Exams Set SortOrder=$curso Where TE_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Question moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select TE_ID, SortOrder From EH_Training_Exams Where TE_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select TE_ID From EH_Training_Exams Where SortOrder=$newso AND Training_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Exams Set SortOrder=$newso Where TE_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Exams Set SortOrder=$curso Where TE_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Question moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT TE_ID, Question, Type, Answer, Choices, Points FROM EH_Training_Exams WHERE TE_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Question">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="Q">Question: </label></td>
          <td><textarea name="Q" id="Q" style="width:400px; height:120px"><?=stripslashes($values[1])?></textarea></td>
        </tr>
        <tr>
          <td><label for="type">Answer Type: </label></td>
          <td><select name="type" id="type">
            <option value="0"<? if($values[2]==0) echo " selected=\"selected\""; ?>>Multiple Choice</option>
            <option value="1"<? if($values[2]==1) echo " selected=\"selected\""; ?>>Essay Response</option>
            <option value="2"<? if($values[2]==2) echo " selected=\"selected\""; ?>>Single Line Response</option>
          </select></td>
        </tr>
        <tr>
          <td><label for="A">Answer: </label></td>
          <td><textarea name="A" id="A" style="width:400px; height:120px"><?=stripslashes($values[3])?></textarea></td>
        </tr>
        <tr>
          <td><label for="Choices">Choices(Multiple Choice, seperate answers with a comma(,)): </label></td>
          <td><textarea name="Choices" id="Choices" style="width:400px; height:120px"><?=stripslashes($values[4])?></textarea></td>
        </tr>
        <tr>
          <td><label for="pts">Points: </label></td>
          <td><input type="text" name="pts" id="pts" value="<?=stripslashes($values[5])?>"></td>
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
  $q = mysql_real_escape_string($_POST['Q'], $mysql_link);
  $type = mysql_real_escape_string($_POST['type'], $mysql_link);
  $ans = mysql_real_escape_string($_POST['A'], $mysql_link);
  $choices = mysql_real_escape_string($_POST['Choices'], $mysql_link);
  $pts = mysql_real_escape_string($_POST['pts'], $mysql_link);
  $query = "UPDATE EH_Training_Exams Set Question='$q', Type='$type', Answer='$ans', Choices='$choices', Points='$pts' WHERE TE_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link); //Training
  $q = mysql_real_escape_string($_POST['Q'], $mysql_link);
  $type = mysql_real_escape_string($_POST['type'], $mysql_link);
  $ans = mysql_real_escape_string($_POST['A'], $mysql_link);
  $choices = mysql_real_escape_string($_POST['Choices'], $mysql_link);
  $pts = mysql_real_escape_string($_POST['pts'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Training_Exams WHERE Training_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Training_Exams
                (Question, Type, Answer, Choices, Points, Training_ID, SortOrder)
                VALUES('$q', '$type', '$ans', '$choices', '$pts', '$group', '$so')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Training_Exams WHERE TE_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Training_Exams Set SortOrder=SortOrder-1 WHERE Training_ID=$group AND SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Training_Exams WHERE TE_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Question deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Training Exam Administration</p>
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
    <label for="selGroup">Select the Course to modify their Exam Questions</label>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
    <a name="adddialog" onClick="$('#add').show()" href="#">
        <span style="color:#6699CC;">Add New Question</span>
    </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Question">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="Q">Question: </label></td>
        <td><textarea name="Q" id="Q" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="type">Answer Type: </label></td>
        <td><select name="type" id="type">
          <option value="0">Multiple Choice</option>
          <option value="1">Essay Response</option>
          <option value="2">Single Line Response</option>
        </select></td>
      </tr>
      <tr>
        <td><label for="A">Answer: </label></td>
        <td><textarea name="A" id="A" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="Choices">Choices(Multiple Choice, seperate answers with a comma(,)): </label></td>
        <td><textarea name="Choices" id="Choices" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="pts">Points: </label></td>
        <td><input type="text" name="pts" id="pts"></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input type="submit" id="Submit" name="Submit"
                 value="Submit" onClick="" />
          <input type="reset" id="Reset" name="Reset" />
          <input type="button" id="Cancel" name="Cancel" value="Cancel"
                 onClick="$('#Reset').click();$('#add').hide();" />
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editdialog" title="Edit Question" refreshOnShow="true">
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
    $("#Cancel").click();
    return false;
  }
  
  function postEdit() {
  var group = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true&group='+group,
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }
  
  function getDataTable() {
    var id = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable="+id,{},function(data){
        $("#response").html(data);
    },'html');
  }

  </script>
  <?php
  include_once("footer.php");
  }
?>