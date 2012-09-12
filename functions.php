<?
include_once("config.php");

function CalculateFCHG($member) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $pts = 0;
  $cr = 0;
  //Every Mission Flown
  $query1 = "SELECT SUM(NumMissions) FROM EH_Battles WHERE Battle_ID IN ( SELECT DISTINCT Battle_ID FROM EH_Battles_Complete WHERE Member_ID =$member AND STATUS =1)";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0];
    }
  //Mission High Score = 2pts
  $query1 = "SELECT Count(Mission_ID) From EH_Battles_Missions WHERE HS_Holder=$member";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0]*2;
    }
  //Battle High Score = 2ptsxnum missions
  $query1 = "SELECT NumMissions From EH_Battles WHERE HS_Holder=$member AND NumMissions>1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0]*2;
    }
  //IS-BW = 1 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$member AND Medal_ID=130";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += $values1[0];
    }
  //IS-SW = 3 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$member AND Medal_ID=132";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += 3*$values1[0];
    }
  //IS-GW = 5 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$member AND Medal_ID=134";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += 5*$values1[0];
    }
  //IS-PW = 10 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$member AND Medal_ID=136";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pts += 10*$values1[0];
    }
  //LoC = 1 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$member AND Medal_ID=137";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $cr +=$values1[0];
    $pts += $values1[0];
    }
  //DFW = 5 pt
  $query1 = "SELECT Count(MC_ID) From EH_Medals_Complete WHERE Member_ID=$member AND Medal_ID=138";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $cr +=$values1[0]*5;
    }
  if($pts) {
    $query1 = "SELECT EMSA_ID FROM EH_Members_Special_Areas WHERE SA_ID=1 AND Member_ID=$member";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "Update EH_Members_Special_Areas Set Value=$pts WHERE EMSA_ID=$values1[0]";
      $result2 = mysql_query($query2, $mysql_link);
      }
    else {
      $query2 = "INSERT INTO EH_Members_Special_Areas  (Member_ID, SA_ID, Value) Values('$member', '1', '$pts')";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  if($cr) {
    $query1 = "SELECT EMSA_ID FROM EH_Members_Special_Areas WHERE SA_ID=2 AND Member_ID=$member";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "Update EH_Members_Special_Areas Set Value=$cr WHERE EMSA_ID=$values1[0]";
      $result2 = mysql_query($query2, $mysql_link);
      }
    else {
      $query2 = "INSERT INTO EH_Members_Special_Areas  (Member_ID, SA_ID, Value) Values('$member', '2', '$cr')";
      $result2 = mysql_query($query2, $mysql_link);
      }
    }
  return $pts;
  }

function CoC($member, $group) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $coc=array();
  if($member) {
    $query = "SELECT Max(EH_Positions.SortOrder), EH_Members_Units.Unit_ID FROM EH_Members_Positions, EH_Members_Units, EH_Positions, EH_Units WHERE EH_Members_Positions.Position_ID=EH_Positions.Position_ID AND EH_Members_Units.Member_ID=$member AND EH_Members_Positions.Member_ID=$member AND EH_Members_Positions.Group_ID=$group AND EH_Members_Units.Group_ID=$group AND EH_Units.Unit_ID=EH_Members_Units.Unit_ID";
// AND EH_Units.UT_ID!=3 AND EH_Units.UT_ID!=2 AND EH_Units.UT_ID!=1
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $coc = CoCRecursive($group, $values[0], $values[1]);
      }
    }
  $query = "SELECT EH_Members.Member_ID, EH_Members.Email FROM EH_Members, EH_Members_Positions, EH_Positions WHERE EH_Members.Member_ID=EH_Members_Positions.Member_ID AND EH_Positions.Position_ID=EH_Members_Positions.Position_ID AND EH_Members_Positions.Group_ID=$group AND EH_Positions.SortOrder>=(SELECT Max(SortOrder)-1 FROM EH_Positions WHERE Group_ID=$group)";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $coc[] = RankAbbrName($values[0], $group, 0)." <$values[1]>";
    }
  $coc = array_unique($coc);
  return implode(", ", $coc);
  }

function CoCRecursive($group, $so, $unit) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $coc=array();
  $query = "SELECT EH_Members.Member_ID, EH_Members.Email, EH_Units.Master_ID, EH_Positions.SortOrder From EH_Members, EH_Members_Positions, EH_Members_Units, EH_Positions, EH_Units WHERE EH_Members.Member_ID=EH_Members_Positions.Member_ID AND EH_Members_Units.Member_ID=EH_Members.Member_ID AND EH_Members_Positions.Position_ID=EH_Positions.Position_ID AND EH_Units.Unit_ID=EH_Members_Units.Unit_ID AND EH_Units.Unit_ID=$unit AND EH_Positions.Group_ID=$group AND EH_Positions.SortOrder>$so AND EH_Units.UT_ID!=3 AND EH_Units.UT_ID!=2 AND EH_Units.UT_ID!=1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $so=$values[3];
    $coc[] = RankAbbrName($values[0], $group, 0)." <$values[1]>";
    $coc = array_merge($coc, CoCRecursive($group, $so, $values[2]));
    }
  $query = "SELECT EH_Members.Member_ID, EH_Members.Email, EH_Units.Master_ID From EH_Members, EH_Members_Positions, EH_Members_Units, EH_Positions, EH_Units WHERE EH_Members.Member_ID=EH_Members_Positions.Member_ID AND EH_Members_Units.Member_ID=EH_Members.Member_ID AND EH_Members_Positions.Position_ID=EH_Positions.Position_ID AND EH_Units.Unit_ID=EH_Members_Units.Unit_ID AND EH_Units.Master_ID=$unit AND EH_Positions.Group_ID=$group AND EH_Positions.SortOrder>$so AND EH_Units.UT_ID!=3 AND EH_Units.UT_ID!=2 AND EH_Units.UT_ID!=1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $coc[] = RankAbbrName($values[0], $group, 0)." <$values[1]>";
    }

  return $coc;
  }

function stripslashes_deep($value) {
  $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

  return $value;
}

