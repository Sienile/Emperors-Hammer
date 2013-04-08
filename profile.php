<?
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
if($rows && array_key_exists("EHID",$_SESSION)) {
    echo "<a href=\"/medalrec.php?memberid=$pin\">Recommend Medal</a>";
    if(has_access($_SESSION['EHID'], "awardmedal"))
        echo " | <a href=\"/medalaward.php?memberid=$pin\">Award Medal</a>";
    echo " | ";
    echo "<a href=\"/promorec.php?memberid=$pin\">Recommend Promotion</a>";
    if(has_access($_SESSION['EHID'], "awardpromo"))
        echo " | <a href=\"/promoaward.php?memberid=$pin\">Award Promotion</a>";
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
		<li><a href="#tabsINPR">INPR</a></li>
		<li><a href="#tabsPlt">Platforms</a></li>
		<li><a href="#tabsSkill">Skills</a></li>
		<li><a href="#tabsFic">Fiction</a></li>
		<li><a href="#tabsImg">Images</a></li>
		<li><a href="#tabsCR">Combat Record</a></li>

	</ul>
	<div id="tabsMain">

		<p>
			Name:
			<?=stripslashes($values[1])?>
			<br /> E-Mail: <img src="/emailimg.php?id=<? echo $values[0]; ?>"
				alt="Member's E-mail Address" border="0" /><br /> Quote:
			<? if($values[3]) echo stripslashes($values[3]); ?>
			<br /> Chat Systems:<br />
			<?
  $queryc = "select EH_ChatSystems.Name, EH_ChatSystems.Abbr, EH_ChatSystems.Image, EH_ChatSystems.LinkFormat, EH_Members_ChatProfile.Chat_Handle From EH_ChatSystems, EH_Members_ChatProfile WHERE EH_ChatSystems.Chat_ID=EH_Members_ChatProfile.Chat_ID AND EH_Members_ChatProfile.Member_ID=$values[0] Order By EH_ChatSystems.Name";
  $resultc = mysql_query($queryc, $mysql_link);
  $rowsc = mysql_num_rows($resultc);
  for($i=0; $i<$rowsc; $i++) {
    $values1 = mysql_fetch_row($resultc);
    $link = str_replace("[username]", $values1[4], $values1[3]);
    echo "&nbsp;&nbsp;&nbsp;&nbsp;";
    if($values1[2])
      echo "<img src=\"/images/Icons/".stripslashes($values1[2])."\" alt=\"$values1[1]\"/>";
    if($values1[3])
      echo "<a href=\"$link\"><abbr title=\"".stripslashes($values1[0])."\">".stripslashes($values1[1])."</abbr></a><br />\n";
    else
      echo "<abbr title=\"".stripslashes($values1[0])."\">".stripslashes($values1[1])."</abbr>: ".stripslashes($values1[4])."<br />\n";
    }
  if($rowsc==0)
    echo "&nbsp;&nbsp;&nbsp;&nbsp;No Chat Systems selected<br />\n";
?>
			<? if($values[4])
			    echo "  Homepage: <a href=\"".stripslashes($values[4])."\">".stripslashes($values[4])."</a>";
			?>
		</p>
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
	        echo "      <div id=\"$values1[1]$values2[1]\">
	        \n";
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
	          $query3 = "SELECT EH_Competitions.Comp_ID, EH_Competitions.Name, EH_Competitions_Participants.Score FROM EH_Competitions, EH_Competitions_Participants WHERE EH_Competitions.Group_ID =$values1[0] AND EH_Competitions.Comp_ID = EH_Competitions_Participants.Comp_ID AND EH_Competitions_Participants.Member_ID =$values[0] ORDER BY EH_Competitions.StartDate";
	          $result3 = mysql_query($query3, $mysql_link);
	          $rows3 = mysql_num_rows($result3);
	          for($k=0; $k<$rows3; $k++) {
	            $values3 = mysql_fetch_row($result3);
	            echo "<a href=\"/compsstats.php?id=$values3[0]\">".stripslashes($values3[1])."</a>";
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
	            TrainingListingDisplay($pin, $mggroup);
	        }// End GT_ID=4
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
	                echo "<img src=\"/images/uniforms/uploaded/$img\" alt=\"$grnamen's Uniform\" />";
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
	                    echo "<img src=\"/images/uniforms/rankbased/$img\" alt=\"$grnamen's Uniform\" />";
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
	                        echo "<a href=\"/unit.php?id=$values4[1]\">".stripslashes($values4[0])."</a>";
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
	                        echo "<a href=\"/unit.php?id=$values4[1]\">".stripslashes($values4[0])."</a>";
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
	    }
	    echo "    </div>\n"; // End Group Container
	    echo "  </div>\n"; //End Group Main Tab
	}
	?>
	<div id="tabsINPR">
		<p>
			<b><?=stripslashes($values[1])?>'s Imperial Navy Pilot Record</b>
		</p>
