<?
session_start();
include_once("config.php");
include_once("functions.php");
if(!isset($_SESSION['EHID'])) {
  Redirect("login.php");
  }
include_once("nav.php");
echo "<p>Emperor's Hammer Administration</p>\n";

$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$query = "SELECT MP_ID FROM EH_Merged_Profiles WHERE From_ID=".$_SESSION['EHID'];
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
if($rows) {
  echo "<h2><a href=\"mergeprofiles.php?merge=true\">Merge Profiles Request Pending</a></h2>\n";
  }
$indent = "&nbsp;&nbsp;&nbsp;&nbsp;";
//Begin Training Functions
echo "<p>Training<br />\n";
if(has_access($_SESSION['EHID'], "acadadmin",true))
 echo $indent."<a href=\"academyadmin.php\">Training Academies Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "traincatadmin",true))
 echo $indent."<a href=\"trainingcatadmin.php\">Training Categories Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "trainadmin",true))
 echo $indent."<a href=\"trainingadmin.php\">Training Course Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "trainawardsadmin",true))
 echo $indent."<a href=\"trainingawardsadmin.php\">Training Awards Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "notesadmin",true))
 echo $indent."<a href=\"trainingnotesadmin.php\">Training Notes Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "examadmin",true))
 echo $indent."<a href=\"trainingexamsadmin.php\">Training Exams Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "examgradeadmin",true))
 echo $indent."<a href=\"examadmin.php\">Exam Grading</a><br />\n";
if(has_access($_SESSION['EHID'], "certadmin",true))
 echo $indent."<a href=\"traininghistoryadmin.php\">Training History Admin</a><br />\n";
echo $indent."<a href=\"testcenter.php\">Training Department Test Center</a><br />\n";
echo "</p>\n";
//End Training Functions
//Begin Roster Functions
echo "<p>Roster Functions<br />\n";
if(has_access($_SESSION['EHID'], "groupadmin",true))
  echo $indent."<a href=\"groupadmin.php\">Groups Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "rosteradmin",true))
  echo $indent."<a href=\"rosteradmin.php\">Roster Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "positionadmin",true))
  echo $indent."<a href=\"positionadmin.php\">Positions Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "rankadmin",true))
  echo $indent."<a href=\"rankadmin.php\">Ranks Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "rankadmin",true))
  echo $indent."<a href=\"ranktypeadmin.php\">Ranks Types Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "unitadmin",true))
  echo $indent."<a href=\"unitadmin.php\">Units Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "unitadmin",true))
  echo $indent."<a href=\"unittypeadmin.php\">Units Types Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "counitadmin",true))
  echo $indent."<a href=\"unitadminco.php\">Unit Roster admin</a><br />\n";
if(has_access($_SESSION['EHID'], "accessadmin",true))
  echo $indent."<a href=\"accessadmin.php\">Access Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "accesspageadmin",true))
  echo $indent."<a href=\"accesspageadmin.php\">Access Pages Admin</a><br />\n";
/*if(has_access($_SESSION['EHID'], "personalhistoryadmin",true))
  echo $indent."<a href=\"personalhistoryadmin.php\">Personal History Admin</a><br />\n";*/
echo $indent."<a href=\"joinanothergroup.php\">Join another Group</a><br />\n";
echo $indent."<a href=\"mergeprofiles.php\">Merge Profiles</a><br />\n";
echo $indent."<a href=\"uniupload.php\">Uniform Upload</a><br />\n";
echo $indent."<a href=\"profileadmin.php\">Edit Profile</a><br />\n";
echo $indent."<a href=\"profile.php?pin=".$_SESSION['EHID']."\">View Profile</a><br />\n";
echo "</p>\n";
//End Roster Functions
//Begin Medals Functions
echo "<p>Medals Functions<br />\n";
echo $indent."<a href=\"medalrec.php\">Recommend Medal</a><br />\n";
if(has_access($_SESSION['EHID'], "awardmedal",true))
  echo $indent."<a href=\"medalaward.php\">Award Medals</a><br />\n";
