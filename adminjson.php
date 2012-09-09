<?php
session_start();
include_once("config.php");
include_once("functions.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);

switch($_GET["func"]){
	case "getRankTypesByGroup":
		getRankTypesByGroup($_GET["id"]);
		break;
	case "getMembersByNotGroup":
		getMembersByNotGroup($_GET["id"]);
		break;
	case "getMembersByGroup":
		getMembersByGroup($_GET["id"]);
		break;
	case "getMedalsByGroup":
		getMedalsByGroup($_GET["id"]);
		break;
	case "getRanksByGroup":
		getRanksByGroup($_GET["id"]);
		break;
	case "getPositionsByGroup":
		getPositionsByGroup($_GET["id"]);
		break;
	case "getAccessByGroup":
		getAccessByGroup($_GET["id"]);
		break;
	case "getTrainingCategoriesByAcademy":
		getTrainingCategoriesByAcademy($_GET["id"]);
		break;
	case "getUnitTypesByGroup":
		getUnitTypesByGroup($_GET["id"]);
		break;
	case "getUnitsByGroup":
		getUnitsByGroup($_GET["id"]);
		break;
	case "getGroupsByMember":
		getGroupsByMember($_GET["id"]);
		break;
	case "getTrainingByAcad":
		getTrainingByAcad($_GET["id"]);
		break;
	case "getCompsByGroup":
		getCompsByGroup($_GET["id"]);
		break;
	case "getPositionsByMember":
		getPositionsByMember($_GET["id"]);
		break;
	case "memberautocomplete":
		memberAutoComplete($_GET["q"]);
		break;
	default:
		echo "Access Denied";
}

function getRankTypesByGroup(){
	/*	
	if (!has_access($_SESSION['EHID'], "rankadmin")){
		echo "Access Denied";
		exit;
	}
	*/
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT RT_ID, Name FROM EH_Ranks_Types WHERE Group_ID=".$id." ORDER BY Name";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getMedalsByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT Medal_ID, Name, MG_ID FROM EH_Medals WHERE";
    if($id!=1)
      $query.=" Group_ID=".$id." And";
    $query.=" Active=1 ORDER BY SortOrder";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			if($row['MG_ID']) {
				$query2 = "SELECT Name From EH_Medals_Groups WHERE MG_ID=".$row['MG_ID']; 
				$result2 = mysql_query($query2);
				$rows2 = mysql_num_rows($result2);
				if($rows2) {
					$values2 = mysql_fetch_row($result2);
					$row['MG_Name']=$values2[0];
					}
				}
			else {
				$row['MG_Name']="";
				}
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}


function getTrainingCategoriesByAcademy(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT TC_ID, Name FROM EH_Training_Categories WHERE TCa_ID=".$id." ORDER BY SortOrder";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}


function getPositionsByMember(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT EH_Positions.Position_ID As Position_ID, EH_Positions.Name As Name FROM EH_Positions, EH_Members_Positions WHERE EH_Members_Positions.Group_ID=".$id." AND EH_Members_Positions.Member_ID=".$_SESSION['EHID']." AND EH_Members_Positions.Position_ID=EH_Positions.Position_ID";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getUnitTypesByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT UT_ID, Name FROM EH_Units_Types WHERE Group_ID=".$id." OR Group_ID=0 ORDER BY SortOrder";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getUnitsByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT Unit_ID, Name FROM EH_Units WHERE Group_ID=".$id." ORDER BY Name";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getMembersByNotGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	$mem = array();
  $query1 = "SELECT Member_ID FROM EH_Members_Groups WHERE Group_ID=$id";
  $result1 = mysql_query($query1);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    $mem[]=$values1[0];
    }
  if(count($mem))
    $memstr = implode(" AND Member_ID!=", $mem);
	$query = "SELECT Member_ID, Name FROM EH_Members";
	if(count($mem))
	  $query.=" WHERE Member_ID!=$memstr";
	$query.=" Order By Name";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getRanksByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
    $query1 = "SELECT RT_ID FROM EH_Ranks_Types WHERE Group_ID=$id";
    $result1 = mysql_query($query1);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $query = "SELECT EH_Ranks.Rank_ID As Rank_ID, EH_Ranks.Name As Name, EH_Ranks_Types.Name As RT_Name FROM EH_Ranks, EH_Ranks_Types WHERE";
      if($id!=1)
        $query.=" EH_Ranks.Group_ID=".$id." AND";
      $query.=" EH_Ranks.RT_ID=EH_Ranks_Types.RT_ID ORDER BY EH_Ranks.SortOrder";
      }
    else {
      $query = "SELECT Rank_ID, Name FROM EH_Ranks";
      if($id!=1)
        $query.=" WHERE Group_ID=".$id;
      $query.=" ORDER BY SortOrder";
      }
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getMembersByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT EH_Members.Member_ID As Member_ID, EH_Members.Name As Name FROM EH_Members, EH_Members_Groups WHERE EH_Members_Groups.Group_ID=".$id." AND EH_Members.Member_ID=EH_Members_Groups.Member_ID ORDER BY Name";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getPositionsByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT Position_ID, Name FROM EH_Positions WHERE Group_ID=".$id." ORDER BY SortOrder";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getAccessByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT Access_ID, Name FROM EH_Access WHERE Group_ID=".$id." ORDER BY Name";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getGroupsByMember(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT EH_Groups.Group_ID, EH_Groups.Name FROM EH_Groups, EH_Members_Groups WHERE EH_Members_Groups.Member_ID=".$id." AND EH_Members_Groups.Group_ID=EH_Groups.Group_ID ORDER BY Group_ID";
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function getTrainingByAcad(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT Training_ID, Name FROM EH_Training WHERE TAc_ID=".$id;
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}


function getCompsByGroup(){
	$id = $_POST["id"];
	if (!is_numeric($id)){
		echo json_encode(array("status"=>false, "msg"=>"Invalid group ID provided"));
		exit;
	}
	
	$toReturn = array("status"=>false,"data"=>array());
	
	$query = "SELECT Comp_ID, Name FROM EH_Competitions WHERE Group_ID=".$id;
    $result = mysql_query($query);
    if (@mysql_num_rows($result) > 0){
		while($row = mysql_fetch_assoc($result)){
			array_push($toReturn["data"], $row);
		}
		$toReturn["status"] = true;
	}
	echo json_encode(stripslashes_deep($toReturn));
}

function memberAutoComplete($q){
	$toReturn = array();
	$query_string = "SELECT Member_ID, Name FROM EH_Members WHERE Name LIKE '%".mysql_real_escape_string($q)."%'";
	$query = mysql_query($query_string);
	while($row = mysql_fetch_assoc($query)){
		array_push($toReturn, array("id"=>$row["Member_ID"],"value"=>$row["Name"]));
	}
	echo json_encode($toReturn);
}
?>