<?
  $query1 = "select UpdateDate, Gender, Species, Birthdate, PlaceBirth, Relationship, Family, Social, SigYouth, SigAdult, AlignAtt, Previous, Hobbies, Traggedies, PhobiaAllergy, View, Enlisting, Comments From EH_Members_INPR WHERE Member_ID=$pin Limit 1";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="25%" valign="top"><b>Name</b></td>
				<td width="75%"><?=stripslashes($values[1])?></td>
			</tr>
<?
if($values1[1]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Gender</b></td>
				<td width="75%"><?=stripslashes($values1[1])?></td>
			</tr>
<?
}
if($values1[2]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Species</b></td>
				<td width="75%"><?=stripslashes($values1[2])?></td>
			</tr>
<?
}
if($values1[3]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Birthdate</b></td>
				<td width="75%"><?=stripslashes($values1[3])?></td>
			</tr>
<?
}
if($values1[4]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Place of Birth</b></td>
				<td width="75%"><?=stripslashes($values1[4])?></td>
			</tr>
<?
}
if($values1[5]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Relationship Status</b></td>
				<td width="75%"><?=stripslashes($values1[5])?></td>
			</tr>
<?
}
if($values1[6]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Family Status</b></td>
				<td width="75%"><?=stripslashes($values1[6])?></td>
			</tr>
<?
}
if($values1[7]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Social Status</b></td>
				<td width="75%"><?=stripslashes($values1[7])?></td>
			</tr>
<?
}
if($values1[8]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Significant Youth Events</b></td>
				<td width="75%"><?=stripslashes($values1[8])?></td>
			</tr>
<?
}
if($values1[9]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Significant Adult Events</b></td>
				<td width="75%"><?=stripslashes($values1[9])?></td>
			</tr>
<?
}
if($values1[10]) {
?>
			<tr>
				<td width="25%" valign="top"><b>EH Alignment</b></td>
				<td width="75%"><?=stripslashes($values1[10])?></td>
			</tr>
<?
}
if($values1[11]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Previous Employment</b></td>
				<td width="75%"><?=stripslashes($values1[11])?></td>
			</tr>
<?
}
if($values1[12]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Hobbies</b></td>				<td width="75%"><?=stripslashes($values1[12])?></td>
			</tr>
<?
}
if($values1[13]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Traggedies</b></td>
				<td width="75%"><?=stripslashes($values1[13])?></td>
			</tr>
<?
}
if($values1[14]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Phobias/Allergies</b></td>
				<td width="75%"><?=stripslashes($values1[14])?></td>
			</tr>
<?
}
if($values1[15]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Views on the EH</b></td>
				<td width="75%"><?=stripslashes($values1[15])?></td>
			</tr>
<?
}
if($values1[16]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Enlisting Reasons</b></td>
				<td width="75%"><?=stripslashes($values1[16])?></td>
			</tr>
<?
}
if($values1[17]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Additional Comments</b></td>
				<td width="75%"><?=stripslashes($values1[17])?></td>
			</tr>
<?
}
if($values1[0]) {
?>
			<tr>
				<td width="25%" valign="top"><b>Date last updated</b></td>
				<td width="75%"><?=date("M j, Y", $values1[0])?></td>
			</tr>
<?
}
?>
		</table>
<?
    }
  else
    echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;No INPR entered.</p>";
