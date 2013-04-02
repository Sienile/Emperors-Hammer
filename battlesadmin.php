<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "battlesadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if($_GET['datatable']) {
    ?>
  <table>
    <tr>
      <td width="80%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
<?php
  $query = "SELECT EH_Battles.Battle_ID FROM EH_Battles, EH_Battles_Categories, EH_Platforms WHERE EH_Battles.Platform_ID=EH_Platforms.Platform_ID AND EH_Battles.BC_ID=EH_Battles_Categories.BC_ID Order By EH_Platforms.Name, EH_Battles_Categories.SortOrder, EH_Battles.BattleNumber";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="80%"><?=BattleName($values[0], 1)?></td>
        <td width="10%"><a id="edit" onclick="getEditForm(<?=$values[0]?>)"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php 
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Battle_ID, Platform_ID, BattleNumber, BC_ID, Name, Description, Reward_Name, Reward_Image, Filename, Wav_Pack, Creator_1, Creator_2, Creator_3, Creator_4, Status, NumMissions FROM EH_Battles WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="plt">Platform: </label></td>
          <td>
    <select name="plt" id="plt" >
    <?php
    $query1 = "SELECT Platform_ID, Name FROM EH_Platforms Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[1])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="bcid">Category: </label></td>
          <td>
    <select name="bcid" id="bcid" >
    <?php
    $query1 = "SELECT BC_ID, Name FROM EH_Battles_Categories Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[3])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="battlenum">Battle Number: </label></td>
          <td><input type="text" name="battlenum" id="battlenum" value="<?=stripslashes($values[2])?>"></td>
        </tr>
        <tr>
          <td><label for="name">Battle Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="nummiss">Number of Missions: </label></td>
          <td><input type="text" name="nummiss" id="nummiss" value="<?=stripslashes($values[15])?>"></td>
        </tr>
        <tr>
          <td><label for="desc">Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[5])?></textarea></td>
        </tr>
        <tr>
          <td><label for="rewardname">Reward Name: </label></td>
          <td><input type="text" name="rewardname" id="rewardname" value="<?=stripslashes($values[6])?>"></td>
        </tr>
        <tr>
          <td><label for="rewardimg">Reward Image: </label></td>
          <td><input type="text" name="rewardimg" id="rewardimg" value="<?=stripslashes($values[7])?>"></td>
        </tr>
        <tr>
          <td><label for="file">Filename: </label></td>
          <td><input type="text" name="file" id="file" value="<?=stripslashes($values[8])?>"></td>
        </tr>
        <tr>
          <td><label for="wav">Wav Pack: </label></td>
          <td><input type="text" name="wav" id="wav" value="<?=stripslashes($values[9])?>"></td>
        </tr>
        <tr>
          <td><label for="Creator1">Creator 1: </label></td>
          <td>
    <select name="Creator1" id="Creator1">
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[10])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="Creator2">Creator 2: </label></td>
          <td>
    <select name="Creator2" id="Creator2">
      <option value="0"<? if($values[11]==0) echo " selected=\"selected\""; ?>>None</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[11])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="Creator3">Creator 3: </label></td>
          <td>
    <select name="Creator3" id="Creator3">
      <option value="0"<? if($values[12]==0) echo " selected=\"selected\""; ?>>None</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[12])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="Creator4">Creator 4: </label></td>
          <td>
    <select name="Creator4" id="Creator4">
      <option value="0"<? if($values[13]==0) echo " selected=\"selected\""; ?>>None</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[13])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="status">Battle Status: </label></td>
          <td>
    <select name="status" id="status">
      <option value="0"<? if($values[14]==0) echo " selected=\"selected\"";?>>Not Active/Available</option>
      <option value="1"<? if($values[14]==1) echo " selected=\"selected\"";?>>Active/Available</option>
    </select></td>
        </tr>

        <tr>
          <td><label for="patches">Patces Required: </label></td>
          <td>
    <select name="patches[]" id="patches" multiple="multiple">
    <?php
    $pos=array();
    $query1 = "SELECT Patch_ID FROM EH_Battles_Patches WHERE Battle_ID=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      $pos[] = $values1[0];
      }
    $query1 = "SELECT EH_Patches.Patch_ID, EH_Patches.Name, EH_Platforms.Abbr FROM EH_Patches, EH_Platforms WHERE EH_Platforms.Platform_ID=EH_Patches.Platform_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      for($q=0; $q<count($pos); $q++) {
        if($values1[0]==$pos[$q])
          echo " selected=\"selected\"";
        }
      echo ">".stripslashes($values1[1])." (".stripslashes($values1[2]).")</option>\n";
      }?>
    </select></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $plt = mysql_real_escape_string($_POST['plt'], $mysql_link);
  $bcid = mysql_real_escape_string($_POST['bcid'], $mysql_link);
  $battlenum = mysql_real_escape_string($_POST['battlenum'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $rewardname = mysql_real_escape_string($_POST['rewardname'], $mysql_link);
  $rewardimg = mysql_real_escape_string($_POST['rewardimg'], $mysql_link);
  $file = mysql_real_escape_string($_POST['file'], $mysql_link);
  $wav = mysql_real_escape_string($_POST['wav'], $mysql_link);
  $Creator1 = mysql_real_escape_string($_POST['Creator1'], $mysql_link);
  $Creator2 = mysql_real_escape_string($_POST['Creator2'], $mysql_link);
  $Creator3 = mysql_real_escape_string($_POST['Creator3'], $mysql_link);
  $Creator4 = mysql_real_escape_string($_POST['Creator4'], $mysql_link);
  $status = mysql_real_escape_string($_POST['status'], $mysql_link);
  $nummiss = mysql_real_escape_string($_POST['nummiss'], $mysql_link);
  $now = time();
  $person = $_SESSION['EHID'];
  $patches=$_POST['patches'];
  $query = "UPDATE EH_Battles Set Platform_ID='$plt', BattleNumber='$battlenum', BC_ID='$bcid', Name='$name', Description='$desc', Last_Updated='$now', Updater_ID='$person', Reward_Name='$rewardname', Reward_Image='$rewardimg', Filename='$file', Wav_Pack='$wav', Creator_1='$Creator1', Creator_2='$Creator2', Creator_3='$Creator3', Creator_4='$Creator4',  Status='$status', NumMissions='$nummiss' WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query1 = "SELECT Patch_ID FROM EH_Battles_Patches WHERE Battle_ID=$id";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($q=0; $q<$rows1; $q++) {
    $values1 = mysql_fetch_row($result1);
    if(in_array($values1[0], $patches)) {
      $pospos = array_search($values1[0], $patches);
      for($j=$pospos; $j<count($patches)-1; $j++)
        if($j+1<count($patches))
          $patches[$j]=$patches[$j+1];
      $patches = array_pop($patches);
      }
    else {
      //They aren't there
      $query2 = "DELETE FROM EH_Battles_Patches WHERE Battle_ID=$id AND Patch_ID=$values1[0]";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  if(count($patches)) {
    foreach($patches as $posadd) {
      if($posadd) {
        $query2 = "INSERT INTO EH_Battles_Patches(Battle_ID, Patch_ID) Values('$id', '$posadd')";
        $result2 = mysql_query($query2, $mysql_link);
        }
      }
    }
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $plt = mysql_real_escape_string($_POST['plt'], $mysql_link);
  $bcid = mysql_real_escape_string($_POST['bcid'], $mysql_link);
  $battlenum = mysql_real_escape_string($_POST['battlenum'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $rewardname = mysql_real_escape_string($_POST['rewardname'], $mysql_link);
  $rewardimg = mysql_real_escape_string($_POST['rewardimg'], $mysql_link);
  $file = mysql_real_escape_string($_POST['file'], $mysql_link);
  $wav = mysql_real_escape_string($_POST['wav'], $mysql_link);
  $Creator1 = mysql_real_escape_string($_POST['Creator1'], $mysql_link);
  $Creator2 = mysql_real_escape_string($_POST['Creator2'], $mysql_link);
  $Creator3 = mysql_real_escape_string($_POST['Creator3'], $mysql_link);
  $Creator4 = mysql_real_escape_string($_POST['Creator4'], $mysql_link);
  $status = mysql_real_escape_string($_POST['status'], $mysql_link);
  $nummiss = mysql_real_escape_string($_POST['nummiss'], $mysql_link);
  $now = time();
  $person = $_SESSION['EHID'];
  $patches=$_POST['patches'];
  $query = "INSERT INTO EH_Battles (Platform_ID, BattleNumber, BC_ID, Name, Description, Released, Last_Updated, Updater_ID, Reward_Name, Reward_Image, Filename, Wav_Pack, Creator_1, Creator_2, Creator_3, Creator_4, Status, NumMissions) VALUES('$plt', '$battlenum', '$bcid', '$name', '$desc', '$now', '$now', '$person', '$rewardname', '$rewardimg', '$file', '$wav', '$Creator1', '$Creator2', '$Creator3', '$Creator4', '$status', '$nummiss')";
  $result = mysql_query($query, $mysql_link);
  $id = mysql_insert_id($mysql_link);
  if(count($patches)) {
    foreach($patches as $posadd) {
      $query1 = "INSERT INTO EH_Battles_Patches(Battle_ID, Patch_ID) Values('$id', '$posadd')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Battles WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Bugs WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Bugs_Notes WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Complete WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Missions WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Patches WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Battles_Reviews WHERE Battle_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Battle deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Battle Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Battle</span>
      </a>
  </p>
  <div id="add-form" title="Add New Battle">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="plt">Platform: </label></td>
        <td>
    <select name="plt" id="plt">
    <?php
    $query1 = "SELECT Platform_ID, Name FROM EH_Platforms Order By Name";
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
        <td><label for="bcid">Category: </label></td>
        <td>
    <select name="bcid" id="bcid">
    <?php
    $query1 = "SELECT BC_ID, Name FROM EH_Battles_Categories Order By SortOrder";
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
        <td><label for="battlenum">Battle Number: </label></td>
        <td><input type="text" name="battlenum" id="battlenum"></td>
      </tr>
      <tr>
        <td><label for="name">Battle Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="nummiss">Number of Missions: </label></td>
        <td><input type="text" name="nummiss" id="nummiss"></td>
      </tr>
      <tr>
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="rewardname">Reward Name: </label></td>
        <td><input type="text" name="rewardname" id="rewardname"></td>
      </tr>
      <tr>
        <td><label for="rewardimg">Reward Image: </label></td>
        <td><input type="text" name="rewardimg" id="rewardimg"></td>
      </tr>
      <tr>
        <td><label for="file">Filename: </label></td>
        <td><input type="text" name="file" id="file"></td>
      </tr>
      <tr>
        <td><label for="wav">Wav Pack: </label></td>
        <td><input type="text" name="wav" id="wav"></td>
      </tr>
      <tr>
        <td><label for="Creator1">Creator 1: </label></td>
        <td>
    <select name="Creator1" id="Creator1">
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
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
        <td><label for="Creator2">Creator 2: </label></td>
        <td>
    <select name="Creator2" id="Creator2">
      <option value="0">None</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
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
        <td><label for="Creator3">Creator 3: </label></td>
        <td>
    <select name="Creator3" id="Creator3">
      <option value="0">None</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
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
        <td><label for="Creator4">Creator 4: </label></td>
        <td>
    <select name="Creator4" id="Creator4">
      <option value="0">None</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
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
        <td><label for="status">Battle Status: </label></td>
        <td>
    <select name="status" id="status">
      <option value="0">Not Active/Available</option>
      <option value="1">Active/Available</option>
    </select></td>
      </tr>
      <tr>
        <td><label for="patches">Patces Required: </label></td>
        <td>
    <select name="patches[]" id="patches" multiple="multiple">
    <?php
    $query1 = "SELECT EH_Patches.Patch_ID, EH_Patches.Name, EH_Platforms.Abbr FROM EH_Patches, EH_Platforms WHERE EH_Platforms.Platform_ID=EH_Patches.Platform_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\">".stripslashes($values1[1])." (".stripslashes($values1[2]).")</option>\n";
      }?>
    </select></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Battle">
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
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id,{},showSuccess,'html');
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function postAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add=true',
        success: showSuccess
    }
    $("#addForm").ajaxSubmit(options);
    return false;
  }

  function postEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true',
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    return false;
  }

  function getDataTable() {
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=true",{},function(data){
        $("#response").html(data);
    },'html');
  }
  $(document).ready(getDataTable);


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