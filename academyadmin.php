<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "acadadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if($_GET['datatable']) {
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
  $query = "SELECT TAc_ID, Name, SortOrder FROM EH_Training_Academies Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
      <tr>
        <td width="60%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a id="edit" onclick="getEditForm(<?=$values[0]?>)"><span style="color:#6699CC;">Edit</span></a></td>
        <td width="10%"><a id="del" onclick="del(<?=$values[0]?>)"><span style="color:#6699CC;">Delete</span></a></td>
    <?php if($i>0) { ?>
          <td width="10%"><a id="up" onclick="moveUp(<?=$values[0]?>)"><span style="color:#6699CC;">Move Up</span></a></td>
    <?php }else{ ?>
          <td width="10%">Move Up</td>
    <?php }if($values[2]<$rows){ ?>
          <td width="10%"><a id="down" onclick="moveDown(<?=$values[0]?>)"><span style="color:#6699CC;">Move Down</span></a></td>
    <?php } else { ?>
          <td width="10%">Move Down</td>
    <?php }  ?>
      </tr>
    <?php 
    } // End For loop ?>
  </table>
<?php 
  }
elseif($_GET['up']) {
  $id = mysql_real_escape_string($_GET['up'], $mysql_link);
  $query = "select TAc_ID, SortOrder From EH_Training_Academies Where TAc_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso-1;
  $initialID = $values[0];
  $query = "select TAc_ID From EH_Training_Academies Where SortOrder=$newso";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Academies Set SortOrder=$newso Where TAc_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Academies Set SortOrder=$curso Where TAc_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Academy moved up successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['down']) {
  $id = mysql_real_escape_string($_GET['down'], $mysql_link);
  $query = "select TAc_ID, SortOrder From EH_Training_Academies Where TAc_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $curso = $values[1];
  $newso = $curso+1;
  $initialID = $values[0];
  $query = "select TAc_ID From EH_Training_Academies Where SortOrder=$newso";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $swapID=$values[0];
  $query = "Update EH_Training_Academies Set SortOrder=$newso Where TAc_ID=$initialID";
  $result = mysql_query($query, $mysql_link);
  $query = "Update EH_Training_Academies Set SortOrder=$curso Where TAc_ID=$swapID";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Academy moved down successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT TAc_ID, Name, Abbr, Description, EntryBrackets, ExitBrackets, Seperator, DefaultNoCourse, Group_ID, Leader, Deputy, Trainers FROM EH_Training_Academies WHERE TAc_ID=$id";
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
          <td><label for="edesc">Description: </label></td>
          <td><textarea name="edesc" id="edesc" style="width:510px; height:120px"><?=stripslashes($values[3])?></textarea></td>
        </tr>
        <tr>
          <td><label for="entry">Entry Bracekts (For ID Lines): </label></td>
          <td><input type="text" name="entry" id="entry" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="exit">Exit Brackets (For ID Lines): </label></td>
          <td><input type="text" name="exit" id="exit" value="<?=stripslashes($values[5])?>"></td>
        </tr>
        <tr>
          <td><label for="sep">Course ID Line Seperator: </label></td>
          <td><input type="text" name="sep" id="sep" value="<?=stripslashes($values[6])?>"></td>
        </tr>
        <tr>
          <td><label for="nocourse">Default for when no courses are taken display: </label></td>
          <td><input type="text" name="nocourse" id="nocourse" value="<?=stripslashes($values[7])?>"></td>
        </tr>
        <tr>
          <td><label for="Group">Group: </label></td>
          <td>
    <select name="Group" id="Group" >
    <?php
    $query1 = "SELECT Group_ID, Name FROM EH_Groups Order By Group_ID";
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
          <td><label for="Leader">Academy Leader Position: </label></td>
          <td>
    <select name="Leader" id="Leader" >
        <option value="0"
<?php
    if($values[9]==0)
      echo " selected=\"selected\"";
    echo ">No Position</option>";
    $query1 = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[9])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
      }
      ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="Deputy">Academy Deputy Position: </label></td>
          <td>
    <select name="Deputy" id="Deputy" >
      <option value="0"
<?php
    if($values[10]==0)
      echo " selected=\"selected\"";
    echo ">No Position</option>";
    $query1 = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
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
          <td><label for="Trainer">Academy Trainer Position: </label></td>
          <td>
    <select name="Trainer" id="Trainer" >
      <option value="0"
