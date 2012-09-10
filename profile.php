<?
$time_start=microtime(true);
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$pin = mysql_real_escape_string($_GET['pin'], $mysql_link);
$query = "SELECT Member_ID, Name, Email, Quote, URL FROM EH_Members WHERE Member_ID=$pin";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows==0)
  $pin =0;
echo "<div align=\"right\">";
if($rows && $_SESSION['EHID']) {
  echo "<a href=\"medalrec.php?memberid=$pin\">Recommend Medal</a>";
  if(has_access($_SESSION['EHID'], "awardmedal"))
    echo " | <a href=\"medalaward.php?memberid=$pin\">Award Medal</a>";
  echo " | ";
  echo "<a href=\"promorec.php?memberid=$pin\">Recommend Promotion</a>";
  if(has_access($_SESSION['EHID'], "awardpromo"))
    echo " | <a href=\"promoaward.php?memberid=$pin\">Award Promotion</a>";
  echo " | ";
  }
echo "<form method=\"get\" action=\"profile.php\">\nPIN #: <input type=\"text\" name=\"pin\" style=\"width: 30px\" value=\"$pin\" />\n</form>\n";
echo "</div>\n";
if($rows) {
  $values = mysql_fetch_row($result);
  $query1 = "SELECT SA_ID, Value FROM EH_Members_Special_Areas WHERE Member_ID=$values[0] Order By SA_ID";
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
  if($sa[3])
    $st=$sa[3];
  else
    $st=0;
?>
<div id="ehtabs">
  <ul>
    <li><a href="#tabsMain">Main</a></li>
    <?
$queryg = "SELECT EH_Groups.Group_ID, EH_Groups.Abbr, EH_Groups.Name, EH_Groups.ProfileTabs, EH_Groups.RankTypeDisplayName, EH_Groups.UniType, EH_Members_Groups.isPrimary, EH_Members_Groups.JoinDate, EH_Members_Ranks.Rank_ID, EH_Members_Ranks.PromotionDate, EH_Members_Units.Unit_ID, EH_Members_Units.UnitPosition, Group_Concat( EH_Members_Positions.Position_ID SEPARATOR '-' ) FROM EH_Groups, EH_Members_Groups, EH_Members_Ranks, EH_Members_Units, EH_Members_Positions WHERE EH_Members_Groups.Group_ID = EH_Groups.Group_ID AND EH_Members_Groups.Member_ID =$values[0] AND EH_Members_Groups.Active =1 AND EH_Members_Ranks.Member_ID = EH_Members_Groups.Member_ID AND EH_Members_Ranks.Group_ID = EH_Members_Groups.Group_ID AND EH_Members_Units.Member_ID = EH_Members_Groups.Member_ID AND EH_Members_Units.Group_ID = EH_Members_Groups.Group_ID AND EH_Members_Positions.Member_ID = EH_Members_Groups.Member_ID AND EH_Members_Positions.Group_ID = EH_Members_Groups.Group_ID GROUP BY EH_Groups.Group_ID";
$resultg = mysql_query($queryg, $mysql_link);
$rowsg = mysql_num_rows($resultg);
for($i=0; $i<$rowsg; $i++) {
  $valuesg = mysql_fetch_row($resultg);
  echo "    <li><a href=\"#tabs$valuesg[1]\">".stripslashes($valuesg[2])." Profile</a></li>\n";
  }
    ?>
    <li><a href="#tabsPlt">Platforms</a></li>

    <li><a href="#tabsSkill">Skills</a></li>
    <li><a href="#tabsFic">Fiction</a></li>
    <li><a href="#tabsImg">Images</a></li>
    <li><a href="#tabsCR">Combat Record</a></li>

  </ul>
<?
flush();
?>
  <div id="tabsMain">
    <p>Name: <?=stripslashes($values[1])?><br />
  E-Mail: <img src="emailimg.php?id=<? echo $values[0]; ?>" alt="Member's E-mail Address" border="0" /><br />
  Quote: <? if($values[3]) echo stripslashes($values[3]); ?><br />
  Chat Systems:<br />
  <?=Chats($values[0]);?>
  <? if($values[4]) 
    echo "  Homepage: <a href=\"".stripslashes($values[4])."\">".stripslashes($values[4])."</a>";
  ?></p>
  </div>
  <?
  mysql_data_seek($resultg, 0);
  for($i=0; $i<$rowsg; $i++) {
    $values1 = mysql_fetch_row($resultg);
    echo "  <div id=\"tabs$values1[1]\">\n";
    echo "<div id=\"tabsgroup$values1[1]\">\n"; // Begin Group Container
    $gttabs = str_replace(";", " OR GT_ID=", $values1[3]);
    echo "      <ul>\n";
    $query2 = "SELECT Name, GT_ID FROM EH_Groups_Tabs WHERE GT_ID=$gttabs Order By SortOrder";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    for($j=0; $j<$rows2; $j++) {
      $values2 = mysql_fetch_row($result2);
      echo "        <li><a href=\"#$values1[1]$values2[1]\">".stripslashes($values1[1])." ".stripslashes($values2[0])."</a></li>\n";
      }
    echo "      </ul>\n";
    mysql_data_seek($result2, 0);
    for($j=0; $j<$rows2; $j++) {
      $values2 = mysql_fetch_row($result2);
      echo "      <div id=\"$values1[1]$values2[1]\">\n";
      if($values1[6]==1)
        $pri = true;
      else
        $pri=false;
      $grname = RankAbbrName($pin, $values1[0], 1);
      $grnamen = RankAbbrName($pin, $values1[0], 0);
      $groupnameheader ="$grname's ".stripslashes($values1[1]);
      if($values2[1]==1) {
        echo "<p><b>$groupnameheader Group Information</b></p>";
        //GT_ID=1 = Group Info
        //Info to include:
        echo "<p>ID Line:<br />\n";
        echo IDLine($values[0], $values1[0], $pri)."<br />\n";
        $rankid = $values1[8];
        $rankdate = $values1[9];
        $rankname = RankName($rankid);
        echo "Group Rank: $rankname<br />";
        $posid = $values1[12];
        $posname = PositionName($posid, "<br />\n");
        echo "Group Position(s): $posname<br />";
        $unit= $values1[10];
        $unitpos= $values1[11];
        $unit = Unit_Display($unit, $unitpos);
        echo "Group Unit Position: $unit<br />";
        if($values1[7])
          echo "Group Join Date: ".date("M j, Y", $values1[7])."<br />";
        if($rankdate)
          echo "Group Last Promotion Date: ".date("M j, Y", $rankdate)."<br />";
        if($values1[4]!="") {
          echo stripslashes($values1[4]).": ".RankType($rankid)."<br />\n";
          }
        if($values1[0]==2 || $pri) {
          if($fchgpts)
            echo "FCHG Ranking: ".FCHGName($fchgpts)." ($fchgpts Points)<br />\n";
          else
            echo "FCHG Ranking: None<br />\n";
          if($cr)
            echo "Combat Rating: ".CombatRating($cr)." ($cr Points)<br />\n";
          else
            echo "Combat Rating: None<br />\n";
          }
        if($values1[0]==6) {
          if($st)
            echo "Stormtrooper Type: ".STType($st)."<br />";
          }
        echo "</p>\n";
        } // End GT_ID=1
      if($values2[1]==2) {
        echo "<p><b>$groupnameheader Medals</b></p>";
        //Medals
        if($pri) {
          $mggroup = NGroups($values1[0], $values[0]);
          }
        else 
          $mggroup = $values1[0];
        echo "<p>";
        MedalsListingDisplay($pin, $mggroup);
        echo "</p>\n";
        } // End GT_ID=2
      if($values2[1]==3) {
        echo "<p><b>$groupnameheader Competitions Participated in</b></p>";
        $ct = 0;
        $query3 = "SELECT EH_Competitions.Comp_ID, EH_Competitions.Name, EH_Competitions_Participants.Score FROM EH_Competitions, EH_Competitions_Participants WHERE EH_Competitions.Group_ID =$values1[0] AND EH_Competitions.Comp_ID = EH_Competitions_Participants.Comp_ID AND EH_Competitions_Participants.Member_ID =$values[0] ORDER BY EH_Competitions.StartDate";
        $result3 = mysql_query($query3, $mysql_link);
        $rows3 = mysql_num_rows($result3);
        for($k=0; $k<$rows3; $k++) {
          $values3 = mysql_fetch_row($result3);
          echo "<a href=\"compsstats.php?id=$values3[0]\">".stripslashes($values3[1])."</a>";
          if($values3[2])
              echo " Score of $values3[2]";
          echo "<br />\n";
          }
        if($rows3==0)
          echo $grname." has not participated in any ".stripslashes($values1[1])." Competitions";
        } // End GT_ID=3
      if($values2[1]==4) {
        echo "<p><b>$groupnameheader Training Completed</b></p>";
        //Medals
        if($pri) {
          $mggroup = NGroups($values1[0], $values[0]);
          }
        else 
          $mggroup = $values1[0];
        echo "<p>";
        TrainingListingDisplay($pin, $mggroup);
        echo "</p>\n";
        }// End GT_ID=4
      if($values2[1]==5) {
      //GT_ID=5 = INPR
        }// End GT_ID=5
      if($values2[1]==6) {
        echo "<p><b>$groupnameheader Uniform</b></p>";
          $img="";
        if($values1[5]==1) {
          //upload
          $query3 = "SELECT Filename FROM EH_Members_Uniforms WHERE Member_ID=$values[0] AND Group_ID=$values1[0]";
          $result3 = mysql_query($query3, $mysql_link);
          $rows3 = mysql_num_rows($result3);
          for($w=0; $w<$rows3; $w++) {
            $values3 = mysql_fetch_row($result3);
            $img= $values3[0];
            }
          echo "<img src=\"images/uniforms/uploaded/$img\" alt=\"$grnamen's Uniform\" />";
          }
        elseif($values1[5]==2) {
          //Assembled
          }
        elseif($values1[5]==3) {
          //Rank Based
          $query3 = "SELECT UniformRankBased FROM EH_Ranks WHERE Rank_ID=$rankid";
          $result3 = mysql_query($query3, $mysql_link);
          $rows3 = mysql_num_rows($result3);
          if($rows3) {
            $values3 = mysql_fetch_row($result3);
            $img = $values3[0];
            echo "<img src=\"images/uniforms/rankbased/$img\" alt=\"$grnamen's Uniform\" />";
            }
          }
        } //End GT_ID=6
      if($values2[1]==7) {
        echo "<p><b>$groupnameheader History</b></p>";
        $query3 = "SELECT History_Type, MemberChange, Reason, Occured From EH_Members_History WHERE Member_ID=$values[0] AND Group_ID=$values1[0] Order By Occured";
        $result3 = mysql_query($query3, $mysql_link);
        $rows3 = mysql_num_rows($result3);
        if($rows3==0)
          echo "No history stored.";
        for($w=0; $w<$rows3; $w++) {
          $values3 = mysql_fetch_row($result3);
          $vals = explode("-", $values3[1]);
          if($values3[0]==1) {
            echo "<p>Rank Change from ";
            $query4 = "SELECT Name From EH_Ranks WHERE Rank_ID=$vals[0]";
            $result4 = mysql_query($query4, $mysql_link);
            $rows4 = mysql_num_rows($result4);
            if($rows4) {
              $values4 = mysql_fetch_row($result4);
              echo stripslashes($values4[0]);
              }
            else {
              echo "No previous rank";
              }
            echo " to ";
            $query4 = "SELECT Name From EH_Ranks WHERE Rank_ID=$vals[1]";
            $result4 = mysql_query($query4, $mysql_link);
            $rows4 = mysql_num_rows($result4);
            if($rows4) {
              $values4 = mysql_fetch_row($result4);
              echo stripslashes($values4[0]);
              }
            echo "<br />\n";
            }
          elseif($values3[0]==2) {
            echo "<p>Unit Transfer:";
            $query4 = "SELECT Name, Unit_ID From EH_Units WHERE Unit_ID=$vals[0]";
            $result4 = mysql_query($query4, $mysql_link);
            $rows4 = mysql_num_rows($result4);
            if($rows4) {
              $values4 = mysql_fetch_row($result4);
              echo "<a href=\"unit.php?id=$values4[1]\">".stripslashes($values4[0])."</a>";
              }
            else {
              echo "No previous unit";
              }
            echo " to ";
            $query4 = "SELECT Name, Unit_ID From EH_Units WHERE Unit_ID=$vals[1]";
            $result4 = mysql_query($query4, $mysql_link);
            $rows4 = mysql_num_rows($result4);
            if($rows4) {
              $values4 = mysql_fetch_row($result4);
              echo "<a href=\"unit.php?id=$values4[1]\">".stripslashes($values4[0])."</a>";
              }
            else {
              echo "Left the unit";
              }
            echo "<br />\n";
            }
          elseif($values3[0]==3) {
            echo "<p>Position Transfer:";
            $query4 = "SELECT Name From EH_Positions WHERE Position_ID=$vals[0]";
            $result4 = mysql_query($query4, $mysql_link);
            $rows4 = mysql_num_rows($result4);
            if($rows4) {
              $values4 = mysql_fetch_row($result4);
              echo stripslashes($values4[0]);
              }
            else {
              echo "No previous position";
              }
            echo " to ";
            $query4 = "SELECT Name From EH_Positions WHERE Position_ID=$vals[1]";
            $result4 = mysql_query($query4, $mysql_link);
            $rows4 = mysql_num_rows($result4);
            if($rows4) {
              $values4 = mysql_fetch_row($result4);
              echo stripslashes($values4[0]);
              }
            else {
              echo "left the  position";
              }
            echo "<br />\n";
            }
            echo "Reason: ".stripslashes($values3[2])."<br />\n";
            echo "Occured on: ".date("F j, Y", $values3[3])."</p>\n";
          }
        } // End GT_ID=7
      if($values2[1]==8) {
        echo "<p><b>$groupnameheader Items</b></p>";
        //$values[0] = Member, $values1[0] = Group
        $query3 = "SELECT EH_Items.Name FROM EH_Items, EH_Members_Items WHERE EH_Members_Items.Member_ID=$values[0] AND EH_Members_Items.Group_ID=$values1[0] AND EH_Members_Items.Status=1 AND EH_Members_Items.Item_ID=EH_Items.Item_ID";
        $result3 = mysql_query($query3, $mysql_link);
        $rows3 = mysql_num_rows($result3);
        if($rows3==0)
          echo "No items owned.";
        for($w=0; $w<$rows3; $w++) {
          $values3 = mysql_fetch_row($result3);
          echo stripslashes($values3[0])."<br />";
          }
        } // End GT_ID=8
      echo "      </div>\n";
      flush();
      }
    echo "    </div>\n"; // End Group Container
    echo "  </div>\n"; //End Group Main Tab
    }
?>
  <div id="tabsPlt">
<p><b><?=stripslashes($values[1])?>'s Platforms</b></p>
    <p><?
  $query1 = "select EH_Platforms.Name From EH_Platforms, EH_Members_Platforms WHERE EH_Platforms.Platform_ID=EH_Members_Platforms.Platform_ID AND EH_Members_Platforms.Member_ID=$values[0] Order By EH_Platforms.Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes($values1[0])."<br />\n";
    }
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;&nbsp;No Platforms Selected";
?></p>
  </div>
  <div id="tabsSkill">