function read_bytes($start,$end,$filename) {
  unset($output);
  $jj = $start; 
  $ii = $end;
  $handle = fopen($filename,"rb");
  while ($ii >= $jj) {
    fseek($handle,$ii);
    $buffer = fread($handle,1);
    $dig1 = unpack("H",$buffer);
    $dig2 = unpack("h",$buffer);
    $output .= strtoupper($dig1[1].$dig2[1]);
    $ii--;
    }
  return hexdec($output);
  }

function Access($id, $page, $menu=false) {
    global $SO;
  if(has_access($id, $page))
    return true;
  else {
    if(!$menu){
        // Don't want to log if this is just building a menu
        $SO->addIP();
    }
    Redirect("login.php");
    return false;
    }
  }

function BattleName($id, $showabbr) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  if($id) {
    $query1 = "SELECT EH_Battles.Name, EH_Battles.BattleNumber, EH_Platforms.Abbr, EH_Platforms.Name, EH_Battles_Categories.Abbr, EH_Battles_Categories.Name FROM EH_Battles, EH_Battles_Categories, EH_Platforms WHERE EH_Battles.Platform_ID=EH_Platforms.Platform_ID AND EH_Battles.BC_ID=EH_Battles_Categories.BC_ID AND EH_Battles.Battle_ID=$id";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values = mysql_fetch_row($result1);
      if($showabbr) {
        $name="<abbr title=\"".stripslashes($values[3])."\">".stripslashes($values[2])."</abbr>-<abbr title=\"".stripslashes($values[5])."\">".stripslashes($values[4])."</abbr> #".stripslashes($values[1]).": ".stripslashes($values[0]);
        }
      else
        $name=stripslashes($values[2])."-".stripslashes($values[4])." #".stripslashes($values[1]).": ".stripslashes($values[0]);
      }
    else {
      $name="";
      }
    }
  else {
    $name="";
    }
  return $name;
  }

function AccessGroups($id, $page) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Position_ID, Group_ID From EH_Members_Positions WHERE Member_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "select EH_Access.Pages, EH_Access.Group_ID From EH_Positions, EH_Access WHERE EH_Positions.Position_ID=$values[0] AND EH_Positions.Access_ID=EH_Access.Access_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j=0; $j<$rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $pages=array();
      $access = explode(";", $values1[0]);
      foreach($access as $tarpage) {
        if($tarpage==$page) {
          $groups[] = $values[1];
          break;
          }
        }
      }
    }
  $groups = array_unique ($groups);
  foreach($groups as $group) {
   if($group==1) {
     $query1 = "select Group_ID FROM EH_Groups";
     $result1 = mysql_query($query1, $mysql_link);
     $rows1 = mysql_num_rows($result1);
     for($i=0; $i<$rows1; $i++) {
       $values1 = mysql_fetch_row($result1);
       $fgroups[] = $values1[0];
       }
     }
   }
  if($fgroups) {
    $groups = $fgroups;
    }
  return $groups;
  }

function has_access($id, $pageacc) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  if (!isset($_SESSION["EHID"])){ 
      return false;
  }
  $query = "select EH_Access.Pages From EH_Positions, EH_Access, EH_Members_Positions WHERE (EH_Positions.Position_ID=EH_Members_Positions.Position_ID) AND EH_Members_Positions.Member_ID=$id AND EH_Positions.Access_ID=EH_Access.Access_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $temp = explode(";", $values[0]);
    foreach($temp as $page)
      $pages[] = $page;
    }
  for($i=0; $i<count($pages); $i++) {
    if($pages[$i]==$pageacc)
      return true;
    }
  return false;
}

function SubUnitList($unit) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $units=array();
  $units[]=$unit;
  $count = 0;
  do {
    $query = "SELECT Unit_ID FROM EH_Units Where Master_ID=$units[$count]";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    for($i=0; $i<$rows; $i++) {
      $values = mysql_fetch_row($result);
      $units[]=$values[0];
      }
    $count++;
    } while($count<count($units));
  return $units;
  }

function Unit($unit, $view) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "SELECT EH_Units.Unit_ID, EH_Units.UT_ID, EH_Units.Group_ID, EH_Units.Base_ID, EH_Units.SiteURL, EH_Units.MessageBoard, EH_Units.Banner, EH_Units.Motto, EH_Units.Nickname, EH_Units.MissionRoll, EH_Units_Types.PageSections, EH_Units_Types.Position, EH_Units_Types.MaxPos FROM EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID = $unit AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $indent=0;
  $indentspace = "&nbsp;&nbsp;&nbsp;";
  if($rows) {
    $values = mysql_fetch_row($result);
    $name = UnitType($values[0]);
    $group = $values[2];
    if($values[3])
      $base = Base($values[3]);
    else
      $base = "No Permanant Base Established";
    if($values[4])
      $url = "<a href=\"$values[4]\">".stripslashes($values[4])."</a>";
    else
      $url = "No site established yet";
    if($values[5])
      $mb =  "<a href=\"$values[5]\">".stripslashes($values[5])."</a>";
    else
      $mb = "No dedicated message board.";
    if($values[6])
      $banner =  "<img src=\"$values[6]\" />";
    else
      $banner = "No Banner yet.";
    if($values[7])
      $motto = stripslashes($values[7]);
    else
      $motto="No motto";
    if($values[8])
      $nick = stripslashes($values[8]);
    else
      $nick = "No nickname";
    if($values[9])
      $MR = stripslashes($values[9]);
    else
      $MR = "No mission roll yet";
    $reports = "<a href=\"unitreports.php?id=$values[0]\">Unit Reports</a>";
    $query1 = "SELECT Report_ID FROM EH_Reports WHERE Unit_ID=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1>0)
      $reports.= " ($rows1)";
    $bc = "<a href=\"unitbattlecerts.php?unit=$values[0]\">Battle Certs</a>";
    $tc = "<a href=\"unittraincerts.php?unit=$values[0]\">Training Certs</a>";
    //Everything replaced using STR Replace
    $display = $values[10];
//TrainingCerts - @TC@, BattleCerts - @BC@
    $display = str_replace("@NA@", $name, $display);
    $display = str_replace("@BA@", $base, $display);
    $display = str_replace("@SU@", $url, $display);
    $display = str_replace("@MB@", $mb, $display);
    $display = str_replace("@BN@", $banner, $display);
    $display = str_replace("@MT@", $motto, $display);
    $display = str_replace("@NN@", $nick, $display);
    $display = str_replace("@MR@", $MR, $display);
    $display = str_replace("@TC@", $tc, $display);
    $display = str_replace("@BC@", $bc, $display);
    $display = str_replace("@Report@", $reports, $display);
    echo $display;
    PersonUnit($values[0], $indent+1, $values[11], $group, $values[12]);
    Roster($group, $values[0], $indent + 1, $view);
    }
  }


