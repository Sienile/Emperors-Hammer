<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "traincatadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "traincatadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  echo "  <table>\n";
  echo "    <tr>\n";
  echo "      <td width=\"60%\">Name</td>\n";
  echo "      <td width=\"10%\">Edit</td>\n";
  echo "      <td width=\"10%\">Delete</td>\n";
  echo "      <td width=\"10%\">Move Up</td>\n";
  echo "      <td width=\"10%\">Move Down</td>\n";
  echo "    </tr>\n";
  $query = "SELECT TC_ID, Name, SortOrder FROM EH_Training_Categories WHERE TCa_ID=$datatable Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "      <tr>\n";
    echo "        <td width=\"60%\">".stripslashes($values[1])."</td>\n";
    echo "        <td width=\"10%\"><a href=\"#\" id=\"edit\" onclick=\"getEditForm(".$values[0].");\"><span style=\"color:#6699CC;\">Edit</span></a></td>\n";
    echo "        <td width=\"10%\"><a href=\"#\" id=\"del\" onclick=\"del(".$values[0].")\"><span style=\"color:#6699CC;\">Delete</span></a></td>\n";
    if($i>0)
      echo "        <td width=\"10%\"><a id=\"up\" onclick=\"moveUp(".$values[0].")\"><span style=\"color:#6699CC;\">Move Up</span></a></td>\n";
    else
      echo "        <td width=\"10%\">Move Up</td>\n";
    if($i+1<$rows)
      echo "        <td width=\"10%\"><a id=\"down\" onclick=\"moveDown(".$values[0].")\"><span style=\"color:#6699CC;\">Move Down</span></a></td>\n";
    else
      echo "        <td width=\"10%\">Move Down</td>\n";
    echo "      </tr>\n";
    } // End for loop
  echo "  </table>\n";
  } // end if $_GET['datatable']
  elseif($_GET['up']) {
  $id = mysql_real_escape_string($_GET['up'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select TC_ID, SortOrder From EH_Training_Categories Where TC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select TC_ID From EH_Training_Categories Where SortOrder=$newso AND TCa_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Categories Set SortOrder=$newso Where TC_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Categories Set SortOrder=$curso Where TC_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Category moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select TC_ID, SortOrder From EH_Training_Categories Where TC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select TC_ID From EH_Training_Categories Where SortOrder=$newso AND TCa_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Categories Set SortOrder=$newso Where TC_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Categories Set SortOrder=$curso Where TC_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Category moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT TC_ID, Name, Abbr, Master_ID, Active, Description, IDLineGroup, TCa_ID FROM EH_Training_Categories WHERE TC_ID=$id";
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
          <td><label for="tcid">Master Category: </label></td>
          <td>
    <select name="tcid" id="tcid" >
      <option value="0"<? if($values[3]==0) echo " selected=\"selected\""; ?>>No Master Category</option>
    <?php
    $query1 = "SELECT TC_ID, Name FROM EH_Training_Categories WHERE TCa_ID=$values[7] AND TC_ID!=$values[0] Order By SortOrder";
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
          <td><label for="acad">Academy: </label></td>
          <td>
    <input type="hidden" name="orgacad" value="<?=$values[7]?>" />
    <select name="acad" id="acad" >
    <?
  $ga = implode (" OR Group_ID=", $groupsaccess);
  $query1 = "SELECT TAc_ID, Name FROM EH_Training_Academies";
  if($ga) {
    $query1 .=" WHERE Group_ID=$ga";
    }
  $query1.=" Order By SortOrder";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    echo "  <option value=\"$values1[0]\"";
    if($values1[0]==$values[7])
      echo " selected=\"selected\"";      
    echo ">".stripslashes($values1[1])."</option>\n";
  }
  ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="avail">Available: </label></td>
          <td>
              <input type="checkbox" name="availalbe" id="available" value="1" <?=($values[4]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="desc">Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[5])?></textarea></td>
        </tr>
        <tr>
          <td><label for="idlinestyle">ID Line Group Style: </label></td>
          <td>
    <select name="idlinestyle" id="idlinestyle" >
      <option value="0"<? if($values[6]==0) echo " selected=\"selected\""; ?>>Display all in the Category</option>
      <option value="1"<? if($values[6]==1) echo " selected=\"selected\""; ?>>Display only the highest level completed in the category</option>
      <option value="2"<? if($values[6]==2) echo " selected=\"selected\""; ?>>Display the category abbr and then /courseabbr/etc.</option>
      <option value="3"<? if($values[6]==3) echo " selected=\"selected\""; ?>>Prefix the category abbr to all courses listed out</option>
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
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $tcid = mysql_real_escape_string($_POST['tcid'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['availalbe'], $mysql_link);
  if($avail)
    $avail=1;
  else
    $avail=0;
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $acad = mysql_real_escape_string($_POST['acad'], $mysql_link);
  $orgacad = mysql_real_escape_string($_POST['orgacad'], $mysql_link);
  $idlinestyle = mysql_real_escape_string($_POST['idlinestyle'], $mysql_link);
  $query = "UPDATE EH_Training_Categories Set Name='$name', Abbr='$abbr', Master_ID='$tcid', Active='$avail', Description='$desc', IDLineGroup='$idlinestyle'";
  if($acad!=$orgacad) {
    $query1 = "SELECT MAX(SortOrder) FROM EH_Training_Categories WHERE TCa_ID=$acad";
    $result = mysql_query($query1, $mysql_link);
    $rows = @mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $newso = $values[0]+1;
      }
    else {
      $newso = 1;
      }
    $query .=", TCa_ID=$acad, SortOrder=$newso";
    }
  $query .=" WHERE TC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $tcid = mysql_real_escape_string($_POST['tcid'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['availalbe'], $mysql_link);
  if($avail)
    $avail=1;
  else
    $avail=0;
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $idlinestyle = mysql_real_escape_string($_POST['idlinestyle'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Training_Categories WHERE TCa_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Training_Categories
                (Name, Abbr, Master_ID, Active, Description, IDLineGroup,
                 SortOrder, TCa_ID)
                VALUES(
                 '$name', '$abbr', '$tcid', '$avail', '$desc', '$idlinestyle',
                 '$so', '$group')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Training_Categories WHERE TC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Training_Categories Set SortOrder=SortOrder-1 WHERE TCa_ID=$group AND SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Training_Categories WHERE TC_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Category deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Training Category Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Academy to modify their Training Category</label>
    <?php $ga = implode (" OR Group_ID=", $groupsaccess); ?>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
    <option value="0">No Academy</option>
  <?php
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
  </select>
  </form>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');getTrainCat();" href="#">
        <span style="color:#6699CC;">Add New Category</span>
    </a>
  </p>
  <div id="add-form" title="Add New Category">
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
        <td><label for="tcid">Master Category: </label></td>
        <td>
    <select name="tcid" id="tcid" >
    </select></td>
      </tr>
      <tr>
        <td><label for="avail">Available: </label></td>
        <td>
            <input type="checkbox" name="availalbe" id="available" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="idlinestyle">ID Line Group Style: </label></td>
        <td>
    <select name="idlinestyle" id="idlinestyle" >
      <option value="0">Display all in the Category</option>
      <option value="1">Display only the highest level completed in the category</option>
      <option value="2">Display the category abbr and then /courseabbr/etc.</option>
      <option value="3">Prefix the category abbr to all courses listed out</option>
    </select></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Category">
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

  function getTrainCat(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#tcid").empty();
	$("#tcid").append('<option value="0">No Master Category</option>');
	getAdminJSONdata("getTrainingCategoriesByAcademy", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#tcid").append('<option value="'+item.TC_ID+'">'+item.Name+'</option>');
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