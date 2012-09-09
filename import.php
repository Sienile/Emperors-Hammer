<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
?>
<h1>Emperor's Hammer Database Import Scripts</h1>
<?
if($_GET['db']=="hf") {
  $groupid=6;
  echo "<p>Beginning Hammer's Fist Data import<br>";
  $newmysql_link = mysql_connect($db_host, "emperors_hf", "wuqPTqFuELXH", TRUE);
  mysql_select_db("emperors_fist", $newmysql_link);

  //Begin Import: HF_Items_Categories
  $query = "SELECT IC_ID, Name, Description, SortOrder, Active FROM HF_Items_Categories";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $desc = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Items_Categories (Name, Description, SortOrder, Active, Group_ID) VALUES ('$name', '$desc', '$values[3]', '$values[4]', '$groupid')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'ItemsCat', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Items_Categories Transferred.<br>";
  //End Import HF_Items_Categories

  //Begin Import: HF_Medals_Groups
  $query = "SELECT MG_ID, Name, Abbr FROM HF_Medals_Groups";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Medals_Groups (Name, Abbr, Group_ID) VALUES ('$name', '$abbr', '$groupid')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'MedalGroup', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
     }
  echo "HF_Medals_Groups Transferred.<br>";
  //End Import HF_Medals_Groups

  //Begin Import: HF_Medals
  $query = "SELECT Medal_ID, Name, Abbr, MG_ID, MT_ID, Image, Available, ShowOnID FROM HF_Medals";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $img = mysql_real_escape_string($values[5]);
    if($values[3]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='MedalGroup' AND OriginalValue=$values[3]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mgid=$values1[0];
        }
      }
    else {
      $mgid=0;
      }
    $query1 = "SELECT Medal_ID FROM EH_Medals";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    $so=$rows1+1;
    $query1 = "INSERT INTO EH_Medals (Name, Abbr, MG_ID, MT_ID, Group_ID, Image, Active, ShowOnID, SortOrder) VALUES ('$name', '$abbr', '$mgid', '$values[4]', '$groupid', '$img', '$values[6]', '$values[7]', '$so')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
     }
  echo "HF_Medals Transferred.<br>";
  //End Import HF_Medals

  //Begin Import: HF_Medals_Upgrades
  $query = "SELECT MU_ID, Base_ID, Name, Abbr, Lower, Upper, Recycles FROM HF_Medals_Upgrades";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[2]);
    $abbr = mysql_real_escape_string($values[3]);
    if($values[3]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $baseid=$values1[0];
        }
      }
    else {
      $baseid=0;
      }
    $query1 = "INSERT INTO EH_Medals_Upgrades (Medal_ID, Name, Abbr, Group_ID, Lower, Upper, Recycles) VALUES ('$baseid', '$name', '$abbr', '$groupid', '$values[4]', '$values[5]', '$values[6]')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'MedalUpgrade', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
     }
  echo "HF_Medals_Upgrades Transferred.<br>";
  //End Import HF_Medals_Upgrades

  //Begin Import: HF_Meetings
  $query = "SELECT Meeting_ID, Name, MeetTime, MeetDay FROM HF_Meetings";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $time = mysql_real_escape_string($values[2]);
    $day = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Meetings (Name, MeetTime, MeetDay) VALUES ('$name', '$time', '$day')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Meetings', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
     }
  echo "HF_Meetings Transferred.<br>";
  //End Import HF_Meetings

  //Begin Import: HF_Ranks
  $query = "SELECT Ranks_ID, Name, Abbr, Active, Type, SortOrder FROM HF_Ranks";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    if($values[4]==1)
      $rt=7;
    else
      $rt=6;
    $query1 = "INSERT INTO EH_Ranks (Name, Abbr, Active, RT_ID, Group_ID, SortOrder) VALUES ('$name', '$abbr', '$values[3]', '$rt', '$groupid', '$values[5]')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
     }
  echo "HF_Ranks Transferred.<br>";
  //End Import HF_Ranks

  //Begin Import: HF_Positions
  $query = "SELECT Position_ID, Name, Abbr, Logo, URL, CS, MinRank, MaxRankAbletoPromoteTo, MedalsCanAward, SortOrder FROM HF_Positions";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $logo = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $minrank=0;
    if($values[6]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[6]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $minrank=$values1[0];
        }
      }
    $maxpromo=0;
    if($values[7]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[7]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $maxpromo=$values1[0];
        }
      }
    $medal="";
    if($values[8]) {
      $medals=explode(";", $values[8]);
      foreach($medals as $val) {
        $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$val";
        $result1 = mysql_query($query1, $mysql_link);
        $rows1 = mysql_num_rows($result1);
        if($rows1) {
          $values1 = mysql_fetch_row($result1);
          $medal[]=$values1[0];
          }
        }
      $medal = implode(";", $medal);
      }
    $query1 = "INSERT INTO EH_Positions (Name, Abbr, Banner, SiteURL, isCS, MinRank, Group_ID, MaxPromotableRank, MedalsAwardable, SortOrder) VALUES ('$name', '$abbr', '$logo', '$url', '$values[5]', '$minrank', '$groupid', '$maxpromo', '$medal', '$values[9]')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Position', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Positions Transferred.<br>";
  //End Import HF_Positions

  //Begin Import: HF_Training_Categories
  $query = "SELECT TC_ID, Name, Description, Active, SingleGroup, SortOrder FROM HF_Training_Categories";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $desc = mysql_real_escape_string($values[2]);
    $active=$values[3];
    if($values[4])
      $idgroup=0;
    else
      $idgroup=1;
    $so = $values[5];
    $query1 = "INSERT INTO EH_Training_Categories (Name, TCa_ID, Description, Active, IDLineGroup, SortOrder) VALUES ('$name', '5', '$desc', '$active', '$idgroup', '$so')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TrainCat', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  $query = "SELECT TC_ID, Master_ID FROM HF_Training_Categories";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainCat' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $masterid=$values1[0];
        }
      else 
        $masterid=0;
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainCat' AND OriginalValue=$values[0]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $newid=$values1[0];
        }
      $query1 = "UPDATE EH_Training_Categories Set Master_ID=$masterid WHERE TC_ID=$newid";
      $result1 = mysql_query($query1, $mysql_link);
    }
  
  echo "HF_Training_Categories Transferred.<br>";
  //End Import HF_Training_Categories

  //Begin Import: HF_Training
  $query = "SELECT Training_ID, Name, Abbr, TC_ID, CourseUp, Description, SortOrder, MinRank, MinPos, MinTime, MinPoints, MaxPoints, InfoDoc, Rewards, Ribbon FROM HF_Training";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainCat' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $tcid=$values1[0];
      }
    else
      $tcid=0;
    $tacid=5;
    $avail=$values[4];
    $desc = mysql_real_escape_string($values[5]);
    $so=$values[6];
    if($values[7]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[7]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $rank=$values1[0];
        }
      }
    else
      $rank=0;
    if($values[8]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[8]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $pos=$values1[0];
        }
      }
    else
      $pos=0;
    $time = $values[9];
    $minpts = $values[10];
    $maxpts = $values[11];
    $infodoc = mysql_real_escape_string($values[12]);
    $rewards = mysql_real_escape_string($values[13]);
    $ribbon = mysql_real_escape_string($values[14]);
    $query1 = "INSERT INTO EH_Training (Name, Abbr, TC_ID, TAc_ID, Available, Description, SortOrder, Min_Rank_ID, Min_Pos_ID, Min_Time, MinPoints, MaxPoints, NotesFile, Rewards, Ribbon) VALUES ('$name', '$abbr', '$tcid', '$tacid', '$avail', '$desc', '$so', '$rank', '$pos', '$time', '$minpts', '$maxpts', '$infodoc', '$rewards', '$ribbon')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Training', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  $query = "SELECT Training_ID, MinCert FROM HF_Training";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $masterid=$values1[0];
        }
      else 
        $masterid=0;
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[0]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $newid=$values1[0];
        }
      $query1 = "UPDATE EH_Training Set Min_Training_ID=$masterid WHERE Training_ID=$newid";
      $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Training Transferred.<br>";
  //End Import HF_Training

  //Begin Import: HF_Training_Awards
  $query = "SELECT TA_ID, Training_ID, Score, TAT_ID, Award_ID FROM HF_Training_Awards";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[1]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $trainid=$values1[0];
        }
      }
    if($values[3]==2) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $award=$values1[0];
        }
      }
    else {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $award=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Training_Awards (Training_ID, Score, TAT_ID, Award_ID) VALUES ('$trainid', '$values[2]', '$values[3]', '$award')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Training_Awards Transferred.<br>";
  //End Import HF_Training_Awards

  //Begin Import: HF_Training_Exams
  $query = "SELECT TE_ID, Question, Type, Answer, Training_ID, Choices, SortOrder, Points FROM HF_Training_Exams";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $q = mysql_real_escape_string($values[1]);
    $type = mysql_real_escape_string($values[2]);
    $a = mysql_real_escape_string($values[3]);
    if($values[4]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $train=$values1[0];
        }
      }
    $choices = mysql_real_escape_string($values[5]);
    $so = mysql_real_escape_string($values[6]);
    $pts = mysql_real_escape_string($values[7]);
    $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, Choices, SortOrder, Points) VALUES ('$q', '$type', '$a', '$train', '$choices', '$so', '$pts')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TrainExam', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Training_Exams Transferred.<br>";
  //End Import HF_Training_Exams

  //Begin Import: HF_Training_Notes
  $query = "SELECT TN_ID, SectionName, SectionText, SortOrder, Training_ID FROM HF_Training_Notes";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $sname = mysql_real_escape_string($values[1]);
    $stext = mysql_real_escape_string($values[2]);
    $so = mysql_real_escape_string($values[3]);
    if($values[4]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $train=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Training_Notes (SectionName, SectionText, SortOrder, Training_ID) VALUES ('$sname', '$stext', '$so', '$train')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Training_Notes Transferred.<br>";
  //End Import HF_Training_Notes

  //Begin Import: HF_Units
  $query = "SELECT Unit_ID, Name, UT_ID, URL, MsgBoard, Active, Banner, Motto, Nickname, MissionRoll FROM HF_Units";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    if($values[2]==0)
      $ut=3;
    elseif($values[2]==6)
      $ut=2;
    elseif($values[2]==1)
      $ut=11;
    elseif($values[2]==2)
      $ut=12;
    elseif($values[2]==3)
      $ut=13;
    elseif($values[2]==4)
      $ut=14;
    $url = mysql_real_escape_string($values[3]);
    $mb = mysql_real_escape_string($values[4]);
    $active = mysql_real_escape_string($values[5]);
    $banner = mysql_real_escape_string($values[6]);
    $motto = mysql_real_escape_string($values[7]);
    $nick = mysql_real_escape_string($values[8]);
    $roll = mysql_real_escape_string($values[9]);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, SiteURL, MessageBoard, Banner, Motto, Nickname, MissionRoll) VALUES ('$name', '$ut', '$active', '$groupid', '$url', '$mb', '$banner', '$motto', '$nick', '$roll')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Unit', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  $query = "SELECT Unit_ID, Master_ID FROM HF_Units";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Unit' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $masterid=$values1[0];
        }
      else 
        $masterid=0;
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Unit' AND OriginalValue=$values[0]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $newid=$values1[0];
        }
      $query1 = "UPDATE EH_Units Set Master_ID=$masterid WHERE Unit_ID=$newid";
      $result1 = mysql_query($query1, $mysql_link);
    }
  
  echo "HF_Units Transferred.<br>";
  //End Import HF_Units

  //Begin Import: HF_SSType
  $query = "SELECT SSType_ID, Name, Cert_ID, Image FROM HF_SSType";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $img = mysql_real_escape_string($values[3]);
    if($values[2]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $train=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_SSType (Name, Cert_ID, Image) VALUES ('$name', '$train', '$img')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'SSType', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_SSType Transferred.<br>";
  //End Import HF_SSType

  //Begin Import: HF_Items
  $query = "SELECT Item_ID, Name, Category, Description, Cost, image, MinCert, MinPos, MinRank, InActive, NumAvail, Devalue, WeeklyCost FROM HF_Items";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='ItemsCat' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $cat=$values1[0];
      }
    $desc = mysql_real_escape_string($values[3]);
    $cost=$values[4];
    $img = mysql_real_escape_string($values[5]);
    if($values[6]) {
      $vals = explode(", ", $values[6]);
      $cert=array();
      foreach($vals as $val) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$val";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $cert[]=$values1[0];
        }
        }
      $cert=implode(";", $cert);
      }
    else
      $cert=0;
    if($values[8]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[8]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $rank=$values1[0];
        }
      }
    else
      $rank=0;
    if($values[7]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[7]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $pos=$values1[0];
        }
      }
    else
      $pos=0;
    $inactive = !$values[9];
    $num = $values[10];
    $deval = $values[11];
    $weekcost = mysql_real_escape_string($values[12]);
    $query1 = "INSERT INTO EH_Items (Name, IC_ID, Group_ID, Description, Cost, Image, Training, MinRank, MinPos, Active, NumAvail, Devalue, WeeklyCost) VALUES ('$name', '$cat', '$groupid', '$desc', '$cost', '$img', '$cert', '$rank', '$pos', '$inactive', '$num', '$deval', '$weekcost')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Item', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Items Transferred.<br>";
  //End Import HF_Items

  //Begin Import: HF_Members
  $query = "SELECT Member_ID, Name, Email, Rank_ID, Positions, Units, PriPosition, Quote, URL, Chat, Active, JoinedOn, Promoted, AES_DECRYPT(UserPassword, 'po-o90-ikopcm tq34ui 8thq54g jwriuegh v89pq34 htgoiuewrnvjrieq yf o;lSF JHUIOJSAIOGF UNWD#E TRHLUIDERHGV*USDHFKLSUGF'), SSType_ID, ICs FROM HF_Members";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $email = mysql_real_escape_string($values[2]);
    $pwh = hash("sha512", $values[13]);
    $quote = mysql_real_escape_string($values[7]);
    $url = mysql_real_escape_string($values[8]);
    $query1 = "INSERT INTO EH_Members (Name, Email, UserPassword, Quote, URL) VALUES ('$name', '$email', '$pwh', '$quote', '$url')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    if($values[9]) {
      $chats = explode(";", $values[9]);
      foreach($chats as $chat) {
        $chat = explode(":", $chat);
        $query1 = "INSERT INTO EH_Members_ChatProfile (Member_ID, Chat_ID, Chat_Handle) VALUES ('$newid', '$chat[0]', '$chat[1]')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      }
    $active = $values[10];
    $join = $values[11];
    $ics = $values[15];
    $query1 = "INSERT INTO EH_Members_Groups (Member_ID, Group_ID, Active, isPrimary, JoinDate, Credits) VALUES ('$newid', '$groupid', '1', '1', '$join', '$ics')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $rank=$values1[0];
      }
    $promodate = $values[12];
    $query1 = "INSERT INTO EH_Members_Ranks (Member_ID, Group_ID, Rank_ID, PromotionDate) VALUES ('$newid', '$groupid', '$rank', '$promodate')";
    $result1 = mysql_query($query1, $mysql_link);
    //Positions
    if($values[4]) {
    $positions = explode(";", $values[4]);
    if(!in_array($values[6], $positions))
      $positions[]=$values[6];
    $pri=$values[6];
    foreach($positions as $pos) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$pos";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $posid=$values1[0];
        }
      if($pos==$pri)
        $priflag=1;
      else
        $priflag=0;
      $posdate = time();
      $query1 = "INSERT INTO EH_Members_Positions (Member_ID, Group_ID, Position_ID, PositionDate, isGroupPrimary) VALUES ('$newid', '$groupid', '$posid', '$posdate', '$priflag')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
    else {
      $query1 = "INSERT INTO EH_Members_Positions (Member_ID, Group_ID, Position_ID, PositionDate, isGroupPrimary) VALUES ('$newid', '$groupid', '0', '".time()."', '0')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Unit' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $unit=$values1[0];
      }
    $unitdate = time();
    $query1 = "INSERT INTO EH_Members_Units (Member_ID, Group_ID, Unit_ID, UnitDate) VALUES ('$newid', '$groupid', '$unit', '$unitdate')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) VALUES ('$newid', '3', '$values[14]')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Member', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Members Transferred.<br>";
  //End Import HF_Members

  //Begin Import: HF_Members_History
  $query = "SELECT Member_ID, History_Type, MemberChange, Reason, Occured FROM HF_Members_History";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $ht = mysql_real_escape_string($values[1]);
    $mc = mysql_real_escape_string($values[2]);
    $reason = mysql_real_escape_string($values[3]);
    $time = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_Members_History (Member_ID, Group_ID, History_Type, MemberChange, Reason, Occured) VALUES ('$mem', '$groupid', '$ht', '$mc', '$reason', '$time')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Members_History Transferred.<br>";
  //End Import HF_Members_History

  //Begin Import: HF_Items_Owned
  $query = "SELECT Member_ID, Item_ID, Issued, DayApproved FROM HF_Items_Owned";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Item' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $item=$values1[0];
      }
    $status = $values[2]+1;
    $time = $values[3];
    $query1 = "INSERT INTO EH_Members_Items (Member_ID, Group_ID, Item_ID, Status, DayApproved) VALUES ('$mem', '$groupid', '$item', '$status', '$time')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Items_Owned Transferred.<br>";
  //End Import HF_Items_Owned

  //Begin Import: HF_Items_Waiting
  $query = "SELECT Member_ID, Item_ID FROM HF_Items_Waiting";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Item' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $item=$values1[0];
      }
    $status = 0;
    $query1 = "INSERT INTO EH_Members_Items (Member_ID, Group_ID, Item_ID, Status) VALUES ('$mem', '$groupid', '$item', '$status')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Items_Waiting Transferred.<br>";
  //End Import HF_Items_Waiting

  //Begin Import: HF_Medals_Complete
  $query = "SELECT Member_ID, Awarder_ID, Medal_ID, DateAwarded, Reason From HF_Medals_Complete";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $time = $values[3];
    $reason = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Group_ID, Awarder_ID, Medal_ID, DateAwarded, Reason, Status) VALUES ('$mem', '$groupid', '$from', '$medal', '$time', '$reason', '1')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Medals_Complete Transferred.<br>";
  //End Import HF_Medals_Complete

  //Begin Import: HF_Medals_Recs
  $query = "SELECT For_ID, From_ID, Medal_ID, Reason, Type From HF_Medals_Recs";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $reason = mysql_real_escape_string($values[3]);
    if($values[4])
      $status=2;
    else
      $status=0;
    $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Group_ID, Awarder_ID, Medal_ID, DateAwarded, Reason, Status) VALUES ('$mem', '$groupid', '$from', '$medal', '$reason', '$status')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Medals_Recs Transferred.<br>";
  //End Import HF_Medals_Recs

  //Begin Import: HF_News
  $query = "SELECT Topic, Poster, Poster_ID, DatePosted, Body FROM HF_News";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[2]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      }
    else {
      $mem=0;
      }
    $topic = mysql_real_escape_string($values[0]);
    $poster = mysql_real_escape_string($values[1]);
    $time = mysql_real_escape_string($values[3]);
    $body = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_News (Group_ID, Topic, Poster, Poster_ID, DatePosted, Body) VALUES ('$groupid', '$topic', '$poster', '$mem', '$time', '$body')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_News Transferred.<br>";
  //End Import HF_News

  //Begin Import: HF_Operations
  $query = "SELECT Operation_ID, Name, StartDate, EndDate, Admin_ID, Description FROM HF_Operations";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $start = mysql_real_escape_string($values[2]);
    $end = mysql_real_escape_string($values[3]);
    $desc = mysql_real_escape_string($values[5]);
    if($values[4]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $admin=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Competitions (Name, Admin_ID, Group_ID, StartDate, EndDate, Description) VALUES ('$name', '$admin', '$groupid', '$start', '$end', '$desc')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Operation', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Operations Transferred.<br>";
  //End Import HF_Operations

  //Begin Import: HF_Operations_Participants
  $query = "SELECT Operation_ID, Member_ID, Score FROM HF_Operations_Participants";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Operation' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $comp=$values1[0];
      }
    $score = $values[2];
    $query1 = "INSERT INTO EH_Competitions_Participants (Comp_ID, Member_ID, Score) VALUES ('$comp', '$mem', '$score')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Operations_Participants Transferred.<br>";
  //End Import HF_Operations_Participants

  //Begin Import: HF_Promotion_Recs
  $query = "SELECT For_ID, From_ID, Type, Reason FROM HF_Promotion_Recs";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $for=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $type = mysql_real_escape_string($values[2]);
    $reason = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Promotion_Recs (For_ID, From_ID, Group_ID, Type, Reason) VALUES ('$for', '$from', '$groupid', '$type', '$reason')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Promotion_Recs Transferred.<br>";
  //End Import HF_Promotion_Recs

  //Begin Import: HF_Reports
  $query = "SELECT Name, Report, ReportNum, Unit_ID, Position_ID, Member_ID, ReportDate FROM HF_Reports";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Unit' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $unit=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[4]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $pos=$values1[0];
      }
    $name = mysql_real_escape_string($values[0]);
    $report = mysql_real_escape_string($values[1]);
    $reportnum = mysql_real_escape_string($values[2]);
    $reportdate = mysql_real_escape_string($values[6]);
    $query1 = "INSERT INTO EH_Reports (Name, Report, ReportNum, Group_ID, Unit_ID, Position_ID, Member_ID, ReportDate) VALUES ('$name', '$report', '$reportnum', '$groupid', '$unit', '$pos', '$mem', '$reportdate')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Reports Transferred.<br>";
  //End Import HF_Reports

  //Begin Import: HF_Training_Complete
  $query = "SELECT Training_ID, Member_ID, DateComplete, Score FROM HF_Training_Complete";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $train=$values1[0];
      }
    $time = mysql_real_escape_string($values[2]);
    $score = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Training_Complete (Training_ID, Member_ID, DateComplete, Score) VALUES ('$train', '$mem', '$time', '$score')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Training_Complete.<br>";
  //End Import HF_Training_Complete

  //Begin Import: HF_Training_Exams_Complete
  $query = "SELECT Member_ID, Training_ID, TE_ID, Answer, Score, Status FROM HF_Training_Exams_Complete";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Training' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $train=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainExam' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $te=$values1[0];
      }
    $ans = mysql_real_escape_string($values[3]);
    $score = mysql_real_escape_string($values[4]);
    $status = mysql_real_escape_string($values[5]);
    $query1 = "INSERT INTO EH_Training_Exams_Complete (Training_ID, Member_ID, TE_ID, Answer, Score, Status) VALUES ('$train', '$mem', '$te', '$ans', '$score', '$status')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "HF_Training_Exams_Complete.<br>";
  //End Import HF_Training_Exams_Complete

  echo "</p>\n";
  echo "<p>Manual stuff to make sure you do:<br>\n";
  echo "Mannually Transfer HF Meeting Logs<br>\n";
  echo "Mannually Add CSOrder, and bases to Positions; Add Access Levels<br>\n";
  echo "Manually Add Bases for Units<br>\n";
  echo "</p>\n";
  }