function sortDbResult(array $data) { 

/**  FROM http://www.php.net/manual/en/function.array-multisort.php
 * Sort DB result 
 * 
 * @param array $data Result of sql query as associative array 
 * 
 * Rest of parameters are optional 
 * [, string $name  [, mixed $name or $order  [, mixed $name or $mode]]] 
 * $name string - column name i database table 
 * $order integer - sorting direction ascending (SORT_ASC) or descending (SORT_DESC) 
 * $mode integer - sorting mode (SORT_REGULAR, SORT_STRING, SORT_NUMERIC) 
 * 
 * <code> 
 * <?php 
 * // You can sort data by several columns e.g. 
 * $data = array(); 
 * for ($i = 1; $i <= 10; $i++) { 
 *     $data[] = array( 'id' => $i, 
 *                      'first_name' => sprintf('first_name_%s', rand(1, 9)), 
 *                      'last_name' => sprintf('last_name_%s', rand(1, 9)), 
 *                      'date' => date('Y-m-d', rand(0, time())) 
 *                  ); 
 * } 
 * $data = sortDbResult($data, 'date', SORT_DESC, SORT_NUMERIC, 'id'); 
 * printf('<pre>%s</pre>', print_r($data, true)); 
 * $data = sortDbResult($data, 'last_name', SORT_ASC, SORT_STRING, 'first_name', SORT_ASC, SORT_STRING);     
 * printf('<pre>%s</pre>', print_r($data, true)); 
 * ?> 
 * </code> 
 * 
 * @return array $data - Sorted data 
 */ 
    $_argList = func_get_args(); 
    $_data = array_shift($_argList); 
    if (empty($_data)) { 
        return $_data; 
    } 
    $_max = count($_argList); 
    $_params = array(); 
    $_cols = array(); 
    $_rules = array(); 
    for ($_i = 0; $_i < $_max; $_i += 3) 
    { 
        $_name = (string) $_argList[$_i]; 
        if (!in_array($_name, array_keys(current($_data)))) { 
            continue; 
        } 
        if (!isset($_argList[($_i + 1)]) || is_string($_argList[($_i + 1)])) { 
            $_order = SORT_ASC; 
            $_mode = SORT_REGULAR; 
            $_i -= 2; 
        } else if (3 > $_argList[($_i + 1)]) { 
            $_order = SORT_ASC; 
            $_mode = $_argList[($_i + 1)]; 
            $_i--; 
        } else { 
            $_order = $_argList[($_i + 1)] == SORT_ASC ? SORT_ASC : SORT_DESC; 
            if (!isset($_argList[($_i + 2)]) || is_string($_argList[($_i + 2)])) { 
                $_mode = SORT_REGULAR; 
                $_i--; 
            } else { 
                $_mode = $_argList[($_i + 2)]; 
            } 
        } 
        $_mode = $_mode != SORT_NUMERIC 
                    ? $_argList[($_i + 2)] != SORT_STRING ? SORT_REGULAR : SORT_STRING 
                    : SORT_NUMERIC; 
        $_rules[] = array('name' => $_name, 'order' => $_order, 'mode' => $_mode); 
    } 
    foreach ($_data as $_k => $_row) { 
        foreach ($_rules as $_rule) { 
            if (!isset($_cols[$_rule['name']])) { 
                $_cols[$_rule['name']] = array(); 
                $_params[] = &$_cols[$_rule['name']]; 
                $_params[] = $_rule['order']; 
                $_params[] = $_rule['mode']; 
            } 
            $_cols[$_rule['name']][$_k] = $_row[$_rule['name']]; 
        } 
    } 
    $_params[] = &$_data; 
    call_user_func_array('array_multisort', $_params); 
    return $_data; 
} 

function Roster($group, $unit, $indent, $view) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "SELECT EH_Units.Unit_ID, EH_Units.Name, EH_Units.Active, EH_Units_Types.Position, EH_Units_Types.MaxPos FROM EH_Units, EH_Units_Types WHERE EH_Units.Master_ID = $unit AND EH_Units.Group_ID=$group AND EH_Units.Active=1 AND EH_Units_Types.UT_ID=EH_Units.UT_ID Order By EH_Units_Types.SortOrder, EH_Units.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i <$rows; $i++) {
    $values = mysql_fetch_row($result);
    for($varCounter = 0; $varCounter< ($indent + 1); $varCounter++)
      echo "&nbsp;&nbsp;&nbsp;";
    echo "<a href=\"unit.php?id=$values[0]\">".UnitType($values[0])."</a>";
    if($values[2]==0)
      echo " - <font color=\"#FF0000\">Inactive</font>";
    echo "<br />\n";
    if($view)
      PersonUnit($values[0], $indent+1,$values[3], $group, $values[4]);
    Roster($group, $values[0], $indent + 1, $view);
    }
  }

