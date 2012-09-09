<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "positionadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "positionadmin");
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
  $query = "SELECT Position_ID, Name, SortOrder FROM EH_Positions WHERE Group_ID=$datatable Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="60%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a href="#" id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
    <?php if($i>0){ ?>
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
  $query = "select Position_ID, SortOrder From EH_Positions Where Position_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select Position_ID From EH_Positions Where SortOrder=$newso AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Positions Set SortOrder=$newso Where Position_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Positions Set SortOrder=$curso Where Position_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Position moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select Position_ID, SortOrder From EH_Positions Where Position_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select Position_ID From EH_Positions Where SortOrder=$newso AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Positions Set SortOrder=$newso Where Position_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Positions Set SortOrder=$curso Where Position_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Position moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Position_ID, Name, Abbr, Description, Banner, SiteURL, isCS, CSOrder, Base_ID, MinRank, MaxRank, Group_ID, Access_ID, MaxPromotableRank, MedalsAwardable FROM EH_Positions WHERE Position_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Position">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
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
          <td><label for="desc">Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[3])?></textarea></td>
        </tr>
        <tr>
          <td><label for="banner">Banner: </label></td>
          <td><input type="text" name="banner" id="banner" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="url">URL: </label></td>
          <td><input type="text" name="url" id="url" value="<?=stripslashes($values[5])?>"></td>
        </tr>
        <tr>
          <td><label for="isCS">Is a CS position: </label></td>
          <td><select name="isCS" id="isCS">
            <option value="0"<?if($values[6]==0) echo " selected=\"selected\""; ?>>Not CS</option>
            <option value="1"<?if($values[6]==1) echo " selected=\"selected\""; ?>>CS</option>
            <option value="2"<?if($values[6]==2) echo " selected=\"selected\""; ?>>A:CS</option>
            <option value="3"<?if($values[6]==3) echo " selected=\"selected\""; ?>>Support Staff</option>
          </select>
          </td>
        </tr>
        <tr>
          <td><label for="csnum">CS Number(if is CS): </label></td>
          <td><input type="text" name="csnum" id="csnum" value="<?=stripslashes($values[7])?>"></td>
        </tr>
        <tr>
          <td><label for="base">Base: </label></td>
          <td>
    <select name="base" id="base" >
      <option value="0"<? if($values[8]==0) echo " selected=\"selected\""; ?>>No base</option>
    <?php
    $query1 = "SELECT Base_ID, Name FROM EH_Bases";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[8])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="minRank">Minimum Rank: </label></td>
          <td>
    <select name="minRank" id="minRank">
      <option value="0"
      <?php
    if($values[9]==0)
      echo " selected=\"selected\"";
    echo ">No min Rank</option>\n";
    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks WHERE Group_ID=$values[11] Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[9])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="maxRank">Maximum Rank: </label></td>
          <td>
    <select name="maxRank" id="maxRank">
      <option value="0"
      <?php
    if($values[10]==0)
      echo " selected=\"selected\"";
    echo ">No max Rank</option>\n";
    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks WHERE Group_ID=$values[11] Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[10])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="access">Access level: </label></td>
          <td>
    <select name="access" id="access">
      <option value="0"
      <?php
    if($values[12]==0)
      echo " selected=\"selected\"";
    echo ">No Access</option>\n";
    $query1 = "SELECT Access_ID, Name FROM EH_Access WHERE Group_ID=$values[11] Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[12])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="maxpromoteRank">Maximum Rank the position can promote: </label></td>
          <td>
    <select name="maxpromoteRank" id="maxpromoteRank">
      <option value="0"
      <?php
    if($values[13]==0)
      echo " selected=\"selected\"";
    echo ">Can't Promote</option>\n";
    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks WHERE Group_ID=$values[11] Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[13])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="medals">Medals able to award: </label></td>
          <td>
    <select name="medals[]" id="medals" multiple="multiple">
      <option value="0"
