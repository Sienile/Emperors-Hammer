<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "shipadmin");
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
  $query = "SELECT Ship_ID, Name FROM EH_Ships Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="80%"><?=stripslashes($values[1])?></td>
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
  $query = "SELECT Ship_ID, Name, Abbr, Manufacturer, SS_ID, ST_ID, Crew, Fighters, Length, Cargo, Description, Power, RPGName, RPGWeapons, RPGSynopsis FROM EH_Ships WHERE Ship_ID=$id";
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
          <td><label for="mfr">Manufacturer: </label></td>
          <td><input type="text" name="mfr" id="mfr" value="<?=stripslashes($values[3])?>"></td>
        </tr>
        <tr>
          <td><label for="ssid">Ship Supplement: </label></td>
          <td>
    <select name="ssid" id="ssid" >
    <?php
    $query1 = "SELECT SS_ID, Name FROM EH_Ships_Supplement Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[4])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="stid">Ship Type: </label></td>
          <td>
    <select name="stid" id="stid" >
    <?php
    $query1 = "SELECT PT_ID, Name FROM EH_Ships_Types Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[5])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="crew">Crew: </label></td>
          <td><textarea name="crew" id="crew" style="width:400px; height:120px"><?=stripslashes($values[6])?></textarea></td>
        </tr>
        <tr>
          <td><label for="fighters">Fighters: </label></td>
          <td><textarea name="fighters" id="fighters" style="width:400px; height:120px"><?=stripslashes($values[7])?></textarea></td>
        </tr>
        <tr>
          <td><label for="len">Length: </label></td>
          <td><textarea name="len" id="len" style="width:400px; height:120px"><?=stripslashes($values[8])?></textarea></td>
        </tr>
        <tr>
          <td><label for="cargo">Cargo: </label></td>
          <td><textarea name="cargo" id="cargo" style="width:400px; height:120px"><?=stripslashes($values[9])?></textarea></td>
        </tr>
        <tr>
          <td><label for="desc">Description: </label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[10])?></textarea></td>
        </tr>
        <tr>
          <td><label for="pwr">Power: </label></td>
          <td><textarea name="pwr" id="pwr" style="width:400px; height:120px"><?=stripslashes($values[11])?></textarea></td>
        </tr>
        <tr>
          <td><label for="rpgname">RPG Power: </label></td>
          <td><textarea name="rpgname" id="rpgname" style="width:400px; height:120px"><?=stripslashes($values[12])?></textarea></td>
        </tr>
        <tr>
          <td><label for="rpgwpn">RPG Weapons: </label></td>
          <td><textarea name="rpgwpn" id="rpgwpn" style="width:400px; height:120px"><?=stripslashes($values[13])?></textarea></td>
        </tr>
        <tr>
          <td><label for="rpgsyn">RPG Synopsis: </label></td>
          <td><textarea name="rpgsyn" id="rpgsyn" style="width:400px; height:120px"><?=stripslashes($values[14])?></textarea></td>
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
  $mfr = mysql_real_escape_string($_POST['mfr'], $mysql_link);
  $ssid = mysql_real_escape_string($_POST['ssid'], $mysql_link);
  $stid = mysql_real_escape_string($_POST['stid'], $mysql_link);
  $crew = mysql_real_escape_string($_POST['crew'], $mysql_link);
  $fighters = mysql_real_escape_string($_POST['fighters'], $mysql_link);
  $len = mysql_real_escape_string($_POST['len'], $mysql_link);
  $cargo = mysql_real_escape_string($_POST['cargo'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $pwr = mysql_real_escape_string($_POST['pwr'], $mysql_link);
  $rpgname = mysql_real_escape_string($_POST['rpgname'], $mysql_link);
  $rpgwpn = mysql_real_escape_string($_POST['rpgwpn'], $mysql_link);
  $rpgsyn = mysql_real_escape_string($_POST['rpgsyn'], $mysql_link);
  $query = "UPDATE EH_Ships Set Name='$name', Abbr='$abbr', Manufacturer='$mfr', SS_ID='$ssid', ST_ID='$stid', Crew='$crew', Fighters='$fighters', Length='$len', Cargo='$cargo', Description='$desc', Power='$pwr', RPGName='$rpgname', RPGWeapons='$rpgwpn', RPGSynopsis='$rpgsyn' WHERE Ship_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $mfr = mysql_real_escape_string($_POST['mfr'], $mysql_link);
  $ssid = mysql_real_escape_string($_POST['ssid'], $mysql_link);
  $stid = mysql_real_escape_string($_POST['stid'], $mysql_link);
  $crew = mysql_real_escape_string($_POST['crew'], $mysql_link);
  $fighters = mysql_real_escape_string($_POST['fighters'], $mysql_link);
  $len = mysql_real_escape_string($_POST['len'], $mysql_link);
  $cargo = mysql_real_escape_string($_POST['cargo'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $pwr = mysql_real_escape_string($_POST['pwr'], $mysql_link);
  $rpgname = mysql_real_escape_string($_POST['rpgname'], $mysql_link);
  $rpgwpn = mysql_real_escape_string($_POST['rpgwpn'], $mysql_link);
  $rpgsyn = mysql_real_escape_string($_POST['rpgsyn'], $mysql_link);
  $query = "INSERT INTO EH_Ships (Name, Abbr, Manufacturer, SS_ID, ST_ID, Crew, Fighters, Length, Cargo, Description, Power, RPGName, RPGWeapons, RPGSynopsis) VALUES('$name', '$abbr', '$mfr', '$ssid', '$stid', '$crew', '$fighters', '$len', '$cargo', '$desc', '$pwr', '$rpgname', '$rpgwpn', '$rpgsyn')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Ships WHERE Ship_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Ship deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Ship Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Ship</span>
      </a>
  </p>
  <div id="add-form" title="Add New Ship">
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
        <td><label for="mfr">Manufacturer: </label></td>
        <td><input type="text" name="mfr" id="mfr"></td>
      </tr>
      <tr>
        <td><label for="ssid">Ship Supplement: </label></td>
        <td>
    <select name="ssid" id="ssid" >
    <?php
    $query1 = "SELECT SS_ID, Name FROM EH_Ships_Supplement Order By SortOrder";
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
        <td><label for="stid">Ship Type: </label></td>
        <td>
    <select name="stid" id="stid" >
    <?php
    $query1 = "SELECT PT_ID, Name FROM EH_Ships_Types Order By SortOrder";
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
        <td><label for="crew">Crew: </label></td>
        <td><textarea name="crew" id="crew" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="fighters">Fighters: </label></td>
        <td><textarea name="fighters" id="fighters" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="len">Length: </label></td>
        <td><textarea name="len" id="len" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="cargo">Cargo: </label></td>
        <td><textarea name="cargo" id="cargo" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="desc">Description: </label></td>
        <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="pwr">Power: </label></td>
        <td><textarea name="pwr" id="pwr" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="rpgname">RPG Power: </label></td>
        <td><textarea name="rpgname" id="rpgname" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="rpgwpn">RPG Weapons: </label></td>
        <td><textarea name="rpgwpn" id="rpgwpn" style="width:400px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="rpgsyn">RPG Synopsis: </label></td>
        <td><textarea name="rpgsyn" id="rpgsyn" style="width:400px; height:120px"></textarea></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Link">
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