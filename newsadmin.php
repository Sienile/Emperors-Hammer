<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "newsadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "newsadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="80%">Topic</td>
      <td width="10%">Edit</td>
      <td width="10%">Delete</td>
    </tr>
    <?php
  $query = "SELECT News_ID, Topic FROM EH_News WHERE Group_ID=$datatable Order By DatePosted DESC";
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
  $query = "SELECT News_ID, Topic, Body FROM EH_News WHERE News_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="topic">Topic: </label></td>
          <td><input type="text" name="topic" id="topic" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="ebody">Body: </label></td>
          <td><textarea name="ebody" id="ebody" style="width:510px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
        <tr>
          <td><label for="resetdate">Reset date to top of news: </label></td>
          <td>
            <input type="checkbox" name="resetdate" id="resetdate" value="1">
          </td>
        </tr>
      </table>
  </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $topic = mysql_real_escape_string($_POST['topic'], $mysql_link);
  $body = mysql_real_escape_string($_POST['ebody'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['resetdate'], $mysql_link);
  if($avail)
    $date=time();
  $query = "UPDATE EH_News Set Topic='$topic', Body='$body'";
  if($avail)
    $query .=", DatePosted='$date'";
  $query.=" WHERE News_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($topic)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $topic = mysql_real_escape_string($_POST['topic'], $mysql_link);
  $body = mysql_real_escape_string($_POST['nbody'], $mysql_link);
  $date=time();
  $member = $_SESSION['EHID'];
  $membername = RankAbbrName($member, $group, 0);
  if(strip_tags($body)==$body)
    $body=nl2br($body);
  $query = "INSERT INTO EH_News
                (Group_ID, Topic, Poster, Poster_ID, DatePosted, Body)
                VALUES('$group', '$topic', '$membername', '$member', '$date', '$body')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($topic)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_News WHERE News_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>News Story deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer News Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify their News</label>
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
  <p>
    <a onClick="$('#add-form').dialog('open');" href="#">
        <span style="color:#6699CC;">Add News</span>
    </a>
  </p>
  <div id="message" style="color: green;"></div>
  <div id="response"></div>
  <div id="add-form" title="Add New News Story">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="topic">Topic: </label></td>
        <td><input type="text" name="topic" id="topic"></td>
      </tr>
      <tr>
        <td><label for="nbody">Body: </label></td>
        <td><textarea name="nbody" id="nbody" style="width:510px; height:200px"></textarea></td>
      </tr>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit News Post">
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
        open: function () {
          $("#ebody").htmlbox({
             skin:"blue",about:false,
             toolbars:[[
		// Cut, Copy, Paste
		"separator","cut","copy","paste",
		// Undo, Redo
		"separator","undo","redo",
		// Bold, Italic, Underline, Strikethrough, Sup, Sub
		"separator","bold","italic","underline","strike","sup","sub",
		// Left, Right, Center, Justify
		"separator","justify","left","center","right",
		// Ordered List, Unordered List, Indent, Outdent
		"separator","ol","ul","indent","outdent",
		// Hyperlink, Remove Hyperlink, Image
		"separator","link","unlink","image"		
		],
		[
          // Show code
		"separator","code",
        // Formats, Font size, Font family, Font color, Font, Background
        "separator","formats","fontsize","fontfamily",
		"separator","fontcolor","highlight",
		],
		[
		//Strip tags
		"separator","removeformat","striptags","hr","paragraph",
		// Styles, Source code syntax buttons
		"separator","quote","styles","syntax"
		]],
              icons:"silk",
              idir:"http://www.emperorshammer.org/js/images/"
             });
          },
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
$(function(){
          $("#nbody").htmlbox({
             skin:"blue",about:false,
             toolbars:[[
		// Cut, Copy, Paste
		"separator","cut","copy","paste",
		// Undo, Redo
		"separator","undo","redo",
		// Bold, Italic, Underline, Strikethrough, Sup, Sub
		"separator","bold","italic","underline","strike","sup","sub",
		// Left, Right, Center, Justify
		"separator","justify","left","center","right",
		// Ordered List, Unordered List, Indent, Outdent
		"separator","ol","ul","indent","outdent",
		// Hyperlink, Remove Hyperlink, Image
		"separator","link","unlink","image"		
		],
		[
          // Show code
		"separator","code",
        // Formats, Font size, Font family, Font color, Font, Background
        "separator","formats","fontsize","fontfamily",
		"separator","fontcolor","highlight",
		],
		[
		//Strip tags
		"separator","removeformat","striptags","hr","paragraph",
		// Styles, Source code syntax buttons
		"separator","quote","styles","syntax"
		]],
              icons:"silk",
              idir:"http://www.emperorshammer.org/js/images/"
             });
});
  </script>
  <?php
  include_once("footer.php");
  }
?>