<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "articleadmin");
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
  $query = "SELECT Article_ID, Publication FROM EH_Articles Order By DateReceived";
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
  $query = "SELECT Article_ID, Publication, Member_ID, Name, Image, Link, DateReceived FROM EH_Articles WHERE Article_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    ?>
  <form id="editForm" method="POST">
    <input type="hidden" name="id" value="<?=$id?>" />
      <table>
        <tr>
          <td><label for="pub">Publication: </label></td>
          <td><input type="text" name="pub" id="pub" value="<?=stripslashes($values[1])?>"></td>
        </tr>
        <tr>
          <td><label for="member">Member: </label></td>
          <td>
    <select name="member" id="member">
      <option value="0"<? if($values[2]==0) echo " selected=\"selected\""; ?>>Not in the database</option>
    <?php
    $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Email!='' Order By Name";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($i=0; $i<$rows1; $i++) {
      $values1 = mysql_fetch_row($result1);
      echo "  <option value=\"$values1[0]\"";
      if($values1[0]==$values[2])
        echo " selected=\"selected\"";
      echo ">".stripslashes($values1[1])."</option>\n";
     }
     ?>
    </select></td>
        </tr>
        <tr>
          <td><label for="name">Name: </label></td>
          <td><input type="text" name="name" id="name" value="<?=stripslashes($values[3])?>"></td>
        </tr>
        <tr>
          <td><label for="img">Image: </label></td>
          <td><input type="text" name="img" id="img" value="<?=stripslashes($values[4])?>"></td>
        </tr>
        <tr>
          <td><label for="lnk">Link: </label></td>
          <td><input type="text" name="lnk" id="lnk" value="<?=stripslashes($values[5])?>"></td>
        </tr>
        <tr>
          <td><label for="date">Date: </label></td>
            <?
            $date = date("m/d/Y", $values[6]);
            ?>
          <td>
              <div id="date_edit"></div>
              <input type="hidden" name="date" id="date" value="<?=$date?>" />
          </td>
        </tr>
      </table>
    </form>
<script type="text/javascript">
$(function() {
    $("#date_edit").datepicker(
        {dateFormat: "mm/dd/yy" ,
         defaultDate: new Date("<?=date("M d, Y",$values[6])?>"),
         altField: "#date"
        }
    );
});
</script>
<?php
    }
  }
elseif($_GET['edit1']) {
  $id = mysql_real_escape_string($_POST['id'], $mysql_link);
  $pub = mysql_real_escape_string($_POST['pub'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $member = mysql_real_escape_string($_POST['member'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $lnk = mysql_real_escape_string($_POST['lnk'], $mysql_link);
  $date = mysql_real_escape_string($_POST['date'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $query = "UPDATE EH_Articles Set Member_ID='$member', Name='$name', Publication='$pub', Image='$img', Link='$lnk', DateReceived='$date' WHERE Article_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." updated successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['add']) {
  $pub = mysql_real_escape_string($_POST['pub'], $mysql_link);
  $name = mysql_real_escape_string($_POST['name'], $mysql_link);
  $member = mysql_real_escape_string($_POST['member'], $mysql_link);
  $img = mysql_real_escape_string($_POST['img'], $mysql_link);
  $lnk = mysql_real_escape_string($_POST['lnk'], $mysql_link);
  $date = mysql_real_escape_string($_POST['datea'], $mysql_link);
  $date = explode("/", $date);
  $date = mktime(0, 0, 0, $date[0], $date[1], $date[2]);
  $query = "INSERT INTO EH_Articles (Publication, Member_ID, Name, Image, Link, DateReceived) VALUES('$pub', '$member', '$name', '$img', '$lnk', '$date')";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>".stripslashes($name)." inserted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
elseif($_GET['del']) {
  $id = mysql_real_escape_string($_GET['del'], $mysql_link);
  $query = "DELETE FROM EH_Articles WHERE Article_ID=$id";
  $result = mysql_query($query, $mysql_link);
  if($result)
    echo "<p>Article deleted successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  include_once("nav.php");
  ?>
  <p>Emperor's Hammer Article Administration</p>
  <p><a href="menu.php">Return to the administration menu</a></p>
  <div id="message" style="color: green" ></div>
  <div id="response"></div>
  <p>
      <a onClick="$('#add-form').dialog('open');" href="#">
          <span style="color:#6699CC;">Add New Article</span>
      </a>
  </p>
  <div id="add-form" title="Add New Article">
  <form id="addForm" method="POST">
    <table>
      <tr>
        <td><label for="pub">Publication: </label></td>
        <td><input type="text" name="pub" id="pub"></td>
      </tr>
      <tr>
        <td><label for="member">Member: </label></td>
        <td>
    <select name="member" id="member">
      <option value="0">Not in the database</option>
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
        <td><label for="name">Name: </label></td>
        <td><input type="text" name="name" id="name"></td>
      </tr>
      <tr>
        <td><label for="img">Image: </label></td>
        <td><input type="text" name="img" id="img"></td>
      </tr>
      <tr>
        <td><label for="lnk">Link: </label></td>
        <td><input type="text" name="lnk" id="lik"></td>
      </tr>
      <tr>
        <td><label for="datea">Date: </label></td>
        <td>
            <div id="date_add"></div>
            <input type="hidden" name="datea" id="datea">
        </td>
      </tr>
    </table>
  </form>
  </div>

  <div id="editArea" title="Edit Article">
    <form id="editForm" method="POST">
    </form>
  </div>

  <div id="datatable"></div>

  <script type="text/javascript">

  $(function() {
    $("#date_add").datepicker({altField: '#datea'});
  });

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

