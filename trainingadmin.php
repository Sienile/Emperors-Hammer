<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "trainadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "trainadmin");
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
  $query = "SELECT Training_ID, Name, SortOrder FROM EH_Training WHERE TAc_ID=$datatable Order By SortOrder";
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
  $query = "select Training_ID, SortOrder From EH_Training Where Training_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select Training_ID From EH_Training Where SortOrder=$newso AND TAc_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training Set SortOrder=$newso Where Training_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training Set SortOrder=$curso Where Training_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Course moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "select Training_ID, SortOrder From EH_Training Where Training_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select Training_ID From EH_Training Where SortOrder=$newso AND TAc_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training Set SortOrder=$newso Where Training_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training Set SortOrder=$curso Where Training_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Course moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Training_ID, Name, Abbr, TC_ID, Available, Description, Min_Training_ID, Min_Rank_ID, Min_Pos_ID, Min_Time, MinPoints, MaxPoints, NotesFile, Rewards, Grader, Ribbon, TAc_ID FROM EH_Training WHERE Training_ID=$id";
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
          <td><label for="acad">Academy: </label></td>
          <td>
    <input type="hidden" name="orgacad" value="<?=$values[16]?>" />
    <select name="acad" id="acad">
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
    if($values1[0]==$values[16])
      echo " selected=\"selected\"";
    echo ">".stripslashes($values1[1])."</option>\n";
  }
?>
    </select></td>
        </tr>
        <tr>
          <td><label for="tcid">Category: </label></td>
          <td>
    <select name="tcid" id="tcid" >
    <?php
    $query1 = "SELECT TC_ID, Name FROM EH_Training_Categories WHERE TCa_ID=$values[16] Order By SortOrder";
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
          <td><label for="minTrain">PreRequisite Course: </label></td>
          <td>
    <select name="minTrain" id="minTrain" >
      <option value="0"
      <?php
    if($values[6]==0)
      echo " selected=\"selected\"";
    echo ">No Prerequsite Course</option>\n";
    $query1 = "SELECT Training_ID, Name FROM EH_Training Order By TAc_ID, SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[6])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="minRank">PreRequisite Rank: </label></td>
          <td>
    <select name="minRank" id="minRank" >
      <option value="0"
      <?php
    if($values[7]==0)
      echo " selected=\"selected\"";
    echo ">No Prerequsite Rank</option>\n";
    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks Order By Group_ID, SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[7])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }?>
    </select></td>
        </tr>
        <tr>
          <td><label for="minPos">Minimum Position: </label></td>
          <td>
    <select name="minPos" id="minPos" >
      <option value="0"
<?php
    if($values[8]==0)
      echo " selected=\"selected\"";
    echo ">No Position Required</option>";
    $query1 = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[8])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }
      ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="minTime">Number of months required in the Group: </label></td>
          <td><input type="text" name="minTime" id="minTime" value="<?=stripslashes($values[9])?>"></td>
        </tr>
        <tr>
          <td><label for="minPoints">Min Points on the test to pass:</label></td>
          <td><input type="text" name="minPoints" id="minPoints" value="<?=stripslashes($values[10])?>"></td>
        </tr>
        <tr>
          <td><label for="maxPoints">Max Points on the test:</label></td>
          <td><input type="text" name="maxPoints" id="maxPoints" value="<?=stripslashes($values[11])?>"></td>
        </tr>
        <tr>
          <td><label for="notesfile">Notes Filename:</label></td>
          <td><input type="text" name="notesFile" id="notesFile" value="<?=stripslashes($values[12])?>"></td>
        </tr>
        <tr>
          <td><label for="Rewards">Rewards(for non-autoawarding awards):</label></td>
          <td><input type="text" name="Rewards" id="Rewards" value="<?=stripslashes($values[13])?>"></td>
        </tr>
        <tr>
          <td><label for="grader">Grader: </label></td>
          <td>
    <select name="grader" id="grader" >
      <option value="0"
