<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "certadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "certadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  $acad = mysql_real_escape_string($_GET['acad'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="80%">Course</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT EH_Training_Complete.CT_ID, EH_Training.Name FROM EH_Training_Complete, EH_Training WHERE EH_Training.Training_ID=EH_Training_Complete.Training_ID AND EH_Training_Complete.Member_ID=$datatable AND EH_Training.TAc_ID=$acad";
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
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT CT_ID, DateComplete, Score, Training_ID FROM EH_Training_Complete WHERE CT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT Name FROM EH_Training WHERE Training_ID=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $name=$values1[0];
      }
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Training Record: <?=$name?>">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="date"><?=$name?>: Date Completed: </label></td>
            <?
            $date = date("m/d/Y", $values[1]);
            ?>
          <td>
              <div id="date_edit"></div>
              <input type="hidden" name="date" id="date" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="score"><?=$name?>: Score: </label></td>
          <td><input type="text" name="score" id="score" value="<?=stripslashes($values[2])?>"></td>
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
         defaultDate: new Date("<?=date("M d, Y",$values[1])?>"),
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
  $date = mysql_real_escape_string($_POST['date'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $query = "UPDATE EH_Training_Complete Set DateComplete='$date', Score='$score' WHERE CT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $member = mysql_real_escape_string($_GET['group'], $mysql_link); //Training
  $train = mysql_real_escape_string($_POST['selTrain'], $mysql_link);
  $date = mysql_real_escape_string($_POST['datea'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $query = "INSERT INTO EH_Training_Complete
                (Training_ID, Member_ID, DateComplete, Score)
                VALUES('$train', '$member', '$date', '$score')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Training_Complete WHERE CT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Training History Administration</p>
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
    <label for="selGroup">Select the Person to modify their Training History</label>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
    <option value="0">No Member</option>
  <?php $ga = implode (" OR Group_ID=", $groupsaccess);
  $query = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])." ($values[0])</option>\n";
  }
?>
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
    <a name="adddialog" onClick="$('#add').show()" href="#">
        <span style="color:#6699CC;">Add New Record</span>
    </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Record">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
    <table>
      <tr>
        <td><label for="selTrain">Course: </label></td>
        <td>
        <select name="selTrain" id="selTrain">
        </select>
        </td>
      </tr>
      <tr>
        <td><label for="datea">Date Complete: </label></td>
        <td>
            <div id="date_add"></div>
            <input type="hidden" name="datea" id="datea" />
        </td>
      </tr>
      <tr>
        <td><label for="score">Score: </label></td>
        <td><input type="text" name="score" id="score" /></td>
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

  <div id="datatable"></div>

  <script type="text/javascript">

  function getTraining(){
	var group = $("#selAcad option:selected").val();
	var postvars = {"id":group}
	$("#selTrain").empty();
	$("#selTrain").append('<option value="0">No Course</option>');
	getAdminJSONdata("getTrainingByAcad", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#selTrain").append('<option value="'+item.Training_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

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
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id+"&group="+groupId,{},showSuccess,'html');
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
    var group = $("#selAcad option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable="+id+"&acad="+group,{},function(data){
        $("#response").html(data);
    },'html');
  }

  </script>
  <?php
  include_once("footer.php");
  }
?>