?>
	</div>
	<div id="tabsPlt">
		<p><b><?=stripslashes($values[1])?>'s Platforms</b></p>
		<p>
			<?
  $query1 = "select EH_Platforms.Name From EH_Platforms, EH_Members_Platforms WHERE EH_Platforms.Platform_ID=EH_Members_Platforms.Platform_ID AND EH_Members_Platforms.Member_ID=$values[0] Order By EH_Platforms.Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "&nbsp;&nbsp;&nbsp;&nbsp;".stripslashes($values1[0])."<br />\n";
    }
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;&nbsp;No Platforms Selected";
?>
		</p>
	</div>
	<div id="tabsSkill">
		<p><b><?=stripslashes($values[1])?>'s Skills</b></p>
		<p>
			<?
  $query1 = "select EH_Skills.Name, EH_Members_Skills.SkillLevel From EH_Skills, EH_Members_Skills WHERE EH_Skills.Skill_ID=EH_Members_Skills.Skill_ID AND EH_Members_Skills.Member_ID=$pin Order By EH_Skills.Name";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($i=0; $i<$rows1; $i++) {
    $values1 = mysql_fetch_row($result1);
    echo "&nbsp;&nbsp;&nbsp;&nbsp;Skill: ".stripslashes($values1[0])." At Level: $values1[1]<br />\n";
    }
  if($rows1==0)
    echo "&nbsp;&nbsp;&nbsp;&nbsp;No Skills Selected";