function PersonUnit($unit, $indent, $useunitpos, $group, $maxunitsize) {
  //Given $unit - Unit_ID, $indent- number of indents to use, $useunitpos - bool to see if need to check for unit position $group - Group ID
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  //$query = "SELECT Member_ID, UnitPosition FROM EH_Members_Units WHERE Unit_ID=$unit AND Group_ID=$group Order By UnitPosition";
  $query = "SELECT EH_Members.Name, EH_Members.Member_ID, EH_Members_Units.UnitPosition, EH_Positions.Abbr, EH_Positions.Name, EH_Ranks.Abbr, EH_Ranks.Name FROM EH_Positions, EH_Members_Positions, EH_Members, EH_Members_Units, EH_Ranks, EH_Members_Ranks WHERE EH_Members_Ranks.Member_ID=EH_Members_Units.Member_ID AND EH_Ranks.Rank_ID=EH_Members_Ranks.Rank_ID AND EH_Members_Positions.Member_ID=EH_Members_Units.Member_ID AND EH_Members_Positions.Position_ID=EH_Positions.Position_ID AND EH_Members_Units.Member_ID=EH_Members.Member_ID AND EH_Members_Units.Unit_ID=$unit AND EH_Members_Units.Group_ID=$group AND EH_Members_Positions.Group_ID=$group AND EH_Members_Ranks.Group_ID=$group Group By EH_Members.Name Order By EH_Members_Units.UnitPosition, EH_Positions.SortOrder DESC, EH_Ranks.SortOrder DESC, EH_Members.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $people[] = $values;
    }
  if($useunitpos) {
    $count = 0;
    for($q=1; $q<=$maxunitsize; $q++) {
      echo $q.") ";
      if($people[$count][2]==$q) {
        echo "<abbr title=\"".stripslashes($people[$count][4])."\">".stripslashes($people[$count][3])."</abbr> <a href=\"profile.php?pin=".stripslashes($people[$count][1])."\"><abbr title=\"".stripslashes($people[$count][6])."\">".stripslashes($people[$count][5])."</abbr> ".stripslashes($people[$count][0])."</a><br>\n";
        $count++;
        }
      else {
        echo "TBA<br>\n";
        }
      }
    }
  else {
    //Actually more complicated due to the fact that it relies on first sort by positionsort, followed by Rank, folled by name
    for($i=0; $i<count($people); $i++) {
      echo "<abbr title=\"".stripslashes($people[$i][4])."\">".stripslashes($people[$i][3])."</abbr> <a href=\"profile.php?pin=".stripslashes($people[$i][1])."\"><abbr title=\"".stripslashes($people[$i][6])."\">".stripslashes($people[$i][5])."</abbr> ".stripslashes($people[$i][0])."</a><br>\n";
      }
    }
  }

function UnitType($id) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "SELECT EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT, EH_Units_Types.DisplayMasterUnit From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$id AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $master = $values[2];
  $name = stripslashes($values[1]);
  if($values[5]==1)
    $name = $values[4]." ".$name;
  elseif($values[5]==2)
    $name = $name." ".$values[4];
  if($values[7]) {
    $query1 = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$master AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $values1 = mysql_fetch_row($result1);
    if($values[7]==1) {
      $name=stripslashes($values1[1])." ".$name;
      }
    elseif($values[7]==2)
      $name=$name." ".stripslashes($values1[1]);
    $master=$values1[2];
    }
  return $name;
  }

function MembersPosition($pos, $group, $sep) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $mem = "";
  $query = "select Member_ID From EH_Members_Positions WHERE Position_ID=$pos AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $mem .="<a href=\"$site_host/profile.php?pin=$values[0]\">".RankAbbrName($values[0], $group, 1)."</a>".$sep;
    }
  return $mem;
}


Function Redirect($sitepath)
{
global $site_host;
$sgurl="$site_host/".$sitepath;
    header("Request-URI: ".$sgurl);
    header("Location: ".$sgurl);
    header("Content-Location: ".$sgurl);
    exit();
}


function PriGroup($pin) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Group_ID From EH_Members_Groups WHERE Member_ID=$pin AND isPrimary=1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    return $values[0];
    }
}

function Professor($academy, $grader) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $name = "";
  $query = "select Group_ID, Trainers From EH_Training_Academies WHERE TAc_ID=$academy";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "select Name From EH_Positions WHERE Position_ID=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $name=stripslashes($values1[0]).": ";
      }
    if(isinGroup($values[0], $grader))
      $group = $values[0];
    else
      $group = PriGroup($grader);
    $name.=RankAbbrName($grader, $group, 1);
    }
  return $name;
}

function isinGroup($group, $person) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select EMG_ID From EH_Members_Groups Where Member_ID=$person AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    return true;
    }
  return false;
}

function GroupName($id) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Name From EH_Groups WHERE Group_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    return stripslashes($values[0]);
    }
}

function IDLine($pin, $group, $isprigroup) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Group_ID, Name, Abbr, MedalBrackets, MedalSeperator, MedalGroupBrackets, IDLineFormat, PositionSeparator, UnitSeparator From EH_Groups WHERE Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $mibracket = $values[3];
    $medalsep = $values[4];
    $mgbracket = $values[5];
    $idline = $values[6];
    $possep = $values[7];
    $unitsep = $values[8];
    }
  $query = "select EH_Members.Member_ID, EH_Members.Name, EH_Members_Ranks.Rank_ID, EH_Members_Units.Unit_ID, EH_Members_Units.UnitPosition From EH_Members, EH_Members_Ranks, EH_Members_Units WHERE EH_Members.Member_ID=$pin AND EH_Members.Member_ID=EH_Members_Ranks.Member_ID AND EH_Members_Ranks.Group_ID=$group AND EH_Members_Units.Member_ID=EH_Members.Member_ID AND EH_Members_Units.Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $rankid=$values[2];
    $unitid=$values[3];
    $unitpos=$values[4];
    $idline = str_replace("@R@", RankAbbr($rankid, 1), $idline);
    $idline = str_replace("@N@", stripslashes($values[1]), $idline);
    $pos = "";
    $query1 = "select Position_ID, isGroupPrimary FROM EH_Members_Positions WHERE Member_ID=$pin AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j=0; $j<$rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $pos .= $values1[0];
      if($j+1<$rows1)
        $pos.="-";
      if($values1[1])
        $pripos = $values1[0];
      }
    $idline = str_replace("@P@", PositionAbbrIDLine($pos, $possep, 1), $idline);
    $idline = str_replace("@U@", UnitsIDLine($unitid, $unitpos, $unitsep, $pripos), $idline);
    if($isprigroup || $group ==2) {
      $query1 = "SELECT SA_ID, Value FROM EH_Members_Special_Areas WHERE Member_ID=$pin Order By SA_ID";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      for($i=0; $i<$rows1; $i++) {
        $values1 = mysql_fetch_row($result1);
        $sa[$values1[0]] = stripslashes($values1[1]);
        }
      if($sa[1])
        $fchgpts = $sa[1];
      else
        $fchgpts = 0;
      if($sa[2])
        $cr=$sa[2];
      else
        $cr=0;
      if($fchgpts) {
        $fchgabbr = FCHGAbbr($fchgpts, 1);
        $idline = str_replace("@F@", "$fchgabbr", $idline);
        }
      else {
        $idline = str_replace("[@F@]", "", $idline);
        }
      if($cr) {
        $combatrating = CombatRating($cr);
        $idline = str_replace("@C@", "$combatrating", $idline);
        }
      else {
      $idline = str_replace("[@C@]", "", $idline);
        }
      }
    else {
      $idline = str_replace("[@F@]", "", $idline);
      $idline = str_replace("[@C@]", "", $idline);
      }
    }
  if($isprigroup) {
    $group = NGroups($group, $pin);
    }
  $idline = str_replace("@M@", Medals_ID_Line($pin, $group, $isprigroup, $medalsep, $mibracket, $mgbracket), $idline);
  $idline = str_replace("@T@", Training_ID_Line($pin, $group, $isprigroup), $idline);
  $idline = str_replace("  ", " ", $idline);
  return $idline;
  }

