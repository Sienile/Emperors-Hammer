<?
include_once("config.php");
include_once("functions.php");

class People {
  function __construct($person) {
    $id=$person;
    }

  public function CoC($group) {
    global $db_host, $db_name, $db_username, $db_password;
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
    $coc=array();
    if($this-id) {
      $query = "SELECT Max(EH_Positions.SortOrder), EH_Members_Units.Unit_ID FROM EH_Members_Positions, EH_Members_Units, EH_Positions, EH_Units WHERE EH_Members_Positions.Position_ID=EH_Positions.Position_ID AND EH_Members_Units.Member_ID=$member AND EH_Members_Positions.Member_ID=".$this-id." AND EH_Members_Positions.Group_ID=$group AND EH_Members_Units.Group_ID=$group AND EH_Units.Unit_ID=EH_Members_Units.Unit_ID";
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

  private function CoCRecursive($group, $so, $unit) {
    global $db_host, $db_name, $db_username, $db_password;
    $mysql_link = mysql_connect($db_host, $db_username, $db_password);
    mysql_select_db($db_name, $mysql_link);
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

function AccessGroups($id, $page) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  if (!isset($_SESSION["EHID"])){ 
      return false;
  }
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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

function PriGroup($pin) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  $query = "select Group_ID From EH_Members_Groups WHERE Member_ID=$pin AND isPrimary=1";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    return $values[0];
    }
}

function isinGroup($group, $person) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  $query = "select EMG_ID From EH_Members_Groups Where Member_ID=$person AND Group_ID=$group";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    return true;
    }
  return false;
}

function Chats($pin) {
  //format of chat info: chatid:username;chatid2:username2;
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  $cht="";
  $query = "select EH_ChatSystems.Name, EH_ChatSystems.Abbr, EH_ChatSystems.Image, EH_ChatSystems.LinkFormat, EH_Members_ChatProfile.Chat_Handle From EH_ChatSystems, EH_Members_ChatProfile WHERE EH_ChatSystems.Chat_ID=EH_Members_ChatProfile.Chat_ID AND EH_Members_ChatProfile.Member_ID=$pin Order By EH_ChatSystems.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $link = str_replace("[username]", $values[4], $values[3]);
    $cht .= "&nbsp;&nbsp;&nbsp;&nbsp;";
    if($values[2])
      $cht.= "<img src=\"images/Icons/".stripslashes($values[2])."\" alt=\"$values[1]\"/>";
    if($values[3])
      $cht.= "<a href=\"$link\"><abbr title=\"".stripslashes($values[0])."\">".stripslashes($values[1])."</abbr></a><br />\n";
    else
      $cht.= "<abbr title=\"".stripslashes($values[0])."\">".stripslashes($values[1])."</abbr>: ".stripslashes($values[4])."<br />\n";
    }
  if($cht=="")
    $cht = "&nbsp;&nbsp;&nbsp;&nbsp;No Chat Systems selected<br />\n";
  return $cht;
}


function Platforms($pin) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  $plt="";
  $query = "select EH_Platforms.Name From EH_Platforms, EH_Members_Platforms WHERE EH_Platforms.Platform_ID=EH_Members_Platforms.Platform_ID AND EH_Members_Platforms.Member_ID=$pin Order By EH_Platforms.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $plt .= "&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes($values[0])."<br />\n";
    }
  if($plt=="")
    $plt = "&nbsp;&nbsp;&nbsp;&nbsp;No Platforms Selected";
  return $plt;
}

function Skills($pin) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  $plt="";
  $query = "select EH_Skills.Name, EH_Members_Skills.SkillLevel From EH_Skills, EH_Members_Skills WHERE EH_Skills.Skill_ID=EH_Members_Skills.Skill_ID AND EH_Members_Skills.Member_ID=$pin Order By EH_Skills.Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    $plt .= "&nbsp;&nbsp;&nbsp;&nbsp;Skill: ".stripslashes($values[0])." At Level: $values[1]<br />\n";
    }
  if($plt=="")
    $plt = "&nbsp;&nbsp;&nbsp;&nbsp;No Skills Selected";
  return $plt;
}