if(has_access($_SESSION['EHID'], "medalapprove",true))
  echo $indent."<a href=\"medalapprove.php\">Medals Approval</a><br />\n";
if(has_access($_SESSION['EHID'], "medaladmin",true))
  echo $indent."<a href=\"medaladmin.php\">Medals Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "medaladmin",true))
  echo $indent."<a href=\"medalgroupsadmin.php\">Medals Groups Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "medaladmin",true))
  echo $indent."<a href=\"medalupgradesadmin.php\">Medals Upgrades Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "medalownedadmin",true))
 echo $indent."<a href=\"medalhistoryadmin.php\">Medal History Admin</a><br />\n";
echo "</p>\n";
//End Medals Functions
//Begin Promotion Functions
echo "<p>Promotion Functions<br />\n";
echo $indent."<a href=\"promorec.php\">Recommend Promotion</a><br />\n";
if(has_access($_SESSION['EHID'], "awardpromo",true))
  echo $indent."<a href=\"promoaward.php\">Award Promotion</a><br />\n";
if(has_access($_SESSION['EHID'], "promoapprove",true))
  echo $indent."<a href=\"promoapprove.php\">Promotion Approval</a><br />\n";
echo "</p>\n";
//End Promotion Functions
//Begin Comps Functions
echo "<p>Competitions Functions<br />\n";
if(has_access($_SESSION['EHID'], "compsadmin",true))
  echo $indent."<a href=\"compsadmin.php\">Group Wide - Competitions Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "compsadmin",true))
  echo $indent."<a href=\"compspartadmin.php\">Group Wide - Competions Participants Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "compapproval",true))
  echo $indent."<a href=\"compapprove.php\">Approve Competitions</a><br />\n";
if(has_access($_SESSION['EHID'], "compspersonadmin",true))
  echo $indent."<a href=\"compspersonadmin.php\">Manage your Competitions</a><br />\n";
if(has_access($_SESSION['EHID'], "compspersonadmin",true))
  echo $indent."<a href=\"compspersonpartadmin.php\">Manage your Competions Participants</a><br />\n";
echo "</p>\n";
//End Comps Functions
//Begin Battle Functions
echo "<p>Battle Functions<br />\n";
if(has_access($_SESSION['EHID'], "battlesadmin",true))
  echo $indent."<a href=\"battlesadmin.php\">Battles Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "battlesadmin",true))
  echo $indent."<a href=\"battlescatadmin.php\">Battles Categories Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "bsfsubmit",true))
  echo $indent."<a href=\"bsfsubmit.php\">BSF Submit</a><br />\n";
if(has_access($_SESSION['EHID'], "bsfapprove",true))
  echo $indent."<a href=\"bsfapprove.php\">BSF Approval</a><br />\n";
if(has_access($_SESSION['EHID'], "combatrateadmin",true))
  echo $indent."<a href=\"cradmin.php\">Combat Ratings Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "fchgadmin",true))
  echo $indent."<a href=\"fchgadmin.php\">FCHG Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "fchgadmin",true))
  echo $indent."<a href=\"recalculatefchgcr.php\">Recalculate ALL FCHG/Combat Rating Points</a><br />\n";
if(has_access($_SESSION['EHID'], "fchgadmin",true))
  echo $indent."<a href=\"personalrecalculate.php\">Recalculate by PIN FCHG/Combat Rating Points</a><br />\n";
if(has_access($_SESSION['EHID'], "sttadmin",true))
  echo $indent."<a href=\"sttypeadmin.php\">Stormtrooper Type Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "patchadmin",true))
  echo $indent."<a href=\"patchadmin.php\">Patches Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "patchadmin",true))
  echo $indent."<a href=\"patchescatadmin.php\">Patch Categories Admin</a><br />\n";
