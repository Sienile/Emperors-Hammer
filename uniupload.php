<?
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
?>
<p>Emperor's Hammer Uniform Uploader</p>
<p><a href="/menu.php">Return to the administration menu</a></p>
<p>
<?
if($_POST['group']) {
  $basedir=$base_path."images/uniforms/uploaded/";
  $memberid = mysql_real_escape_string($_SESSION['EHID'], $mysql_link);
  $group = mysql_real_escape_string($_POST['group'], $mysql_link);
  if($_FILES["uni"]["type"]=="image/jpeg" || $_FILES["uni"]["type"]=="image/pjpeg" || $_FILES["uni"]["type"]=="image/png") {
    $ext="jpg";
    if($_FILES["uni"]["type"]=="image/png")
      $ext="png";
    $err=0;
    }
  if($_FILES["uni"]["type"]!="image/jpeg" && $_FILES["uni"]["type"]!="image/pjpeg" && $_FILES["uni"]["type"]!="image/png") {
    $err=1;
    }
  if($_FILES["uni"]["size"]>2000000) {
    $err=2;
    }
  $now=time();
  if(!$err) {
    $query1 = "SELECT EMU_ID, Filename From EH_Members_Uniforms WHERE Member_ID=$memberid AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      unlink($basedir.$values1[1]);
      $newname=$basedir."$memberid-$group.$ext";
      move_uploaded_file($_FILES["uni"]["tmp_name"], $newname);
      $query2 = "UPDATE EH_Members_Uniforms Set Filename='$memberid-$group.$ext', UniformDate=$now WHERE EMU_ID=$values1[0]";
      $result2 = mysql_query($query2, $mysql_link);
      }
    else {
      $newname=$basedir."$memberid-$group.$ext";
      move_uploaded_file($_FILES["uni"]["tmp_name"], $newname);
      $query2 = "INSERT INTO EH_Members_Uniforms (Member_ID, Group_ID, Filename, UniformDate) Values('$memberid', '$group', '$memberid-$group.$ext', '$now')";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  if(!$err && $result2)
    echo "<p>Uniform Uploaded!</p>\n";
  elseif(!$err && !$result2)
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  if($err==1)
    echo "<p>Wrong Filetype! Only jpg or png file types</p>\n";
  if($err==2)
    echo "<p>Wrong Filesize! File was too large, File needs to be less than 2000000 bytes.</p>\n";
  }
$query1 = "SELECT EH_Groups.Group_ID, EH_Groups.Name FROM EH_Members_Groups, EH_Groups WHERE EH_Groups.Group_ID=EH_Members_Groups.Group_ID AND EH_Groups.UniType=1 AND EH_Groups.Active!=0 AND EH_Members_Groups.Member_ID=".$_SESSION['EHID']." Order By EH_Groups.Group_ID";
$result1 = mysql_query($query1, $mysql_link);
$rows1 = mysql_num_rows($result1);
if($rows1) {
?>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
  <table>
<?

?>
    <tr>
      <td><label for="group">Group: </label></td>
      <td>
        <select name="group" id="group">
<?
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "          <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
    }
?>
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="uni">Uniform (required to be <2MB, png or jpg): </label></td>
      <td><input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
      <input name="uni" id="uni" type="file" />
      </td>
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
}
else {
  echo "<p>Currently, you're not in a group that currently supports uploaded uniforms.</p>\n";
  }
include_once("footer.php");
?>