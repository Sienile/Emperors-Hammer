<?
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
?>
<p>Emperor's Hammer Imperial Navy Pilot Record Administration</p>
<p><a href="menu.php">Return to the administration menu</a></p>
<p>
<?
if($_POST['memberid']) {
  $memberid = mysql_real_escape_string($_POST['memberid'], $mysql_link);
  $insert = mysql_real_escape_string($_POST['insert'], $mysql_link);
  $gender = mysql_real_escape_string($_POST['gender'], $mysql_link);
  $species = mysql_real_escape_string($_POST['species'], $mysql_link);
  $birth = mysql_real_escape_string($_POST['birth'], $mysql_link);
  $place = mysql_real_escape_string($_POST['place'], $mysql_link);
  $relationship = mysql_real_escape_string($_POST['relationship'], $mysql_link);
  $family = mysql_real_escape_string($_POST['family'], $mysql_link);
  $social = mysql_real_escape_string($_POST['social'], $mysql_link);
  $sigyouth = mysql_real_escape_string($_POST['sigyouth'], $mysql_link);
  $sigadult = mysql_real_escape_string($_POST['sigadult'], $mysql_link);
  $alignatt = mysql_real_escape_string($_POST['alignatt'], $mysql_link);
  $prev = mysql_real_escape_string($_POST['prev'], $mysql_link);
  $hobbies = mysql_real_escape_string($_POST['hobbies'], $mysql_link);
  $trag = mysql_real_escape_string($_POST['trag'], $mysql_link);
  $phobia = mysql_real_escape_string($_POST['phobia'], $mysql_link);
  $views = mysql_real_escape_string($_POST['views'], $mysql_link);
  $enlisting = mysql_real_escape_string($_POST['enlisting'], $mysql_link);
  $comments = mysql_real_escape_string($_POST['comments'], $mysql_link);
  $now = time();
  if($insert) {
  $query = "INSERT INTO EH_Members_INPR (Gender, Species, Birthdate, PlaceBirth, Relationship, Family, Social, SigYouth, SigAdult, AlignAtt, Previous, Hobbies, Traggedies, PhobiaAllergy, View, Enlisting, Comments, UpdateDate, Member_ID) Values ('$gender', '$species', '$birth', '$place', '$relationship', '$family', '$social', '$sigyouth', '$sigadult', '$alignatt', '$prev', '$hobbies', '$trag', '$phobia', '$views', '$enlisting', '$comments', '$now', '$memberid')";
  $result = mysql_query($query, $mysql_link);
    }
  else {
  $query = "UPDATE EH_Members_INPR Set Gender='$gender', Species='$species', Birthdate='$birth', PlaceBirth='$place', Relationship='$relationship', Family='$family', Social='$social', SigYouth='$sigyouth', SigAdult='$sigadult', AlignAtt='$alignatt', Previous='$prev', Hobbies='$hobbies', Traggedies='$trag', PhobiaAllergy='$phobia', View='$views', Enlisting='$enlisting', Comments='$comments', UpdateDate='$now' WHERE Member_ID=$memberid";
  $result = mysql_query($query, $mysql_link);
    }
  if($result)
    echo "<p>INPR Updated!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
?>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
  <table>
<?
  echo "<input type=\"hidden\" name=\"memberid\" value=\"".$_SESSION['EHID']."\" id=\"memberid\" />\n";
  $query = "SELECT Gender, Species, Birthdate, PlaceBirth, Relationship, Family, Social, SigYouth, SigAdult, AlignAtt, Previous, Hobbies, Traggedies, PhobiaAllergy, View, Enlisting, Comments From EH_Members_INPR WHERE Member_ID=".$_SESSION['EHID'];
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $gender = stripslashes($values[0]);
    $species = stripslashes($values[1]);
    $birthdate = stripslashes($values[2]);
    $placeBirth = stripslashes($values[3]);
    $relationship = stripslashes($values[4]);
    $family = stripslashes($values[5]);
    $social = stripslashes($values[6]);
    $sigYouth = stripslashes($values[7]);
    $sigAdult = stripslashes($values[8]);
    $alignAtt = stripslashes($values[9]);
    $previous = stripslashes($values[10]);
    $hobbies = stripslashes($values[11]);
    $traggedies = stripslashes($values[12]);
    $phobiaAllergy = stripslashes($values[13]);
    $view = stripslashes($values[14]);
    $enlisting = stripslashes($values[15]);
    $comments = stripslashes($values[16]);
    }
  if($rows==0)
    echo "<input type=\"hidden\" name=\"insert\" value=\"1\" id=\"insert\" />\n";
  else
    echo "<input type=\"hidden\" name=\"insert\" value=\"0\" id=\"insert\" />\n";
