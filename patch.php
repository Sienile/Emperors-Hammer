<?
session_start();
include_once("config.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("functions.php");
if($_GET['datatable']=="bug") {
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT PB_ID, Member_ID, DateReported, Description FROM EH_Patches_Bugs WHERE Patch_ID=$id And Status=1 Order By DateReported";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<table>\n";
  echo "  <tr>\n";
  echo "    <td>Description:</td>\n";
  echo "    <td>".stripslashes($values[3])."</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>Posted By:</td>\n";
  echo "    <td><a href=\"/profile/$values[1]\">".RankAbbrName($values[1], PriGroup($values[1]), 1)."</a> On ".date("F j, Y", $values[2])."</td>\n";
  echo "  </tr>\n";
  if($_SESSION['EHID'] && (has_access($_SESSION['EHID'], "patchadmin") || $values[1]==$_SESSION['EHID'])) {
    echo "  <tr>\n";
    echo "    <td><a id=\"edit\" onclick=\"getBugEditForm($values[0])\">Edit</a></td>\n";
    echo "    <td><a id=\"del\" onclick=\"delBug($values[0])\">Delete</a></td>\n";
    echo "  </tr>\n";
    }
  echo "</table>\n";
  }
}
elseif($_GET['datatable']=="rev") {
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT PR_ID, Member_ID, DatePosted, Review FROM EH_Patches_Reviews WHERE Patch_ID=$id Order By DatePosted";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<table>\n";
  echo "  <tr>\n";
  echo "    <td>Review:</td>\n";
  echo "    <td>".stripslashes($values[3])."</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>Posted By:</td>\n";
  echo "    <td><a href=\"/profile/$values[1]\">".RankAbbrName($values[1], PriGroup($values[1]), 1)."</a> On ".date("F j, Y", $values[2])."</td>\n";
  echo "  </tr>\n";
  if($_SESSION['EHID'] && (has_access($_SESSION['EHID'], "patchadmin") || $values[1]==$_SESSION['EHID'])) {
    echo "  <tr>\n";
    echo "    <td><a id=\"edit\" onclick=\"getRevEditForm($values[0])\">Edit</a></td>\n";
    echo "    <td><a id=\"del\" onclick=\"delRev($values[0])\">Delete</a></td>\n";
    echo "  </tr>\n";
    }
  echo "</table>\n";
  }
}
elseif($_GET['edit'] && $_GET['area']=="rev") {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Review From EH_Patches_Reviews Where PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editRevDiv" class="ajaxForm" title="Edit Review">
    <form id="editRevForm" method="POST" onSubmit="postRevEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="rev">Review:</label></td>
          <td><textarea name="rev" id="rev" style="width:400px; height:120px"><?=stripslashes($values[0])?></textarea></td>
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
elseif($_GET['edit1']=="rev") {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $rev = mysql_real_escape_string($_POST['rev'], $mysql_link);
  $query = "UPDATE EH_Patches_Reviews Set Review='$rev' WHERE PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Review updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }

elseif($_GET['edit'] && $_GET['area']=="bug") {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Description From EH_Patches_Bugs Where PB_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editBugDiv" class="ajaxForm" title="Edit Bug Report">
    <form id="editBugForm" method="POST" onSubmit="postBugEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="desc">Description:</label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"><?=stripslashes($values[0])?></textarea></td>
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
elseif($_GET['edit1']=="bug") {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $query = "UPDATE EH_Patches_Bugs Set Description='$desc' WHERE PB_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Bug Report updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']=="rev") {
 ?>
<div id="addRevDiv" class="ajaxForm" title="Add Review">
    <form id="addRevForm" method="POST" onSubmit="postRevAdd(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="rev">Review:</label></td>
          <td><textarea name="rev" id="rev" style="width:400px; height:120px"></textarea></td>
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
elseif($_GET['add1']=="rev") {
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
  $rev = mysql_real_escape_string($_POST['rev'], $mysql_link);
  $query = "INSERT INTO EH_Patches_Reviews (Patch_ID, Member_ID, DatePosted, Review) VALUES('$id', '".$_SESSION['EHID']."', '".time()."', '$rev')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Review inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }

elseif($_GET['add']=="bug") {
 ?>
<div id="addBugDiv" class="ajaxForm" title="Add Bug Report">
    <form id="addBugForm" method="POST" onSubmit="postBugAdd(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="desc">Description:</label></td>
          <td><textarea name="desc" id="desc" style="width:400px; height:120px"></textarea></td>
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
elseif($_GET['add1']=="bug") {
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['desc'], $mysql_link);
  $query = "INSERT INTO EH_Patches_Bugs (Patch_ID, Member_ID, DateReported, Description, Status) VALUES('$id', '".$_SESSION['EHID']."', '".time()."', '$desc', '1')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Bug Report inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']=="rev") {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Patches_Reviews WHERE PR_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Review deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']=="bug") {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Patches_Bugs WHERE PB_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Bug Report deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
include_once("nav.php");
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Patch_ID, Name, Filename, PC_ID, Platform_ID, Creator, ReleasedDate, UpdatedDate, Image, Description From EH_Patches WHERE Patch_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
echo "<h3 align=\"center\">Emperor's Hammer Patch</h3>\n";
echo "<div id=\"message\" style=\"color: green\" ></div>\n";
if($rows) {
  $values = mysql_fetch_row($result);
  $query1 = "SELECT Name, Abbr From EH_Platforms WHERE Platform_ID=$values[4]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pltname=stripslashes($values1[0]);
    $pltabbr=stripslashes($values1[1]);
    }
  $query1 = "SELECT Name From EH_Patches_Categories WHERE PC_ID=$values[3]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $catname=stripslashes($values1[0]);
    }
echo "<p><a href=\"/patcharchive.php?cat=$values[3]&amp;plt=$values[4]\">Return to the Patch Category Selection Screen</a></p>\n";
?>




<div id="patch">

  <ul>

    <li><a href="#tabsMain">Information</a></li>
    <li><a href="#tabsRev">Reviews</a></li>

    <li><a href="#tabsBugs">Bugs</a></li>
  </ul>

  <div id="tabsMain">
<h4><?=$catname?> patches for <?=$pltname?>: <?=stripslashes($values[1])?></h4>
<table>
  <tr>
    <td><?=$values[1]?><br>
Description: <?=stripslashes($values[9])?><br>
Creator(s): <?=stripslashes($values[5])?><br>
Released: <?=date("F j, Y", $values[6])?><br>
Updated: <?=date("F j, Y", $values[7])?><br>
</td>
<td style="vertical-align:top"><a href="http://sco.emperorshammer.org/patches/<?=stripslashes($values[2])?>">Download</a>
<?
if($values[10])
  echo "<img src=\"/images/patches/stripslashes($values[8])\" />";
?>
</td>
</tr>
</table>
  </div>
  <div id="tabsRev">
<h4>Reviews</h4>
  <div id="Revdata"></div>
<?
if($_SESSION['EHID']) {
?>
  <p><a onclick="getRevAddForm(<?=$values[0]?>)"><span style="color:#6699CC;">Add New Review</span></a></p>
<?
}
?>
  </div>
  <div id="tabsBugs">
<h4>Bug Reports</h4>
  <div id="Bugsdata"></div>
<?
if($_SESSION['EHID']) {
?>
  <p><a onclick="getBugAddForm(<?=$values[0]?>)"><span style="color:#6699CC;">Add New Bug Report</span></a></p>
<?
}
?>
  </div>
</div>

<script type="text/javascript">


  function getRevAddForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?add=rev",{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("addRevDiv");
        $("#editArea").show();
    },'html');
  }

  function postRevAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add1=rev&id=<?=$values[0]?>',
        success: showSuccess
    }
    $("#addRevForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getBugAddForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?add=bug",{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("addBugDiv");
        $("#editArea").show();
    },'html');
  }

  function postBugAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add1=bug&id=<?=$values[0]?>',
        success: showSuccess
    }
    $("#addBugForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getRevEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=rev&edit="+id,{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("editRevDiv");
        $("#editArea").show();
    },'html');
  }

  function postRevEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=rev',
        success: showSuccess
    }
    $("#editRevForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function getBugEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=bug&edit="+id,{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("editBugDiv");
        $("#editArea").show();
    },'html');
  }

  function postBugEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=bug',
        success: showSuccess
    }
    $("#editBugForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function destroyForm(){
      $("#editArea").hide('fast',function(){
        $("#editArea").remove();
        getDataTable();
      });
  }
  
  function delRev(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=rev&del="+id,{},showSuccess,'html');
  }

  function delBug(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?area=bug&del="+id,{},showSuccess,'html');
  }
  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
  }

  function getDataTable() {
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=rev&id=<?=$values[0]?>",{},function(data){
        $("#Revdata").html(data);
    },'html');
    $.get("<?=$_SERVER['PHP_SELF']?>?datatable=bug&id=<?=$values[0]?>",{},function(data){
        $("#Bugsdata").html(data);
    },'html');
  }

  $(document).ready(getDataTable);
	$(function() {

		$("#patch").tabs();
	});

</script>

<?
  }
else {
  echo "<p>The page you were looking for does not exist</p>";
  }
include_once("footer.php");
}
?>