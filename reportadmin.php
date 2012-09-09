<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "reportadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "reportadmin");
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
  $query = "SELECT Report_ID, Name FROM EH_Reports WHERE Group_ID=$datatable AND Member_ID=".$_SESSION['EHID']." Order By ReportDate";
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
  $query = "SELECT Report_ID, Name, Report, ReportNum FROM EH_Reports WHERE Report_ID=$id";
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
          <td><label for="resetdate">Reset Report Date: </label></td>
          <td>
              <input type="checkbox" name="resetdate" id="resetdate" value="1">
          </td>
        </tr>
        <tr>
          <td><label for="ereport">Report: </label></td>
          <td><textarea name="ereport" id="ereport" style="width:510px; height:120px"><?=stripslashes($values[2])?></textarea></td>
        </tr>
        <tr>
          <td><label for="reportnum">Report Number: </label></td>
          <td><input type="text" name="reportnum" id="reportnum" value="<?=stripslashes($values[3])?>"></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $report = mysql_real_escape_string($_POST['ereport'], $mysql_link);
  $reportnum = mysql_real_escape_string($_POST['reportnum'], $mysql_link);
  $avail = mysql_real_escape_string($_POST['resetdate'], $mysql_link);
  if($avail)
    $date=time();
  else
    $date=0;
  $query = "UPDATE EH_Reports Set Name='$name', Report='$report', ReportNum='$reportnum'";
  if($date)
    $query.=", ReportDate=$date";
   $query.=" WHERE Report_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $pos = mysql_real_escape_string($_POST['pos'], $mysql_link);
  $report = mysql_real_escape_string($_POST['areport'], $mysql_link);
  $reportnum = mysql_real_escape_string($_POST['reportnum'], $mysql_link);
  $date = time();
  $mem = $_SESSION['EHID'];
  $query = "SELECT Unit_ID FROM EH_Members_Units WHERE Group_ID=$group AND Member_ID=$mem";
  $result = mysql_query($query, $mysql_link);
  $rows = @mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $unit = $values[0];
    }
  $poster = mysql_real_escape_string(RankAbbrName($mem, $group, 1), $mysql_link);
  if(strip_tags($report)==$report)
    $report=nl2br($report);
  $query = "INSERT INTO EH_Reports (Name, Poster, Report, ReportNum, Group_ID, Unit_ID, Position_ID, Member_ID, ReportDate) VALUES('$name', '$poster', '$report', '$reportnum', '$group', '$unit', '$pos', '$mem', '$date')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $group = mysql_real_escape_string($_GET['group'], $mysql_link);
  $query = "DELETE FROM EH_Reports WHERE Report_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Report deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Report Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Group to modify Reports</label>
    <?php $ga = implode (" OR Group_ID=", $groupsaccess); ?>
  <select name="selGroup" id="selGroup" onChange="getDataTable(); getPositionsByMember();">
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
    <a onClick="$('#add-form').dialog('open')" href="#">
        <span style="color:#6699CC;">Add New Report</span>
    </a>
  </p>
  <div id="add-form" title="Add New Report">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="pos">Position: </label></td>
        <td>
            <select name="pos" id="pos" >
            </select>
        </td>
      </tr>
      <tr>
        <td><label for="areport">Report: </label></td>
        <td><textarea name="areport" id="areport" style="width:510px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="reportnum">Report Number: </label></td>
        <td><input type="text" name="reportnum" id="reportnum"></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Report">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  function getPositionsByMember(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#pos").empty();
	getAdminJSONdata("getPositionsByMember", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#pos").append('<option value="'+item.Position_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }

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
          $("#ereport").htmlbox({
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
          $("#areport").htmlbox({
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