function IDLine($pin, $group, $isprigroup) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  $query = "select Member_ID, Name From EH_Members WHERE Member_ID=$pin";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "select Rank_ID From EH_Members_Ranks WHERE Member_ID=$pin AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $rankid=$values1[0];
      }
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
    $query1 = "select Unit_ID, UnitPosition From EH_Members_Units WHERE Member_ID=$pin AND Group_ID=$group";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $unitid=$values1[0];
      $unitpos = $values1[1];
      }
    $idline = str_replace("@U@", UnitsIDLine($unitid, $unitpos, $unitsep, $pripos), $idline);
    if($isprigroup || $group ==2) {
      $query1 = "select Value From EH_Members_Special_Areas WHERE Member_ID=$pin AND SA_ID=1";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      $fchgpts=0;
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $fchgpts = $values1[0];
        }
      if($fchgpts) {
        $fchgabbr = FCHGAbbr($fchgpts, 1);
        $idline = str_replace("@F@", "$fchgabbr", $idline);
        }
      else {
        $idline = str_replace("[@F@]", "", $idline);
        }
      $query1 = "select Value From EH_Members_Special_Areas WHERE Member_ID=$pin AND SA_ID=2";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      $crpts=0;
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $crpts = $values1[0];
        }
      if($crpts) {
        $combatrating = CombatRating($crpts);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  $query = "select Position_ID, isCS, CSOrder, Group_ID From EH_Positions WHERE Position_ID=$pos";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  $unit = "";
  if($rows) {
    $values = mysql_fetch_row($result);
    $query1 = "select CSAbbrL1, CSAbbrL2, CSAbbrL3 From EH_Groups Where Group_ID=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      if($values[1]==1) {
        $unit=$values1[0]."-".$values[2];
        }
      if($values[1]==2) {
        $unit=$values1[1]."-".$values[2];
        }
      if($values[1]==3) {
        $unit=$values1[2]."-".$values[2];
        }
      }
    }
  return stripslashes($unit);
  }

function Unit_Base($unit, $unitsep) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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

function Base($id) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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

function RankAbbr($id, $showabbr) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
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
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  mysql_select_db($db_name, $mysql_link);
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
    $name=$abbr="";
    if($rows1) {
      if($values[3]) {
        $query2 = "select Name, Abbr From EH_Medals_Groups Where MG_ID=$values[3]";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        if($rows2) {
          $values2 = mysql_fetch_row($result2);
          $name=$values2[0]." ";
          $abbr=$values2[1]."-";
          }
        }
      $name.=stripslashes($values[1]);
      $abbr.=stripslashes($values[2]);
      $img=stripslashes($values[6]);
      echo "<script type=\"text/javascript\">\n
<!--
$(document).ready(function() {
$(\"#medal$values[0]$values[7]\").wTooltip({ style: { background: \"#000000\", color: \"#686a6b\" },content: \"<table><tr><td colspan=\\'2\\'>$name ($abbr)</td></tr><tr>";
if($img)
  echo "<td style=\\'vertical-align:middle; text-align: left;\\'><img src=\\'images/medals/$img\\' alt=\\'$name\\' /></td><td>";
else
  echo "<td colspan=\\'2\\'>";
for($j=0; $j<$rows1; $j++) {
  $values1 = mysql_fetch_row($result1);
  echo "<p>";
  if($values1[1])
    echo "Awarded By: ".addslashes(RankAbbrName($values1[1], $values1[4], 0)) ." ";
  if($values1[2])
    echo "Awarded On: ".date("M j, Y", $values1[2]) ." ";
  if($values1[3])
    echo "Awarded For: ".addslashes(stripslashes(nl2br(rtrim($values1[3]))));
  echo "</p>";
  }
echo "</td></tr></table>\" }); 
});
//-->
</script>\n";
      echo "<span id=\"medal$values[0]$values[7]\">";
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
      echo "</span>";
      echo "<br />\n";
      }
    }
  if($totcount==0) {
    $rankname = RankAbbrName($pin, $group, 1);
    echo "$rankname has not earned any medals yet.";
    }
}

function TrainingListingDisplay($pin, $group) {
  global $db_host, $db_name, $db_username, $db_password;
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  if(!is_int($group))
    $group = str_replace(";", " OR Group_ID=", $group);
  mysql_select_db($db_name, $mysql_link);
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