<p><b><?=stripslashes($values[1])?>'s Skills</b></p>
    <p><?
  $query1 = "select EH_Skills.Name, EH_Members_Skills.SkillLevel From EH_Skills, EH_Members_Skills WHERE EH_Skills.Skill_ID=EH_Members_Skills.Skill_ID AND EH_Members_Skills.Member_ID=$pin Order By EH_Skills.Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    $plt .= "&nbsp;&nbsp;&nbsp;&nbsp;Skill: ".stripslashes($values1[0])." At Level: $values1[1]<br />\n";
    }
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;&nbsp;No Skills Selected";
?></p>
  </div>
  <div id="tabsFic">
<p><b><?=stripslashes($values[1])?>'s Fiction</b></p>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="75%">Title</td>
        <td width="25%">Date Approved</td>
      </tr>
  <?
  $query1 = "SELECT Fiction_ID, Title, DatePosted FROM EH_Fiction WHERE Member_ID=$values[0] AND Approved=1 Order By DatePosted";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "      <tr>\n";
    echo "        <td width=\"75%\"><a href=\"story.php?id=$values1[0]\">".stripslashes($values1[1])."</td>";
    echo "        <td width=\"25%\">".date("M j, Y", $values1[2])."</td>";
    echo "      </tr>\n";
    }
  ?>
    </table>
  </div>