if($_GET['db']=="dir") {
  echo "<p>Beginning Directorate Data import<br>";
  $groupid=4;
  $newmysql_link = mysql_connect($db_host, "emperors_dir", "NqJVZv3SALI1", TRUE);
  mysql_select_db("emperors_directorate", $newmysql_link);

  //Begin Import: medals
  $query = "SELECT id, medal, short FROM medals";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Medals (Name, Abbr, MT_ID, Group_ID, Active, SortOrder, ShowOnID) VALUES ('$name', '$abbr', '1', '$groupid', '1', '$values[0]', '1')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "medals Transferred.<br>";
  //End Import medals

  //Begin Import: position
  $query = "SELECT id, position, short, weight FROM position WHERE id<13";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $so = $values[3]+1;
    $query1 = "INSERT INTO EH_Positions (Name, Abbr, Group_ID, SortOrder) VALUES ('$name', '$abbr', '$groupid', '$so')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Position', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "position Transferred.<br>";
  //End Import position

  //Begin Import: ranks
  $query = "SELECT id, rank, short, weight FROM ranks WHERE id<12";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $so = $values[3];
    $query1 = "INSERT INTO EH_Ranks (Name, Abbr, Group_ID, SortOrder, Active) VALUES ('$name', '$abbr', '$groupid', '$so', 1)";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "ranks Transferred.<br>";
  //End Import ranks

  //Add MC Unit
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Ministry Council', '3', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $mcnewid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'MC', '0', '$mcnewid')";
  $result1 = mysql_query($query1, $mysql_link);
  //End MC Unit

  //Begin Import: territory
  $query = "SELECT id, territory, open, motto, webpage FROM territory WHERE id<5";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $active = mysql_real_escape_string($values[2]);
    $motto = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Master_ID, Active, Group_ID, SiteURL, Motto) VALUES ('$name', '8', '0', '$active', '$groupid', '$url', '$motto')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Territory', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "territory Transferred.<br>";
  //End Import territory

  //Begin Import: systems
  $query = "SELECT id, system, open, motto, webpage, territory FROM systems WHERE id<5";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $active = mysql_real_escape_string($values[2]);
    $motto = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Territory' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $ter=$values1[0];
      }
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Master_ID, Active, Group_ID, SiteURL, Motto) VALUES ('$name', '9', '$ter', '$active', '$groupid', '$url', '$motto')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'System', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "systems Transferred.<br>";
  //End Import systems

  //Begin Import: planets
  $query = "SELECT id, planet, open, motto, webpage, system FROM planets WHERE id<17";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $active = mysql_real_escape_string($values[2]);
    $motto = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='System' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $sys=$values1[0];
      }
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Master_ID, Active, Group_ID, SiteURL, Motto) VALUES ('$name', '10', '$sys', '$active', '$groupid', '$url', '$motto')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Planet', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "planets Transferred.<br>";
  //End Import planets

  //Add Train Unit
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Training', '1', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TrainUnit', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  //End Train Unit

  //Add Rsv Unit
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Reserves', '2', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'RsvUnit', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  //End Rsv Unit
  $rsvpos = 12;
  $trainpos = 1;

  //Begin Import: members
  $query = "SELECT pin, name, email, rank, position, territory, system, planet, UNIX_TIMESTAMP(joined), credits, UNIX_TIMESTAMP(promoted) FROM members";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $email = mysql_real_escape_string($values[2]);
    srand(time());
    $pool = "ABCDEFGHIJKLMNOPQRSTUZWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "1234567890";
    $pool .="!@#$%^&*()_-+=[]{};:<>,./?|`~";
    for($l=0; $l<10; $l++) {
      $pw .= substr($pool, (rand()%(strlen($pool))), 1);
      }
    $hash_value = hash("sha512", $pw);

    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $rank=$values1[0];
      }

    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[4]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $pos=$values1[0];
      }

    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Planet' AND OriginalValue=$values[7]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $unit=$values1[0];
      }

    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='System' AND OriginalValue=$values[6]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Territory' AND OriginalValue=$values[5]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($unit==0 && $values[4]==$rsvpos) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='RsvUnit'";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($unit==0 && $values[4]==$trainpos) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainUnit'";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    else {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='MC'";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Members (Name, Email, UserPassword) VALUES ('$name', '$email', '$hash_value')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $pri=1;
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Member', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);

    $query1 = "INSERT INTO EH_Members_Groups (Member_ID, Group_ID, Active, isPrimary, JoinDate, Credits) VALUES ('$newid', '$groupid', '1', '$pri', '$values[8]', '$values[9]')";
    $result1 = mysql_query($query1, $mysql_link);
    $now=time();
    $query1 = "INSERT INTO EH_Members_Positions (Member_ID, Group_ID, Position_ID, isGroupPrimary, PositionDate) VALUES ('$newid', '$groupid', '$pos', '1', '$now')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Ranks (Member_ID, Group_ID, Rank_ID, PromotionDate) VALUES ('$newid', '$groupid', '$rank', '$values[10]')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Units (Member_ID, Group_ID, Unit_ID, UnitDate) VALUES ('$newid', '$groupid', '$unit', '$now')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "members Transferred.<br>";
  //End Import memebers

  //Begin Import: comps
  $query = "SELECT id, title, submitter, UNIX_TIMESTAMP(startdate), UNIX_TIMESTAMP(enddate), parties, awards, otherinfo, contactinfo  FROM comps";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $admin=$values1[0];
      }
    $start = mysql_real_escape_string($values[3]);
    $end = mysql_real_escape_string($values[4]);
    $parties = mysql_real_escape_string($values[5]);
    $awards = mysql_real_escape_string($values[6]);
    $otherinfo = mysql_real_escape_string($values[7]);
    $contact = mysql_real_escape_string($values[8]);
    $query1 = "INSERT INTO EH_Competitions (Name, Admin_ID, Group_ID, StartDate, EndDate, Scope, Awards, Description) VALUES ('$name', '$admin', '$groupid', '$start', '$end', '$parties', '$awards', '$otherinfo')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Comps', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "comps Transferred.<br>";
  //End Import comps
  $tacid = 4;
  $query1 = "INSERT INTO EH_Training_Categories (Name, TCa_ID, Active, SortOrder) VALUES ('Directorate Courses', '$tacid', '1', '1')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TrainCat', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);

  //Begin Import: courses
  $query = "SELECT id, coursename, short, prof, description, pass, hide, courseorder  FROM courses";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $prof=$values1[0];
      }
    $desc = mysql_real_escape_string($values[3]);
    $pass = mysql_real_escape_string($values[4]);
    $active = !mysql_real_escape_string($values[5]);
    $so = mysql_real_escape_string($values[6]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainCat' AND OriginalValue=0";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $cat=$values1[0];
      }
    $query1 = "INSERT INTO EH_Training (Name, Abbr, TC_ID, TAc_ID, Available, Description, SortOrder, MinPoints, Grader) VALUES ('$name', '$abbr', '$cat', '$tacid', '$active', '$desc', '$so', '$pass', '$prof')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Courses', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "courses Transferred.<br>";
  //End Import courses

  //Begin Import: courses_grads
  $query = "SELECT course, student, grade, UNIX_TIMESTAMP(testdate)  FROM courses_grads";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $stud=$values1[0];
      }
    $grade = mysql_real_escape_string($values[2]);
    $day = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Training_Complete (Training_ID, Member_ID, DateComplete, Score) VALUES ('$course', '$stud', '$day', '$grade')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "courses_grads Transferred.<br>";
  //End Import courses_grads

  //Begin Import: courses_notes
  $query = "SELECT course, notetitle, notetext, note_order  FROM courses_notes";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    $title = mysql_real_escape_string($values[1]);
    $body = mysql_real_escape_string($values[2]);
    $so = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Training_Notes (SectionName, SectionText, SortOrder, Training_ID) VALUES ('$title', '$body', '$so', '$course')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "courses_grads Transferred.<br>";
  //End Import courses_notes

  //Begin Import: courses_test
  $query = "SELECT course, question, correct, question_order, type, marks  FROM courses_test";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    $q = mysql_real_escape_string($values[1]);
    $a = mysql_real_escape_string($values[2]);
    $so = mysql_real_escape_string($values[3]);
    $type = mysql_real_escape_string($values[4])+1;
    $val = mysql_real_escape_string($values[5]);
    $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder, Points) VALUES ('$q', '$type', '$a', '$course', '$so', '$val')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "courses_test Transferred.<br>";
  //End Import courses_test

  //Begin Import: fiction
  $query = "SELECT title, `text`, author_id, UNIX_TIMESTAMP(`date`)  FROM fiction";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $title = mysql_real_escape_string($values[0]);
    $body = mysql_real_escape_string($values[1]);
    $day = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Fiction (Member_ID, Title, Body, DatePosted, Approved) VALUES ('$mem', '$title', '$body', '$day', '1')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "fiction Transferred.<br>";
  //End Import fiction

  //Begin Import: members_medals
  $query = "SELECT recipent, awarder, medal, reason, UNIX_TIMESTAMP(`date`), approved FROM members_medals";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $for=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $reason = mysql_real_escape_string($values[3]);
    $day = mysql_real_escape_string($values[4]);
    $status = mysql_real_escape_string($values[5]);
    $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status) VALUES ('$for', '$medal', '$from', '$groupid', '$day', '$reason', '$status')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "members_medals Transferred.<br>";
  //End Import members_medals

  //Begin Import: news
  $query = "SELECT title, `text`, poster, UNIX_TIMESTAMP(`date`)  FROM news";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $title = mysql_real_escape_string($values[0]);
    $body = mysql_real_escape_string($values[1]);
    $day = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_News (Group_ID, Topic, Poster_ID, DatePosted, Body) VALUES ('$groupid', '$title', '$mem', '$day', '$body')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "news Transferred.<br>";
  //End Import news

  //Begin Import: reports
  $query = "SELECT position, territory, system, planet, numb, `text`, UNIX_TIMESTAMP(`date`), poster, pin  FROM reports";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[8]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $memname = mysql_real_escape_string($values[7]);
    $body = mysql_real_escape_string($values[5]);
    $day = mysql_real_escape_string($values[6]);
    $num = mysql_real_escape_string($values[4]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $pos=$values1[0];
      }

    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Planet' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $unit=$values1[0];
      }

    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='System' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Territory' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Reports (Poster, Report, ReportNum, Group_ID, Unit_ID, Position_ID, Member_ID, ReportDate) VALUES ('$memname', '$body', '$num', '$groupid', '$unit', '$pos', '$mem', '$day')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "reports Transferred.<br>";
  //End Import reports

  echo "</p>\n";
  echo "<p>Manually Fix:<br>\n";
  echo "Positions: isCs/CSOrder<br>\n";
  echo "</p>\n";
  }
