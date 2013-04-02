<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "trainawardsadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "trainawardsadmin");
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
  $query = "SELECT EH_Training_Awards.TA_ID, EH_Training.Name, EH_Training_Awards.Score FROM EH_Training_Awards, EH_Training WHERE EH_Training_Awards.Training_ID=$datatable AND EH_Training.Training_ID=EH_Training_Awards.Training_ID Order By Score";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="80%"><? echo stripslashes($values[1])." (Score: ".stripslashes($values[2]).")"; ?></td>
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
  $query = "SELECT TA_ID, Score, Award_ID, TAT_ID, Training_ID FROM EH_Training_Awards WHERE TA_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT EH_Training_Academies.Group_ID FROM EH_Training, EH_Training_Academies WHERE EH_Training.Training_ID=$values[4] AND EH_Training.TAc_ID=EH_Training_Academies.TAc_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $groupid = $values1[0];
      }
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="score">Score: </label></td>
          <td><input type="text" name="score" id="score" value="<?=stripslashes($values[1])?>"></td>
        </tr>
<?
if($values[3]==1) {
//Medal
?>
        <tr>
          <td><label for="award">Medal: </label></td>
          <td>
            <select name="award" id="award">
<?

    $query1 = "SELECT Medal_ID, Name FROM EH_Medals WHERE";
    if($groupid!=1)
      $query1.=" Group_ID=$groupid AND";
    $query1.=" Active=1 Order By SortOrder";
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
            </select>
          </td>
        </tr>
<?
}
else {
?>

        <tr>
          <td><label for="award">Rank: </label></td>
          <td>
            <select name="award" id="award">
<?

    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks WHERE";
    if($groupid!=1)
      $query1.=" Group_ID=$groupid AND";
    $query1.=" Active=1 Order By SortOrder";
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
            </select>
          </td>
        </tr>
<?
}
?>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $award = mysql_real_escape_string($_POST['award'], $mysql_link);
  $query = "UPDATE EH_Training_Awards Set Score='$score', Award_ID='$award' WHERE TA_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Award updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link); // Training
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $tat = mysql_real_escape_string($_POST['tat'], $mysql_link);
  if($tat==1)
    $award = mysql_real_escape_string($_POST['medal'], $mysql_link);
  elseif($tat==2)
    $award = mysql_real_escape_string($_POST['rank'], $mysql_link);
  $query = "INSERT INTO EH_Training_Awards
                (Training_ID, Score, TAT_ID, Award_ID)
                VALUES('$group', '$score', '$tat', '$award')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Award inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Training_Awards WHERE TA_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Award deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Training Awards Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
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
    <label for="selGroup">Select the Course to modify their Awards</label>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Award</span>
    </a>
  </p>
  <div id="add-form" title="Add New Award">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="score">Score: </label></td>
        <td><input type="text" name="score" id="score"></td>
      </tr>
      <tr>
        <td><label for="tat">Award Type: </label></td>
        <td>
          <select name="tat" id="tat">
<?
    $query1 = "SELECT TAT_ID, Name FROM EH_Training_Awards_Types";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }
?>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="medal">Medal: </label></td>
        <td>
          <select name="medal" id="medal">
            <option value="0">None</option>
<?
    $query1 = "SELECT Medal_ID, Name FROM EH_Medals WHERE Active=1 Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }
?>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="rank">Rank: </label></td>
        <td>
          <select name="rank" id="rank">
            <option value="0">None</option>
<?
    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks WHERE Active=1 Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }
?>
          </select>
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Award">
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