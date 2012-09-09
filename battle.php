<?
session_start();
include_once("config.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("functions.php");
if($_GET['datatable']=="bug") {
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Bug_ID, Poster_ID, Date_Added, MissionsAffected, Description FROM EH_Battles_Bugs WHERE Battle_ID=$id And Status=1 Order By Date_Added";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<table>\n";
  echo "  <tr>\n";
  echo "    <td>Description:</td>\n";
  echo "    <td>".stripslashes($values[4])."</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>Missions Affected:</td>\n";
  echo "    <td>".stripslashes($values[3])."</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>Posted By:</td>\n";
  echo "    <td><a href=\"profile.php?pin=$values[1]\">".RankAbbrName($values[1], PriGroup($values[1]), 1)."</a> On ".date("F j, Y", $values[2])."</td>\n";
  echo "  </tr>\n";
  if($_SESSION['EHID'] && (has_access($_SESSION['EHID'], "battlesadmin") || $values[1]==$_SESSION['EHID'])) {
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
$query = "SELECT Review_ID, Poster_ID, Date_Added, Description, Rating FROM EH_Battles_Reviews WHERE Battle_ID=$id And Status=1 Order By Date_Added";
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
  echo "    <td>Rating:</td>\n";
  echo "    <td>".stripslashes($values[4])."</td>\n";
  echo "  </tr>\n";
  echo "  <tr>\n";
  echo "    <td>Posted By:</td>\n";
  echo "    <td><a href=\"profile.php?pin=$values[1]\">".RankAbbrName($values[1], PriGroup($values[1]), 1)."</a> On ".date("F j, Y", $values[2])."</td>\n";
  echo "  </tr>\n";
  if($_SESSION['EHID'] && (has_access($_SESSION['EHID'], "battlesadmin") || $values[1]==$_SESSION['EHID'])) {
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
  $query = "SELECT Description, Rating From EH_Battles_Reviews Where Review_ID=$id";
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
          <td><label for="score">Rating:</label></td>
          <td><select name="score" id="score">
<?
for($c=0; $c<=5; $c++) {
  echo "  <option value=\"$c\"";
  if($values[1]==$c)
    echo " selected=\"selected\"";
  echo ">$c</option>";
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
elseif($_GET['edit1']=="rev") {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $rev = mysql_real_escape_string($_POST['rev'], $mysql_link);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $query = "UPDATE EH_Battles_Reviews Set Description='$rev', Rating='$score' WHERE Review_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Review updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }

elseif($_GET['edit'] && $_GET['area']=="bug") {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Description, MissionsAffected From EH_Battles_Bugs Where Bug_ID=$id";
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
          <td><label for="missions">Missions Affected:</label></td>
          <td><textarea name="missions" id="missions" style="width:400px; height:120px"><?=stripslashes($values[1])?></textarea></td>
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
  $missions = mysql_real_escape_string($_POST['missions'], $mysql_link);
  $query = "UPDATE EH_Battles_Bugs Set Description='$desc', MissionsAffected='$missions' WHERE Bug_ID=$id";
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
          <td><label for="score">Rating:</label></td>
          <td><select name="score" id="score">
<?
for($c=0; $c<=5; $c++) {
  echo "  <option value=\"$c\">$c</option>";
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
elseif($_GET['add1']=="rev") {
  $id = mysql_real_escape_string($_GET['id'], $mysql_link);
  $rev = mysql_real_escape_string($_POST['rev'], $mysql_link);
  $score = mysql_real_escape_string($_POST['score'], $mysql_link);
  $query = "INSERT INTO EH_Battles_Reviews (Battle_ID, Poster_ID, Date_Added, Description, Rating, Status) VALUES('$id', '".$_SESSION['EHID']."', '".time()."', '$rev', '$score', '1')";
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
          <td><label for="missions">Missions Affected:</label></td>
          <td><textarea name="missions" id="missions" style="width:400px; height:120px"></textarea></td>
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
  $missions = mysql_real_escape_string($_POST['missions'], $mysql_link);
  $query = "INSERT INTO EH_Battles_Bugs (Battle_ID, Poster_ID, Date_Added, Description, MissionsAffected, Status) VALUES('$id', '".$_SESSION['EHID']."', '".time()."', '$desc', '$missions', '1')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Bug Report inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']=="rev") {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Battles_Reviews WHERE Review_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Review deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del'] && $_GET['area']=="bug") {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Battles_Bugs WHERE Bug_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Bug Report deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
include_once("nav.php");
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "SELECT Battle_ID, Platform_ID, BattleNumber, BC_ID, Name, Description, Released, Last_Updated, Updater_ID, Reward_Name, Reward_Image, Filename, Wav_Pack, Highscore, HS_Holder, Creator_1, Creator_2, Creator_3, Creator_4, Status, NumMissions FROM EH_Battles WHERE Battle_ID=$id";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
echo "<h3 align=\"center\">Emperor's Hammer Battle</h3>\n";
echo "<div id=\"message\" style=\"color: green\" ></div>\n";
if($rows) {
  $values = mysql_fetch_row($result);
echo "<p><a href=\"battlescat.php?cat=$values[3]&amp;plt=$values[1]\">Return to the Battle Category Selection Screen</a></p>\n";
?>




<div id="battles">

  <ul>

    <li><a href="#tabsMain">Information</a></li>
    <li><a href="#tabsRev">Reviews</a></li>

    <li><a href="#tabsBugs">Bugs</a></li>
    <li><a href="#tabsHS">High Scores</a></li>
    <li><a href="#tabsStats">Statistics</a></li>
  </ul>

  <div id="tabsMain">
<h4><b><?=BattleName($values[0], 1)?></b></h4>
<table>
  <tr>
    <td><b>Battle Name<b>: <?=stripslashes($values[4])?><br>
<b>Number of Missions</b>: <?=stripslashes($values[20])?><br>
<b>Battle Creator</b>: <a href="profile.php?pin=<?=$values[15]?>"><?=RankAbbrName($values[15], PriGroup($values[15]), 1)?></a><br>
<?
if($values[16])
  echo "<b>Battle Creator</b>: <a href=\"profile.php?pin=$values[16]\">".RankAbbrName($values[16], PriGroup($values[16]), 1)."</a><br>";
if($values[17])
  echo "<b>Battle Creator</b>: <a href=\"profile.php?pin=$values[17]\">".RankAbbrName($values[17], PriGroup($values[17]), 1)."</a><br>";
if($values[18])
  echo "<b>Battle Creator</b>: <a href=\"profile.php?pin=$values[18]\">".RankAbbrName($values[18], PriGroup($values[18]), 1)."</a><br>";
?>
<b>Battle Released</b>: <?=date("F j, Y", $values[6])?><br>
<b>Patches</b>:<br>
<?
$query2 = "SELECT EH_Patches.Patch_ID, EH_Patches.Name From EH_Battles_Patches, EH_Patches WHERE EH_Battles_Patches.Patch_ID=EH_Patches.Patch_ID AND EH_Battles_Patches.Battle_ID=$values[0]";
$result2 = mysql_query($query2, $mysql_link);
$rows2 = mysql_num_rows($result2);
if($rows2==0)
  echo "No Patches Required.";
for($j=0;$j<$rows2; $j++) {
  $values2 = mysql_fetch_row($result2);
  echo "<a href=\"patch.php?id=$values2[0]\">".stripslashes($values2[1])."</a><br>\n";
  }
?>
<b>Wave Pack</b>: <? if($values[12]) echo "<a href=\"http://www.emperorshammer.org/tc/battles/wavpacks/$values[12]\">Download</a>"; else echo "None"; ?><br>
</td>
<td style="vertical-align:top">
<a href="http://www.emperorshammer.org/tc/battles/<?=$values[11]?>"><b>Download</b></a><br/>
<b>Medal Name</b>: <?=stripslashes($values[9])?><br/>
<b>Average Rating</b>: 
<?
$query2 = "SELECT AVG(Rating) FROM EH_Battles_Reviews WHERE Battle_ID=$id And Status=1";
$result2 = mysql_query($query2, $mysql_link);
$rows2 = mysql_num_rows($result2);
if($rows) {
  $values2 = mysql_fetch_row($result2);
  echo $values2[0];
  }
if($values[10])
  echo "<img src=\"$values[10]\" /><br />";
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
  <div id="tabsHS">
<h4>High Scores</h4>
<p>
<?
$query1 = "SELECT Mission_Num, Name, Highscore, HS_Holder From EH_Battles_Missions WHERE Battle_ID=$values[0] Order By Mission_Num";
$result1 = mysql_query($query1, $mysql_link);
$rows1 = mysql_num_rows($result1);
if($rows1>1) {
?>
Battle: <?=$values[13]?> by: <a href="profile.php?pin=<?=$values[14]?>"><?=RankAbbrName($values[14], PriGroup($values[14]), 1)?></a><br>
<?
}
for($j=0;$j<$rows1; $j++) {
  $values1 = mysql_fetch_row($result1);
  echo "Mission $values1[0]";
  if($values1[1])
    echo " ($values1[1])";
  echo ": $values1[2] by: <a href=\"profile.php?pin=$values1[3]\">".RankAbbrName($values1[3], PriGroup($values1[3]), 1)."</a><br>";
  }
?></p>
  </div>
  <div id="tabsStats">
<h4>Statistics</h4>
  <?
$query1 = "SELECT Date_Completed, Member_ID From EH_Battles_Complete Where Status=1 AND Battle_ID=$values[0]";
$result1 = mysql_query($query1, $mysql_link);
$rows1 = mysql_num_rows($result1);
if($rows1==0)
  echo "No completions yet.";
for($j=0;$j<$rows1; $j++) {
  $values1 = mysql_fetch_row($result1);
  echo "<a href=\"profile.php?pin=$values1[1]\">".RankAbbrName($values1[1], PriGroup($values1[1]), 1)."</a>";
  if($values1[0])
    echo " Completed On: ".date("F j, Y", $values1[0]);
  echo "<br>";
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

		$("#battles").tabs();
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