?>
		</p>
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
			    echo "        <td width=\"75%\"><a href=\"/story.php?id=$values1[0]\">".stripslashes($values1[1])."</td>";
			    echo "        <td width=\"25%\">".date("M j, Y", $values1[2])."</td>";
			    echo "      </tr>\n";
			}
			?>
		</table>
	</div>
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
			    echo "        <td width=\"75%\"><a href=\"/image.php?id=$values1[0]\">".stripslashes($values1[1])."</td>";
			    echo "        <td width=\"25%\">".date("M j, Y", $values1[2])."</td>";
			    echo "      </tr>\n";
			}
			?>
		</table>
	</div>
	<div id="tabsCR">
		<p>
			<b><?=stripslashes($values[1])?>'s Combat Record</b>
		</p>
		<?
		if($fchgpts)
		    echo "    <p>".FCHGImage($fchgpts)."<br />".FCHGName($fchgpts)." ($fchgpts Points)</p>\n";
		if($cr)
		    echo "    <p>Combat Rating: ".CombatRating($cr)." ($cr)</p>\n";
		
		
		function battleNameRow($row){
		    $string = "";
		    $string .= "&nbsp;&nbsp;&nbsp;<abbr title=\"".$row["platform"]."\">".$row["platform_abbr"]."</abbr>-";
		    $string .= "<abbr title=\"".$row["category"]."\">".$row["category_abbr"]."</abbr>";
		    $string .= " ".$row["battle_number"].": ";
		    $string .= "<a href=\"/battle.php?id=".$row["battle_id"]."\">".$row["name"]."</a>";
		    if (!empty($row["reward_name"])){
		        $string .= " (".stripslashes($row["reward_name"]).")";
		    }
		    $string .= "<br />\n";
		    return $string;
		}
		
		
		/*********************************************************************\
		 * This query will provide a large percentage of the information needed
		 * below
		 *
		\*********************************************************************/
		$bl_query = "select DISTINCT eb.Battle_ID as 'battle_id',
		eb.BattleNumber as 'battle_number',
		eb.Name as 'name',
		eb.Reward_Name as 'reward_name',
		eb.Highscore as 'highscore',
		eb.HS_Holder as 'highscore_holder',
		p.Name as 'platform',
		p.Abbr as 'platform_abbr',
		cat.Name as 'category',
		cat.Abbr as 'category_abbr'
		from EH_Battles_Complete as ebc
		inner join EH_Battles as eb
		on eb.Battle_ID = ebc.Battle_ID
		and ebc.Member_ID = $pin
		and ebc.Status = 1
		inner join EH_Platforms as p
		on eb.Platform_ID = p.Platform_ID
		inner join EH_Battles_Categories as cat
		on cat.BC_ID = eb.BC_ID
		order by p.Platform_ID, cat.BC_ID, battle_number";
		
		$bl_result = mysql_query($bl_query, $mysql_link);
		$battle_count = mysql_num_rows($bl_result);
		
		echo "    <p>Battle Stats:<br />\n";
		
		$count_query = "select distinct ebc.Battle_ID, sum(eb.NumMissions) as mission_count,
		                eb.BC_ID, count(ebc.Battle_ID) as 'battle_count'
		                from EH_Battles_Complete as ebc
                        inner join EH_Battles as eb
                        on eb.Battle_ID = ebc.Battle_ID
                        and ebc.Member_ID = $pin
                        and ebc.status = 1
                        group by BC_ID";
		
		$count_results = mysql_query($count_query, $mysql_link);
		
		// Overall Missions
		$overall_count = 0;
	    // Battle Count
	    $battle_count = 0;
		// Free mission count
		$mission_count = 0;
	    
		while($count_row = @mysql_fetch_assoc($count_results)){
		    if ($count_row["BC_ID"] == 11){
		        $mission_count += $count_row["battle_count"];
		    }else{
		        $battle_count += $count_row["battle_count"];
		    }
		    $overall_count += $count_row["mission_count"];
		}

		echo "Total overall missions flown: $overall_count<br />\n";
		echo "Battles Completed: $battle_count<br />\n";
		echo "Free Missions Completed: $mission_count<br /><br />\n";
		
        $bhs_query = "SELECT eb.Battle_ID AS 'battle_id', eb.Highscore AS 'highscore', p.Name AS 'platform', p.Abbr AS 'platform_abbr', cat.Name AS 'category', cat.Abbr AS 'category_abbr', eb.BattleNumber AS 'battle_number'
                      FROM EH_Battles AS eb
                      INNER JOIN EH_Platforms AS p ON eb.Platform_ID = p.Platform_ID
                      INNER JOIN EH_Battles_Categories AS cat ON eb.BC_ID = cat.BC_ID
                      WHERE eb.BC_ID !=11
                      AND eb.HS_Holder =$pin
                      order by p.Platform_ID, cat.BC_ID, battle_number";
        $bhs_results = mysql_query($bhs_query, $mysql_link);
        $bhs_count = mysql_num_rows($bhs_results);
        
        echo "Battle High Scores (".$bhs_count."): <br />\n";
        
        if ($bhs_count < 1){
            echo "&nbsp;&nbsp;&nbsp;No Battle High Scores<br />\n";
        }else{
            while($bhs_row = mysql_fetch_assoc($bhs_results)){
                $string = "";
                $string .= "&nbsp;&nbsp;&nbsp;<abbr title=\"".$bhs_row['platform']."\">".$bhs_row['platform_abbr']."</abbr>-";
                $string .= "<abbr title=\"".$bhs_row['category']."\">".$bhs_row['category_abbr']."</abbr>";
                $string .= " ".$bhs_row['battle_number'].": ";
                $string .= "<a href=\"/battle.php?id=".$bhs_row['battle_id']."\">".$bhs_row['name']."</a>";
                $string .= " Score: ".$bhs_row['highscore']."<br />\n";
                echo $string;
            }
        }

		$mhs_query = "select distinct ebm.Mission_ID as 'mission_id',
                        ebm.Battle_ID as 'battle_id',
                        ebm.Mission_Num as 'mission_number',
                        ebm.Highscore as 'highscore',
                        p.Name as 'platform',
                        p.Abbr as 'platform_abbr',
                        cat.Name as 'category',
                        cat.Abbr as 'category_abbr',
                        eb.BattleNumber as 'battle_number'
                    FROM EH_Battles_Missions as ebm
                    inner join EH_Battles as eb
                    on ebm.Battle_ID = eb.Battle_ID
                    and ebm.HS_Holder = $pin
                    inner join EH_Platforms as p
                    on eb.Platform_ID = p.Platform_ID
                    inner join EH_Battles_Categories as cat
                    on eb.BC_ID = cat.BC_ID
                    order by p.Platform_ID, cat.BC_ID, battle_number";
		$mhs_results = mysql_query($mhs_query, $mysql_link);
		$mhs_count = mysql_num_rows($mhs_results);
		
		echo "<strong>Mission High Scores</strong> ($mhs_count): <br />\n";
		
        if ($mhs_count < 1){
            echo "&nbsp;&nbsp;&nbsp;No Mission High Scores<br />\n";
        }else{
            while($mhs_row = mysql_fetch_assoc($mhs_results)){
                $string = "";
                $string .= "&nbsp;&nbsp;&nbsp;<abbr title=\"".$mhs_row['platform']."\">".$mhs_row['platform_abbr']."</abbr>-";
                $string .= "<abbr title=\"".$mhs_row['category']."\">".$mhs_row['category_abbr']."</abbr>";
                $string .= " ".$mhs_row['battle_number']." - Mission ".$mhs_row['mission_number'].": ";
                $string .= "<a href=\"/battle.php?id=".$mhs_row['battle_id']."\">".$mhs_row['name']."</a>";
                $string .= " Score: ".$mhs_row['highscore']."<br />\n";
                echo $string;
            }
        }

		
		    $bc_query = "select DISTINCT eb.Battle_ID as 'battle_id',
                            eb.Name as 'name',
                            eb.BattleNumber as 'battle_number',
                            p.Name as 'platform',
                            p.Abbr as 'platform_abbr',
                            cat.Name as 'category',
                            cat.Abbr as 'category_abbr'
                        from EH_Battles as eb
                            inner join EH_Platforms as p
                                on eb.Platform_ID = p.Platform_ID
                                and eb.Status = 1
                                and BC_ID != 11
                                and (Creator_1 = '$pin' or Creator_2 = '$pin' or Creator_3 = '$pin' or Creator_4 = '$pin')
                            inner join EH_Battles_Categories as cat
                            on eb.BC_ID = cat.BC_ID
                       order by p.Platform_ID, cat.BC_ID, eb.BattleNumber";
	    $bc_results = mysql_query($bc_query, $mysql_link);
        $bc_count = mysql_num_rows($bc_results);
		
        echo "<strong>Battles Created</strong> ($bc_count): <br />\n";
        
        if ($bc_count < 1){
            echo "&nbsp;&nbsp;&nbsp;No Battles Created<br />\n";
        }else{
            while($bc_row = mysql_fetch_assoc($bc_results)){
                $string = "";
                $string .= "&nbsp;&nbsp;&nbsp;<abbr title=\"".$bc_row['platform']."\">".$bc_row['platform_abbr']."</abbr>-";
                $string .= "<abbr title=\"".$bc_row['category']."\">".$bc_row['category_abbr']."</abbr>";
                $string .= " ".$bc_row['battle_number'].": ";
                $string .= "<a href=\"/battle.php?id=".$bc_row['battle_id']."\">".$bc_row['name']."</a><br />\n";
                echo $string;
            }
        }

		
		$mc_query = "select DISTINCT eb.Battle_ID as 'battle_id',
                            eb.Name as 'name',
                            eb.BattleNumber as 'battle_number',
                            p.Name as 'platform',
                            p.Abbr as 'platform_abbr',
                            cat.Name as 'category',
                            cat.Abbr as 'category_abbr'
                        from EH_Battles as eb
                            inner join EH_Platforms as p
                                on eb.Platform_ID = p.Platform_ID
                                and eb.Status = 1
                                and BC_ID = 11
                                and (Creator_1 = '$pin' or Creator_2 = '$pin' or Creator_3 = '$pin' or Creator_4 = '$pin')
                            inner join EH_Battles_Categories as cat
                            on eb.BC_ID = cat.BC_ID
                      order by p.Platform_ID, cat.BC_ID, eb.BattleNumber";
	    
        $mc_results = mysql_query($mc_query, $mysql_link);
        $mc_count = mysql_num_rows($mc_results);
		
        echo "<strong>Free Missions Created</strong> ($mc_count): <br />\n";
        
        if ($mc_count < 1){
            echo "&nbsp;&nbsp;&nbsp;No free missions created<br />\n";
        }else{
            while($mc_row = mysql_fetch_assoc($mc_results)){
                $string = "";
                $string .= '&nbsp;&nbsp;&nbsp;<abbr title="'.$mc_row["platform"].'">'.$mc_row["platform_abbr"].'</abbr>-';
                $string .= '<abbr title="'.$mc_row["category"].'">'.$mc_row["category_abbr"].'</abbr>';
                $string .= ' '.$mc_row["battle_number"].": ";
                $string .= '<a href="/battle.php?id='.$mc_row["battle_id"].'">'.$mc_row["name"].'</a><br />';
                echo $string;
            }
        }

		echo "</p>\n<hr style=\"width:50%\" />";
		echo "<p><h4 style=\"text-align: center\">Completed Combat Engangements</h4><hr style=\"width: 50%\" />\n";
        
        if ($overall_count < 1){
            echo "&nbsp;&nbsp;&nbsp;No Battles / Missions Completed<br />\n";
        }else{
            $cur_platform = null;
            $cur_category = null;
            while($row = mysql_fetch_assoc($bl_result)){
                if ($cur_platform != $row["platform"]){
                    if (!is_null($cur_platform)){
                        echo "<br />";
                    }

                    // Output header
                    echo "<strong>".$row["platform"]."</strong><hr />";
                    $cur_platform = $row["platform"];
                }
                
                if ($cur_category != $row["category"]){
                    echo "<br />&nbsp;<span style=\"border-bottom: 1px solid white\"><strong><em>".$row["category"]."</em></strong></span><br />";
                    $cur_category = $row["category"];
                }
                
                // Output the row data
                echo battleNameRow($row);
            }
 
        } // End if have battle results

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
?>