<?php
    if($values[11]==0)
      echo " selected=\"selected\"";
    echo ">No Position</option>";
    $query1 = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
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
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['edesc'], $mysql_link);
  $entry = mysql_real_escape_string($_POST['entry'], $mysql_link);
  $exit = mysql_real_escape_string($_POST['exit'], $mysql_link);
  $sep = mysql_real_escape_string($_POST['sep'], $mysql_link);
  $nocourse = mysql_real_escape_string($_POST['nocourse'], $mysql_link);
  $group = mysql_real_escape_string($_POST['Group'], $mysql_link);
  $leader = mysql_real_escape_string($_POST['Leader'], $mysql_link);
  $deputy = mysql_real_escape_string($_POST['Deputy'], $mysql_link);
  $trainer = mysql_real_escape_string($_POST['Trainer'], $mysql_link);
  $query = "UPDATE EH_Training_Academies Set Name='$name', Abbr='$abbr', Description='$desc', EntryBrackets='$entry', ExitBrackets='$exit', Seperator='$sep', DefaultNoCourse='$nocourse', Group_ID='$group', Leader='$leader', Deputy='$deputy', Trainers='$trainer' WHERE TAc_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $abbr = mysql_real_escape_string($_POST['abbr'], $mysql_link);
  $desc = mysql_real_escape_string($_POST['adesc'], $mysql_link);
  $entry = mysql_real_escape_string($_POST['entry'], $mysql_link);
  $exit = mysql_real_escape_string($_POST['exit'], $mysql_link);
  $sep = mysql_real_escape_string($_POST['sep'], $mysql_link);
  $nocourse = mysql_real_escape_string($_POST['nocourse'], $mysql_link);
  $group = mysql_real_escape_string($_POST['Group'], $mysql_link);
  $leader = mysql_real_escape_string($_POST['Leader'], $mysql_link);
  $deputy = mysql_real_escape_string($_POST['Deputy'], $mysql_link);
  $trainer = mysql_real_escape_string($_POST['Trainer'], $mysql_link);
  $query = "SELECT MAX(SortOrder) FROM EH_Training_Academies";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0]+1;
    }
  else {
    $so = 1;
    }
  $query = "INSERT INTO EH_Training_Academies (Name, Abbr, Description, SortOrder, EntryBrackets, ExitBrackets, Seperator, DefaultNoCourse, Group_ID, Leader, Deputy, Trainers) VALUES('$name', '$abbr', '$desc', '$so', '$entry', '$exit', '$sep', '$nocourse', '$group', '$leader', '$deputy', '$trainer')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "SELECT SortOrder FROM EH_Training_Academies WHERE TAc_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $so = $values[0];
    }
  $query = "UPDATE EH_Training_Academies Set SortOrder=SortOrder-1 WHERE SortOrder>=$so";
  $result = mysql_query($query, $mysql_link);
  $query = "DELETE FROM EH_Training_Academies WHERE TAc_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Academy deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Academy Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Academy</span>
      </a>
  </p>
  <div id="add-form" title="Add New News Story">
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
        <td><label for="adesc">Description: </label></td>
        <td><textarea name="adesc" id="adesc" style="width:510px; height:120px"></textarea></td>
      </tr>
      <tr>
        <td><label for="entry">Entry Bracekts (For ID Lines): </label></td>
        <td><input type="text" name="entry" id="entry"></td>
      </tr>
      <tr>
        <td><label for="exit">Exit Brackets (For ID Lines): </label></td>
        <td><input type="text" name="exit" id="exit"></td>
      </tr>
      <tr>
        <td><label for="sep">Course ID Line Seperator: </label></td>
        <td><input type="text" name="sep" id="sep"></td>
      </tr>
      <tr>
        <td><label for="nocourse">Default for when no courses are taken display: </label></td>
        <td><input type="text" name="nocourse" id="nocourse"></td>
      </tr>
      <tr>
        <td><label for="Group">Group: </label></td>
        <td>
  <select name="Group" id="Group" >
      <?php
  $query = "SELECT Group_ID, Name FROM EH_Groups Order By Group_ID";
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
        <td><label for="Leader">Academy Leader Position: </label></td>
        <td>
  <select name="Leader" id="Leader" >
    <option value="0">No Position</option>
  <?php
  $query = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
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
        <td><label for="Deputy">Academy Deputy Position: </label></td>
        <td>
  <select name="Deputy" id="Deputy" >
    <option value="0">No Position</option>
<?php
  $query = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
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
        <td><label for="Trainer">Academy Trainer Position: </label></td>
        <td>
  <select name="Trainer" id="Trainer" >
    <option value="0">No Position</option>
<?php
  $query = "SELECT Position_ID, Name FROM EH_Positions Order By Group_ID, SortOrder DESC";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
    }
    ?>
  </select></td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Academy">
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

  function moveUp(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?up="+id,{},showSuccess,'html');
  }

  function moveDown(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?down="+id,{},showSuccess,'html');
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
        open: function () {
          $("#edesc").htmlbox({
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
          $("#adesc").htmlbox({
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