echo "</p>\n";
//End Battle Functions
//Begin Works Functions
echo "<p>Created Works Functions<br />\n";
echo $indent."Personal Fiction Admin<br />\n";
if(has_access($_SESSION['EHID'], "fictionadmin",true))
  echo $indent."Global Fiction Admin<br />\n";
if(has_access($_SESSION['EHID'], "filesadmin",true))
  echo $indent."Files Admin<br />\n";
if(has_access($_SESSION['EHID'], "filesadmin",true))
  echo $indent."<a href=\"filescategoryadmin.php\">Files Category Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "historyadmin",true))
  echo $indent."History Admin<br />\n";
if(has_access($_SESSION['EHID'], "imgadmin",true))
  echo $indent."Images Admin<br />\n";
echo $indent."Image Submission<br />\n";
if(has_access($_SESSION['EHID'], "imgadmin",true))
  echo $indent."<a href=\"imgcatadmin.php\">Images Categories</a><br />\n";
if(has_access($_SESSION['EHID'], "nladmin",true))
  echo $indent."<a href=\"newsletteradmin.php\">Newsletters Admin</a><br />\n";
echo "</p>\n";
//End Works Functions
//Begin Store Functions
echo "<p>Store/Item Functions<br />\n";
echo "</p>\n";
//End Store Functions
if(has_access($_SESSION['EHID'], "security",true)){
echo "<p>Security Functions<br />\n";
echo $indent."<a href=\"security.php\">Security Dashboard</a><br />\n";
}
//Begin General Functions
echo "<p>Other Functions<br />\n";
if(has_access($_SESSION['EHID'], "baseadmin",true))
  echo $indent."<a href=\"baseadmin.php\">Bases Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "allianceadmin",true))
  echo $indent."<a href=\"allianceadmin.php\">Alliances Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "articleadmin",true))
  echo $indent."<a href=\"articleadmin.php\">Articles Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "benefactoradmin",true))
  echo $indent."<a href=\"benefactoradmin.php\">Benefactors Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "chatadmin",true))
  echo $indent."<a href=\"chatadmin.php\">Chat Systems Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "heroadmin",true))
  echo $indent."<a href=\"herosadmin.php\">Heroes Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "linkadmin",true))
  echo $indent."<a href=\"linkadmin.php\">Links Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "linkadmin",true))
  echo $indent."<a href=\"linkcatadmin.php\">Links Category Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "linkadmin",true))
  echo $indent."Links Comments Admin - Utilize controls on the links list<br />\n";
if(has_access($_SESSION['EHID'], "meetingadmin",true))
  echo $indent."<a href=\"meetingadmin.php\">Meetings Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "meetingadmin",true))
  echo $indent."<a href=\"meetinglogadmin.php\">Meeting Log Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "newsadmin",true))
  echo $indent."<a href=\"newsadmin.php\">News Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "pageadmin",true))
  echo $indent."<a href=\"pagesadmin.php\">Pages Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "platformadmin",true))
  echo $indent."<a href=\"platformadmin.php\">Platforms Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "reportadmin",true))
  echo $indent."<a href=\"reportadmin.php\">Reports Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "serveradmin",true))
  echo $indent."<a href=\"serveradmin.php\">Server Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "shipadmin",true))
  echo $indent."<a href=\"shipadmin.php\">Ships Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "shipadmin",true))
  echo $indent."<a href=\"shipsuplementadmin.php\">Ships Supplement Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "shipadmin",true))
  echo $indent."<a href=\"shiptypesadmin.php\">Ships Types Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "siteawardadmin",true))
  echo $indent."<a href=\"siteawardadmin.php\">Site Awards Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "skillsadmin",true))
  echo $indent."<a href=\"skilladmin.php\">Skills Admin</a><br />\n";
if(has_access($_SESSION['EHID'], "uniformadmin",true))
  echo $indent."Uniform Admin<br />\n";
echo $indent."<a href=\"logout.php\">Logout</a><br />\n";
echo "</p>\n";
//End General Functions
include_once("footer.php");
?>
