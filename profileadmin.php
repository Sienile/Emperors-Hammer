<?
session_start();
include_once("config.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("functions.php");
if($_GET['datatable']==1) {
    ?>
  <table>
    <tr>
      <td width="80%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
<?php
  $query = "SELECT EH_Members_ChatProfile.EMCP_ID, EH_Members_ChatProfile.Chat_Handle, EH_ChatSystems.Name FROM EH_Members_ChatProfile, EH_ChatSystems WHERE EH_ChatSystems.Chat_ID=EH_Members_ChatProfile.Chat_ID AND EH_Members_ChatProfile.Member_ID='".$_SESSION['EHID']."' Order By EH_Members_ChatProfile.EMCP_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="80%"><?=stripslashes($values[2])?>: <?=$values[1]?></td>
        <td width="10%"><a id="edit" onclick="getChatEditForm(<?=$values[0]?>)"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a id="del" onclick="delchat(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php 
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['datatable']==2) {
    ?>
  <table>
    <tr>
      <td width="90%">Name</td>
      <td width="10%">Delete</td>
    </tr>
<?php
  $query = "SELECT EH_Members_Platforms.EMP_ID, EH_Platforms.Name FROM EH_Members_Platforms, EH_Platforms WHERE EH_Platforms.Platform_ID=EH_Members_Platforms.Platform_ID AND EH_Members_Platforms.Member_ID='".$_SESSION['EHID']."' Order By EH_Platforms.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="90%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a id="del" onclick="delplt(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php 
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['datatable']==3) {
    ?>
  <table>
    <tr>
      <td width="80%">Name</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
<?php
  $query = "SELECT EH_Members_Skills.EMS_ID, EH_Members_Skills.SkillLevel, EH_Skills.Name FROM EH_Members_Skills, EH_Skills WHERE EH_Skills.Skill_ID=EH_Members_Skills.Skill_ID AND EH_Members_Skills.Member_ID='".$_SESSION['EHID']."' Order By EH_Skills.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="80%"><?=stripslashes($values[2])?>: <?=stripslashes($values[1])?></td>
        <td width="10%"><a id="edit" onclick="getSkillEditForm(<?=$values[0]?>)"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a id="del" onclick="delskill(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
      </tr>
    <?php 
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['edit'] && $_GET['area']==1) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT EH_Members_ChatProfile.EMCP_ID, EH_Members_ChatProfile.Chat_Handle, EH_ChatSystems.Name FROM EH_Members_ChatProfile, EH_ChatSystems WHERE EH_Members_ChatProfile.EMCP_ID=$id AND EH_Members_ChatProfile.Chat_ID=EH_ChatSystems.Chat_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editChatDiv" class="ajaxForm" title="Edit Chat">
    <form id="editChatForm" method="POST" onSubmit="postChatEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Chat Handle (For <?=stripslashes($values[2])?>): </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
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
elseif($_GET['edit1']==1) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $query = "UPDATE EH_Members_ChatProfile Set Chat_Handle='$name' WHERE EMCP_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Chat updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit'] && $_GET['area']==3) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT EH_Members_Skills.EMS_ID, EH_Members_Skills.SkillLevel, EH_Skills.Name FROM EH_Members_Skills, EH_Skills WHERE EH_Members_Skills.EMS_ID=$id AND EH_Members_Skills.Skill_ID=EH_Skills.Skill_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editSkillDiv" class="ajaxForm" title="Edit Skill">
    <form id="editSkillForm" method="POST" onSubmit="postSkillEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="name">Skill Level (For <?=stripslashes($values[2])?>): </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[1])?>"></td>
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
elseif($_GET['edit1']==3) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $query = "UPDATE EH_Members_Skills Set SkillLevel='$name' WHERE EMS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Skill updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']==1) {
 ?>
<div id="addChatDiv" class="ajaxForm" title="Add Chat">
    <form id="addChatForm" method="POST" onSubmit="postChatAdd(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="chat">Chat System: </label></td>
          <td><select name="chat" id="chat">
<?
  $query = "SELECT Chat_ID, Name FROM EH_ChatSystems Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
    }
?>
          </select></td>
        </tr>
        <tr>
          <td><label for="name">Chat Handle: </label></td>
          <td><input type="text" name="name" id="name"></td>
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
<?
  }
elseif($_GET['add1']==1) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $chat = mysql_real_escape_string($_POST['chat'], $mysql_link);
  $query = "INSERT INTO EH_Members_ChatProfile (Member_ID, Chat_ID, Chat_Handle) VALUES('".$_SESSION['EHID']."', '$chat', '$name')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Chat inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']==2) {
 ?>