function UnitsIDLine($unitid, $unitpos, $unitsep, $pripos) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Position_ID, isCS, Base_ID, Group_ID From EH_Positions WHERE Position_ID=$pripos";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $unit = "";
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[1]!=0) {
      $unit = CSIDLine($values[0]);
      $unit.=$unitsep.Base($values[2]);
      }
    else {
      $unit = Units_ID_Line($unitid, $unitpos, $unitsep);
      $unit.=Unit_Base($unitid, $unitsep);
      }
    }
  return $unit;
  }

function CSIDLine($pos) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select EH_Positions.Position_ID, EH_Positions.isCS, EH_Positions.CSOrder, EH_Groups.CSAbbrL1, EH_Groups.CSAbbrL2, EH_Groups.CSAbbrL3 From EH_Positions, EH_Groups WHERE EH_Positions.Position_ID=$pos AND EH_Positions.Group_ID=EH_Groups.Group_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $unit = "";
  if($rows) {
    $values = mysql_fetch_row($result);
    if($values[1]==1) {
      $unit=$values[3]."-".$values[2];
      }
    if($values[1]==2) {
      $unit=$values[4]."-".$values[2];
      }
    if($values[1]==3) {
      $unit=$values[5]."-".$values[2];
      }
    }
  return stripslashes($unit);
  }

function Unit_Base($unit, $unitsep) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Base_ID From EH_Units WHERE Unit_ID=$unit";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $values = mysql_fetch_row($result);
  if($values[0]) {
    return $unitsep.Base($values[0]);
    }
  else
    return;
  }

function Units_ID_Line($unitid, $unitpos, $unitsep) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT, EH_Units_Types.DisplayMasterUnit From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$unitid AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $master = $values[2];
  $name = stripslashes($values[1]);
  if($values[5]==1)
    $name = $values[4]." ".$name;
  elseif($values[5]==2)
    $name = $name." ".$values[4];
  if($values[7]) {
    $query1 = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$master AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $values1 = mysql_fetch_row($result1);
    if($values[7]==1) {
      $name=stripslashes($values1[1])." ".$name;
      }
    elseif($values[7]==2)
      $name=$name." ".stripslashes($values1[1]);
    $master=$values1[2];
    }
  $returnval = "";
  if($values[2]==0) {
    $returnval = "<a href=\"unit.php?id=$values[0]\">$name";
    if($unitpos)
      $returnval.="-".$unitpos;
    $returnval.="</a>";
    }
  else {
    $returnval = "<a href=\"unit.php?id=$values[0]\">".$name;
    if($unitpos)
      $returnval.="-".$unitpos;
    $returnval.="</a>".$unitsep.UnitsRec_ID_Line($master, $unitsep);
    }
  return $returnval;
}

function UnitsRec_ID_Line($unitid, $unitsep) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT, EH_Units_Types.DisplayMasterUnit From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$unitid AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $master = $values[2];
  $name = stripslashes($values[1]);
  if($values[5]==1)
    $name = $values[4]." ".$name;
  elseif($values[5]==2)
    $name = $name." ".$values[4];
  if($values[7]) {
    $query1 = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$master AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $values1 = mysql_fetch_row($result1);
    if($values[7]==1) {
      $name=stripslashes($values1[1])." ".$name;
      }
    elseif($values[7]==2)
      $name=$name." ".stripslashes($values1[1]);
    $master=$values1[2];
    }
  $returnval = "";
  if($values[2]==0)
    $returnval = "<a href=\"unit.php?id=$values[0]\">$name</a>";
  else {
    $returnval = "<a href=\"unit.php?id=$values[0]\">".$name."</a>".$unitsep.UnitsRec_ID_Line($master, $unitsep);
    }
  return $returnval;
}

function Unit_Display($unit, $unitspos) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT, EH_Units_Types.DisplayMasterUnit From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$unit AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $master = $values[2];
  $name = stripslashes($values[1]);
  if($values[5]==1)
    $name = $values[4]." ".$name;
  elseif($values[5]==2)
    $name = $name." ".$values[4];
  if($values[7]) {
    $query1 = "select EH_Units.Unit_ID, EH_Units.Name, EH_Units.Master_ID, EH_Units.Base_ID, EH_Units_Types.Name, EH_Units_Types.PrefixPostfixType, EH_Units_Types.DisplayUT From EH_Units, EH_Units_Types WHERE EH_Units.Unit_ID=$master AND EH_Units.UT_ID=EH_Units_Types.UT_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $values1 = mysql_fetch_row($result1);
    if($values[7]==1) {
      $name=stripslashes($values1[1])." ".$name;
      }
    elseif($values[7]==2)
      $name=$name." ".stripslashes($values1[1]);
    $master=$values1[2];
    }
  $returnval = "";
  if($unitspos)
    $unitpos = $unitspos;
  else
    $unitpos=0;
  $returnval = "<a href=\"unit.php?id=$values[0]\">$name";
  if($unitpos)
    $returnval.="-".$unitpos;
  $returnval.="</a>";
  return $returnval;
  }

