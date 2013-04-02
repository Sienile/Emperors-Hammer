<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "rosteradmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "rosteradmin");
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
  $query = "SELECT EH_Members.Member_ID, EH_Members.Name FROM EH_Members, EH_Members_Groups WHERE EH_Members_Groups.Group_ID=$datatable AND EH_Members_Groups.Member_ID=EH_Members.Member_ID Order By Name";
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
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT EH_Members.Member_ID, EH_Members.Name, EH_Members.Email, EH_Members_Groups.Active, EH_Members_Groups.JoinDate, EH_Members_Ranks.Rank_ID, EH_Members_Units.Unit_ID, EH_Members_Units.UnitPosition FROM EH_Members, EH_Members_Groups, EH_Members_Ranks, EH_Members_Units WHERE EH_Members.Member_ID=$id AND EH_Members.Member_ID=EH_Members_Groups.Member_ID AND EH_Members_Groups.Group_ID=$group AND EH_Members.Member_ID=EH_Members_Ranks.Member_ID AND EH_Members_Ranks.Group_ID=$group AND EH_Members.Member_ID=EH_Members_Units.Member_ID AND EH_Members_Units.Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
    <input type="hidden" name="group" value="<?=$group?>" />
      <table>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="email">E-mail: </label></td>
          <td><input type="text" name="email" id="email" value="<?=stripslashes($values[2])?>"></td>
        </tr>
        <tr>
          <td><label for="status">Status: </label></td>
          <td><select name="status" id="status">
            <option value="0"<? if($values[3]==0) echo " selected=\"selected\"";?>>Inactive</option>
            <option value="1"<? if($values[3]==1) echo " selected=\"selected\"";?>>Active</option>
          </select></td>
        </tr>
        <tr>
          <td>Group Join Date(currently disabled to edit)</td>
          <td><input type="hidden" name="joindate" value="<?=$values[4]?>"></td>
        </tr>
        <tr>
          <td><label for="pos">Positions: </label></td>
          <td>
    <select name="pos[]" id="pos" multiple="multiple">
    <?php
    $pos=array();
    $query1 = "SELECT Position_ID FROM EH_Members_Positions WHERE Group_ID=$group AND Member_ID=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      $pos[] = $values1[0];
      }
    $query1 = "SELECT Position_ID, Name FROM EH_Positions WHERE Group_ID=$group Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      for($q=0; $q<count($pos); $q++) {
        if($values1[0]==$pos[$q])
          echo " selected=\"selected\"";
        }
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="pripos">Primary Position: </label></td>
          <td>
    <select name="pripos" id="pripos">
    <?php
    $query1 = "SELECT Position_ID FROM EH_Members_Positions WHERE Group_ID=$group AND Member_ID=$values[0] AND isGroupPrimary=1";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      $pripos = $values1[0];
      }
    $query1 = "SELECT Position_ID, Name FROM EH_Positions WHERE Group_ID=$group Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$pripos)
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="rank">Rank:</label></td>
          <td><select name="rank" id="rank" >
    <?
    $query1 = "SELECT EH_Ranks.Rank_ID, EH_Ranks.Name, EH_Ranks_Types.Name FROM EH_Ranks, EH_Ranks_Types WHERE ";
    if($group!=1)
      $query1.=" EH_Ranks.Group_ID=$group AND ";
    $query1.="EH_Ranks.RT_ID=EH_Ranks_Types.RT_ID Order By EH_Ranks_Types.RT_ID, EH_Ranks.SortOrder";
    $query.= " Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[5])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])." (".stripslashes($values1[2]).")</option>\n";
      }?>
          </select></td>
        </tr>
        <tr>
          <td><label for="unit">Unit:</label></td>
          <td><select name="unit" id="unit" >
    <?
    $query1 = "SELECT EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units_Types.SelectorDisplayMasterUnit FROM EH_Units, EH_Units_Types WHERE EH_Units.Group_ID=$group AND EH_Units_Types.UT_ID=EH_Units.UT_ID Order By EH_Units.Master_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[6])
        echo " selected=\"selected\"";
      echo ">";
      if($values1[3]==1) {
        $query2 = "SELECT Name FROM EH_Units WHERE Unit_ID=$values1[2]";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        for($j=0; $j<$rows2; $j++) {
          $values2 = mysql_fetch_row($result2);
          echo stripslashes($values2[0])." - ";
          }
        }
      echo stripslashes($values1[1]);
      if($values1[3]==2) {
        $query2 = "SELECT Name FROM EH_Units WHERE Unit_ID=$values1[2]";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        for($j=0; $j<$rows2; $j++) {
          $values2 = mysql_fetch_row($result2);
          echo " - ". stripslashes($values2[0]);
          }
        }
      echo "</option>\n";
      }?>
          </select></td>
        </tr>
        <tr>
          <td><label for="unitpos">Unit Position (if applicable): </label></td>
          <td><input type="text" name="unitpos" id="unitpos" value="<?=stripslashes($values[7])?>"></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $time = time();
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $group = mysql_real_escape_string($_POST['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $email = mysql_real_escape_string($_POST['email'], $mysql_link);
  $status = mysql_real_escape_string($_POST['status'], $mysql_link);
  $joindate = mysql_real_escape_string($_POST['joindate'], $mysql_link);
  $pos = $_POST['pos'];
  $pripos = mysql_real_escape_string($_POST['pripos'], $mysql_link);
  $rank = mysql_real_escape_string($_POST['rank'], $mysql_link);
  $unit = mysql_real_escape_string($_POST['unit'], $mysql_link);
  $unitpos = mysql_real_escape_string($_POST['unitpos'], $mysql_link);
  $query = "UPDATE EH_Members Set Name='$name', Email='$email' WHERE Member_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "UPDATE EH_Members_Groups Set Active='$status', JoinDate='$joindate' WHERE Member_ID=$id AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  //Update Rank
  $query1 = "SELECT Rank_ID FROM EH_Members_Ranks WHERE Group_ID=$group AND Member_ID=$id";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $prevrank = $values1[0];
    }
  else {
    $prevrank=0;
    }
  if($prevrank!=$rank) {
     $query = "UPDATE EH_Members_Ranks Set Rank_ID='$rank', PromotionDate='$time' WHERE Member_ID=$id AND Group_ID=$group";
     $result = mysql_query($query, $mysql_link);
     $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 1, '".$prevrank."-".$rank."', 'Change to rank', '$time')";
     $result = mysql_query($query, $mysql_link);
     }
  //Update Unit
  $query1 = "SELECT Unit_ID FROM EH_Members_Units WHERE Group_ID=$group AND Member_ID=$id";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $prevunit = $values1[0];
      }
  if($prevunit!=$unit) {
     $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 2, '".$prevunit."-".$unit."', 'Change to unit', '$time')";
     $result = mysql_query($query, $mysql_link);
     }
   $query = "UPDATE EH_Members_Units Set Unit_ID='$unit', UnitPosition='$unitpos'";
   if($prevunit!=$unit)
     $query .= ", UnitDate='$time'";
   $query .= " WHERE Member_ID=$id AND Group_ID=$group";
   $result = mysql_query($query, $mysql_link);

  //Update Positions
  $query1 = "SELECT Position_ID FROM EH_Members_Positions WHERE Group_ID=$group AND Member_ID=$id";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($q=0; $q<$rows1; $q++) {
    $values1 = mysql_fetch_row($result1);
    if(in_array($values1[0], $pos)) {
      $pospos = array_search($values1[0], $pos);
      for($j=$pospos; $j<count($pos); $j++)
        if($j+1<count($pos))
          $pos[$j]=$pos[$j+1];
        else
          $pos[$j]=0;
      }
    else {
      //They aren't there
      $query1 = "DELETE FROM EH_Members_Positions WHERE Group_ID=$group AND Member_ID=$id AND Position_ID=$values1[0]";
      $result1 = mysql_query($query1, $mysql_link);
      $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 3, '".$values1[0]."-0', 'Left position', '$time')";
      $result = mysql_query($query, $mysql_link);
      }
    }
  if(count($pos)) {
    foreach($pos as $posadd) {
      $query1 = "INSERT INTO EH_Members_Positions(Member_ID, Position_ID, Group_ID, isGroupPrimary, PositionDate) Values('$id', '$posadd', '$group', '0', '$time')";
      $result1 = mysql_query($query1, $mysql_link);
      $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 3, '0-".$posadd."', 'Change to position', '$time')";
      $result = mysql_query($query, $mysql_link);
      }
    }
  $query1 = "Update EH_Members_Positions Set isGroupPrimary=0 WHERE Group_ID=$group AND Member_ID=$id";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "SELECT Position_ID FROM EH_Members_Positions WHERE Group_ID=$group AND Member_ID=$id AND Position_ID=$pripos";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $query1 = "Update EH_Members_Positions Set isGroupPrimary=1 WHERE Group_ID=$group AND Member_ID=$id AND Position_ID=$pripos";
    $result1 = mysql_query($query1, $mysql_link);
    }
  else {
      $query1 = "INSERT INTO EH_Members_Positions(Member_ID, Position_ID, Group_ID, isGroupPrimary, PositionDate) Values('$id', '$pripos', '$group', '1', '$time')";
      $result1 = mysql_query($query1, $mysql_link);
      $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 3, '0-".$pripos."', 'Change to position Primary', '$time')";
      $result = mysql_query($query, $mysql_link);
    }

  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $time = time();
  $id = mysql_real_escape_string($_POST['memberid'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $email = mysql_real_escape_string($_POST['email'], $mysql_link);
  $status = mysql_real_escape_string($_POST['status'], $mysql_link);
  $joindate = $time;
  $pripos = mysql_real_escape_string($_POST['pripos'], $mysql_link);
  $rank = mysql_real_escape_string($_POST['rank'], $mysql_link);
  $unit = mysql_real_escape_string($_POST['unit'], $mysql_link);
  $unitpos = mysql_real_escape_string($_POST['unitpos'], $mysql_link);
  if($id==0) {
    srand(time());
    $pool = "ABCDEFGHIJKLMNOPQRSTUZWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "1234567890";
    $pool .="!@#$%^&*()_-+=[]{};:<>,./?|`~";
    for($l=0; $l<10; $l++) {
      $pw .= substr($pool, (rand()%(strlen($pool))), 1);
      }
    $hash_value = hash("sha512", $pw);
    $query = "INSERT INTO EH_Members (Name, Email, UserPassword) VALUES('$name', '$email', '$hash_value')";
    $result = mysql_query($query, $mysql_link);
    $id = mysql_insert_id($mysql_link);
    }
  $query = "INSERT INTO EH_Members_Groups (Active, JoinDate, Member_ID, Group_ID) VALUES ('$status', '$joindate', '$id', '$group')";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO EH_Members_Ranks (Rank_ID, PromotionDate, Member_ID, Group_ID) VALUES ('$rank', '$time', '$id', '$group')";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 1, '0-".$rank."', 'Initial Rank', '$time')";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 2, '0-".$unit."', 'Initial Unit', '$time')";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO EH_Members_Units (Unit_ID, UnitPosition, UnitDate, Member_ID, Group_ID) VALUES('$unit', '$unitpos', '$time', '$id', '$group')";
  $result = mysql_query($query, $mysql_link);
  if(count($pos)) {
    foreach($pos as $posadd) {
      $query1 = "INSERT INTO EH_Members_Positions(Member_ID, Position_ID, Group_ID, isGroupPrimary, PositionDate) Values('$id', '$posadd', '$group', '0', '$time')";
      $result1 = mysql_query($query1, $mysql_link);
      $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 3, '0-".$posadd."', 'Initial Position', '$time')";
      $result = mysql_query($query, $mysql_link);
      }
    }
  $query1 = "SELECT Position_ID FROM EH_Members_Positions WHERE Group_ID=$group AND Member_ID=$id AND Position_ID=$pripos";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $query1 = "Update EH_Members_Positions Set isGroupPrimary=1 WHERE Group_ID=$group AND Member_ID=$id AND Position_ID=$pripos";
    $result1 = mysql_query($query1, $mysql_link);
    }
  else {
      $query1 = "INSERT INTO EH_Members_Positions(Member_ID, Position_ID, Group_ID, isGroupPrimary, PositionDate) Values('$id', '$pripos', '$group', '1', '$time')";
      $result1 = mysql_query($query1, $mysql_link);
      $query = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$id', '$group', 3, '0-".$pripos."', 'Initial Position Primary', '$time')";
      $result = mysql_query($query, $mysql_link);
    }
  if($result)
    echo "<p>Person inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "DELETE FROM EH_Members_Groups WHERE Member_ID=$id AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Members_Positions WHERE Member_ID=$id AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Members_Ranks WHERE Member_ID=$id AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Members_Units WHERE Member_ID=$id AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Members_History WHERE Member_ID=$id AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Member deleted from group successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Roster Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their Roster</label>
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
    <a onClick="getMembers(); getPositions(); getRanks(); getUnits(); $('#add-form').dialog('open')" href="#">
        <span style="color:#6699CC;">Add New Person</span>
    </a>
  </p>
  <div id="add-form" title="Add New Person">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="memberid">Member: </label></td>
        <td><select name="memberid" id="memberid">
        </select></td>
      </tr>
      <tr>
        <td><label for="name">Name (only if not in the Database): </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="email">E-mail (only if not in the Database): </label></td>
        <td><input type="text" name="email" id="email" value="<?=$values[2]?>"></td>
      </tr>
        <tr>
          <td><label for="status">Status: </label></td>
          <td><select name="status" id="status">
            <option value="0">Inactive</option>
            <option value="1">Active</option>
          </select></td>
        </tr>
        <tr>
          <td><label for="pos">Positions: </label></td>
          <td><select name="pos[]" id="pos" multiple="multiple">
          </select></td>
        </tr>
        <tr>
          <td><label for="pripos">Primary Position: </label></td>
          <td><select name="pripos" id="pripos">
          </select></td>
        </tr>
        <tr>
          <td><label for="rank">Rank:</label></td>
          <td><select name="rank" id="rank" >
          </select></td>
        </tr>
        <tr>
          <td><label for="unit">Unit:</label></td>
          <td><select name="unit" id="unit" >
          </select></td>
        </tr>
        <tr>
          <td><label for="unitpos">Unit Position (if applicable): </label></td>
          <td><input type="text" name="unitpos" id="unitpos"></td>
        </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Person">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  function getEditForm(id) {
    var groupId = $("#selGroup option:selected").val();
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id+"&group="+groupId,{},function(data){
      $("#editArea").html(data);
    },'html').complete(function() {
      $("#editArea").dialog("open");
      });
  }

  function getMembers(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#memberid").empty();
	$("#memberid").append('<option value="0">Not in Database</option>');
	getAdminJSONdata("getMembersByNotGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#memberid").append('<option value="'+item.Member_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

  function getUnits(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#unit").empty();
	getAdminJSONdata("getUnitsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#unit").append('<option value="'+item.Unit_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

  function getPositions(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#pripos").empty();
	$("#pos").empty();
	getAdminJSONdata("getPositionsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#pripos").append('<option value="'+item.Position_ID+'">'+item.Name+'</option>');
					$("#pos").append('<option value="'+item.Position_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

  function getRanks(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#rank").empty();
	getAdminJSONdata("getRanksByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#rank").append('<option value="'+item.Rank_ID+'">'+item.Name+' ('+item.RT_Name+')</option>');
				});
			}
		}
	);
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
        width: 600,
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
        width: 600,
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