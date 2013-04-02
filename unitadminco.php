<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "counitadmin");
$groupsaccess = AccessGroups($_SESSION['EHID'], "counitadmin");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
if(is_numeric($_GET['datatable'])) {
  $datatable = mysql_real_escape_string($_GET['datatable'], $mysql_link);
  ?>
  <table>
    <tr>
      <td width="90%">Name</td>
      <td width="10%">Edit</td>
    </tr>
    <?php
  $query = "SELECT Unit_ID, Name FROM EH_Units WHERE Unit_ID=$datatable Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    ?>
      <tr>
        <td width="90%"><?=stripslashes($values[1])?></td>
        <td width="10%"><a href="#" id="edit" onclick="getEditForm(<?=$values[0]?>);"><span style="color:#6699CC;">Edit</span></a></td>
      </tr>
    <?php
    } // End for loop
    ?>
  </table>
<?php
  } // end if $_GET['datatable']
elseif($_GET['edit']) {
  $id = mysql_real_escape_string($_GET['edit'], $mysql_link);
  $query = "SELECT Unit_ID, SiteURL, MessageBoard, Banner, Motto, Nickname FROM EH_Units WHERE Unit_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
    <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="url">URL: </label></td>
          <td><input type="text" name="url" id="url" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="mb">Message Board: </label></td>
          <td><input type="text" name="mb" id="mb" value="<?=stripslashes($values[2])?>"></td>
        </tr>
        <tr>
          <td><label for="banner">Banner: </label></td>
          <td><input type="text" name="banner" id="banner" value="<?=stripslashes($values[3])?>"></td>
        </tr>
        <tr>
          <td><label for="motto">Motto: </label></td>
          <td><input type="text" name="motto" id="motto" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="nickname">Nickname: </label></td>
          <td><input type="text" name="nickname" id="nickname" value="<?=stripslashes($values[5])?>"></td>
        </tr>
      </table>
    </form>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $url = mysql_real_escape_string($_POST['url'], $mysql_link);
  $mb = mysql_real_escape_string($_POST['mb'], $mysql_link);
  $banner = mysql_real_escape_string($_POST['banner'], $mysql_link);
  $motto = mysql_real_escape_string($_POST['motto'], $mysql_link);
  $nickname = mysql_real_escape_string($_POST['nickname'], $mysql_link);
  $query = "UPDATE EH_Units Set SiteURL='$url', MessageBoard='$mb', Banner='$banner', Motto='$motto', Nickname='$nickname' WHERE Unit_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Unit updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Unit Roster Administration</p>
  <p><a href="/menu.php">Return to the administration menu</a></p>
  <form name="selgroupform">
    <label for="selGroup">Select the Unit to modify their Units</label>
    <?php $ga = implode (" OR EH_Units.Group_ID=", $groupsaccess); ?>
  <select name="selGroup" id="selGroup" onChange="getDataTable()">
    <option value="0">No Group</option>
  <?php
  $query = "SELECT EH_Units.Unit_ID, EH_Units.Name FROM EH_Units, EH_Members_Units WHERE EH_Units.Unit_ID=EH_Members_Units.Unit_ID AND EH_Members_Units.Member_ID=".$_SESSION['EHID'];
  if($ga) {
    $query .=" AND (EH_Units.Group_ID=$ga)";
    }
  $query.=" Order By EH_Units.Group_ID";
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
  <div id="datatable"></div>

  <div id="editArea" title="Edit Unit">
    <form id="editForm" method="POST">
    </form>
  </div>

  <script type="text/javascript">

  function getEditForm(id) {
    $.get("<?=$_SERVER['PHP_SELF']?>?edit="+id,{},function(data){
      $("#editArea").html(data);
    },'html').complete(function() {
      $("#editArea").dialog("open");
      });
  }

  function showSuccess(data,status){
    $("#message").html(data);
    getDataTable();
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