<?
flush();
?>
  <div id="tabsImg">
<p><b><?=stripslashes($values[1])?>'s Images</b></p>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="75%">Title</td>
        <td width="25%">Date Approved</td>
      </tr>
<?
  $query1 = "SELECT Images_ID, Name, DateSubmitted FROM EH_Images WHERE Member_ID=$values[0] AND Approved=1 Order By DateSubmitted";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "      <tr>\n";
    echo "        <td width=\"75%\"><a href=\"image.php?id=$values1[0]\">".stripslashes($values1[1])."</td>";
    echo "        <td width=\"25%\">".date("M j, Y", $values1[2])."</td>";
    echo "      </tr>\n";
    }
  ?>
    </table>
  </div>
<?
flush();
?>
  <div id="tabsCR">
<p><b><?=stripslashes($values[1])?>'s Combat Record</b></p>
  <?
  if($fchgpts)
    echo "    <p>".FCHGImage($fchgpts)."<br />".FCHGName($fchgpts)." ($fchgpts Points)</p>\n";
  if($cr)
    echo "    <p>Combat Rating: ".CombatRating($cr)." ($cr)</p>\n";
  echo "    <p>Battle Stats:<br />\n";
  $query1 = "SELECT SUM(EH_Battles.NumMissions) FROM EH_Battles, EH_Battles_Complete WHERE EH_Battles_Complete.Battle_ID=EH_Battles.Battle_ID AND EH_Battles_Complete.Member_ID=$values[0] AND EH_Battles_Complete.Status=1";
  $result1 = mysql_query($query1, $mysql_link);
  $values1 = mysql_fetch_row($result1);
  $query2 = "SELECT SUM(NumMissions) FROM EH_Battles WHERE Battle_ID IN ( SELECT DISTINCT Battle_ID FROM EH_Battles_Complete WHERE Member_ID =$values[0] AND STATUS =1)";
  $result2 = mysql_query($query2, $mysql_link);
  $values2 = mysql_fetch_row($result2);
  echo "Total Missions Flown: $values1[0] (Unique Missions Flown: $values2[0])<br />\n";
  $query1 = "SELECT EH_Battles.NumMissions FROM EH_Battles, EH_Battles_Complete WHERE EH_Battles_Complete.Battle_ID=EH_Battles.Battle_ID AND EH_Battles_Complete.Member_ID=$values[0] AND EH_Battles_Complete.Status=1 AND EH_Battles.NumMissions>1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  $query2 = "SELECT COUNT(Battle_ID) FROM EH_Battles WHERE NumMissions>1 AND Battle_ID IN ( SELECT DISTINCT Battle_ID FROM EH_Battles_Complete WHERE Member_ID =$values[0] AND STATUS =1)";
  $result2 = mysql_query($query2, $mysql_link);
  $values2 = mysql_fetch_row($result2);
  echo "Battles Completed: $rows1 (Unique Battles Completed: $values2[0])<br />\n";
  $query1 = "SELECT EH_Battles.NumMissions FROM EH_Battles, EH_Battles_Complete WHERE EH_Battles_Complete.Battle_ID=EH_Battles.Battle_ID AND EH_Battles_Complete.Member_ID=$values[0] AND EH_Battles_Complete.Status=1 AND EH_Battles.NumMissions=1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  $query2 = "SELECT COUNT(Battle_ID) FROM EH_Battles WHERE NumMissions=1 AND Battle_ID IN ( SELECT DISTINCT Battle_ID FROM EH_Battles_Complete WHERE Member_ID =$values[0] AND STATUS =1)";
  $result2 = mysql_query($query2, $mysql_link);
  $values2 = mysql_fetch_row($result2);
  echo "Free Missions Completed: $rows1 (Unique Free Missions Completed: $values2[0])<br />\n";
  echo "Battle High Scores: <br />\n";
  $query1 = "SELECT Battle_ID, Name, BattleNumber, Platform_ID, BC_ID, Highscore FROM EH_Battles WHERE HS_Holder=$values[0]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;No Battle High Scores<br />\n";
  for($a=0; $a<$rows1; $a++) {
    $values1 = mysql_fetch_row($result1);
    echo "&nbsp;&nbsp;&nbsp;<a href=\"battle.php?id=$values1[0]\">".BattleName($values1[0], 1)."</a> Score: ".stripslashes($values1[5])."<br />\n";
    }
  echo "Mission High Scores: <br />\n";
  $query1 = "SELECT Battle_ID, Name, BattleNumber, Platform_ID, BC_ID FROM EH_Battles";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  $count=0;
  for($a=0; $a<$rows1; $a++) {
    $values1 = mysql_fetch_row($result1);
    $query2 = "SELECT Mission_Num, Name, Highscore From EH_Battles_Missions WHERE Battle_ID=$values1[0] AND HS_Holder=$values[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    $count+=$rows2;
    for($b=0; $b<$rows2; $b++) {
      $values2 = mysql_fetch_row($result2);
      echo "&nbsp;&nbsp;&nbsp;<a href=\"battle.php?id=$values1[0]\">".BattleName($values1[0], 1)." Mission ".stripslashes($values2[0]).": ".stripslashes($values2[1])."</a> Score: ".stripslashes($values2[2])."<br />\n";
      }
    }
  if($count==0)
    echo "&nbsp;&nbsp;&nbsp;No Mission High Scores<br />\n";
  echo "Battles Created: <br />\n";
  $query1 = "SELECT Battle_ID, Name, BattleNumber, Platform_ID, BC_ID FROM EH_Battles WHERE (Creator_1=$values[0] OR Creator_2=$values[0] OR Creator_3=$values[0] OR Creator_4=$values[0]) AND NumMissions>1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;No Battles created<br />\n";
  for($a=0; $a<$rows1; $a++) {
    $values1 = mysql_fetch_row($result1);
    echo "&nbsp;&nbsp;&nbsp;<a href=\"battle.php?id=$values1[0]\">".BattleName($values1[0], 1)."</a><br />\n";
    }
  echo "Free Missions Created: <br />\n";
  $query1 = "SELECT Battle_ID, Name, BattleNumber, Platform_ID, BC_ID FROM EH_Battles WHERE (Creator_1=$values[0] OR Creator_2=$values[0] OR Creator_3=$values[0] OR Creator_4=$values[0]) AND NumMissions=1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;No Free missions created<br />\n";
  for($a=0; $a<$rows1; $a++) {
    $values1 = mysql_fetch_row($result1);
    echo "&nbsp;&nbsp;&nbsp;<a href=\"battle.php?id=$values1[0]\">".BattleName($values1[0], 1)."</a><br />\n";
    }
  echo "</p>\n";
  echo "<p>Battles Completed Listing:<br />\n";
  $query1 = "SELECT EH_Battles.Battle_ID, EH_Battles.Name, EH_Battles.BattleNumber, EH_Battles.Reward_Name, EH_Platforms.Name, EH_Platforms.Abbr, EH_Battles_Categories.Name, EH_Battles_Categories.Abbr, EH_Battles_Complete.Date_Completed FROM EH_Battles, EH_Platforms, EH_Battles_Complete, EH_Battles_Categories WHERE EH_Battles.NumMissions>1 AND EH_Battles.Battle_ID=EH_Battles_Complete.Battle_ID AND EH_Battles_Complete.Member_ID=$values[0] AND EH_Battles_Complete.Status=1 AND EH_Battles.BC_ID=EH_Battles_Categories.BC_ID AND EH_Battles.Platform_ID=EH_Platforms.Platform_ID ORDER By EH_Battles_Categories.SortOrder, EH_Platforms.Name, EH_Battles.BattleNumber";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;No Battles Completed<br />\n";
  for($a=0; $a<$rows1; $a++) {
    $values1 = mysql_fetch_row($result1);
    echo "<a href=\"battle.php?id=$values1[0]\">".BattleName($values1[0], 1)."</a>";
    if($values1[3])
      echo " (".stripslashes($values1[3]).")";
    if($values1[8])
      echo " Completed: ".date("F j, Y", $values1[8]);
    echo "<br />\n";
    }
  echo "</p>\n";
  echo "<p>Free Missions Completed Listing:<br />\n";
  $query1 = "SELECT EH_Battles.Battle_ID, EH_Battles.Name, EH_Battles.BattleNumber, EH_Battles.Reward_Name, EH_Platforms.Name, EH_Platforms.Abbr, EH_Battles_Categories.Name, EH_Battles_Categories.Abbr, EH_Battles_Complete.Date_Completed FROM EH_Battles, EH_Platforms, EH_Battles_Complete, EH_Battles_Categories WHERE EH_Battles.NumMissions=1 AND EH_Battles.Battle_ID=EH_Battles_Complete.Battle_ID AND EH_Battles_Complete.Member_ID=$values[0] AND EH_Battles_Complete.Status=1 AND EH_Battles.BC_ID=EH_Battles_Categories.BC_ID AND EH_Battles.Platform_ID=EH_Platforms.Platform_ID ORDER By EH_Battles_Categories.SortOrder, EH_Platforms.Name, EH_Battles.BattleNumber";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;No Free missions Completed<br />\n";
  for($a=0; $a<$rows1; $a++) {
    $values1 = mysql_fetch_row($result1);
    echo "<a href=\"battle.php?id=$values1[0]\">".BattleName($values1[0], 1)."</a>";
    if($values1[3])
     echo " (".stripslashes($values1[3]).")";
    if($values1[8])
      echo " Completed: ".date("F j, Y", $values1[8]);
    echo "<br />\n";
    }
  echo "</p>\n";
?>
  </div>
</div>
<script type="text/javascript">
	$(function() {
		$("#ehtabs").tabs();
    <?
mysql_data_seek($resultg, 0);
for($i=0; $i<$rowsg; $i++) {
  $valuesg = mysql_fetch_row($resultg);
  echo "		$(\"#tabsgroup$valuesg[1]\").tabs();
\n";
  }
    ?>
	});
</script>
<?
  }
else {
  echo "<p>The page you were looking for does not exist</p>";
  }
include_once("footer.php");
$timelen = microtime(true)-$time_start;
echo"Script executed in $timelen";
?>