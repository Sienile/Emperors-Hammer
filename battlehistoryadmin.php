<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "battlehistoryadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "battlehistoryadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  $acad = mysql_real_escape_string($_GET['acad'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="80%">Battle Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT Complete_ID, Battle_ID FROM EH_Battles_Complete WHERE Member_ID=$datatable ORDER By Date_Completed";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="80%"><?=stripslashes(BattleName($values[1], 1))?></td>
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
  $query = "SELECT Complete_ID, Date_Completed, Scores, Battle_ID FROM EH_Battles_Complete WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $name = BattleName($values[3], 0);
    ?>
    <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="date"><?=$name?>: Date Awarded: </label></td>
            <?
            $date = date("m/d/Y", $values[1]);
            ?>
          <td>
              <div id="date_edit"></div>
              <input type="hidden" name="date" id="date" value="<?=$date?>" />
          </td>
        </tr>
        <tr>
          <td><label for="scores"><?=$name?>: Scores: </label></td>
          <td><textarea name="scores" id="scores" style="width:400px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
      </table>
    </form>
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
  $scores = mysql_real_escape_string($_POST['scores'], $mysql_link);
  $query = "UPDATE EH_Battles_Complete Set Date_Completed='$date', Scores='$scores' WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $member = mysql_real_escape_string($_GET['member'], $mysql_link);
  $battle = mysql_real_escape_string($_POST['battle'], $mysql_link);
  $date = mysql_real_escape_string($_POST['datea'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $scores = mysql_real_escape_string($_POST['scores'], $mysql_link);
  $query = "INSERT INTO EH_Battles_Complete
                (Member_ID, Battle_ID, Date_Completed, Scores, Status)
                VALUES('$member', '$battle', '$date', '$scores', '1')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Battles_Complete WHERE Complete_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Record deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Battle History Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
    <label for="selGroup">Select the Person to modify their Medals History</label>
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
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Record</span>
    </a>
  </p>
  <div id="add-form" title="Add New Record">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="battle">Battle: </label></td>
        <td>
        <select name="battle" id="battle">
<?
  $query1 = "SELECT Battle_ID FROM EH_Battles Order By Platform_ID, BC_ID, BattleNumber";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($h=0; $h<$rows1; $h++) {
    $values1 = mysql_fetch_row($result1);
    echo "        <option value=\"$values1[0]\">".BattleName($values1[0], 0)."</option>\n";
    }
?>
        </select>
        </td>
      </tr>
      <tr>
        <td><label for="datea">Date Awarded: </label></td>
        <td>
            <div id="date_add"></div>
            <input type="hidden" name="datea" id="datea" />
        </td>
      </tr>
      <tr>
        <td><label for="reason">Scores: </label></td>
        <td><textarea name="scores" id="scores" style="width:400px; height:120px"></textarea></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Record">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  $(function() {
    $("#date_add").datepicker({altField: '#datea'});
  });

  function getEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id,{},function(data){
      $("#editArea").html(data);
    },'html').complete(function() {
      $("#editArea").dialog("open");
      });
  }

  function del(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id,{},showSuccess,'html');
  }
  
  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function postAdd() {
    var group = $("#selAcad option:selected").val();
    var member = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add=true&member='+member,
        success: showSuccess
    }
    $("#addForm").ajaxSubmit(options);
    return false;
  }
  
  function postEdit() {
  var group = $("#selGroup option:selected").val();
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true',
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