<div id="addPltDiv" class="ajaxForm" title="Add Platform">
    <form id="addPltForm" method="POST" onSubmit="postPltAdd(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="chat">Platform: </label></td>
          <td><select name="chat" id="chat">
<?
  $query = "SELECT Platform_ID, Name FROM EH_Platforms where Platform_ID NOT IN (select Platform_ID FROM EH_Members_Platforms where Member_ID = ".$_SESSION['EHID'].") Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
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
<?
  }
elseif($_GET['add1']==2) {
  $chat = mysql_real_escape_string($_POST['chat'], $mysql_link);
  $query = "INSERT INTO EH_Members_Platforms (Member_ID, Platform_ID) VALUES('".$_SESSION['EHID']."', '$chat')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Platform inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']==3) {
 ?>
<div id="addSkillDiv" class="ajaxForm" title="Add Chat">
    <form id="addSkillForm" method="POST" onSubmit="postSkillAdd(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="chat">Skill: </label></td>
          <td><select name="chat" id="chat">
<?
  $query = "SELECT Skill_ID, Name FROM EH_Skills where Skill_ID NOT IN (select Skill_id FROM EH_Members_Skills where Member_ID = ".$_SESSION['EHID'].") Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
    }
?>
          </select></td>
        </tr>
        <tr>
          <td><label for="name">Skill Level: </label></td>
          <td><input type="text" name="name" id="name"></td>
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
<?
  }
elseif($_GET['add1']==3) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $chat = mysql_real_escape_string($_POST['chat'], $mysql_link);
  $query = "INSERT INTO EH_Members_Skills (Member_ID, Skill_ID, SkillLevel) VALUES('".$_SESSION['EHID']."', '$chat', '$name')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Skill inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']==1) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Members_ChatProfile WHERE EMCP_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Chat Profile deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']==2) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Members_Platforms WHERE EMP_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Platform deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']==3) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Members_Skills WHERE EMS_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Skill deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['profile']) {
  $member = $_SESSION['EHID'];
  $query = "SELECT Member_ID, QUote, URL FROM EH_Members WHERE Member_ID=$member";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
?>
  <form id="editprofile" method="POST" onSubmit="postProfileEdit(); return false;">
    <table>
      <tr>
        <td><label for="quote">Quote: </label></td>
        <td><input type="text" name="quote" id="name" value="<?=stripslashes($values[1])?>"></td>
      </tr>
      <tr>
        <td><label for="url">Homepage: </label></td>
        <td><input type="text" name="url" id="url" value="<?=stripslashes($values[2])?>"></td>
      </tr>
<?
if(isinGroup(6, $member)) {
//If HF
?>
      <tr>
        <td><label for="stid">Stormtrooper Type: </label></td>
        <td><select name="stid" id="stid">
  <?
  $query1 = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$member AND SA_ID=3";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $sttype=$values1[0];
    }
  $certsheld="";
  $query1 = "SELECT Training_ID FROM EH_Training_Complete WHERE Member_ID=$member";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j = 1; $j <= $rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $certsheld.="$values1[0]";
    if($j<$rows1)
      $certsheld.=" OR Cert_ID=";
    }
  $query1 = "SELECT SSType_ID, Name FROM EH_SSType WHERE Cert_ID=$certsheld Order By Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j = 1; $j <= $rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    echo "  <option value=\"$values1[0]\"";
    if($sttype==$values1[0])
      echo " selected=\"selected\"";
    echo ">".stripslashes($values1[1])."</option>\n";
    } ?>
        </select></td>
      </tr>
<?
}
?>
      <tr>
        <td><label for="ship">Ship (soon): </label></td>
        <td>Coming Soon</td>
      </tr>
      <tr>
        <td><label for="prigroup">PrimaryGroup: </label></td>
        <td><select name="prigroup" id="prigroup">
      <?php
  $query = "SELECT EH_Members_Groups.Group_ID, EH_Groups.Name, EH_Members_Groups.isPrimary FROM EH_Groups, EH_Members_Groups WHERE EH_Members_Groups.Group_ID=EH_Groups.Group_ID AND EH_Members_Groups.Member_ID=".$_SESSION['EHID']." Order By EH_Groups.Group_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\"";
    if($values[2])
      echo " selected=\"selected\"";
    echo ">".stripslashes($values[1])."</option>\n";
    }
  ?>
  </select></td>
      </tr>
      <tr>
        <td><label for="pw">Change Password: </label></td>
        <td><input type="text" name="pw" id="pw"></td>
      </tr>
      <tr>
        <td><label for="cpw">Confirm Change Password: </label></td>
        <td><input type="text" name="cpw" id="cpw"></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <input type="submit" id="Submit" name="Submit" value="Submit" />
          <input type="reset" id="Reset" name="Reset" value="Reset" />
        </td>
      </tr>
    </table>
  </form>
