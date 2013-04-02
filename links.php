<?
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if($_GET['add']) {
  $id=mysql_real_escape_string($_GET['add'], $mysql_link);
?>
<div id="addCommentDiv" class="ajaxForm" title="Add Comment">
    <form id="addCommentForm" method="POST" onSubmit="postCommentAdd(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="comment">Comment: </label></td>
          <td><textarea name="comment" id="comment" style="width:400px; height:120px"></textarea></td>
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
elseif($_GET['add1']) {
  $chat = mysql_real_escape_string($_POST['comment'], $mysql_link);
  $linkid = mysql_real_escape_string($_POST['id'], $mysql_link);
  $now = time();
  $query = "INSERT INTO EH_Links_Comments (Link_ID, Member_ID, DatePosted, Comment) VALUES('$linkid', '".$_SESSION['EHID']."', '$now', '$chat')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Comment inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
}
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Links_Comments WHERE LCo_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Comment deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
}
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT LCo_ID, Comment From EH_Links_Comments WHERE LCo_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
<div id="editDiv" class="ajaxForm" title="Edit Skill">
    <form id="editForm" method="POST" onSubmit="postEdit(); return false;">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="comment">Comment: </label></td>
          <td><textarea name="comment" id="comment" style="width:400px; height:120px"><?=stripslashes($values[1])?></textarea></td>
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
  $comment = mysql_real_escape_string($_POST['comment'], $mysql_link);
  $now = time();
  $query = "UPDATE EH_Links_Comments Set Comment='$comment', DatePosted='$now' WHERE LCo_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Comment updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
include_once("nav.php");
?>
  <script type="text/javascript">
    $(function() {
      $("#accordion").accordion();
      });

  function getCommentAddForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?add="+id,{},function(data){
        if ($("#editArea").length < 1){
            makeDiv("editArea","editArea","body","display:none;");
        }
        $("#editArea").html(data);
        dressAjaxForm("addCommentDiv");
        $("#editArea").show();
    },'html');
  }

  function postCommentAdd() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?add1=true',
        success: showSuccess
    }
    $("#addCommentForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function destroyForm(){
      $("#editArea").hide('fast',function(){
        $("#editArea").remove();
      });
  }

  function showSuccess(data,status){
    $("#message").html(data);
  }

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

  function postEdit() {
    var options ={
        url: '<?=$_SERVER['PHP_SELF']?>?edit1=true',
        success: showSuccess
    }
    $("#editForm").ajaxSubmit(options);
    destroyForm();
    return false;
  }

  function del(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?del="+id,{},showSuccess,'html');
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

  </script>
<div id="message"></div>

<div id="accordion">
<?
$query = "SELECT LC_ID, Name, Description From EH_Links_Categories Order By SortOrder";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
?>
  <h3><a href="#"><?=stripslashes($values[1])?></a></h3>
  <div><p><?=stripslashes($values[2])?></p>
<?
$query1 = "SELECT Link_ID, Name, URL, Description From EH_Links WHERE LC_ID=$values[0] Order By Name";
$result1 = mysql_query($query1, $mysql_link);
$rows1 = mysql_num_rows($result1);
for($j=0; $j<$rows1; $j++) {
  $values1 = mysql_fetch_row($result1);
?>
    <p><a href="http://<?=stripslashes($values1[2])?>"><span style="color:#6699CC;"><?=stripslashes($values1[1])?></span></a><br>
    <?=stripslashes($values1[3])?></p>
<?
$query2 = "SELECT LCo_ID, Member_ID, DatePosted, Comment From EH_Links_Comments WHERE Link_ID=$values1[0] Order By DatePosted";
$result2 = mysql_query($query2, $mysql_link);
$rows2 = mysql_num_rows($result2);
?>
    <p>Comments (<?=stripslashes($rows2)?>):<br>
<?

for($k=0; $k<$rows2; $k++) {
  $values2 = mysql_fetch_row($result2);
  echo $values2[3] ." Posted By: ";
  $query3 = "SELECT Name From EH_Members WHERE Member_ID=$values2[1]";
  $result3 = mysql_query($query3, $mysql_link);
  $rows3 = mysql_num_rows($result3);
  if($rows3) {
    $values3 = mysql_fetch_row($result3);
    echo "<a href=\"/profile/$values2[1]\"><span style=\"color:#6699CC;\">".stripslashes($values3[0])."</span></a>";
    }
  echo " on: ".date("F j, Y", $values2[2]);
  if($_SESSION['EHID'] && ($_SESSION['EHID']==$values2[1] || has_access($_SESSION['EHID'], "linkadmin"))) {
    echo " - <a id=\"edit\" onclick=\"getEditForm($values2[0])\"><span style=\"color:#6699CC;\">Edit</span></a>";
    echo " - <a id=\"del\" onclick=\"del($values2[0])\"><span style=\"color:#6699CC;\">Delete</span></a>";
    }
  echo "<br>\n";
  }
?>
    </p>
<?
if($_SESSION['EHID']) {
?>
    <p><a onclick="getCommentAddForm(<?=$values1[0]?>)"><span style="color:#6699CC;">Add New Comment</span></a></a></p>
<?
  }
}
?>
  </div>
<?
  }
?>
</div>
<?
include_once("footer.php");
}
?>