if($_GET['db']=="db") {
  echo "<p>Beginning Dark Brotherhood Data import</p>\n";
  $groupid=3;
  $newmysql_link = mysql_connect($db_host, "emperors_db", "RTFclXEIiwyD", TRUE);
  mysql_select_db("emperors_db", $newmysql_link);

  //Begin Import: db_medals
  $query = "SELECT id, medal, short, weight FROM db_medals";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $so = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Medals (Name, Abbr, MT_ID, Group_ID, Active, SortOrder, ShowOnID) VALUES ('$name', '$abbr', '1', '$groupid', '1', '$so', '1')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_medals Transferred.<br>";
  //End Import db_medals

  //Begin Import: db_medal_upgrades
  $query = "SELECT ID, `Long`, Short, Number, Parent FROM db_medal_upgrades";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $num = mysql_real_escape_string($values[3]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[4]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }

    $query1 = "INSERT INTO EH_Medals_Upgrades (Medal_ID, Name, Abbr, Group_ID, Lower) VALUES ('$medal', '$name', '$abbr', '$groupid', '$num')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_medal_upgrades Transferred.<br>";
  //End Import db_medal_upgrades

  //Begin Import: db_position
  $query = "SELECT id, position, short, weight FROM db_position";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $so = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Positions (Name, Abbr, Group_ID, SortOrder) VALUES ('$name', '$abbr', '$groupid', '$so')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Position', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_position Transferred.<br>";
  //End Import db_position
  $trainpos = 13;
  $rsvpos = 14;

  //Begin Import: db_ranks
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Apprentice', 'APP', 1, 1, 3, 1, 'APP_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '23', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Apprentice', 'APP', 1, 2, 3, 2, 'APP_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '23', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Apprentice', 'APP', 1, 3, 3, 3, 'APP_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '23', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Novice', 'NOV', 1, 1, 3, 4, 'NOV_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '22', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Novice', 'NOV', 1, 2, 3, 5, 'NOV_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '22', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Novice', 'NOV', 1, 3, 3, 6, 'NOV_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '22', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Acolyte', 'ACO', 1, 1, 3, 7, 'ACO_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '21', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Acolyte', 'ACO', 1, 2, 3, 8, 'ACO_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '21', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Acolyte', 'ACO', 1, 3, 3, 9, 'ACO_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '21', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Protector', 'PRT', 1, 1, 3, 10, 'PRT_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '20', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Protector', 'PRT', 1, 2, 3, 11, 'PRT_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '20', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Protector', 'PRT', 1, 3, 3, 12, 'PRT_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '20', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Guardian', 'GRD', 1, 1, 3, 13, 'GRD_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '19', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Guardian', 'GRD', 1, 2, 3, 14, 'GRD_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '19', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Guardian', 'GRD', 1, 3, 3, 15, 'GRD_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '19', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Jedi Hunter', 'JH', 1, 1, 3, 16, 'JH_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '18', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Jedi Hunter ', 'JH', 1, 2, 3, 17, 'JH_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '18', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Jedi Hunter', 'JH', 1, 3, 3, 18, 'JH_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '18', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Knight', 'DJK', 1, 1, 3, 19, 'DJK_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '17', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Knight', 'DJK', 1, 2, 3, 20, 'DJK_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '17', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Knight', 'DJK', 1, 3, 3, 21, 'DJK_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '17', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Sith Warrior', 'SW', 1, 1, 3, 22, 'SW_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '14', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Obelisk Warrior', 'OW', 1, 2, 3, 23, 'OW_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '16', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Krath Priest', 'KP', 1, 3, 3, 24, 'KP_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '15', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Sith Battlemaster', 'SBM', 1, 1, 3, 25, 'SBM_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '11', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Obelisk Battlemaster', 'OBM', 1, 2, 3, 26, 'OBM_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '13', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Krath Archpriest', 'KAP', 1, 3, 3, 27, 'KAP_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '12', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Sith Battlelord', 'SBL', 1, 1, 3, 28, 'SBL_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '8', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Obelisk Battlelord', 'OBL', 1, 2, 3, 29, 'OBL_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '10', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Krath Epis', 'KE', 1, 3, 3, 30, 'KE_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '9', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Sith Warlord', 'SWL', 1, 1, 3, 31, 'SWL_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '5', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Obelisk Warlord', 'OWL', 1, 2, 3, 32, 'OWL_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '7', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Krath Pontifex', 'KPN', 1, 3, 3, 33, 'KPN_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '6', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Side Adept', 'DA', 1, 1, 3, 34, 'DA_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '4', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Side Adept', 'DA', 1, 2, 3, 35, 'DA_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '4', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Side Adept', 'DA', 1, 3, 3, 36, 'DA_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '4', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Master', 'DJM', 1, 1, 3, 37, 'DJM_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '3', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Master', 'DJM', 1, 2, 3, 38, 'DJM_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '3', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Master', 'DJM', 1, 3, 3, 39, 'DJM_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '3', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Primarch', 'DJP', 1, 1, 3, 40, 'DJP_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '2', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Primarch', 'DJP', 1, 2, 3, 41, 'DJP_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '2', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Dark Jedi Primarch', 'DJP', 1, 3, 3, 42, 'DJP_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '2', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Grand Master', 'GM', 1, 1, 3, 43, 'GM_Sith.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '1', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Grand Master', 'GM', 1, 2, 3, 44, 'GM_Obelisk.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '1', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`, `UniformRankBased`) VALUES('Grand Master', 'GM', 1, 3, 3, 45, 'GM_Krath.jpg');";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '1', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  echo "db_ranks Transferred.<br>";
  //End Import db_ranks

  //Add DC Unit
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Dark Council', '3', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $dcnewid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'DC', '0', '$dcnewid')";
  $result1 = mysql_query($query1, $mysql_link);
  //End DC Unit

  //Begin Import: db_clans
  $query = "SELECT id, name, open, motto, webpage FROM db_clans";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $open = mysql_real_escape_string($values[2]);
    $motto = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, SiteURL, Motto) VALUES ('$name', '4', '$open', '$groupid', '$url', '$motto')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Clan', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_clans Transferred.<br>";
  //End Import db_clans

  //Begin Import: db_houses
  $query = "SELECT id, name, open, motto, webpage, clan FROM db_houses";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $open = mysql_real_escape_string($values[2]);
    $motto = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Clan' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $master=$values1[0];
      }
    else
      $master=0;
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, SiteURL, Motto, Master_ID) VALUES ('$name', '15', '$open', '$groupid', '$url', '$motto', '$master')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'House', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_houses Transferred.<br>";
  //End Import db_houses

  //Begin Import: db_battleteams
  $query = "SELECT id, name, open, motto, webpage, house FROM db_battleteams";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $open = mysql_real_escape_string($values[2]);
    $motto = mysql_real_escape_string($values[3]);
    $url = mysql_real_escape_string($values[4]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='House' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $master=$values1[0];
      }
    else
      $master=0;
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, SiteURL, Motto, Master_ID) VALUES ('$name', '16', '$open', '$groupid', '$url', '$motto', '$master')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'BT', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_battleteams Transferred.<br>";
  //End Import db_battleteams

  //Add Train Unit
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Initiates', '1', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TrainUnit', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  //End Train Unit

  //Add Rsv Unit
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Rouges', '2', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'RsvUnit', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  //End Rsv Unit
  $rsvpos = 14;
  $trainpos = 13;
  $btl = 11;
  $btm = 12;
  $house = 9;
  $clan = 7;

  //Begin Import: db_members
  $query = "SELECT pin, name, email, dbOrder, rank, position, unit, BTMSpot, UNIX_TIMESTAMP(joined), UNIX_TIMESTAMP(promoted), inactive FROM db_members";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $active = !$values[10];
    $name = mysql_real_escape_string($values[1]);
    $email = mysql_real_escape_string($values[2]);
    srand(time());
    $pool = "ABCDEFGHIJKLMNOPQRSTUZWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "1234567890";
    $pool .="!@#$%^&*()_-+=[]{};:<>,./?|`~";
    for($l=0; $l<10; $l++) {
      $pw .= substr($pool, (rand()%(strlen($pool))), 1);
      }
    $hash_value = hash("sha512", $pw);

    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[4]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1==1) {
      $values1 = mysql_fetch_row($result1);
      $rank=$values1[0];
      }
    else {
      for($j = 0; $j < $rows1; $j++) {
        $values1 = mysql_fetch_row($result1);
        $query2 = "SELECT Rank_ID, RT_ID FROM EH_Ranks Where Rank_ID=$values1[0]";
        $result2 = mysql_query($query2, $mysql_link);
        $rows2 = mysql_num_rows($result2);
        for($k = 0; $k < $rows2; $k++) {
          $values2 = mysql_fetch_row($result2);
          if($values2[1]==1 && $values[3]==1) { // Sith
            $rank = $values1[0];
            }
          elseif($values2[1]==2 && $values[3]==3) { //Obbie
            $rank = $values1[0];
            }
          elseif($values2[1]==3 && $values[3]==2) { //Krath
            $rank = $values1[0];
            }
          }
        }
      }

    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[5]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $pos=$values1[0];
      }

    $query1 = "INSERT INTO EH_Members (Name, Email, UserPassword) VALUES ('$name', '$email', '$hash_value')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $pri=1;
    $unitspot=0;
    if($values[5]==$btm || $values[5]==$btl) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='BT' AND OriginalValue=$values[6]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        $unitspot=$values[7];
        }
      }
    if($values[5]==$house) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='House' AND OriginalValue=$values[6]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($values[5]==$clan) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Clan' AND OriginalValue=$values[6]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($values[5]<=6) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='DC' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($values[5]==$trainpos) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TrainUnit' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='RsvUnit' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Member', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);

    $query1 = "INSERT INTO EH_Members_Groups (Member_ID, Group_ID, Active, isPrimary, JoinDate) VALUES ('$newid', '$groupid', '$active', '$pri', '$values[8]')";
    $result1 = mysql_query($query1, $mysql_link);
    $now=time();
    $query1 = "INSERT INTO EH_Members_Positions (Member_ID, Group_ID, Position_ID, isGroupPrimary, PositionDate) VALUES ('$newid', '$groupid', '$pos', '1', '$now')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Ranks (Member_ID, Group_ID, Rank_ID, PromotionDate) VALUES ('$newid', '$groupid', '$rank', '$values[9]')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Units (Member_ID, Group_ID, Unit_ID, UnitDate, UnitPosition) VALUES ('$newid', '$groupid', '$unit', '$now', '$unitspot')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_members Transferred.<br>";
  //End Import db_members

  //Begin Import: db_members_medals
  $query = "SELECT `id`, PIN, Medal, Quantity FROM db_members_medals";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $member=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $query2 = "SELECT `ID`, AwarderPIN, Reason, UNIX_TIMESTAMP(`Date`) FROM db_member_medal_history WHERE Recipient=$values[1] AND Medal=$values[2]";
    $result2 = mysql_query($query2, $newmysql_link);
    $rows2 = mysql_num_rows($result2);
    for($j = 0; $j < $rows2; $j++) {
      $values2 = mysql_fetch_row($result2);
      $reason = mysql_real_escape_string($values2[2]);
      $from=0;
      $query3 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values2[1]";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      if($rows3) {
        $values3 = mysql_fetch_row($result3);
        $from=$values3[0];
        }
      $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status) VALUES ('$member', '$medal', '$from', '$groupid', '$values2[3]', '$reason', '1')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    $now=time();
    for($j=$values[3]; $j>$rows2; $j--) {
      $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status) VALUES ('$member', '$medal', '0', '$groupid', '$now', 'Adding to Database', '1')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "db_members_medals Transferred.<br>";
  //End Import db_members_medals

  //Begin Import: db_pending_medals
  $query = "SELECT ID, Recipient, AwarderPIN, Medal, Reason FROM db_pending_medals";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $reason = mysql_real_escape_string($values[4]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $for=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, Reason, Status) VALUES ('$for', '$medal', '$from', '$groupid', '$reason', '0')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_pending_medals Transferred.<br>";
  //End Import db_pending_medals

  //Begin Import: db_comps
  $query = "SELECT id, title, submitter, UNIX_TIMESTAMP(startdate), UNIX_TIMESTAMP(enddate), parties, awards, otherinfo FROM db_comps";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $title = mysql_real_escape_string($values[1]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $admin=$values1[0];
      }
    $start = mysql_real_escape_string($values[3]);
    $end = mysql_real_escape_string($values[4]);
    $scope = mysql_real_escape_string($values[5]);
    $award = mysql_real_escape_string($values[6]);
    $info = mysql_real_escape_string($values[7]);
    $query1 = "INSERT INTO EH_Competitions (Name, Admin_ID, Group_ID, StartDate, EndDate, Scope, Awards, Description) VALUES ('$title', '$admin', '$groupid', '$start', '$end', '$scope', '$award', '$info')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_comps Transferred.<br>";
  //End Import db_comps
