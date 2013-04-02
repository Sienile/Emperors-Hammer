<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "groupadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "groupadmin");
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
  $query = "SELECT Group_ID, Name FROM EH_Groups WHERE Group_ID=$datatable";
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
  $query = "SELECT Group_ID, Name, Abbr, ShortDesc, LongDesc, Active, Banner, ProfileTabs, MedalBrackets, MedalSeperator, MedalGroupBrackets, RankTypeDisplayName, IDLineFormat, PositionSeparator, UnitSeparator, CSAbbrL1, CSAbbrL2, CSAbbrL3, UniType, GroupJoinContact, JoinMailBlurb, CompAdmin FROM EH_Groups WHERE Group_ID=$id";
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
          <td><label for="sdesc">Short Description: </label></td>
          <td><textarea name="sdesc" id="sdesc" style="width:400px; height:120px"><?=stripslashes($values[3])?></textarea></td>
        </tr>
        <tr>
          <td><label for="ldesc">Long Description: </label></td>
          <td><textarea name="ldesc" id="ldesc" style="width:400px; height:120px"><?=stripslashes($values[4])?></textarea></td>
        </tr>
        <tr>
          <td><label for="active">Active: </label></td>
          <td><select name="active" id="active">
            <option value="0"<? if($values[5]==0) echo " selected=\"selected\""; ?>>Inactive</option>
            <option value="1"<? if($values[5]==1) echo " selected=\"selected\""; ?>>Active</option>
            <option value="2"<? if($values[5]==2) echo " selected=\"selected\""; ?>>Active - But not joinable group</option>
          </select></td>
        </tr>
        <tr>
          <td><label for="banner">Banner: </label></td>
          <td><input type="text" name="banner" id="banner" value="<?=stripslashes($values[6])?>"></td>
        </tr>
        <tr>
          <td><label for="tabs">Profile Tabs: </label></td>
          <td><select name="tabs[]" id="tabs" multiple="multiple">
<?
    $tabs = explode(";", $values[7]);
    $query1 = "SELECT GT_ID, Name FROM EH_Groups_Tabs Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      for($q=0; $q<count($tabs); $q++) {
        if($tabs[$q]==$values1[0])
          echo " selected=\"selected\"";
        }
      echo ">".stripslashes($values1[1])."</option>\n";
      }
?>
          </select></td>
        </tr>
        <tr>
          <td><label for="medalbrack">Medal Brackets: </label></td>
          <td><input type="text" name="medalbrack" id="medalbrack" value="<?=stripslashes($values[8])?>"></td>
        </tr>
        <tr>
          <td><label for="medalsep">Medal Seperator: </label></td>
          <td><input type="text" name="medalsep" id="medalsep" value="<?=stripslashes($values[9])?>"></td>
        </tr>
        <tr>
          <td><label for="medalgbrack">Medal Group Brackets: </label></td>
          <td><input type="text" name="medalgbrack" id="medalgbrack" value="<?=stripslashes($values[10])?>"></td>
        </tr>
        <tr>
          <td><label for="ranktypedispname">If Displaying Rank Type, what is it called: </label></td>
          <td><input type="text" name="ranktypedispname" id="ranktypedispname" value="<?=stripslashes($values[11])?>"></td>
        </tr>
        <tr>
          <td colspan="2">The following are commands are available for ID Lines:<br>
           @P@ - Positions<br>
           @R@ - Rank<br>
           @N@ - Name<br>
           @U@ - Unit<br>
           @F@ - FCHG<br>
           @C@ - Combat Rating<br>
           @M@ - Medals
           @T@ - Training</td>
        </tr>
        <tr>
          <td><label for="idlinefmt">ID Line Format: </label></td>
          <td><textarea name="idlinefmt" id="idlinefmt" style="width:400px; height:120px"><?=stripslashes($values[12])?></textarea></td>
        </tr>
        <tr>
          <td><label for="possep">Position Separator: </label></td>
          <td><input type="text" name="possep" id="possep" value="<?=stripslashes($values[13])?>"></td>
        </tr>
        <tr>
          <td><label for="unitsep">Unit Separator: </label></td>
          <td><input type="text" name="unitsep" id="unitsep" value="<?=stripslashes($values[14])?>"></td>
        </tr>
        <tr>
          <td><label for="csl1">CS Abbr Level 1: </label></td>
          <td><input type="text" name="csl1" id="csl1" value="<?=stripslashes($values[15])?>"></td>
        </tr>
        <tr>
          <td><label for="csl2">CS Abbr Level 2: </label></td>
          <td><input type="text" name="csl2" id="csl2" value="<?=stripslashes($values[16])?>"></td>
        </tr>
        <tr>
          <td><label for="csl3">CS Abbr Level 3: </label></td>
          <td><input type="text" name="csl3" id="csl3" value="<?=stripslashes($values[17])?>"></td>
        </tr>
        <tr>
          <td><label for="unitype">Uniform Type: </label></td>
          <td><select name="unitype" id="unitype">
            <option value="1"<? if($values[18]==1) echo " selected=\"selected\""; ?>>Upload</option>
            <option value="2"<? if($values[18]==2) echo " selected=\"selected\""; ?>>Assembled</option>
            <option value="3"<? if($values[18]==3) echo " selected=\"selected\""; ?>>Rank Based</option>
          </select></td>
        </tr>
        <tr>
          <td><label for="joincontact">Join Form Contact Position: </label></td>
          <td><select name="joincontact" id="joincontact">
