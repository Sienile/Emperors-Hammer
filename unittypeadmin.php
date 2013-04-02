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
      <td width="60%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
      <td width="10%">Move Up</td>
      <td width="10%">Move Down</td>
    </tr>
    <?php
  $query = "SELECT UT_ID, Name, SortOrder FROM EH_Units_Types WHERE Group_ID=$datatable Order By SortOrder";
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
  $query = "select UT_ID, SortOrder From EH_Units_Types Where UT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select UT_ID From EH_Units_Types Where SortOrder=$newso AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Units_Types Set SortOrder=$newso Where UT_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Units_Types Set SortOrder=$curso Where UT_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Type moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select UT_ID, SortOrder From EH_Units_Types Where UT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select UT_ID From EH_Units_Types Where SortOrder=$newso AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Units_Types Set SortOrder=$newso Where UT_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Units_Types Set SortOrder=$curso Where UT_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Type moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT UT_ID, Name, DisplayUnitBase, PrefixPostfixType, DisplayUT, SelectorDisplayMasterUnit, DisplayMasterUnit, Position, MaxPos, PageSections FROM EH_Units_Types WHERE UT_ID=$id";
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
          <td><label for="dispunitbase">Display Unit Base: </label></td>
          <td>
              <input type="checkbox" name="dispunitbase" id="dispunitbase" value="1" <?=($values[2]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="preposttype">Prefix/Postfix Unit Type: </label></td>
          <td><select name="preposttype" id="preposttype">
              <option value="0"<? if($values[3]==0) echo " selected=\"selected\""; ?>>None</option>
              <option value="1"<? if($values[3]==1) echo " selected=\"selected\""; ?>>Prefix</option>
              <option value="2"<? if($values[3]==2) echo " selected=\"selected\""; ?>>Postfix</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="disput">Display Unit Type: </label></td>
          <td>
              <input type="checkbox" name="disput" id="disput" value="1" <?=($values[4]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="seldispmaster">Selector Display Master Unit: </label></td>
          <td><select name="seldispmaster" id="seldispmaster">
              <option value="0"<? if($values[5]==0) echo " selected=\"selected\""; ?>>None</option>
              <option value="1"<? if($values[5]==1) echo " selected=\"selected\""; ?>>Prefix</option>
              <option value="2"<? if($values[5]==2) echo " selected=\"selected\""; ?>>Postfix</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="dispmaster">Display Master Unit: </label></td>
          <td><select name="dispmaster" id="dispmaster">
              <option value="0"<? if($values[6]==0) echo " selected=\"selected\""; ?>>None</option>
              <option value="1"<? if($values[6]==1) echo " selected=\"selected\""; ?>>Prefix</option>
              <option value="2"<? if($values[6]==2) echo " selected=\"selected\""; ?>>Postfix</option>
            </select>
          </td>
        </tr>
        <tr>
          <td><label for="usepos">Number positions in unit: </label></td>
          <td>
              <input type="checkbox" name="usepos" id="usepos" value="1" <?=($values[7]==1) ? "checked=\"checked\"" : ""?> >
          </td>
        </tr>
        <tr>
          <td><label for="maxpos">Max Number of Positions: </label></td>
          <td><input type="text" name="maxpos" id="maxpos" value="<?=stripslashes($values[8])?>"></td>
        </tr>
        <tr>
          <td colspan="2">Available Page Sections:<br>
          Training Certs - @TC@<br>
          Battle Certs - @BC@<br>
          Message Bpard - @MB@<br>
          Name - @NA@<br>
          Motto - @MT@<br>
          Base - @BA@<br>
          Site - @SU@<br>
          Banner - @BN@<br>
          Nickname - @NN@<br>
          MissionRoll - @MR@</td>
        </tr>
        <tr>
          <td><label for="pagesec">Page Sections: </label></td>
          <td><textarea name="pagesec" id="page" style="width:400px; height:120px"><?=stripslashes($values[9])?></textarea></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $dispunitbase = mysql_real_escape_string($_POST['dispunitbase'], $mysql_link);
  if($dispunitbase)
    $dispunitbase=1;
  else
    $dispunitbase=0;
  $preposttype = mysql_real_escape_string($_POST['preposttype'], $mysql_link);
  $disput = mysql_real_escape_string($_POST['disput'], $mysql_link);
  if($disput)
    $disput=1;
  else
    $disput=0;
  $seldispmaster = mysql_real_escape_string($_POST['seldispmaster'], $mysql_link);
  $dispmaster = mysql_real_escape_string($_POST['dispmaster'], $mysql_link);
  $usepos = mysql_real_escape_string($_POST['usepos'], $mysql_link);
  if($usepos)
    $usepos=1;
  else
    $usepos=0;
  $maxpos = mysql_real_escape_string($_POST['maxpos'], $mysql_link);
  $pagesec = mysql_real_escape_string($_POST['pagesec'], $mysql_link);
  $query = "UPDATE EH_Units_Types Set Name='$name', DisplayUnitBase='$dispunitbase', PrefixPostfixType='$preposttype', DisplayUT='$disput', SelectorDisplayMasterUnit='$seldispmaster', DisplayMasterUnit='$dispmaster', Position='$usepos', MaxPos='$maxpos', PageSections='$pagesec' WHERE UT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $dispunitbase = mysql_real_escape_string($_POST['dispunitbase'], $mysql_link);
  if($dispunitbase)
    $dispunitbase=1;
  else
    $dispunitbase=0;
  $preposttype = mysql_real_escape_string($_POST['preposttype'], $mysql_link);
  $disput = mysql_real_escape_string($_POST['disput'], $mysql_link);
  if($disput)
    $disput=1;
  else
    $disput=0;
  $seldispmaster = mysql_real_escape_string($_POST['seldispmaster'], $mysql_link);
  $dispmaster = mysql_real_escape_string($_POST['dispmaster'], $mysql_link);
  $usepos = mysql_real_escape_string($_POST['usepos'], $mysql_link);
  if($usepos)
    $usepos=1;
  else
    $usepos=0;
  $maxpos = mysql_real_escape_string($_POST['maxpos'], $mysql_link);
  $pagesec = mysql_real_escape_string($_POST['pagesec'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Units_Types WHERE Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Units_Types
                (Name, DisplayUnitBase, PrefixPostfixType, DisplayUT, SelectorDisplayMasterUnit, DisplayMasterUnit, Position, MaxPos, PageSections, Group_ID, SortOrder)
                VALUES('$name', '$dispunitbase', '$preposttype', '$disput', '$seldispmaster', '$dispmaster', '$usepos', '$maxpos', '$pagesec', '$group', '$so')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Units_Types WHERE UT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Units_Types Set SortOrder=SortOrder-1 WHERE Group_ID=$group AND SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Units_Types WHERE UT_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Type deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Unit Type Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the group to modify their Unit Types</label>
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
        <span style="color:#6699CC;">Add New Unit Type</span>
    </a>
  </p>
  <div id="add-form" title="Add New Unit Type">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="dispunitbase">Display Unit Base: </label></td>
        <td>
          <input type="checkbox" name="dispunitbase" id="dispunitbase" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="preposttype">Prefix/Postfix Unit Type: </label></td>
        <td><select name="preposttype" id="preposttype">
            <option value="0">None</option>
            <option value="1">Prefix</option>
            <option value="2">Postfix</option>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="disput">Display Unit Type: </label></td>
        <td>
          <input type="checkbox" name="disput" id="disput" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="seldispmaster">Selector Display Master Unit: </label></td>
        <td><select name="seldispmaster" id="seldispmaster">
            <option value="0">None</option>
            <option value="1">Prefix</option>
            <option value="2">Postfix</option>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="dispmaster">Display Master Unit: </label></td>
        <td><select name="dispmaster" id="dispmaster">
            <option value="0">None</option>
            <option value="1">Prefix</option>
            <option value="2">Postfix</option>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="usepos">Number positions in unit: </label></td>
        <td>
            <input type="checkbox" name="usepos" id="usepos" value="1">
        </td>
      </tr>
      <tr>
        <td><label for="maxpos">Max Number of Positions: </label></td>
        <td><input type="text" name="maxpos" id="maxpos"></td>
      </tr>
      <tr>
        <td colspan="2">Available Page Sections:<br>
        Training Certs - @TC@<br>
        Battle Certs - @BC@<br>
        Message Bpard - @MB@<br>
        Name - @NA@<br>
        Motto - @MT@<br>
        Base - @BA@<br>
        Site - @SU@<br>
        Banner - @BN@<br>
        Nickname - @NN@<br>
        MissionRoll - @MR@</td>
      </tr>
      <tr>
        <td><label for="pagesec">Page Sections: </label></td>
        <td><textarea name="pagesec" id="page" style="width:400px; height:120px"></textarea></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Unit Type">
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