<?php
    if($values[14]==0)
      echo " selected=\"selected\"";
    echo ">None</option>";
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[14])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }
      ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="ribbon">Ribbon Image:</label></td>
          <td><input type="text" name="ribbon" id="ribbon" value="<?=stripslashes($values[15])?>"></td>
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
  $minTrain = mysql_real_escape_string($_POST['minTrain'], $mysql_link);
  $minRank = mysql_real_escape_string($_POST['minRank'], $mysql_link);
  $minPos = mysql_real_escape_string($_POST['minPos'], $mysql_link);
  $minTime = mysql_real_escape_string($_POST['minTime'], $mysql_link);
  $minPoints = mysql_real_escape_string($_POST['minPoints'], $mysql_link);
  $notesFile = mysql_real_escape_string($_POST['notesFile'], $mysql_link);
  $Rewards = mysql_real_escape_string($_POST['Rewards'], $mysql_link);
  $maxPoints = mysql_real_escape_string($_POST['maxPoints'], $mysql_link);
  $grader = mysql_real_escape_string($_POST['grader'], $mysql_link);
  $ribbon = mysql_real_escape_string($_POST['ribbon'], $mysql_link);
  $acad = mysql_real_escape_string($_POST['acad'], $mysql_link);
  $orgacad = mysql_real_escape_string($_POST['orgacad'], $mysql_link);
  $query = "UPDATE EH_Training Set Name='$name', Abbr='$abbr', TC_ID='$tcid', Available='$avail', Description='$desc', Min_Training_ID='$minTrain', Min_Rank_ID='$minRank', Min_Pos_ID='$minPos', Min_Time='$minTime', MinPoints='$minPoints', MaxPoints='$maxPoints', NotesFile='$notesFile', Rewards='$Rewards', Grader='$grader', Ribbon='$ribbon'";
  if($acad!=$orgacad) {
    $query1 = "SELECT MAX(SortOrder) FROM EH_Training WHERE TAc_ID=$acad";
    $result = mysql_query($query1, $mysql_link);
    $rows = @mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $newso = $values[0]+1;
      }
    else {
      $newso = 1;
      }
    $query .=", TAc_ID=$acad, SortOrder=$newso";
    echo "<p>NOTE: Make sure you reedit this option to have it now be selecting the correct category.</p>";
    }
  $query.=" WHERE Training_ID=$id";
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
  $minTrain = mysql_real_escape_string($_POST['minTrain'], $mysql_link);
  $minRank = mysql_real_escape_string($_POST['minRank'], $mysql_link);
  $minPos = mysql_real_escape_string($_POST['minPos'], $mysql_link);
  $minTime = mysql_real_escape_string($_POST['minTime'], $mysql_link);
  $minPoints = mysql_real_escape_string($_POST['minPoints'], $mysql_link);
  $notesFile = mysql_real_escape_string($_POST['notesFile'], $mysql_link);
  $Rewards = mysql_real_escape_string($_POST['Rewards'], $mysql_link);
  $maxPoints = mysql_real_escape_string($_POST['maxPoints'], $mysql_link);
  $grader = mysql_real_escape_string($_POST['grader'], $mysql_link);
  $ribbon = mysql_real_escape_string($_POST['ribbon'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Training WHERE TAc_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Training
                (Name, Abbr, TC_ID, Available, Description, Min_Training_ID,
                 Min_Rank_ID, Min_Pos_ID, Min_Time, MinPoints, MaxPoints,
                 NotesFile, Rewards, Grader, Ribbon, SortOrder, TAc_ID)
                VALUES(
                 '$name', '$abbr', '$tcid', '$avail', '$desc', '$minTrain',
                 '$minRank', '$minPos', '$minTime', '$minPoints', '$maxPoints',
                 '$notesFile', '$Rewards', '$grader', '$ribbon', '$so', '$group')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Training WHERE Training_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Training Set SortOrder=SortOrder-1 WHERE TAc_ID=$group AND SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Training WHERE Training_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Course deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Training Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Academy to modify their Training</label>
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
      <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add New Course</span>
    </a>
  </p>
  <div id="add-form" title="Add New Course">
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
        <td><label for="tcid">Category: </label></td>
        <td>
            <select name="tcid" id="tcid" >
<?php
  $query1 = "SELECT TC_ID, Name FROM EH_Training_Categories WHERE TCa_ID=$values[16] Order By SortOrder";
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
        <td><label for="avail">Available: </label></td>
        <td><input type="Checkbox" name="availalbe" id="available" value="1"></td>
      </tr>
      <tr>
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="minTrain">PreRequisite Course: </label></td>
        <td>
  <select name="minTrain" id="minTrain" >
    <option value="0">No Prerequsite Course</option>
<?php
  $query1 = "SELECT Training_ID, Name FROM EH_Training Order By TAc_ID, SortOrder";
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
        <td><label for="minRank">PreRequisite Rank: </label></td>
        <td>
  <select name="minRank" id="minRank" >
    <option value="0">No Prerequsite Rank</option>
<?php
    $query1 = "SELECT Rank_ID, Name FROM EH_Ranks Order By Group_ID, SortOrder";
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
        <td><label for="minPos">Minimum Position: </label></td>
        <td>
  <select name="minPos" id="minPos">
    <option value="0">No Position Required</option>
<?php
  $query1 = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
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
        <td><label for="minTime">Number of months required in the Group: </label></td>
        <td><input type="text" name="minTime" id="minTime"></td>
      </tr>
      <tr>
        <td><label for="minPoints">Min Points on the test to pass:</label></td>
        <td><input type="text" name="minPoints" id="minPoints"></td>
      </tr>
      <tr>
        <td><label for="maxPoints">Max Points on the test:</label></td>
        <td><input type="text" name="maxPoints" id="maxPoints"></td>
      </tr>
      <tr>
        <td><label for="notesfile">Notes Filename:</label></td>
        <td><input type="text" name="notesFile" id="notesFile"></td>
      </tr>
      <tr>
        <td><label for="Rewards">Rewards(for non-autoawarding awards):</label></td>
        <td><input type="text" name="Rewards" id="Rewards"></td>
      </tr>
      <tr>
        <td><label for="grader">Grader: </label></td>
        <td>
  <select name="grader" id="grader" >
    <option value="0">None</option>
<?php
  $query1 = "SELECT Member_ID, Name FROM EH_Members Order By Name";
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
        <td><label for="ribbon">Ribbon Image:</label></td>
        <td>
            <input type="text" name="ribbon" id="ribbon">
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Course">
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