function STType($id) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Name From EH_SSType WHERE SSType_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    return stripslashes($values[0]);
    }
  else
    return;
  }

function Base($id) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Name, Types From EH_Bases WHERE Base_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    if($values[1])
      return VesselTypeAbbr($values[1], 1)." ".stripslashes($values[0]);
    else
      return stripslashes($values[0]);
    }
  else
    return;
  }

function VesselTypeAbbr($id, $dispayabbr) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Name, Abbr From EH_Ships WHERE Ship_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $values = mysql_fetch_row($result);
  $abbr="";
  if($dispayabbr)
    $abbr.="<abbr title=\"".stripslashes($values[0])."\">";
  $abbr.=stripslashes($values[1]);
  if($dispayabbr)
    $abbr.="</abbr>";
  return $abbr;
  }

function Training_ID_Line($pin, $group, $ispri) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  $query = "select TAc_ID, EntryBrackets, ExitBrackets, Seperator, DefaultNoCourse, Name From EH_Training_Academies WHERE Group_ID=$group Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $certs="";
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $nocert = "<abbr title=\"".stripslashes($values[5])."\">".stripslashes($values[1])."</abbr>".stripslashes($values[4]).stripslashes($values[2])."<br />\n";
    $certs.="<abbr title=\"".stripslashes($values[5])."\">".stripslashes($values[1])."</abbr>";
    $cat =0;
    $count=0;
    $query1 = "select TC_ID, IDLineGroup, Abbr, Name From EH_Training_Categories WHERE TCa_ID=$values[0] Order By SortOrder";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j=0; $j<$rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "select EH_Training.Abbr, EH_Training_Complete.CT_ID, EH_Training.Name From EH_Training, EH_Training_Complete WHERE EH_Training.TC_ID=$values1[0] AND EH_Training.Training_ID=EH_Training_Complete.Training_ID AND EH_Training_Complete.Member_ID=$pin Order By EH_Training.SortOrder";
      if($values1[1]==1)
        $query2 .= " DESC Limit 1";
      $result2 = mysql_query($query2, $mysql_link);
      $rows2 = mysql_num_rows($result2);
      for($k=0; $k<$rows2; $k++) {
        $values2 = mysql_fetch_row($result2);
        if($count==0 && strlen($values[1])>1)
          $certs.=":";
        $count++;
        if($values1[1]==0 || $values1[1]==1)
          $certs .="<abbr title=\"".stripslashes($values2[2])."\">".stripslashes($values2[0])."</abbr>".stripslashes($values[3]);
        elseif($values1[1]==3)
          $certs .="<abbr title=\"".stripslashes($values2[2])."\">".stripslashes($values1[2]).stripslashes($values2[0])."</abbr>".stripslashes($values[3]);
        elseif($values1[1]==2) {
          if($cat!=$values1[0]) {
            $certs.="<abbr title=\"".stripslashes($values1[3])."\">".stripslashes($values1[2])."</abbr>";
            }
          $certs .="<abbr title=\"".stripslashes($values2[2])."\">/".stripslashes($values2[0])."</abbr>";
          }
        $cat=$values1[0];
        }
      if($values1[1]==2 && $rows2)
        $certs.=$values[3];
      }
    if($count)
      $certs= substr($certs, 0, strlen($certs)-strlen($values[3]));
    if($count==0)
      $certs.=stripslashes($values[4]);
    $certs.=stripslashes($values[2]);
    $certs.="<br />\n";
    if($count==0) {
      $certs=str_replace($nocert, "", $certs);
      }
    }
  $certs = substr($certs, 0, strlen($certs)-strlen("<br />\n"));
  return $certs;
}

function NGroups($group, $pin) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "SELECT Group_ID FROM EH_Members_Groups WHERE Member_ID=$pin AND Active=1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($q=0; $q<$rows; $q++) {
    $values = mysql_fetch_row($result);
    $ngroups .= $values[0];
    if($q+1 < $rows)
      $ngroups.=";";
    }
  $ngroups = str_replace(";", " AND Group_ID!=", $ngroups);
  $mggroup =  $group;
  $query3 = "SELECT Group_ID FROM EH_Groups WHERE Group_ID!=$ngroups";
  $result3 = mysql_query($query3, $mysql_link);
  $rows3 = mysql_num_rows($result3);
  for($q=0; $q<$rows3; $q++) {
    $values3 = mysql_fetch_row($result3);
    $mggroup.=";$values3[0]";
    }
  return $mggroup;
  }