$acad = 3;
  //Begin Import: db_courses
  $query = "SELECT id, coursename, short, prof, description, pass, hide FROM db_courses";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $title = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $prof=$values1[0];
      }
    $desc = mysql_real_escape_string($values[4]);
    $pass = mysql_real_escape_string($values[5]);
    $active = !mysql_real_escape_string($values[6]);
    $query1 = "INSERT INTO EH_Training (Name, Abbr, TAc_ID, Available, Description, MaxPoints, Grader) VALUES ('$title', '$abbr', '$acad', '$active', '$desc', '$pass', '$prof')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Courses', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_courses Transferred.<br>";
  //End Import db_courses

  //Begin Import: db_courses_grads
  $query = "SELECT id, course, student, grade, UNIX_TIMESTAMP(testdate) FROM db_courses_grads";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $grade = mysql_real_escape_string($values[3]);
    $day = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_Training_Complete (Training_ID, Member_ID, DateComplete, Score) VALUES ('$course', '$mem', '$day', '$grade')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_courses_grads Transferred.<br>";
  //End Import db_courses_grads

  //Begin Import: db_courses_notes
  $query = "SELECT id, course, notetitle, notetext, note_order FROM db_courses_notes";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    $title = mysql_real_escape_string($values[2]);
    $body = mysql_real_escape_string($values[3]);
    $order = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_Training_Notes (Training_ID, SectionName, SectionText, SortOrder) VALUES ('$course', '$title', '$body', '$order')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_courses_notes Transferred.<br>";
  //End Import db_courses_notes

  //Begin Import: db_courses_test
  $query = "SELECT id, course, question, correct, question_order, type, marks FROM db_courses_test";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    $q = mysql_real_escape_string($values[2]);
    $a = mysql_real_escape_string($values[3]);
    $o = mysql_real_escape_string($values[4]);
    if($values[5]==1)
      $type=2;
    else
      $type=1;
    $val = $values[6];
    $query1 = "INSERT INTO EH_Training_Exams (Training_ID, Question, Type, Answer, SortOrder, Points) VALUES ('$course', '$q', '$type', '$a', '$o', '$val')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_courses_test Transferred.<br>";
  //End Import db_courses_test

  //Begin Import: db_news
  $query = "SELECT id, title, `text`, poster, UNIX_TIMESTAMP(`date`) FROM db_news";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Courses' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $poster=$values1[0];
      }
    $title = mysql_real_escape_string($values[1]);
    $body = mysql_real_escape_string($values[2]);
    $day = mysql_real_escape_string($values[4]);
    $query1 = "INSERT INTO EH_News (Group_ID, Topic, Poster_ID, DatePosted, Body) VALUES ('$groupid', '$title', '$poster', '$day', '$body')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_news Transferred.<br>";
  //End Import db_news

  //Begin Import: db_reports
  $query = "SELECT id, position, clan, house, battleteam, numb, `text`, UNIX_TIMESTAMP(`date`), poster, pin FROM db_reports";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $pos=$values1[0];
      }
    $unit=0;
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='BT' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $unit=$values1[0];
      }
    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='House' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Clan' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    if($unit==0) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='DC' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    $num = mysql_real_escape_string($values[5]);
    $body = mysql_real_escape_string($values[6]);
    $day = mysql_real_escape_string($values[7]);
    $poster = mysql_real_escape_string($values[8]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[9]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $query1 = "INSERT INTO EH_Reports (Poster, Report, ReportNum, Group_ID, Unit_ID, Position_ID, Member_ID, ReportDate) VALUES ('$mem', '$body', '$num', '$groupid', '$unit', '$pos', '$mem', '$day')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "db_reports Transferred.<br>";
  //End Import db_reports

  echo "<p>Manually Fix:<br>\n";
  echo "Fix Medals<br>\n";
  echo "Fix Position isCS/CSOrder<br>\n";
  echo "Fix Courses Sort Order, and Course Categories<br>\n";
  echo "</p>\n";
  }
