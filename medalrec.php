<?
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
?>
<p>Emperor's Hammer Medal Recommendation</p>
<p><a href="menu.php">Return to the administration menu</a></p>
<p>
<?
if($_POST['memberid']) {
  $memberid = mysql_real_escape_string($_POST['memberid'], $mysql_link);
  $selGroup = mysql_real_escape_string($_POST['selGroup'], $mysql_link);
  $medalid = mysql_real_escape_string($_POST['medalid'], $mysql_link);
  $reason = mysql_real_escape_string($_POST['reason'], $mysql_link);
  $fromid = mysql_real_escape_string($_SESSION['EHID'], $mysql_link);
  $now = time();
  $num = mysql_real_escape_string($_POST['num'], $mysql_link);
  if(is_numeric($num)) {
    for($i=0; $i<$num; $i++) {
      $query = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status) VALUES('$memberid', '$medalid', '$fromid', '$selGroup', '$now', '$reason', 0)";
      $result = mysql_query($query, $mysql_link);
      }
    }
  if($result)
    echo "<p>Medal Recommended successfully!</p>\n";
  else
    echo "<p>ERROR! E-mail: $adminlink with details on what went wrong, with what data was attempted to be submitted</p>";
  }
else {
  if($_GET['memberid'])
    $memberid= mysql_real_escape_string($_GET['memberid'], $mysql_link);
  else
    $memberid=0;
  }
?>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
  <table>
<?
if($memberid==0 || $memberid==$_SESSION['EHID']) {
?>
    <tr>
      <td><label for="memberid">Member: </label></td>
      <td>
        <select name="memberid" id="memberid" onChange="getGroups()">
         <option value="0">None Selected</option>
<?
  $query1 = "SELECT Member_ID, Name FROM EH_Members WHERE Member_ID!=".$_SESSION['EHID']." AND Email!='' Order By Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "          <option value=\"$values1[0]\">".stripslashes($values1[1])."</option>\n";
  }
?>
        </select>
      </td>
    </tr>
<?
  }
else {
  echo "<input type=\"hidden\" name=\"memberid\" value=\"$memberid\" id=\"memberid\" />\n";
?>

    <tr>
      <td>Member:</td>
      <td><?=RankAbbrName($memberid, PriGroup($memberid), 1)?></td>
    </tr>
<?
  }
?>
    <tr>
      <td><label for="selGroup">Group: </label></td>
      <td>
        <select name="selGroup" id="selGroup" onChange="getMedals()">
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="medalid">Medal: </label></td>
      <td>
        <select name="medalid" id="medalid">
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="reason">Reason: </label></td>
      <td><textarea name="reason" id="reason" style="width:400px; height:120px"></textarea></td>
    </tr>
    <tr>
      <td><label for="num">Number: </label></td>
      <td><input type="text" name="num" id="num" value="1" size="5"></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" id="Submit" name="Submit" value="Submit" />
        <input type="reset" id="Reset" name="Reset" value="Reset" />
      </td>
    </tr>
  </table>
</form>
<script type="text/javascript">
  $(document).ready(getGroups);
  function getMedals(){
	var group = $("#selGroup option:selected").val();
	var postvars = {"id":group}
	$("#medalid").empty();
	$("#medalid").append('<option value="0">None</option>');
	getAdminJSONdata("getMedalsByGroup", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					var trash = '<option value="'+item.Medal_ID+'">';
					if(item.MG_ID!=0)
						trash +=item.MG_Name+' - ';
					trash +=item.Name+'</option>';
					$("#medalid").append(trash);
				});
			}
		}
	);
  }
  
  function getGroups(){
	if(<?=$memberid?>!=0 && <?=$memberid?>!=<?=$_SESSION['EHID']?>) {
        var group = <?=$memberid?>;
	}
	else {
	var group = $("#memberid option:selected").val();
	}
	var postvars = {"id":group}
	$("#selGroup").empty();
	$("#selGroup").append('<option value="0">None</option>');
	getAdminJSONdata("getGroupsByMember", postvars,function(data){
			if (data != false){
				$.each(data, function(index, item){
					$("#selGroup").append('<option value="'+item.Group_ID+'">'+item.Name+'</option>');
				});
			}
		}
	);
  }
</script>

<?
include_once("footer.php");
?>