function Medals_ID_Line($pin, $group, $isprigroup, $sep, $indivbracket, $groupbracket) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  $mgid=0;
  $totcount =0;
  if(strlen($indivbracket))
    $indivbrackets = str_split($indivbracket, strlen($indivbracket)/2);
  if(strlen($groupbracket))
    $groupbrackets = str_split($groupbracket, strlen($groupbracket)/2);
  $query = "select Medal_ID, Name, Abbr, MG_ID, MT_ID, ShowOnID, Image From EH_Medals Where Group_ID=$group Order By SortOrder, MG_ID, Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "select MC_ID From EH_Medals_Complete Where Medal_ID=$values[0] AND Member_ID=$pin AND Status=1";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    $totcount+=$rows1;
    if($rows1) {
      if($mgid!=0 && $mgid==$values[3]) {
        $medals=substr($medals, 0, strlen($medals)-strlen($indivbrackets[1])-strlen($sep));
        }
      switch($values[4]) {
        case 1:
        //Regular medals, just xnumber
        if(strlen($indivbracket))
          $medals.=$indivbrackets[0];
        $medals.="<abbr title=\"".stripslashes($values[1])."\">".stripslashes($values[2])."</abbr>";
        if($rows1>1)
          $medals .= "x$rows1";
        if(strlen($indivbracket))
          $medals.=$indivbrackets[1];
        $medals.=$sep;
        break;
        case 2:
        //Grouped Medals Group Abbr - 
        if($mgid!=$values[3]) {
          $mgid=$values[3];
          $query2 = "select Abbr, Name From EH_Medals_Groups Where MG_ID=$mgid";
          $result2 = mysql_query($query2, $mysql_link);
          $values2 = mysql_fetch_row($result2);
          if(strlen($indivbracket))
            $medals.=$indivbrackets[0];
          $medals.="<abbr title=\"".stripslashes($values2[1])."\">".stripslashes($values2[0])."</abbr>-<abbr title=\"".stripslashes($values2[1])." - ".stripslashes($values[1])."\">".stripslashes($values[2])."</abbr>";
          if($rows1>1)
            $medals.="x$rows1";
          if(strlen($indivbracket))
            $medals.=$indivbrackets[1];
          $medals.=$sep;
          }
        else {
          $query2 = "select Name From EH_Medals_Groups Where MG_ID=$mgid";
          $result2 = mysql_query($query2, $mysql_link);
          $values2 = mysql_fetch_row($result2);
          $medals.="-<abbr title=\"".stripslashes($values2[0])." - ".stripslashes($values[1])."\">".stripslashes($values[2])."</abbr>";
          if($rows1>1)
            $medals.="x$rows1";
          if(strlen($indivbracket))
            $medals.=$indivbrackets[1];
          $medals.=$sep;
          }
        break;
        case 3:
        //Upgrades
        $query2 = "select Abbr, Name From EH_Medals_Upgrades Where Medal_ID=$values[0] AND Upper>$rows1 AND Lower <=$rows1";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        $values2 = mysql_fetch_row($result2);
        if($rows2) {
          if(strlen($indivbracket))
            $medals.=$indivbrackets[0];
          $medals.="<abbr title=\"".stripslashes($values2[1])."\">".stripslashes($values2[0])."</abbr>";
          if(strlen($indivbracket))
            $medals.=$indivbrackets[1];
          $medals.=$sep;
          }
        else {
          if(strlen($indivbracket))
            $medals.=$indivbrackets[0];
          $medals.="<abbr title=\"".stripslashes($values[1])."\">".stripslashes($values[2])."</abbr>";
          if(strlen($indivbracket))
            $medals.=$indivbrackets[1];
          $medals.=$sep;
          }
        break;
        case 4:
        //Recursive Upgrades
        $count = $rows1;
        $query2 = "select Upper, Abbr, Name From EH_Medals_Upgrades Where Medal_ID=$values[0] Order By Upper DESC Limit 1";
        $result2 = mysql_query($query2, $mysql_link);
        $values2 = mysql_fetch_row($result2);
        $maxlim = $values2[0]-1;
        while($count>$maxlim) {
          if(strlen($indivbracket))
            $medals.=$indivbrackets[0];
          $medals.="<abbr title=\"".stripslashes($values2[2])."\">".stripslashes($values2[1])."</abbr>";
          if(strlen($indivbracket))
            $medals.=$indivbrackets[1];
          $medals.=$sep;
          $count -=$maxlim;
          }
        $query2 = "select Abbr, Name From EH_Medals_Upgrades Where Medal_ID=$values[0] AND Upper>$count AND Lower <=$count";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        $values2 = mysql_fetch_row($result2);
        if(strlen($indivbracket))
          $medals.=$indivbrackets[0];
        if($rows2)
          $medals.="<abbr title=\"".stripslashes($values2[1])."\">".stripslashes($values2[0])."</abbr>";
        else
          $medals.="<abbr title=\"".stripslashes($values[1])."\">".stripslashes($values[2])."</abbr>";
        if(strlen($indivbracket))
          $medals.=$indivbrackets[1];
        $medals.=$sep;
        break;
        }
      }
    }
  $medals = substr($medals, 0, strlen($medals)-strlen($sep));
  if(strlen($groupbracket))
    $medalsret .=$groupbrackets[0];
  $medalsret .=$medals;
  if(strlen($groupbracket))
    $medalsret .=$groupbrackets[1];
  return $medalsret;
  }

function PositionName($positions, $sep) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $position = str_replace("-", " OR Position_ID=", $positions);
  $query = "select Name From EH_Positions WHERE (Position_ID=$position) Order By SortOrder DESC";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $abbr = "";
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $abbr .= stripslashes($values[0]);
    if($i+1<$rows)
      $abbr.=$sep;
    }
  return $abbr;
}

function PositionAbbrIDLine($positions, $sep, $showabbr) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $position = str_replace("-", " OR Position_ID=", $positions);
  $query = "select Abbr, Name From EH_Positions WHERE (Position_ID=$position) Order By SortOrder DESC";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $abbr = "";
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    if($showabbr)
      $abbr .= "<abbr title=\"".stripslashes($values[1])."\">";
    $abbr .= stripslashes($values[0]);
    if($showabbr)
      $abbr .="</abbr>";
    if($i+1<$rows)
      $abbr.=$sep;
    }
  return $abbr;
}

function CombatRating($value) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $abbr = "";
  $query = "select Name From EH_Combat_Ratings Where Points<=$value ORDER By Points DESC LIMIT 1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $abbr = stripslashes($values[0]);
    }
  return $abbr;
  }


function FCHGImage($value) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $abbr = "";
  $query = "select Image, Name From EH_FCHG Where Points<=$value ORDER By Points DESC LIMIT 1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $abbr .= "<img src=\"images/fchg/".stripslashes($values[0])."\" alt=\"".stripslashes($values[1])."\" width=\"180\" height=\"68\" />";
    }
  return $abbr;
  }


function FCHGName($value) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $abbr = "";
  $query = "select Name From EH_FCHG Where Points<=$value ORDER By Points DESC LIMIT 1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $abbr .= stripslashes($values[0]);
    }
  return $abbr;
  }


function FCHGAbbr($value, $showabbr) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $abbr = "";
  $query = "select Abbr, Name From EH_FCHG Where Points<=$value ORDER By Points DESC LIMIT 1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    if($showabbr)
      $abbr .="<abbr title=\"".stripslashes($values[1])."\">";
    $abbr .= stripslashes($values[0]);
    if($showabbr)
      $abbr .="</abbr>";
    }
  return $abbr;
  }


function RankAbbr($id, $showabbr) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $abbr = "";
  $query = "select Abbr, Name From EH_Ranks Where Rank_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    if($showabbr)
      $abbr .="<abbr title=\"".stripslashes($values[1])."\">";
    $abbr .= stripslashes($values[0]);
    if($showabbr)
      $abbr .="</abbr>";
    }
  return $abbr;
  }