if($_GET['db']=="tc") {
  echo "<p>Beginning TIE Corps Data import</p>\n";
  $newmysql_link = mysql_connect($db_host, "emperors_tc", "27iB1bDPKwaw", TRUE);
  mysql_select_db("emperors_members", $newmysql_link);
  $groupid=2;
  //Begin Import: TC_medals
  $query = "INSERT INTO `EH_Medals_Groups` (`Name`, `Abbr`, `Group_ID`) VALUES('Medal of Tactics', 'MoT', 2);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'MGMoT', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Groups` (`Name`, `Abbr`, `Group_ID`) VALUES('Medal of Communication', 'MoC', 2);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'MGMoC', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Groups` (`Name`, `Abbr`, `Group_ID`) VALUES('Iron Star', 'IS', 2);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'MGIS', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);

  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Medal of Honour', 'MoH', 0, 1, 2, '', 1, 1, 33);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '1', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Imperial Cross', 'IC', 0, 1, 2, '', 1, 1, 42);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '3', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Order of the Renegade', 'OoR', 0, 1, 2, '', 1, 1, 43);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '2', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Grand Order of the Emperor', 'GOE', 0, 1, 2, '', 1, 1, 44);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '4', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Gold Star of the Empire', 'GS', 0, 1, 2, '', 1, 1, 45);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '5', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Silver Star of the Empire', 'SS', 0, 1, 2, '', 1, 1, 46);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '6', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Bronze Star of the Empire', 'BS', 0, 1, 2, '', 1, 1, 47);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '7', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Palpatine Crescent', 'PC', 0, 1, 2, '', 1, 1, 48);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '8', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Imperial Security Medal', 'ISM', 0, 1, 2, '', 1, 1, 49);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '9', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Medal of Instruction', 'MoI', 0, 3, 2, '', 1, 1, 50);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '18', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='MGMoT' AND OriginalValue=0";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $mg=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Blue Hammer', 'bh', $mg, 2, 2, '', 1, 1, 51);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '21', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Green Hammer', 'gh', $mg, 2, 2, '', 1, 1, 52);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '20', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Red Hammer', 'rh', $mg, 2, 2, '', 1, 1, 53);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '19', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='MGMoC' AND OriginalValue=0";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $mg=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Bronze Oak Cluster', 'BoC', $mg, 2, 2, '', 1, 1, 54);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '28', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Silver Oak Cluster', 'SoC', $mg, 2, 2, '', 1, 1, 55);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '27', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Gold Oak Cluster', 'GoC', $mg, 2, 2, '', 1, 1, 56);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '26', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Platinum Oak Cluster', 'PoC', $mg, 2, 2, '', 1, 1, 57);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '25', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Diamond Oak Cluster', 'DoC', $mg, 2, 2, '', 1, 1, 58);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '24', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='MGIS' AND OriginalValue=0";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $mg=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Bronze Ribbon', 'BR', $mg, 2, 2, '', 1, 1, 59);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '17', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Bronze Wings', 'BW', $mg, 2, 2, '', 1, 1, 60);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '13', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Silver Ribbon', 'SR', $mg, 2, 2, '', 1, 1, 61);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '16', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Silver Wings', 'SW', $mg, 2, 2, '', 1, 1, 62);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '12', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Gold Ribbon', 'GR', $mg, 2, 2, '', 1, 1, 63);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '15', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Gold Wings', 'GW', $mg, 2, 2, '', 1, 1, 64);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '11', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Platinum Ribbon', 'PR', $mg, 2, 2, '', 1, 1, 65);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '14', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Platinum Wings', 'PW', $mg, 2, 2, '', 1, 1, 66);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '10', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Legion of Combat', 'LoC', 0, 3, 2, '', 1, 1, 67);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '22', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Distinguished Flying Cross', 'DFC', 0, 3, 2, '', 1, 1, 68);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '23', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Order of the Vanguard', 'OV', 0, 3, 2, '', 1, 1, 69);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '35', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Commendation of Bravery', 'CoB', 0, 1, 2, '', 1, 1, 70);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '32', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Commendation of Excellence', 'CoE', 0, 1, 2, '', 1, 1, 71);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '30', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Commendation of Loyalty', 'CoL', 0, 1, 2, '', 1, 1, 72);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '29', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Commendation of Service', 'CoS', 0, 1, 2, '', 1, 1, 73);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '31', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Letter of Achievement', 'LoA', 0, 1, 2, '', 1, 0, 74);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '33', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Medals` (`Name`, `Abbr`, `MG_ID`, `MT_ID`, `Group_ID`, `Image`, `Active`, `ShowOnID`, `SortOrder`) VALUES('Medal of Allegiance', 'MoA', 0, 1, 2, '', 1, 1, 75);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Medal', '33', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);

  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=18";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $medal=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Medal of Instruction - Blue Cross', 'MoI-BC', 2, 5, 10, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Medal of Instruction - Gold Cross', 'MoI-GC', 2, 10, 25, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Medal of Instruction - Platinum Cross', 'MoI-PC', 2, 25, 50, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Medal of Instruction - Emerald Cross', 'MoI-EC', 2, 50, 100, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Medal of Instruction - Diamond Cross', 'MoI-DC', 2, 100, 2147483647, 0);";
  $result = mysql_query($query, $mysql_link);

  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=22";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $medal=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Legion of Combat - Copper Scimitar', 'LoC-CS', 2, 5, 50, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Legion of Combat - Iridium Scimitar', 'LoC-IS', 2, 50, 100, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Legion of Combat - Thallium Scimitar', 'LoC-TS', 2, 100, 200, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Legion of Combat - Rubidium Scimitar', 'LoC-RS', 2, 200, 500, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Legion of Combat - Platinum Scimitar', 'LoC-PS', 2, 500, 2147483647, 0);";
  $result = mysql_query($query, $mysql_link);


  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=23";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $medal=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Distinguished Flying Cross - Bronze Wings', 'DFC-BW', 2, 5, 10, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Distinguished Flying Cross - Silver Wings', 'DFC-SW', 2, 10, 20, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES($medal, 'Distinguished Flying Cross - Gold Wings', 'DFC-GW', 2, 20, 2147483647, 0);";
  $result = mysql_query($query, $mysql_link);

  $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=35";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $medal=$values1[0];
    }
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Second Echelon', 'OV-2E', 2, 2, 3, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Third Echelon', 'OV-3E', 2, 3, 4, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Fourth Echelon', 'OV-4E', 2, 4, 5, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Fith Echelon', 'OV-5E', 2, 5, 6, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Sixth Echelon', 'OV-6E', 2, 6, 7, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Seventh Echelon', 'OV-7E', 2, 7, 8, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Eighth Echelon', 'OV-8E', 2, 8, 9, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Nineth Echelon', 'OV-9E', 2, 9, 10, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Tenth Echelon', 'OV-10E', 2, 10, 11, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Eleventh Echelon', 'OV-11E', 2, 11, 12, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Twelveth Echelon', 'OV-12E', 2, 12, 13, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Thirteenth Echelon', 'OV-13E', 2, 13, 14, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Fourteenth Echelon', 'OV-14E', 2, 14, 15, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Fifteenth Echelon', 'OV-15E', 2, 15, 16, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Sixteenth Echelon', 'OV-16E', 2, 16, 17, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Seventeenth Echelon', 'OV-17E', 2, 17, 18, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Eighteenth Echelon', 'OV-18E', 2, 18, 19, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Nineteenth Echelon', 'OV-19E', 2, 19, 20, 0);";
  $result = mysql_query($query, $mysql_link);
  $query = "INSERT INTO `EH_Medals_Upgrades` (`Medal_ID`, `Name`, `Abbr`, `Group_ID`, `Lower`, `Upper`, `Recycles`) VALUES(69, 'Order of the Vanguard - Twentieth Echelon', 'OV-20E', 2, 20, 21, 0);";
  $result = mysql_query($query, $mysql_link);
  echo "TC_medals Transferred.<br>";
  //End Import TC_medals

  //Begin Import: TC_ranks
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Cadet', 'CT', 1, 4, 2, 1);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '1', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Sub-Lieutenant', 'SL', 1, 4, 2, 2);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '2', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Lieutenant', 'LT', 1, 4, 2, 3);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '3', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Lieutenant Commander', 'LCM', 1, 4, 2, 4);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '4', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Commander', 'CM', 1, 4, 2, 5);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '5', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Captain', 'CPT', 1, 4, 2, 6);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '6', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Major', 'MAJ', 1, 4, 2, 7);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '7', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Lieutenant Colonel', 'LC', 1, 4, 2, 8);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '8', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Colonel', 'COL', 1, 4, 2, 9);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '9', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('General', 'GN', 1, 4, 2, 10);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '10', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Rear Admiral', 'RA', 1, 5, 2, 11);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '11', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Vice Admiral', 'VA', 1, 5, 2, 12);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '12', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Admiral', 'AD', 1, 5, 2, 13);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '13', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Fleet Admiral', 'FA', 1, 5, 2, 14);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '14', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('High Admiral', 'HA', 1, 5, 2, 15);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '15', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Sector Admiral', 'SA', 1, 5, 2, 16);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '16', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query = "INSERT INTO `EH_Ranks` (`Name`, `Abbr`, `Active`, `RT_ID`, `Group_ID`, `SortOrder`) VALUES('Grand Admiral', 'GA', 1, 5, 2, 17);";
  $result = mysql_query($query, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Rank', '17', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  echo "TC_ranks Transferred.<br>";
  //End Import TC_ranks

  //Begin Import: TC_ehposition
  $query = "SELECT EH_ID, EH_Name, EH_Abbrev from TC_ehposition";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Positions (Name, Abbr, Group_ID) VALUES ('$name', '$abbr', '$groupid')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'CPosition', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_ehposition Transferred.<br>";
  //End Import TC_ehposition

  //Begin Import: TC_extra_position
  $query = "SELECT EP_ID, EP_Name, EP_Abbrev from TC_extra_position";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Positions (Name, Abbr, Group_ID) VALUES ('$name', '$abbr', '$groupid')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'EPosition', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_extra_position Transferred.<br>";
  //End Import TC_extra_position

  //Begin Import: TC_position
  $query = "SELECT POS_ID, POS_Name, POS_Abbrev from TC_position WHERE POS_Abbrev!='EHCS'";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Positions (Name, Abbr, Group_ID) VALUES ('$name', '$abbr', '$groupid')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Position', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_position Transferred.<br>";
  //End Import TC_position

  //Begin Import: TC_fighters
  $query = "SELECT F_ID, F_Name, F_Abbrev from TC_fighters";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Ships (Name, Abbr) VALUES ('$name', '$abbr')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Craft', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_fighters Transferred.<br>";
  //End Import TC_fighters

  //Begin Import: TC_ship
  $query = "SELECT SHP_ID, SHP_Type, SHP_Name FROM TC_ship";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Craft' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $type=$values1[0];
      }
    $name = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Bases (Name, BT_ID, Types) VALUES ('$name', '1', '$type')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Bases', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_ship Transferred.<br>";
  //End Import TC_ship

  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Admirality Board', '3', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TCCS', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);

  //Begin Import: TC_wing
  $query = "SELECT W_ID, W_Status, W_Name, W_Ship, W_Motto, W_Banner, W_URL, W_MB, W_Nick FROM TC_wing";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Bases' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $base=$values1[0];
      }
    $name = mysql_real_escape_string($values[2]);
    $active = mysql_real_escape_string($values[1]);
    $motto = mysql_real_escape_string($values[4]);
    $banner = mysql_real_escape_string($values[5]);
    $url = mysql_real_escape_string($values[6]);
    $mb = mysql_real_escape_string($values[7]);
    $nick = mysql_real_escape_string($values[8]);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, Base_ID, SiteURL, MessageBoard, Banner, Motto, Nickname) VALUES ('$name', '5', '$active', '$groupid', '$base', '$url', '$mb', '$banner', '$motto', '$nick')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Wing', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_wing Transferred.<br>";
  //End Import TC_wing

  //Begin Import: TC_squadron
  $query = "SELECT SQN_ID, SQN_Status, SQN_Name, SQN_Wing, SQN_Motto, SQN_F1Nick, SQN_F2Nick, SQN_F3Nick, SQN_F1Motto, SQN_F2Motto, SQN_F3Motto, SQN_F1Craft, SQN_F2Craft, SQN_F3Craft, SQN_Banner, SQN_URL, SQN_Obj, SQN_MB, SQN_Nick FROM TC_squadron";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[2]);
    $active = mysql_real_escape_string($values[1]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Craft' AND OriginalValue=$values[11]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $f1craft=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Craft' AND OriginalValue=$values[12]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $f2craft=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Craft' AND OriginalValue=$values[13]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $f3craft=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Wing' AND OriginalValue=$values[3]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $wing=$values1[0];
      }
    $motto = mysql_real_escape_string($values[4]);
    $f1nick = mysql_real_escape_string($values[5]);
    $f2nick = mysql_real_escape_string($values[6]);
    $f3nick = mysql_real_escape_string($values[7]);
    $f1motto = mysql_real_escape_string($values[8]);
    $f2motto = mysql_real_escape_string($values[9]);
    $f3motto = mysql_real_escape_string($values[10]);
    $banner = mysql_real_escape_string($values[14]);
    $url = mysql_real_escape_string($values[15]);
    $get_Objective = array("","Assassination","Assault","Aviation","Close support","Deep strike","Escort","Escort/interception","Fighter suppression","Fleet Commander's escort","Grand Master's escort","Heavy assault","Heavy strike","Heavy support","Infiltration","Interdiction","Kidnapping","Long-range support","Pacification","Psychological warfare","Reconnaissance","Special insertion/extraction","Special operations","Strike","Test","Training","VIP escort");
    $objective = $get_Objective[$values[16]];
    $mb = mysql_real_escape_string($values[17]);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, Master_ID, SiteURL, MessageBoard, Banner, Motto, Nickname, MissionRoll) VALUES ('$name', '6', '$active', '$groupid', '$wing', '$url', '$mb', '$banner', '$motto', '$nick', '$objective')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Squad', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, Master_ID, Motto, Nickname, Craft) VALUES ('Flight 1', '7', '$active', '$groupid', '$newid', '$f1motto', '$f1nick', '$f1craft')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, Master_ID, Motto, Nickname, Craft) VALUES ('Flight 2', '7', '$active', '$groupid', '$newid', '$f2motto', '$f2nick', '$f2craft')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID, Master_ID, Motto, Nickname, Craft) VALUES ('Flight 3', '7', '$active', '$groupid', '$newid', '$f3motto', '$f3nick', '$f3craft')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_squadron Transferred.<br>";
  //End Import TC_squadron

  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Platform Daedalus', '1', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TCTrain', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);
  $query1 = "INSERT INTO EH_Units (Name, UT_ID, Active, Group_ID) VALUES ('Reserves', '2', '1', '$groupid')";
  $result1 = mysql_query($query1, $mysql_link);
  $newid = mysql_insert_id($mysql_link);
  $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'TCRsv', '0', '$newid')";
  $result1 = mysql_query($query1, $mysql_link);

  //Begin Import: TC_members
  $query = "SELECT M_PIN, M_Name, M_Rank, M_EMail, M_Status, M_Position, M_Sqn, M_SqnSlot, M_Date, M_UniformFileName, M_Quote, M_CraftType, M_CraftName, M_FCHGPts, M_ExtraPosition, M_LastPromo, M_NonTCPosition, M_URL, M_Wing, M_UniformDate FROM TC_members WHERE M_PIN!=1 AND (M_Status!=4 OR M_Status<=> NULL) Order By M_PIN";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name=$email=$active=$quote=$url="";
    $name = mysql_real_escape_string($values[1]);
    $email = mysql_real_escape_string($values[3]);
    if($values[4]==3)
      $active=1;
    else
      $active=0;
    srand(time());
    $pool = "ABCDEFGHIJKLMNOPQRSTUZWXYZ";
    $pool .= "abcdefghijklmnopqrstuvwxyz";
    $pool .= "1234567890";
    $pool .="!@#$%^&*()_-+=[]{};:<>,./?|`~";
    for($l=0; $l<10; $l++) {
      $pw .= substr($pool, (rand()%(strlen($pool))), 1);
      }
    $hash_value = hash("sha512", $pw);
    if($values[10])
      $quote = mysql_real_escape_string($values[10]);
    if($values[18])
      $url = mysql_real_escape_string($values[18]);
    $query1 = "INSERT INTO EH_Members (Name, Email, UserPassword, Quote, URL) VALUES ('$name', '$email', '$hash_value', '$quote', '$url')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $pri=1;
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Member', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Groups (Member_ID, Group_ID, Active, isPrimary, JoinDate) VALUES ('$newid', '$groupid', '$active', '$pri', '$values[8]')";
    $result1 = mysql_query($query1, $mysql_link);
    if($values[2]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Rank' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $rank=$values1[0];
        }
      }
    else {
      $rank=0;
      }
    $promodate = $values[15];
    $now=time();
    $query1 = "INSERT INTO EH_Members_Ranks (Member_ID, Group_ID, Rank_ID, PromotionDate) VALUES ('$newid', '$groupid', '$rank', '$promodate')";
    $result1 = mysql_query($query1, $mysql_link);
    $unitspot=0;
    if($values[5]==7) { //EHCS
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='CPosition' AND OriginalValue=$values[16]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $position=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TCCS' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($values[5]>=10) {//TCCS
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[5]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $position=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TCCS' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($values[5]==1) { //TRN
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[5]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $position=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TCTrain' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($values[5]<7 && $values[5]) { //TC member
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Position' AND OriginalValue=$values[5]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $position=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Squad' AND OriginalValue=$values[6]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        $unitspot=$values[7];
        }
      if($unit==0) {
        $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Wing' AND OriginalValue=$values[18]";
        $result1 = mysql_query($query1, $mysql_link);
        $rows1 = mysql_num_rows($result1);
        if($rows1) {
          $values1 = mysql_fetch_row($result1);
          $unit=$values1[0];
          }
        }
      }
    else { //RSV
      $position=0;
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='TCRsv' AND OriginalValue=0";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    $query1 = "INSERT INTO EH_Members_Units (Member_ID, Group_ID, Unit_ID, UnitDate, UnitPosition) VALUES ('$newid', '$groupid', '$unit', '$now', '$unitspot')";
    $result1 = mysql_query($query1, $mysql_link);
    $query1 = "INSERT INTO EH_Members_Positions (Member_ID, Group_ID, Position_ID, isGroupPrimary, PositionDate) VALUES ('$newid', '$groupid', '$position', '1', '$now')";
    $result1 = mysql_query($query1, $mysql_link);
    $extrapositions = str_split($values[14]);
    for($j=1; $j<count($extrapositions); $j++) {
      if($extrapositions[$j]) {
        $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='EPosition' AND OriginalValue=$j";
        $result1 = mysql_query($query1, $mysql_link);
        $rows1 = mysql_num_rows($result1);
        if($rows1) {
          $values1 = mysql_fetch_row($result1);
          $position=$values1[0];
          }
        $query1 = "INSERT INTO EH_Members_Positions (Member_ID, Group_ID, Position_ID, isGroupPrimary, PositionDate) VALUES ('$newid', '$groupid', '$position', '0', '$now')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      }
    if($values[9]) {
      $fn = mysql_real_escape_string($values[9]);
      $fd = mysql_real_escape_string($values[19]);
      $query1 = "INSERT INTO EH_Members_Uniforms (Member_ID, Group_ID, Filename, UniformDate, Approved) VALUES ('$newid', '$groupid', '$fn', '$fd', '1')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($values[11]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Craft' AND OriginalValue=$values[11]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $craft=$values1[0];
        }
      $query1 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) VALUES ('$newid', '4', '$craft')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    $craftname = mysql_real_escape_string($values[12]);
    if($craftname) {
      $query1 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) VALUES ('$newid', '5', '$craftname')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($values[13]) {
      $query1 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) VALUES ('$newid', '1', '$values[13]')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_members Transferred.<br>";
  //End Import TC_members

  //Begin Import: TC_competitions
  $query = "SELECT C_ID, C_PIN, C_Name, C_Targets, C_StartDate, C_EndDate, C_Medals, C_Description FROM TC_competitions";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $admin=$values1[0];
      }
    $name = mysql_real_escape_string($values[2]);
    $scope = mysql_real_escape_string($values[3]);
    $start = mysql_real_escape_string($values[4]);
    $end = mysql_real_escape_string($values[5]);
    $awards = mysql_real_escape_string($values[6]);
    $desc = mysql_real_escape_string($values[7]);
    $query1 = "INSERT INTO EH_Competitions (Name, Admin_ID, Group_ID, StartDate, EndDate, Scope, Awards, Description) VALUES ('$name', '$admin', '$groupid', '$start', '$end', '$scope', '$awards', '$desc')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_competitions Transferred.<br>";
  //End Import TC_competitions

  //Begin Import: TC_INPR
  $query = "SELECT INPR_ID, INPR_PIN, INPR_Date, INPR_Gender, INPR_Species, INPR_DateBirth, INPR_PlaceBirth, INPR_Marital, INPR_Family, INPR_Social, INPR_SigYouth, INPR_SigAdult, INPR_AlignAtt, INPR_Previous, INPR_Hobbies, INPR_Tragedies, INPR_PhobiaAllergy, INPR_View, INPR_Enlisting, INPR_Comments FROM TC_INPR";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $date = mysql_real_escape_string($values[2]);
    $gender = mysql_real_escape_string($values[3]);
    $species = mysql_real_escape_string($values[4]);
    $birth = mysql_real_escape_string($values[5]);
    $placebirth = mysql_real_escape_string($values[6]);
    $Marital = mysql_real_escape_string($values[7]);
    $fam = mysql_real_escape_string($values[8]);
    $social = mysql_real_escape_string($values[9]);
    $sigy = mysql_real_escape_string($values[10]);
    $siga = mysql_real_escape_string($values[11]);
    $align = mysql_real_escape_string($values[12]);
    $prev = mysql_real_escape_string($values[13]);
    $hobbies = mysql_real_escape_string($values[14]);
    $trag = mysql_real_escape_string($values[15]);
    $phob = mysql_real_escape_string($values[16]);
    $view = mysql_real_escape_string($values[17]);
    $enlisting = mysql_real_escape_string($values[18]);
    $comments = mysql_real_escape_string($values[19]);
    $query1 = "INSERT INTO EH_Members_INPR (Member_ID, UpdateDate, Gender, Species, Birthdate, PlaceBirth, Relationship, Family, Social, SigYouth, SigAdult, AlignAtt, Previous, Hobbies, Traggedies, PhobiaAllergy, View, Enlisting, Comments) VALUES ('$mem', '$date', '$gender', '$species', '$birth', '$placebirth', '$Marital', '$fam', '$social', '$sigy', '$siga', '$align', '$prev', '$hobbies', '$trag', '$phob', '$view', '$enlisting', '$comments')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_INPR Transferred.<br>";
  //End Import TC_INPR
  $acadid=2;
  //Begin Import: TC_iwats
  $query = "SELECT I_ID, I_Name, I_Abbrev, I_Status, I_PROF, I_Pct, I_URL FROM TC_iwats Order By I_Name";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 1; $i <=$rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[4]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[4]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $admin=$values1[0];
        }
      }
    else {
      $admin=0;
      }
    $name = mysql_real_escape_string($values[1]);
    $abbr = mysql_real_escape_string($values[2]);
    if($values[3]==1)
      $status = 1;
    else
      $status=0;
    $percent = mysql_real_escape_string($values[5]);
    $url = mysql_real_escape_string($values[6]);
    $query1 = "INSERT INTO EH_Training (Name, Abbr, TAc_ID, Available, SortOrder, MinPoints, NotesFile) VALUES ('$name', '$abbr', '$acadid', '$status', '$i', '$percent', '$url')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwats', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_iwats Transferred.<br>";
  //End Import TC_iwats

  //Begin Import: TC_iwats_exams
  $query = "SELECT TC_iwats_exams.*, TC_iwats_answers.* FROM TC_iwats_exams, TC_iwats_answers WHERE TC_iwats_exams.IE_CourseID=TC_iwats_answers.IA_CourseID";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[4]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwats' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      $q = mysql_real_escape_string($values[3]);
      $a = mysql_real_escape_string($values[37]);
      if($q && 1<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '1')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."1', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[4]);
      $a = mysql_real_escape_string($values[38]);
      if($q && 2<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '2')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."2', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[5]);
      $a = mysql_real_escape_string($values[39]);
      if($q && 3<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '3')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."3', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[6]);
      $a = mysql_real_escape_string($values[40]);
      if($q && 4<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '4')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."4', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[6]);
      $a = mysql_real_escape_string($values[40]);
      if($q && 5<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '5')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."5', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[7]);
      $a = mysql_real_escape_string($values[41]);
      if($q && 6<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '6')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."6', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[8]);
      $a = mysql_real_escape_string($values[42]);
      if($q && 7<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '7')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."7', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[9]);
      $a = mysql_real_escape_string($values[43]);
      if($q && 8<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '8')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."8', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[10]);
      $a = mysql_real_escape_string($values[44]);
      if($q && 9<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '9')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."9', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[11]);
      $a = mysql_real_escape_string($values[45]);
      if($q && 10<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '10')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."10', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[12]);
      $a = mysql_real_escape_string($values[46]);
      if($q && 11<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '11')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."11', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[13]);
      $a = mysql_real_escape_string($values[47]);
      if($q && 12<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '12')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."12', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[14]);
      $a = mysql_real_escape_string($values[48]);
      if($q && 13<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '13')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."13', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[15]);
      $a = mysql_real_escape_string($values[49]);
      if($q && 14<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '14')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."14', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[16]);
      $a = mysql_real_escape_string($values[50]);
      if($q && 15<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '15')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."15', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[17]);
      $a = mysql_real_escape_string($values[51]);
      if($q && 16<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '16')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."16', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[18]);
      $a = mysql_real_escape_string($values[52]);
      if($q && 17<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '17')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."17', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[19]);
      $a = mysql_real_escape_string($values[53]);
      if($q && 18<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '18')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."18', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[20]);
      $a = mysql_real_escape_string($values[54]);
      if($q && 19<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '19')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."19', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[21]);
      $a = mysql_real_escape_string($values[55]);
      if($q && 20<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '20')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."20', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[22]);
      $a = mysql_real_escape_string($values[56]);
      if($q && 21<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '21')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."21', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[23]);
      $a = mysql_real_escape_string($values[57]);
      if($q && 22<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '22')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."22', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[24]);
      $a = mysql_real_escape_string($values[58]);
      if($q && 23<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '23')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."23', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[25]);
      $a = mysql_real_escape_string($values[59]);
      if($q && 24<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '24')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."24', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[26]);
      $a = mysql_real_escape_string($values[60]);
      if($q && 25<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '25')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."25', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[27]);
      $a = mysql_real_escape_string($values[61]);
      if($q && 26<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '26')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."26', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[28]);
      $a = mysql_real_escape_string($values[62]);
      if($q && 27<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '27')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."27', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[29]);
      $a = mysql_real_escape_string($values[63]);
      if($q && 28<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '28')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."28', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[30]);
      $a = mysql_real_escape_string($values[64]);
      if($q && 29<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '29')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."29', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      $q = mysql_real_escape_string($values[31]);
      $a = mysql_real_escape_string($values[65]);
      if($q && 30<=$values[2]) {
        $query1 = "INSERT INTO EH_Training_Exams (Question, Type, Answer, Training_ID, SortOrder) VALUES ('$q', '1', '$a', '$course', '30')";
        $result1 = mysql_query($query1, $mysql_link);
        $newid = mysql_insert_id($mysql_link);
        $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'iwatsTest', '".$course."30', '$newid')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      }
      }
    }
  echo "TC_iwats_exams Transferred.<br>";
  //End Import TC_iwats_exams

  //Begin Import: TC_iwats_passed
  $query = "SELECT IP_CourseID, IP_PIN, IP_Pct, IP_Date FROM TC_iwats_passed WHERE IP_Passed=1";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $member=$course=0;
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwats' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $course=$values1[0];
      }
    else {
      $course=0;
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $member=$values1[0];
      }
    if($values[2]==999)
      $score=0;
    else
      $score = mysql_real_escape_string($values[2]);
    $day = mysql_real_escape_string($values[3]);
    if($member && $course) {
      $query1 = "INSERT INTO EH_Training_Complete (Training_ID, Member_ID, DateComplete, Score) VALUES ('$course', '$member', '$day', '$score')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_iwats_passed Transferred.<br>";
  //End Import TC_iwats_passed

  //Begin Import: TC_news
  $query = "SELECT N_PIN, N_Date, N_Title, N_News FROM TC_news";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    $day = mysql_real_escape_string($values[1]);
    $title = mysql_real_escape_string($values[2]);
    $body = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_News (Group_ID, Topic, Poster_ID, DatePosted, Body) VALUES ('$groupid', '$title', '$mem', '$day', '$body')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_news Transferred.<br>";
  //End Import TC_news

  //Begin Import: TC_report
  $query = "SELECT RP_PIN, RP_Date, RP_Type, RP_Name, RP_Seq, RP_Text, RP_IDline FROM TC_report";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $unit=$pos=$mem=0;
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $mem=$values1[0];
      }
    if($values[2]==1) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Squad' AND OriginalValue=$values[3]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($values[2]==2) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Wing' AND OriginalValue=$values[3]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $unit=$values1[0];
        }
      }
    elseif($values[2]==4) {
      $query1 = "SELECT Position_ID FROM EH_Positions WHERE Abbr='DEAN'";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $pos=$values1[0];
        }
      }
    elseif($values[2]==5) {
      $query1 = "SELECT Position_ID FROM EH_Positions WHERE Abbr='SOO'";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $pos=$values1[0];
        }
      }
    elseif($values[2]==6) {
      $query1 = "SELECT Position_ID FROM EH_Positions WHERE Abbr='TCCOM'";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $pos=$values1[0];
        }
      }
    $day = mysql_real_escape_string($values[1]);
    $num = mysql_real_escape_string($values[4]);
    $body = mysql_real_escape_string($values[5]);
    $poster = mysql_real_escape_string($values[6]);
    $query1 = "INSERT INTO EH_Reports (Poster, Report, ReportNum, Group_ID, Unit_ID, Position_ID, Member_ID, ReportDate) VALUES ('$poster', '$body', '$num', '$groupid', '$unit', '$pos', '$mem', '$day')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_report Transferred.<br>";
  //End Import TC_report

  //Begin Import: TC_temp_iwats
  $query = "SELECT TI_PIN, TI_Course, TI_Q1, TI_Q2, TI_Q3, TI_Q4, TI_Q5, TI_Q6, TI_Q7, TI_Q8, TI_Q9, TI_Q10, TI_Q11, TI_Q12, TI_Q13, TI_Q14, TI_Q15, TI_Q16, TI_Q17, TI_Q18, TI_Q19, TI_Q20, TI_Q21, TI_Q22, TI_Q23, TI_Q24, TI_Q25, TI_Q26, TI_Q27, TI_Q28, TI_Q29, TI_Q30 FROM TC_temp_iwats WHERE TI_Status=3";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[0]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $member=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwats' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $course=$values1[0];
        if($values[2]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."1";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[2]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[3]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."2";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[3]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[4]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."3";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[4]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[5]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."4";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[5]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[6]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."5";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[6]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[7]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."6";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[7]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[8]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."7";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[8]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[9]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."8";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[9]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[10]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."9";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[10]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[11]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."10";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[11]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[12]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."11";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[12]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[13]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."12";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[13]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[14]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."13";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[14]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[15]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."14";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[15]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[16]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."15";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[16]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[17]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."16";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[17]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[18]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."17";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[18]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[19]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."18";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[19]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[20]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."19";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[20]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[21]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."20";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[21]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[22]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."21";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[22]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[23]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."22";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[23]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[24]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."23";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[24]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[25]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."24";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[25]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[26]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."25";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[26]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[27]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."26";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[27]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[28]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."27";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[28]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[29]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."28";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[29]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[30]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."29";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[30]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        if($values[31]) {
          $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='iwatsTest' AND OriginalValue=".$course."30";
          $result1 = mysql_query($query1, $mysql_link);
          $rows1 = mysql_num_rows($result1);
          if($rows1) {
            $values1 = mysql_fetch_row($result1);
            $qid=$values1[0];
            }
          $query1 = "INSERT INTO EH_Training_Exams_Complete (Member_ID, Training_ID, TE_ID, Answer, Status) VALUES ('$member', '$course', '$qid', '$values[31]', '2')";
          $result1 = mysql_query($query1, $mysql_link);
          }
        }
      }
    }
  echo "TC_temp_iwats Transferred.<br>";
  //End Import TC_temp_iwats

  //Begin Import: TC_temp_mdl
  $query = "SELECT TM_PIN, TM_RCP, TM_MDL, TM_Reason FROM TC_temp_mdl WHERE TM_Status=3";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $for=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $reason = mysql_real_escape_string($values[3]);
    $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, Reason) VALUES ('$for', '$medal', '$from', '$groupid', '$reason')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_temp_mdl Transferred.<br>";
  //End Import TC_temp_mdl

  //Begin Import: TC_temp_promo
  $query = "SELECT TP_PIN, TP_RCP, TP_Reason FROM TC_temp_promo WHERE TP_Status=3";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $for=$values1[0];
      }
    $reason = mysql_real_escape_string($values[2]);
    $query1 = "INSERT INTO EH_Medals_Complete (For_ID, From_ID, Group_ID, Reason) VALUES ('$for', '$from', '$groupid', '$reason')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_temp_promo Transferred.<br>";
  //End Import TC_temp_promo

  //Begin Import: TC_patch
  $query = "SELECT P_ID, P_Name, P_Platform, P_Type, P_CreatorName, P_Desc, P_Filename, P_ImgFilename, P_Date, P_Updated FROM TC_patch";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $plt = mysql_real_escape_string($values[2]);
    switch($plt) {
      case 1:
      $platform = 24;
      break;
      case 2:
      $platform = 25;
      break;
      case 3:
      $platform = 32;
      break;
      case 4:
      $platform = 26;
      break;
      case 5:
      $platform = 23;
      break;
      case 7:
      $platform = 6;
      break;
      case 8:
      $platform = 11;
      break;
      }
    $type = mysql_real_escape_string($values[3]);
    if($type!=1)
      $type=2;
    $creator = mysql_real_escape_string($values[4]);
    $desc = mysql_real_escape_string($values[5]);
    $fn = mysql_real_escape_string($values[6]);
    $img = mysql_real_escape_string($values[7]);
    $date = mysql_real_escape_string($values[8]);
    $update = mysql_real_escape_string($values[9]);
    $query1 = "INSERT INTO EH_Patches (Name, Filename, PC_ID, Platform_ID, Creator, ReleasedDate, UpdatedDate, Image, Description) VALUES ('$name', '$fn', '$type', '$platform', '$creator', '$date', '$update', '$img', '$desc')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Patch', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_patch Transferred.<br>";
  //End Import TC_patch
  set_time_limit (90);

  //Begin Import: TC_battles
  $query = "SELECT B_ID, B_Name, B_Status, B_Platform, B_Subgroup, B_NR, B_Filename, B_Creator, B_Added, B_Updated, B_WavpackFilename, B_Comments, B_Medal, B_MedalFilename, B_Missions, B_Creator2, B_Creator3, B_Patch1, B_Patch2, B_Patch3, B_Patch4, B_Patch5 FROM TC_battles";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $name = mysql_real_escape_string($values[1]);
    $status = mysql_real_escape_string($values[2]);
    $plt = mysql_real_escape_string($values[3]);
    switch($plt) {
      case 1:
      $platform = 24;
      break;
      case 2:
      $platform = 25;
      break;
      case 3:
      $platform = 32;
      break;
      case 4:
      $platform = 26;
      break;
      case 5:
      $platform = 23;
      break;
      case 7:
      $platform = 6;
      break;
      case 8:
      $platform = 11;
      break;
      }
    $sg = mysql_real_escape_string($values[4]);

    switch($sg) {
      case 1:
      $bc=1;
      break;
      case 2:
      $bc=2;
      break;
      case 3:
      $bc=3;
      break;
      case 4:
      $bc=4;
      break;
      case 5:
      $bc=5;
      break;
      case 6:
      $bc=6;
      break;
      case 7:
      $bc=7;
      break;
      case 8:
      $bc=8;
      break;
      case 9:
      $bc=9;
      break;
      case 10:
      $bc=10;
      break;
      case 11:
      $bc=12;
      break;
      case 12:
      $bc=13;
      break;
      case 13:
      $bc=11;
      break;
      }
    $num = mysql_real_escape_string($values[5]);
    $fn = mysql_real_escape_string($values[6]);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[7]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $creator1=$values1[0];
      }
    else {
      $creator1=0;
      }
    $release=$values[8];
    $update=$values[9];
    $wav=mysql_real_escape_string($values[10]);
    $desc=mysql_real_escape_string($values[11]);
    $medal=mysql_real_escape_string($values[12]);
    $medalimg=mysql_real_escape_string($values[13]);
    $nummis=mysql_real_escape_string($values[14]);
    if($values[15]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[15]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $creator2=$values1[0];
        }
      }
    else {
      $creator2=0;
      }
    if($values[16]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[16]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $creator3=$values1[0];
        }
      }
    else {
      $creator3=0;
      }
    if($values[17]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[17]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch1=$values1[0];
        }
      }
    else {
      $patch1=0;
      }
    if($values[18]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[18]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch2=$values1[0];
        }
      }
    else {
      $patch2=0;
      }
    if($values[19]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[19]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch3=$values1[0];
        }
      }
    else {
      $patch3=0;
      }
    if($values[20]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[20]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch4=$values1[0];
        }
      }
    else {
      $patch4=0;
      }
    if($values[21]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[21]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch5=$values1[0];
        }
      }
    else {
      $patch5=0;
      }
    $query1 = "INSERT INTO EH_Battles (Platform_ID, BattleNumber, BC_ID, Name, Description, NumMissions, Released, Last_Updated, Reward_Name, Reward_Image, Filename, Wav_Pack, Creator_1, Creator_2, Creator_3, Status) VALUES ('$platform', '$num', '$bc', '$name', '$desc', '$nummis', '$release', '$update', '$medal', '$medalimg', '$fn', '$wav', '$creator1', '$creator2', '$creator3', '$status')";
    $result1 = mysql_query($query1, $mysql_link);
    $newid = mysql_insert_id($mysql_link);
    $query1 = "INSERT INTO EH_ConvertInfo (Group_ID, `Table`, OriginalValue, NewValue) VALUES ('$groupid', 'Battle', '$values[0]', '$newid')";
    $result1 = mysql_query($query1, $mysql_link);
    if($patch1) {
      $query1 = "INSERT INTO EH_Battles_Patches (Battle_ID, Patch_ID) VALUES ('$newid', '$patch1')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($patch2) {
      $query1 = "INSERT INTO EH_Battles_Patches (Battle_ID, Patch_ID) VALUES ('$newid', '$patch2')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($patch3) {
      $query1 = "INSERT INTO EH_Battles_Patches (Battle_ID, Patch_ID) VALUES ('$newid', '$patch3')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($patch4) {
      $query1 = "INSERT INTO EH_Battles_Patches (Battle_ID, Patch_ID) VALUES ('$newid', '$patch4')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($patch5) {
      $query1 = "INSERT INTO EH_Battles_Patches (Battle_ID, Patch_ID) VALUES ('$newid', '$patch5')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_battles Transferred.<br>";
  //End Import TC_battles

  //Begin Import: TC_bugs
  $query = "SELECT Bug_ID, Bug_BattleID, BUG_PIN, BUG_Date, BUG_Content, BUG_Status, BUG_DateProcessed, BUG_Type FROM TC_bugs";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[7]==1) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $battle=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $status = mysql_real_escape_string($values[5]);
      if($status==1)
        $status=0;
      else
        $status=1;
      $update = mysql_real_escape_string($values[6]);
      $query1 = "INSERT INTO EH_Battles_Bugs (Battle_ID, Poster_ID, Date_Added, Description, Status) VALUES ('$battle', '$member', '$date', '$info', '$status')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($values[7]==2) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $status = mysql_real_escape_string($values[5]);
      if($status==1)
        $status=0;
      else
        $status=2;
      $update = mysql_real_escape_string($values[6]);
      $query1 = "INSERT INTO EH_Patches_Bugs (Patch_ID, Member_ID, DateReported, Description, Status) VALUES ('$patch', '$member', '$date', '$info', '$status')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_bugs Transferred.<br>";
  //End Import TC_bugs

  //Begin Import: TC_reviews
  $query = "SELECT R_ID, R_BattleID, R_PIN, R_Date, R_Content, R_Type, R_Rating FROM TC_reviews";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($values[5]==1) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $battle=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $rate = mysql_real_escape_string($values[6]);
      $query1 = "INSERT INTO EH_Battles_Reviews (Battle_ID, Poster_ID, Date_Added, Description, Status, Rating) VALUES ('$battle', '$member', '$date', '$info', '1', '$rate')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    if($values[5]==2) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Patch' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $patch=$values1[0];
        }
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[2]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $mem=$values1[0];
        }
      $date = mysql_real_escape_string($values[3]);
      $info = mysql_real_escape_string($values[4]);
      $query1 = "INSERT INTO EH_Patches_Reviews (Patch_ID, Member_ID, DatePosted, Review) VALUES ('$patch', '$member', '$date', '$info')";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_reviews Transferred.<br>";
  //End Import TC_reviews

  //Begin Import: TC_highscore
  $query = "SELECT B_ID, B_Missions FROM TC_battles";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query2 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      $battle=$values2[0];
      }
    $query1 = "SELECT HS_Score, HS_PIN FROM TC_high_scores WHERE HS_MissionID=0 AND HS_BattleID=$values[0]";
    $result1 = mysql_query($query1, $newmysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values1[1]";
      $result2 = mysql_query($query2, $mysql_link);
      $rows2 = mysql_num_rows($result2);
      if($rows2) {
        $values2 = mysql_fetch_row($result2);
        $mem=$values2[0];
        }
      else {
        $mem=0;
        }
      if($battle) {
        $query2 = "UPDATE EH_Battles Set HS_Holder='$mem', Highscore='$values1[0]' WHERE Battle_ID=$battle";
        $result2 = mysql_query($query2, $mysql_link);
        }
      }
    $query1 = "SELECT HS_Score, HS_PIN FROM TC_high_scores WHERE HS_MissionID!=0 AND HS_BattleID=$values[0] Order By HS_MissionID";
    $result1 = mysql_query($query1, $newmysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j=1; $j<=$rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values1[1]";
      $result2 = mysql_query($query2, $mysql_link);
      $rows2 = mysql_num_rows($result2);
      if($rows2) {
        $values2 = mysql_fetch_row($result2);
        $mem=$values2[0];
        }
      else {
        $mem=0;
        }
      if($battle) {
        $query2 = "INSERT INTO EH_Battles_Missions (Battle_ID, Mission_Num, Highscore, HS_Holder) VALUES ('$battle', '$j', '$values1[0]', '$mem')";
        $result2 = mysql_query($query2, $mysql_link);
        }
      }
    }
  echo "TC_highscore Transferred.<br>";
  //End Import TC_highscore

  //Begin Import: TC_temp_bsf
  $query = "SELECT TB_PIN, TB_Recc, TB_BattleID, TB_Queue, TB_Filename, TB_Scores FROM TC_temp_bsf WHERE TB_Status=1";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $from=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $for=$values1[0];
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $battle=$values1[0];
      }
    $queue = mysql_real_escape_string($values[3]);
    $queue = str_split($queue);
    $qn=0;
    for($q=0; $q<count($queue); $q++) {
      if($queue[$q]==2)
        $qn+=pow(2, $q);
      }
    $fn = mysql_real_escape_string($values[4]);
    $scores = mysql_real_escape_string($values[5]);
    $scores = str_replace("¤", ";", $scores);
    $query1 = "INSERT INTO EH_Battles_Complete (Battle_ID, Filename, Member_ID, Status, Scores, TACStatus, Rec_ID) VALUES ('$battle', '$fn', '$for', '0', '$scores', '$qn', '$from')";
    $result1 = mysql_query($query1, $mysql_link);
    }
  echo "TC_temp_bsf Transferred.<br>";
  //End Import TC_temp_bsf

  //Begin Import: TC_user_medals_count
  $member=$medal=$oldpin=0;
  $query = "SELECT `MC_ID`, MC_PIN, MC_MedalID, MC_MedalCount FROM TC_user_medals_count WHERE MC_MedalCount>0 Order By MC_PIN, MC_MedalID";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    if($oldpin!=$values[1]) {
      $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[1]";
      $result1 = mysql_query($query1, $mysql_link);
      $rows1 = mysql_num_rows($result1);
      if($rows1) {
        $values1 = mysql_fetch_row($result1);
        $member=$values1[0];
        $oldpin=$values[1];
        }
      }
    $query1 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Medal' AND OriginalValue=$values[2]";
    $result1 = mysql_query($query1, $mysql_link);
    $rows1 = mysql_num_rows($result1);
    if($rows1) {
      $values1 = mysql_fetch_row($result1);
      $medal=$values1[0];
      }
    $query2 = "SELECT MH_Date, MH_Giver, MH_Reason FROM TC_medals_history WHERE MH_PIN=$values[1] AND MH_MedalID=$values[2]";
    $result2 = mysql_query($query2, $newmysql_link);
    $rows2 = mysql_num_rows($result2);
    for($j = 0; $j < $rows2; $j++) {
      $values2 = mysql_fetch_row($result2);
      $reason = mysql_real_escape_string($values2[2]);
      $from=0;
      $query3 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values2[1]";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      if($rows3) {
        $values3 = mysql_fetch_row($result3);
        $from=$values3[0];
        }
      if($member) {
        $query1 = "INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status) VALUES ('$member', '$medal', '$from', '$groupid', '$values2[0]', '$reason', '1')";
        $result1 = mysql_query($query1, $mysql_link);
        }
      }
    $now=time();
    set_time_limit (90);
    if($member) {
      $query1 ="INSERT INTO EH_Medals_Complete (Member_ID, Medal_ID, Awarder_ID, Group_ID, DateAwarded, Reason, Status) VALUES ";
      for($j=$values[3]; $j>$rows2; $j--) {
        $query1 .= "('$member', '$medal', '0', '$groupid', '$now', 'Adding to Database', '1'), ";
        }
      $query1 = substr($query1, 0, -2);
      $query1 .=";";
      $result1 = mysql_query($query1, $mysql_link);
      }
    }
  echo "TC_user_medals_count Transferred.<br>";
  //End Import TC_user_medals_count

  echo "<p>Items to Fix:<br>\n";
  echo "Fix Positions<br>\n";
  echo "Fixe EH_Ships<br>\n";
  echo "Fixe Categories for Training<br>\n";
  echo "</p>\n";
  }