?>
    <tr>
      <td><label for="gender">Gender:</label></td>
      <td><input type="text" name="gender" id="gender" value="<?=$gender?>"></td>
    </tr>
    <tr>
      <td><label for="species">Species:</label></td>
      <td><input type="text" name="species" id="species" value="<?=$species?>" /></td>
    </tr>
    <tr>
      <td><label for="birth">Birty Date:</label></td>
      <td><input type="text" name="birth" id="birth" value="<?=$birthdate?>" /></td>
    </tr>
    <tr>
      <td><label for="place">Place of birth:</label></td>
      <td><input type="text" name="place" id="place" value="<?=$placeBirth?>" /></td>
    </tr>
    <tr>
      <td><label for="relationship">Relationship Status:</label></td>
      <td><input type="text" name="relationship" id="relationship" value="<?=$relationship?>" /></td>
    </tr>
    <tr>
      <td><label for="family">Family Status:</label></td>
      <td><textarea name="family" id="family" style="width:510px; height:200px"><?=$family?></textarea></td>
    </tr>
    <tr>
      <td><label for="social">Social Status:</label></td>
      <td><input type="text" name="social" id="social" value="<?=$social?>" /></td>
    </tr>
    <tr>
      <td><label for="sigyouth">Significant events in Youth:</label></td>
      <td><textarea name="sigyouth" id="sigyouth" style="width:510px; height:200px"><?=$sigYouth?></textarea></td>
    </tr>
    <tr>
      <td><label for="sigadult">Significant events in Adulthood:</label></td>
      <td><textarea name="sigadult" id="sigadult" style="width:510px; height:200px"><?=$sigAdult?></textarea></td>
    </tr>
    <tr>
      <td><label for="alignatt">Alignment Attributes:</label></td>
      <td><textarea name="alignatt" id="alignatt" style="width:510px; height:200px"><?=$alignAtt?></textarea></td>
    </tr>
    <tr>
      <td><label for="prev">Previous Employment:</label></td>
      <td><textarea name="prev" id="prev" style="width:510px; height:200px"><?=$previous?></textarea></td>
    </tr>
    <tr>
      <td><label for="hobbies">Hobbies:</label></td>
      <td><textarea name="hobbies" id="hobbies" style="width:510px; height:200px"><?=$hobbies?></textarea></td>
    </tr>
    <tr>
      <td><label for="trag">Tragedies:</label></td>
      <td><textarea name="trag" id="trag" style="width:510px; height:200px"><?=$traggedies?></textarea></td>
    </tr>
    <tr>
      <td><label for="phobia">Phobias/Allergies:</label></td>
      <td><textarea name="phobia" id="phobia" style="width:510px; height:200px"><?=$phobiaAllergy?></textarea></td>
    </tr>
    <tr>
      <td><label for="views">Views on the EH:</label></td>
      <td><textarea name="views" id="views" style="width:510px; height:200px"><?=$view?></textarea></td>
    </tr>
    <tr>
      <td><label for="enlisting">Reasons for Enlisting:</label></td>
      <td><textarea name="enlisting" id="enlisting" style="width:510px; height:200px"><?=$enlisting?></textarea></td>
    </tr>
    <tr>
      <td><label for="comments">Comments:</label></td>
      <td><textarea name="comments" id="comments" style="width:510px; height:200px"><?=$comments?></textarea></td>
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
include_once("footer.php");
?>