function RankName($id) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Name From EH_Ranks Where Rank_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $abbr = stripslashes($values[0]);
    }
  return $abbr;
  }


function RankType($rankid) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $abbr = "";
  $query = "select EH_Ranks_Types.Name From EH_Ranks_Types, EH_Ranks Where EH_Ranks.Rank_ID=$rankid AND EH_Ranks.RT_ID=EH_Ranks_Types.RT_ID";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $abbr .= stripslashes($values[0]);
    }
  return $abbr;
  }


function GroupSplit($value, $group) {
  //returns the id for a $group in $value
  $id = 0;
  $list = explode(";", $value);
  for($i=0; $i<count($list);$i++) {
    $list[$i] = explode(":", $list[$i]);
    if($list[$i][0]==$group) {
      $id=$list[$i][1];
      break;
      }
    }
  return $id;
  }

function RankAbbrName($pin, $group, $showabbr) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $rankname = "";
  $query = "select Name From EH_Members Where Member_ID=$pin";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "select Rank_ID From EH_Members_Ranks Where Member_ID=$pin AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $rankname = RankAbbr($values1[0], $showabbr) . " ".stripslashes($values[0]);
      }
    else
      $rankname = stripslashes($values[0]);
    }
  return $rankname;
  }

function AcademyName($id) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  $query = "select Name From EH_Training_Academies Where TAc_ID=$id";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $name="";
  if($rows) {
    $values = mysql_fetch_row($result);
    $name = stripslashes($values[0]);
    }
  return $name;
  }


function MedalsListingDisplay($pin, $group) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  $mgid=0;
  $totcount =0;
  $query = "select Medal_ID, Name, Abbr, MG_ID, MT_ID, ShowOnID, Image, Group_ID From EH_Medals Where Group_ID=$group Order By SortOrder, MG_ID, Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "select MC_ID, Awarder_ID, DateAwarded, Reason, Group_ID From EH_Medals_Complete Where Medal_ID=$values[0] AND Member_ID=$pin AND Status=1";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    $totcount+=$rows1;
    if($rows1) {
      switch($values[4]) {
        case 1:
        //Regular medals, just xnumber
        echo stripslashes($values[1]);
        if($rows1>1)
          echo " x$rows1";
        break;
        case 2:
        //Grouped Medals Group Abbr - 
        if($mgid!=$values[3]) {
          $mgid=$values[3];
          $query2 = "select Name From EH_Medals_Groups Where MG_ID=$mgid";
          $result2 = mysql_query($query2, $mysql_link);
          $values2 = mysql_fetch_row($result2);
          echo stripslashes($values2[0])."<br />";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes($values[1]);
          if($rows1>1)
            echo " x$rows1";
          }
        else {
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes($values[1]);
          if($rows1>1)
            echo " x$rows1";
          }
        break;
        case 3:
        //Upgrades
        $query2 = "select Name From EH_Medals_Upgrades Where Medal_ID=$values[0] AND Upper>$rows1 AND Lower <=$rows1";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        $values2 = mysql_fetch_row($result2);
        if($rows2) {
          echo stripslashes($values2[0])." ($rows1 awards)";
          }
        else {
          echo stripslashes($values[1]);
          if($rows1>1)
            echo " ($rows1 awards)";
          }
        break;
        case 4:
        //Recursive Upgrades
        $count = $rows1;
        $query2 = "select Upper, Name From EH_Medals_Upgrades Where Medal_ID=$values[0] Order By Upper DESC Limit 1";
        $result2 = mysql_query($query2, $mysql_link);
        $values2 = mysql_fetch_row($result2);
        $maxlim = $values2[0]-1;
        while($count>$maxlim) {
          echo stripslashes($values2[1])."<br />\n";
          $count -=$maxlim;
          }
        $query2 = "select Name From EH_Medals_Upgrades Where Medal_ID=$values[0] AND Upper>$count AND Lower <=$count";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        $values2 = mysql_fetch_row($result2);
        if($rows2)
          echo stripslashes($values2[0]);
        else
          echo stripslashes($values[1]);
        break;
        }
      echo "<br />\n";
      }
    }
  if($totcount==0) {
    $rankname = RankAbbrName($pin, $group, 1);
    echo "$rankname has not earned any medals yet.";
    }
}

function TrainingListingDisplay($pin, $group) {
  global $db_host, $db_name, $db_username, $db_password, $mysql_link;
if(!isset($mysql_link)) {
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    }
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  $totcount =0;
  $disp="";
  $query = "select TAc_ID, Name From EH_Training_Academies Where Group_ID=$group Order By SortOrder";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $disp .= "<p>".stripslashes($values[1])." Courses:<br />\n";
    $dispno="<p>".stripslashes($values[1])." Courses:<br />\n</p>\n";
    $query1 = "select EH_Training.Name, EH_Training_Complete.DateComplete, EH_Training_Complete.Score, EH_Training.Ribbon, EH_Training_Complete.CT_ID From EH_Training, EH_Training_Complete WHERE EH_Training_Complete.Member_ID=$pin AND EH_Training_Complete.Training_ID=EH_Training.Training_ID AND EH_Training.TAc_ID=$values[0] Order By EH_Training_Complete.DateComplete, EH_Training.Training_ID";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    for($k=0; $k<$rows1; $k++) {
      $values1 = mysql_fetch_row($result1);
      $totcount++;
      $disp.= stripslashes($values1[0]);
      if($values1[1])
        $disp.= " Completed on ". date("F j, Y", $values1[1]);
      if($values1[2])
        $disp.= " | Score: $values1[2]";
      $disp.= "<br />\n";
      }
    if($totcount==0) {
      if(strlen($group)<5) {
        $rankname = RankAbbrName($pin, $group, 1);
        $disp.= "$rankname has not completed training in this academy.";
        }
      }
    $disp.= "</p>\n";
    if($totcount==0) {
      $disp=str_replace($dispno, "", $disp);
      }
    $totcount=0;
    }
  echo $disp;
  }

?>