if($_GET['db']=="tc2") {
  echo "<p>Beginning TIE Corps Data import</p>\n";
  $newmysql_link = mysql_connect($db_host, "emperors_tc", "27iB1bDPKwaw", TRUE);
  mysql_select_db("emperors_members", $newmysql_link);
  $groupid=2;

  //Begin Import: TC_battles_flown
  $query = "SELECT M_PIN FROM TC_members WHERE M_PIN!=1 Order By M_PIN";
  $result = mysql_query($query, $newmysql_link);
  $rows = mysql_num_rows($result);
  for($i = 0; $i < $rows; $i++) {
    $values = mysql_fetch_row($result);
    $member=0;
    $query2 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Member' AND OriginalValue=$values[0]";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    if($rows2) {
      $values2 = mysql_fetch_row($result2);
      $member=$values2[0];
      }
    $query1 = "SELECT GROUP_CONCAT(BF_score SEPARATOR ';' ) AS scores, BF_BattleID, BF_Date FROM `TC_battles_flown` WHERE BF_PIN =$values[0] AND BF_MissionID !=0 Group By BF_Date, BF_BattleID";
    $result1 = mysql_query($query1, $newmysql_link);
    $rows1 = mysql_num_rows($result1);
    for($j = 0; $j < $rows1; $j++) {
      $values1 = mysql_fetch_row($result1);
      $query2 = "SELECT NewValue FROM EH_ConvertInfo WHERE Group_ID=$groupid AND `Table`='Battle' AND OriginalValue=$values1[1]";
      $result2 = mysql_query($query2, $mysql_link);
      $rows2 = mysql_num_rows($result2);
      if($rows2) {
        $values2 = mysql_fetch_row($result2);
        $battle=$values2[0];
        }
      $query3 = "INSERT INTO EH_Battles_Complete (Battle_ID, Date_Completed, Member_ID, Status, Scores) VALUES ('$battle', '$values1[2]', '$member', '1', '$values1[0]')";
      $result3 = mysql_query($query3, $mysql_link);
      }
    }
  echo "TC_battles_flown Transferred.<br>";
  //End Import TC_battles_flown

  }
?>
Fix All Medals in Sort Order, remove duplicates<br>
Fix All Access for all Positions<br>
<a href="import.php?db=db">Import Dark Brotherhood Database</a><br>
<a href="import.php?db=dir">Import Directorate Database</a><br>
<a href="import.php?db=hf">Import Hammer's Fist Database</a><br>
<a href="import.php?db=tc">Import TIE Corps Database</a><br>
<a href="import.php?db=tc2">Import Painful Battles TIE Corps Database</a><br>
<?
include_once("footer.php")
?>