<?
    $query1 = "SELECT Position_ID, Name FROM EH_Positions WHERE Group_ID=$values[0] Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values[19]==$values1[0])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }
?>
          </select></td>
        </tr>
        <tr>
          <td><label for="jointext">Join Mail Information: </label></td>
          <td><textarea name="jointext" id="jointext" style="width:400px; height:120px"><?=stripslashes($values[20])?></textarea></td>
        </tr>
        <tr>
          <td><label for="compadmin">Comp Admin Position: </label></td>
          <td><select name="compadmin" id="compadmin">
<?
    $query1 = "SELECT Position_ID, Name FROM EH_Positions WHERE Group_ID=$values[0] Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values[21]==$values1[0])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }
?>
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
  $sdesc = mysql_real_escape_string($_POST['sdesc'], $mysql_link);
  $ldesc = mysql_real_escape_string($_POST['ldesc'], $mysql_link);
  $active = mysql_real_escape_string($_POST['active'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $tabs = mysql_real_escape_string(implode(";", $_POST['tabs']), $mysql_link);
  $medalbrack = mysql_real_escape_string($_POST['medalbrack'], $mysql_link);
  $medalsep = mysql_real_escape_string($_POST['medalsep'], $mysql_link);
  $medalgbrack = mysql_real_escape_string($_POST['medalgbrack'], $mysql_link);
  $ranktypedispname = mysql_real_escape_string($_POST['ranktypedispname'], $mysql_link);
  $idlinefmt = mysql_real_escape_string($_POST['idlinefmt'], $mysql_link);
  $possep = mysql_real_escape_string($_POST['possep'], $mysql_link);
  $unitsep = mysql_real_escape_string($_POST['unitsep'], $mysql_link);
  $csla = mysql_real_escape_string($_POST['csl1'], $mysql_link);
  $cslb = mysql_real_escape_string($_POST['csl2'], $mysql_link);
  $cslc = mysql_real_escape_string($_POST['csl3'], $mysql_link);
  $unitype = mysql_real_escape_string($_POST['unitype'], $mysql_link);
  $joincontact = mysql_real_escape_string($_POST['joincontact'], $mysql_link);
  $jointext = mysql_real_escape_string($_POST['jointext'], $mysql_link);
  $compadmin = mysql_real_escape_string($_POST['compadmin'], $mysql_link);
  $query = "UPDATE EH_Groups Set Name='$name', Abbr='$abbr', ShortDesc='$sdesc', LongDesc='$ldesc', Active='$active', Banner='$banner', ProfileTabs='$tabs', MedalBrackets='$medalbrack', MedalSeperator='$medalsep', MedalGroupBrackets='$medalgbrack', RankTypeDisplayName='$ranktypedispname', IDLineFormat='$idlinefmt', PositionSeparator='$possep', UnitSeparator='$unitsep', CSAbbrL1='$csla', CSAbbrL2='$cslb', CSAbbrL3='$cslc', UniType='$unitype', GroupJoinContact='$joincontact', JoinMailBlurb='$jointext', CompAdmin='$compadmin' WHERE Group_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $sdesc = mysql_real_escape_string($_POST['sdesc'], $mysql_link);
  $ldesc = mysql_real_escape_string($_POST['ldesc'], $mysql_link);
  $active = mysql_real_escape_string($_POST['active'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $tabs = mysql_real_escape_string(implode(";", $_POST['tabs']), $mysql_link);
  $medalbrack = mysql_real_escape_string($_POST['medalbrack'], $mysql_link);
  $medalsep = mysql_real_escape_string($_POST['medalsep'], $mysql_link);
  $medalgbrack = mysql_real_escape_string($_POST['medalgbrack'], $mysql_link);
  $ranktypedispname = mysql_real_escape_string($_POST['ranktypedispname'], $mysql_link);
  $idlinefmt = mysql_real_escape_string($_POST['idlinefmt'], $mysql_link);
  $possep = mysql_real_escape_string($_POST['possep'], $mysql_link);
  $unitsep = mysql_real_escape_string($_POST['unitsep'], $mysql_link);
  $csla = mysql_real_escape_string($_POST['csl1'], $mysql_link);
  $cslb = mysql_real_escape_string($_POST['csl2'], $mysql_link);
  $cslc = mysql_real_escape_string($_POST['csl3'], $mysql_link);
  $unitype = mysql_real_escape_string($_POST['unitype'], $mysql_link);
  $query = "INSERT INTO EH_Groups (Name, Abbr, ShortDesc, LongDesc, Active, Banner, ProfileTabs, MedalBrackets, MedalSeperator, MedalGroupBrackets, RankTypeDisplayName, IDLineFormat, PositionSeparator, UnitSeparator, CSAbbrL1, CSAbbrL2, CSAbbrL3, UniType) VALUES('$name', '$abbr', '$sdesc', '$ldesc', '$active', '$banner', '$tabs', '$medalbrack', '$medalsep', '$medalgbrack', '$ranktypedispname', '$idlinefmt', '$possep', '$unitsep', '$csla', '$cslb', '$cslc', '$unitype')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Groups WHERE Group_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Group deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Group Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify</label>
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
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Group</span>
    </a>
  </p>
  <div id="add-form" title="Add New Group">
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
        <td><label for="sdesc">Short Description: </label></td>
        <td><textarea name="sdesc" id="sdesc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="ldesc">Long Description: </label></td>
        <td><textarea name="ldesc" id="ldesc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="active">Active: </label></td>
        <td><select name="active" id="active">
          <option value="0">Inactive</option>
          <option value="1">Active</option>
          <option value="2">Active - But not joinable group</option>
        </select></td>
      </tr>
      <tr>
        <td><label for="banner">Banner: </label></td>
        <td><input type="text" name="banner" id="banner"></td>
      </tr>
      <tr>
        <td><label for="tabs">Profile Tabs: </label></td>
        <td><select name="tabs[]" id="tabs" multiple="multiple">
<?
    $query1 = "SELECT GT_ID, Name FROM EH_Groups_Tabs Order By SortOrder";
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
        <td><label for="medalbrack">Medal Brackets: </label></td>
        <td><input type="text" name="medalbrack" id="medalbrack"></td>
      </tr>
      <tr>
        <td><label for="medalsep">Medal Seperator: </label></td>
        <td><input type="text" name="medalsep" id="medalsep"></td>
      </tr>
      <tr>
        <td><label for="medalgbrack">Medal Group Brackets: </label></td>
        <td><input type="text" name="medalgbrack" id="medalgbrack"></td>
      </tr>
      <tr>
        <td><label for="ranktypedispname">If Displaying Rank Type, what is it called: </label></td>
        <td><input type="text" name="ranktypedispname" id="ranktypedispname"></td>
      </tr>
      <tr>
        <td colspan="2">The following are commands are available for ID Lines:<br>
         @P@ - Positions<br>
         @R@ - Rank<br>
         @N@ - Name<br>
         @U@ - Unit<br>
         @F@ - FCHG<br>
         @C@ - Combat Rating<br>
         @M@ - Medals
         @T@ - Training</td>
      </tr>
      <tr>
        <td><label for="idlinefmt">ID Line Format: </label></td>
        <td><textarea name="idlinefmt" id="idlinefmt" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="possep">Position Separator: </label></td>
        <td><input type="text" name="possep" id="possep"></td>
      </tr>
      <tr>
        <td><label for="unitsep">Unit Separator: </label></td>
        <td><input type="text" name="unitsep" id="unitsep"></td>
      </tr>
      <tr>
        <td><label for="csl1">CS Abbr Level 1: </label></td>
        <td><input type="text" name="csl1" id="csl1"></td>
      </tr>
      <tr>
        <td><label for="csl2">CS Abbr Level 2: </label></td>
        <td><input type="text" name="csl2" id="csl2"></td>
      </tr>
      <tr>
        <td><label for="csl3">CS Abbr Level 3: </label></td>
        <td><input type="text" name="csl3" id="csl3"></td>
      </tr>
      <tr>
        <td><label for="unitype">Uniform Type: </label></td>
        <td><select name="unitype" id="unitype">
          <option value="1">Upload</option>
          <option value="2">Assembled</option>
          <option value="3">Rank Based</option>
        </select></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Group">
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