<?php
    if($values[14]==0 || $values[14]=="")
      echo " selected=\"selected\"";
    echo ">None</option>";
    $medals = explode(";", $values[14]);
    $query1 = "SELECT Medal_ID, Name FROM EH_Medals WHERE";
    if($values[11]!=1)
      $query1 .=" Group_ID=$values[11] AND";
    $query1 .=" Active=1 Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      for($q=0; $q<count($tabs); $q++) {
        if($medals[$q]==$values1[0])
          echo " selected=\"selected\"";
        }
      echo ">".stripslashes($values1[1])."</option>\n";
      }
      ?>
    </select></td>
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
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $isCS = mysql_real_escape_string($_POST['isCS'], $mysql_link);
  $csnum = mysql_real_escape_string($_POST['csnum'], $mysql_link);
  $base = mysql_real_escape_string($_POST['base'], $mysql_link);
  $minRank = mysql_real_escape_string($_POST['minRank'], $mysql_link);
  $maxRank = mysql_real_escape_string($_POST['maxRank'], $mysql_link);
  $access = mysql_real_escape_string($_POST['access'], $mysql_link);
  $maxpromoteRank = mysql_real_escape_string($_POST['maxpromoteRank'], $mysql_link);
  $medals = mysql_real_escape_string(implode(";", $_POST['medals']), $mysql_link);
  $query = "UPDATE EH_Positions Set Name='$name', Abbr='$abbr', Description='$desc', Banner='$banner', SiteURL='$url', isCS='$isCS', CSOrder='$csnum', Base_ID='$base', MinRank='$minRank', MaxRank='$maxRank', Access_ID='$access', MaxPromotableRank='$maxpromoteRank', MedalsAwardable='$medals' WHERE Position_ID=$id";
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
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $isCS = mysql_real_escape_string($_POST['isCS'], $mysql_link);
  $csnum = mysql_real_escape_string($_POST['csnum'], $mysql_link);
  $base = mysql_real_escape_string($_POST['base'], $mysql_link);
  $minRank = mysql_real_escape_string($_POST['minRank'], $mysql_link);
  $maxRank = mysql_real_escape_string($_POST['maxRank'], $mysql_link);
  $access = mysql_real_escape_string($_POST['access'], $mysql_link);
  $maxpromoteRank = mysql_real_escape_string($_POST['maxpromoteRank'], $mysql_link);
  $medals = mysql_real_escape_string(implode(";", $_POST['medals']), $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Positions WHERE Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Positions
                (Name, Abbr, Description, Banner, SiteURL, isCS, CSOrder, Base_ID, MinRank, MaxRank, Access_ID, MaxPromotableRank, MedalsAwardable, SortOrder, Group_ID)
                VALUES('$name', '$abbr', '$desc', '$banner', '$url', '$isCS', '$csnum', '$base', '$minRank', '$maxRank', '$access', '$maxpromoteRank', '$medals', '$so', $group)";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Positions WHERE Position_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Positions Set SortOrder=SortOrder-1 WHERE Group_ID=$group AND SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Positions WHERE Position_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Position deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Position Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Positions</label>
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
    <a name="adddialog" onClick="getAccess(); getRanks(); getMedals(); $('#add').show()" href="#">
        <span style="color:#6699CC;">Add New Position</span>
    </a>
  </p>
  <div class="ajaxForm" style="display:none;" id="add" title="Add New Position">
  <form id="addForm" method="POST" onSubmit="postAdd(); return false;">
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
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="banner">Banner: </label></td>
        <td><input type="text" name="banner" id="banner"></td>
      </tr>
      <tr>
        <td><label for="url">URL: </label></td>
        <td><input type="text" name="url" id="url"></td>
      </tr>
      <tr>
        <td><label for="isCS">Is a CS position: </label></td>
        <td><select name="isCS" id="isCS">
            <option value="0"<?if($values[6]==0) echo " selected=\"selected\""; ?>>Not CS</option>
            <option value="1"<?if($values[6]==1) echo " selected=\"selected\""; ?>>CS</option>
            <option value="2"<?if($values[6]==2) echo " selected=\"selected\""; ?>>A:CS</option>
            <option value="3"<?if($values[6]==3) echo " selected=\"selected\""; ?>>Support Staff</option>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="csnum">CS Number(if is CS): </label></td>
        <td><input type="text" name="csnum" id="csnum"></td>
      </tr>
      <tr>
        <td><label for="base">Base: </label></td>
        <td>
    <select name="base" id="base" >
      <option value="0">No base</option>
    <?php
    $query1 = "SELECT Base_ID, Name FROM EH_Bases";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
      </tr>
      <tr>
        <td><label for="minRank">Minimum Rank: </label></td>
        <td>
    <select name="minRank" id="minRank">
    </select></td>
      </tr>
      <tr>
        <td><label for="maxRank">Maximum Rank: </label></td>
        <td>
    <select name="maxRank" id="maxRank">
    </select></td>
      </tr>
      <tr>
        <td><label for="access">Access level: </label></td>
        <td>
    <select name="access" id="access">
    </select></td>
      </tr>
      <tr>
        <td><label for="maxpromoteRank">Maximum Rank the position can promote: </label></td>
        <td>
    <select name="maxpromoteRank" id="maxpromoteRank">
    </select></td>
      </tr>
      <tr>
        <td><label for="medals">Medals able to award: </label></td>
        <td>
    <select name="medals[]" id="medals" multiple="multiple">
    </select></td>
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

  <div id="editdialog" title="Edit Position" refreshOnShow="true">
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

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

  function getMedals(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#medals").empty();
	$("#medals").append('<option value="0">None</option>');
	getAdminJSONdata("getMedalsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#medals").append('<option value="'+item.Medal_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }
  
  function getAccess(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#access").empty();
	$("#access").append('<option value="0">No Access Level</option>');
	getAdminJSONdata("getAccessByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#access").append('<option value="'+item.Access_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }
  
  function getRanks(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#minRank").empty();
	$("#minRank").append('<option value="0">No min Rank</option>');
	$("#maxRank").empty();
	$("#maxRank").append('<option value="0">No max Rank</option>');
	$("#maxpromoteRank").empty();
	$("#maxpromoteRank").append('<option value="0">No max Rank</option>');
	getAdminJSONdata("getRanksByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#minRank").append('<option value="'+item.Rank_ID+'">'+item.Name+'</option>');
					$("#maxRank").append('<option value="'+item.Rank_ID+'">'+item.Name+'</option>');
					$("#maxpromoteRank").append('<option value="'+item.Rank_ID+'">'+item.Name+'</option>');
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