<?
  }// End Edit Profile
}
elseif($_GET['profile1']) {
/*
ship
*/
  $id = mysql_real_escape_string($_SESSION['EHID'], $mysql_link);
  $prigroup = mysql_real_escape_string($_POST['prigroup'], $mysql_link);
  $quote = mysql_real_escape_string($_POST['quote'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $stid = mysql_real_escape_string($_POST['stid'], $mysql_link);

  $query1 = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$id AND SA_ID=3";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $query = "UPDATE EH_Members_Special_Areas Set Value='$stid' WHERE Member_ID=$id AND SA_ID=3";
    $result = mysql_query($query, $mysql_link);
    }
  else {
    $query = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) Values('$id', '3', '$stid')";
    $result = mysql_query($query, $mysql_link);
    }
  $pw = $_POST['pw'];
  $cpw = $_POST['cpw'];
  $pwc="";
  if($pw!="")  {
    if($pw==$cpw) {
      $pwc = hash("sha512", $pw);
      }
    else {
      $error= "Passwords do not match";
      }
    }
  $query = "UPDATE EH_Members Set Quote='$quote', URL='$url'";
  if($pwc)
    $query.=", UserPassword='$pwc'";
  $query .=" WHERE Member_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "UPDATE EH_Members_Groups Set isPrimary=0 WHERE Member_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $query = "UPDATE EH_Members_Groups Set isPrimary=1 WHERE Member_ID=$id AND Group_ID=$prigroup";
  $result = mysql_query($query, $mysql_link);
  if($error)
    echo "<p>$error</p>\n";
  if($result)
    echo "<p>Profile data updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Profile Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>

  <div id="chatdata"></div>
  <p><a onclick="getChatAddForm(<?=$values[0]?>)"><span style="color:#6699CC;">Add New Chat</span></a></p>

  <div id="pltdata"></div>
  <p><a onclick="getPltAddForm(<?=$values[0]?>)"><span style="color:#6699CC;">Add New Platform</span></a></p>

  <div id="skilldata"></div>
  <p><a onclick="getSkillAddForm(<?=$values[0]?>)"><span style="color:#6699CC;">Add New Skill</span></a></p>
  <div id="profileedit"></div>

  <script type="text/javascript">
  function getChatAddForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?add=1",{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("addChatDiv");
        $("#editArea").show();
    },'html');
  }

  function postChatAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add1=1',
        success: showSuccess
    }
    $("#addChatForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getPltAddForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?add=2",{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("addPltDiv");
        $("#editArea").show();
    },'html');
  }

  function postPltAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add1=2',
        success: showSuccess
    }
    $("#addPltForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getSkillAddForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?add=3",{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("addSkillDiv");
        $("#editArea").show();
    },'html');
  }

  function postSkillAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add1=3',
        success: showSuccess
    }
    $("#addSkillForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getChatEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=1&edit="+id,{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("editChatDiv");
        $("#editArea").show();
    },'html');
  }

  function destroyForm(){
      $("#editArea").hide('fast',function(){
        $("#editArea").remove();
        getDataTable();
      });
  }
  
  function postChatEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=1',
        success: showSuccess
    }
    $("#editChatForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getSkillEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=3&edit="+id,{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("editSkillDiv");
        $("#editArea").show();
    },'html');
  }

  function postSkillEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=3',
        success: showSuccess
    }
    $("#editSkillForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function delchat(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=1&del="+id,{},showSuccess,'html');
  }

  function delplt(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=2&del="+id,{},showSuccess,'html');
  }

  function delskill(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=3&del="+id,{},showSuccess,'html');
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function getDataTable() {
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=1",{},function(data){
        $("#chatdata").html(data);
    },'html');
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=2",{},function(data){
        $("#pltdata").html(data);
    },'html');
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=3",{},function(data){
        $("#skilldata").html(data);
    },'html');
  }

  $(document).ready(getDataTable);

  function getprofileEdit() {
    $.get("<?=$_SERVER['PHP_SELF']?>?profile=1",{},function(data){
        $("#profileedit").html(data);
    },'html');
  }

  function showSuccessP(data,status){
    $("#message").html(data);
    getprofileEdit();
  }

  function postProfileEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?profile1=true',
        success: showSuccessP
    }
    $("#editprofile").ajaxSubmit(options);
    return false;
  }

  $(document).ready(getprofileEdit);
</script>
 <?php
  include_once("footer.php");
  }
?>