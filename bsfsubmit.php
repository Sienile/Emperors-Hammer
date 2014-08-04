<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "bsfsubmit");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
include_once("nav.php");
if($_POST['Submit']){
  $memberid = mysql_real_escape_string($_POST['Member'], $mysql_link);
  $battleid = mysql_real_escape_string($_POST['Battle'], $mysql_link);
  $recid=$_SESSION['EHID'];
  $now = time();
  $query = "SELECT EH_Battles.Battle_ID, EH_Battles.Name, EH_Battles.BattleNumber, EH_Platforms.Abbr, EH_Battles_Categories.Abbr, EH_Battles.NumMissions, EH_Battles.Reward_Name, EH_Battles.Highscore, EH_Battles.HS_Holder, EH_Platforms.FileExt FROM EH_Battles, EH_Battles_Categories, EH_Platforms WHERE EH_Battles.Platform_ID=EH_Platforms.Platform_ID AND EH_Battles.BC_ID=EH_Battles_Categories.BC_ID AND EH_Battles.Battle_ID=$battleid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $battlename = BattleName($values[0], 0);
    $battlenummissions = $values[5];
    $battlereward = $values[6];
    $battleHS = $values[7];
    $battleHSpin =$values[8];
    $plext =$values[9];
    $plabbr = $values[3];
    }
  $error="";
  $errornum=0;
  if(strtolower(substr($_FILES['pltfile']['name'],strpos($_FILES['pltfile']['name'], ".")+1)) != $plext) {
    $error = "Error, Did Not upload the correct file type.";
    }
/*
Bits to check
1 - Filesize wrong +2^0
2 - Skill Check wrong +2^1
3 - Mission Scores don't add up+2^2
4 - Mission Count wrong+2^3
5 - Check for total score=0+2^4
6 - Resubmission of plt file+2^5
7 - High Score+2^6
8 - Game error
*/
  if($error=="") {
    $target_path = "plts/".substr(time(),-5);
    str_replace(" ","",$uploadfile = $target_path.basename($_FILES['pltfile']['name']));
    if (move_uploaded_file($_FILES['pltfile']['tmp_name'], $uploadfile)) {
      $filename = $uploadfile;
      }
    else {
      $error = "Upload of the pilotfile has failed - BSF aborted.";
      }
    }

    if($error=="" && $errornum==0) {
      switch ($plabbr) {
      // ====================================================================================
      //
      //  TIE FIGHTER
      //
      // ====================================================================================
        case "TIE":
          // if the filesize is wrong, set bit 5

          if (filesize($filename) != 3856) {
            $errornum+=1;
            }
          // let`s get some data from the pilotfile
          $totalscore = read_bytes(4,7,$filename);
          $lasers = read_bytes(1908,1911,$filename);
          $lhits = read_bytes(1912,1915,$filename);
          $whs = read_bytes(1920,1921,$filename);
          $whits = read_bytes(1922,1923,$filename);
          $laserless = $totalscore - 3* $lhits;
          $mscore = array($laserless);
          $skill = read_bytes(8,9,$filename);
          // if the file does not pass the skill check set bit 6
          /*$quotient = $totalscore / $skill;
          if ($quotient != 4) {
            $errornum+=2;
            }*/

          // determine which battles were finished
          $i = 1;
          $maxb = 0;
          while ($i <= 20) {
            $byte = 616 + $i;
            if (read_bytes($byte,$byte,$filename) == 3) {
              $maxb++;
              }
            else {
              break;
              }
            $i++;
            }

          // determine how many missions were completed per completed battle
          $i = 1;
          $maxm = 0;
          $battle = array("");
          while ($i <= $maxb) {
            $byte = 636 + $i;
            $val = read_bytes($byte,$byte,$filename);
            $battle[$i] = $val;
            $maxm = $maxm + $val;
            $i++;
          }

          // get the score for each completed mission
          $i = 1;
          $tscore = 0;
          $k = 1;
          while ($i <= $maxb) {					// for all completed battles
            $j = 1;
            while ($j <= 8) {						// and 8 missions per battle
              $mid = ($i - 1) * 8 + $j;
              if ($j <= $battle[$i]) {
                $startbyte = 982 + ($mid * 4);
                $endbyte = 982 + ($mid * 4) + 3;
                $mscore[$k] = read_bytes($startbyte,$endbyte,$filename);
                $tscore = $tscore + $mscore[$k];
                $k++;
              }
              $j++;
            }
            $j = 1;
            $i++;
          }
          $mscore[0] = $totalscore;
          // if the mission scores don`t add up, count is wrong, set bit 3
          if ($tscore != $totalscore) {
            $errornum+=4;
            }
          break;

      // ====================================================================================
      //
      //  X-WING VS TIE FIGHTER
      //
      // ====================================================================================

        case "XvT":
        case "BoP":
          // let`s get some data from the pilotfile
          $lhits = read_bytes(5182,5185,$filename);
          $lasers = read_bytes(5194,5197,$filename);
          $whs = read_bytes(5218,5221,$filename);
          $whits = read_bytes(5206,5209,$filename);

          // determine how many missions were completed, and if a mission was completed,
          // save its score and time
          $mscore = array("");
          $mtime = array("");
          $startscore = 80502;
          $i = 0;
          $totalscore = 0;
          $maxm = 0;
          $k = 999;

          while ($i < 100) {
            $startbyte = $startscore + 36*$i;
            $endbyte = $startbyte +3;

            // first let`s read missions to see where we need to start counting....
            $no = read_bytes($startbyte,$endbyte,$filename);

            if ($no >= 1) {
              // set the offset: save all scores in slot $i - $k
              if ($k == 999) { $k = $i; }

              $i++;
              $slot = $i - $k;
              $maxm++;

              $startbyte += 12;
              $endbyte += 12;
              $mscore[$slot] = read_bytes($startbyte,$endbyte,$filename);

              $startbyte += 4;
              $endbyte += 4;
              $mtime[$slot] = read_bytes($startbyte,$endbyte,$filename);

              $totalscore += $mscore[$slot];
            }
          else { $i++; }

          }
          $mscore[0] = $totalscore;
          break;

      // ====================================================================================
      //
      //  X-WING ALLIANCE
      //
      // ====================================================================================

        case "XWA":
          if (filesize($filename) != 152076) {
            $errornum+=1;
            }

          // determine how many missions were completed, and if a mission was completed,
          // save its score and time
          $mscore = array("");
          $mtime = array("");
          $start = 46814;
          $i = 0;
          $totalscore = 0;
          $maxm = 0;
          while ($i < 255) {
            $k = $i + 1;
            $startbyte = $start + $i * 48 + 16;
            $endbyte = $startbyte + 3;
            $completed[$k] = read_bytes($startbyte,$endbyte,$filename);

            $startbyte = $start + $i * 48 + 40;
            $endbyte = $startbyte + 3;
            $score[$k] = read_bytes($startbyte,$endbyte,$filename);
            $totalscore += $score[$k];
            if ($completed[$k] && $score[$k] > 0) { $maxm++; }

            $startbyte = $start + $i * 48 + 56;
            $endbyte = $startbyte + 3;
            $bonus[$k] = read_bytes($startbyte,$endbyte,$filename) / 10;
            $totalscore += $bonus[$k];
            $mscore[$k] = $score[$k] + $bonus[$k];

            $startbyte = $start + $i * 48 + 28;
            $endbyte = $startbyte + 3;
            $time[$k] = read_bytes($startbyte,$endbyte,$filename);

            $i++;
          }
          $mscore[0] = $totalscore;
          break;

      // ====================================================================================
      //
      //  X-WING
      //
      // ====================================================================================

        case "XW":
          $startcount = 544;
          $i = 0;
          $maxm = 0;

          while ($i < 18) {
              $startbyte = $startcount + $i;
              $endbyte = $startbyte;
              $i++;
              $complete[$i] = read_bytes($startbyte,$endbyte,$filename);
              if ($complete[$i] >= 1) { $maxm++; }
          }
          $startscore = 160;
          $i = 0;
          $totalscore = 0;
          while ($i < $maxm) {
            $startbyte = $startscore + $i * 4;
            $endbyte = $startbyte + 3;
            $i++;
            if ($complete[$i] == 1) {
              $mscore[$i] = read_bytes($startbyte,$endbyte,$filename);
              $totalscore += $mscore[$i];
            }
          }
          $mscore[0] = $totalscore;
          break;
      }
    }
  //End Error Check for Switch
  // if the mission count is wrong
  if ($maxm != $battlenummissions) {
    $errornum+=8;
    }
  // check for a total score of 0
  if ($mscore[0] == 0) {
    $errornum+=16;
    }
  //See if they're resubmitting the same plt file
  $playedbefore=0;
  $query = "SELECT Scores, Status FROM EH_Battles_Complete WHERE Member_ID=$memberid AND Battle_ID=$battleid";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  if($rows) {
    $values = mysql_fetch_row($result);
    $playedbefore=1;
    if($values[1])
      $errornum+=32;
    else {
      $pscores = explode(";", $values[0]);
      if($pscores[0]==$mscore[0])
        $errornum+=32;
      else {
        for($i=1; $i<count($pscores); $i++)
          if($pscores[$i]==$mscore[$i])
            $errornum+=32;
        }
      }
    }
  //Check Battle HS
  if($battlenummissions>1 && $mscore[0]>$battleHS)
    $errornum+=64;
  else {
  //Check Mission HS
    $query = "SELECT Highscore FROM EH_Battles_Missions WHERE Battle_ID=$battleid ORDER By Mission_Num";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    for($i=1; $i<=$rows; $i++) {
      $values = mysql_fetch_row($result);
      if($mscore[$i]>$values[0]) {
        $errornum+=64;
        break;
        }
      }
    }
  $mscore = array_slice($mscore, 0, $maxm+1);
  if($error=="") {
    $scores = implode(";", $mscore);
    if($errornum==0)
      $status=1;
    else
      $status=0;
    $query = "INSERT INTO EH_Battles_Complete (Battle_ID, Date_Completed, Filename, Member_ID, Status, Scores, TACStatus, Rec_ID) Values ('$battleid', '$now', '$filename', '$memberid', '$status', '$scores', '$errornum', '$recid')";
    $result = mysql_query($query, $mysql_link);
    }
  if($errornum==0) {
    //If No errors, if not completed in the past add to their FCHG points, e-mail person and TAC about the acceptance
    $query = "SELECT Value FROM EH_Members_Special_Areas WHERE Member_ID=$memberid AND SA_ID=1";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      $fchgnew = $values[0]+$battlenummissions;
      $query1 = "UPDATE EH_Members_Special_Areas Set Value=$fchgnew WHERE Member_ID=$memberid AND SA_ID=1";
      $result1 = mysql_query($query1, $mysql_link);
      }
    else {
      $query1 = "INSERT INTO EH_Members_Special_Areas (Member_ID, SA_ID, Value) Values ('$memberid', 1, $battlenummissions)";
      $result1 = mysql_query($query1, $mysql_link);
      }
    $query = "SELECT Member_ID, Email FROM EH_Members WHERE Member_ID=$memberid";
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      if(isinGroup($memberid, 2))
        $prigroup = 2;
      else
        $prigroup = PriGroup($values[0]);
      $to = RankAbbrName($values[0], $prigroup,0)." <$values[1]>";
      }
    $to .=", Tactical Office <tac@emperorshammer.org>";
    $to .=", ".CoC($memberid, $prigroup);
    $subject = "BSF Submitted for $battlename";
    $body = RankAbbrName($recid, PriGroup($recid), 0) ." has submitted a BSF for battle: $battlename.\n";
    $body .= "During Automated Analysis, it was determined this was a legitimate file, and has been added to your completion record.";
    if($battlereward)
      $body .= " For this completion, you have been awarded the $battlereward.";
    $body .="\n";
    if($battlenummissions>1) {
      $body .="Scores:\nBattle: $mscore[0]\n";
      for($i=1; $i<count($mscore); $i++)
        $body .="Mission $i) $mscore[$i]\n";
      }
    else {
      $body .="Score: $mscore[0]\n";
      }
    $body .= "If there is an error, please discuss with the Tactical Officer.\n";
    $headers = "From: $postmaster\n";
    $headers .= "X-Mailer: PHP\n"; // mailer
    $headers .= "Return-Path: $postmaster\n";  // Return path for errors
    //Mail it!
    $grade = mail($to, $subject, $body, $headers);
    storeEmail($recipient, '', '', $subject, $body);
    }
  else {
    //If errors, and E-mail TAC only, for analysis
    $query = "SELECT Member_ID, Email FROM EH_Members WHERE Member_ID=".$_SESSION['EHID'];
    $result = mysql_query($query, $mysql_link);
    $rows = mysql_num_rows($result);
    if($rows) {
      $values = mysql_fetch_row($result);
      if(isinGroup($memberid, 2))
        $prigroup = 2;
      else
        $prigroup = PriGroup($values[0]);
      $to = RankAbbrName($values[0], $prigroup,0)." <$values[1]>";
      }
    $subject = "BSF Submitted requires Analysis";
    $body = RankAbbrName($recid, PriGroup($recid), 0) ." has submitted a BSF for battle: $battlename for ".RankAbbrName($memberid, PriGroup($memberid),0).".\n";
    $body .= "During automated analysis the following issues were detected:\n";
    $errorbin = decbin($errornum);
    $errorbinrev = strrev($errorbin);
    for($i=0; $i<8; $i++) {
      if($errorbinrev[$i]==1) {
        switch($i) {
          case 0:
          $body.="File Size incorrect\n";
          break;
          case 1:
          $body.="Skill Check wrong\n";
          break;
          case 2:
          $body.="Mission Scores don't add up\n";
          break;
          case 3:
          $body.="Mission Count incorrect\n";
          break;
          case 4:
          $body.="Total Score equals 0\n";
          break;
          case 5:
          $body.="Possible resubmission of pilot file\n";
          break;
          case 6:
          $body.="Possible new High Score\n";
          break;
          case 7:
          $body.="Game Error\n";
          break;
          }
        }
      }
    $body .= "This is just to let you know the BSF has been submitted, but not automatically processed.\n";
    $headers = "From: $postmaster\n";
    $headers .= "X-Mailer: PHP\n"; // mailer
    $headers .= "Return-Path: $postmaster\n";  // Return path for errors
    //Mail it!
    $grade = mail($to, $subject, $body, $headers);
    storeEmail($recipient, '', '', $subject, $body);
    $to = "Tactical Office <tac@emperorshammer.org>";
    $subject = "BSF Submitted requires Analysis";
    $body = RankAbbrName($recid, PriGroup($recid), 0) ." has submitted a BSF for battle: $battlename for ".RankAbbrName($memberid, PriGroup($memberid),0).".\n";
    $body .= "During automated analysis the following issues were detected:\n";
    $errorbin = decbin($errornum);
    $errorbinrev = strrev($errorbin);
    for($i=0; $i<8; $i++) {
      if($errorbinrev[$i]==1) {
        switch($i) {
          case 0:
          $body.="File Size incorrect\n";
          break;
          case 1:
          $body.="Skill Check wrong\n";
          break;
          case 2:
          $body.="Mission Scores don't add up\n";
          break;
          case 3:
          $body.="Mission Count incorrect\n";
          break;
          case 4:
          $body.="Total Score equals 0\n";
          break;
          case 5:
          $body.="Possible resubmission of pilot file\n";
          break;
          case 6:
          $body.="Possible new High Score\n";
          break;
          case 7:
          $body.="Game Error\n";
          break;
          }
        }
      }
    $body .= "This error can be checked throught the BSF admin function on the EH site.\n\nThis automated message was generated by the BSF Submission process.\n";
    $headers = "From: $postmaster\n";
    $headers .= "X-Mailer: PHP\n"; // mailer
    $headers .= "Return-Path: $postmaster\n";  // Return path for errors
    //Mail it!
    $grade = mail($to, $subject, $body, $headers);
    storeEmail($recipient, '', '', $subject, $body);
    }
  if($error)
    echo "<p>$error</p>\n";
  else
    echo "<p>BSF Processed</p>\n";
  }
?>
<p>Emperor's Hammer Battle Completion Submission Form</p>
<p><a href="/menu.php">Return to the administration menu</a></p>
<form method="POST" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>">
  <table>
    <tr>
      <td><label for="Member">Member: </label></td>
      <td><select name="Member" id="Member">
      <?php
  $query = "SELECT Member_ID, Name FROM EH_Members WHERE Member_ID!=".$_SESSION['EHID']." AND Email!='' Order By Name";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".stripslashes($values[1])."</option>\n";
    }
  ?>
      </select></td>
    </tr>
    <tr>
      <td><label for="Battle">Select Battle: </label></td>
      <td><select name="Battle" id="Battle">
      <?php
  $query = "SELECT EH_Battles.Battle_ID FROM EH_Battles, EH_Battles_Categories, EH_Platforms WHERE EH_Battles.Platform_ID=EH_Platforms.Platform_ID AND EH_Battles.BC_ID=EH_Battles_Categories.BC_ID Order By EH_Platforms.Name, EH_Battles_Categories.SortOrder, EH_Battles.BattleNumber";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=0; $i<$rows; $i++) {
    $values = mysql_fetch_row($result);
    echo "  <option value=\"$values[0]\">".BattleName($values[0], 0)."</option>\n";
    }
  ?>
      </select></td>
    </tr>
    <tr>
      <td><label for="pltfile">Select Pilot File</label></td>
      <td><input name="pltfile" type="file" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="submit" id="Submit" name="Submit" value="Submit" />
        <input type="reset" id="Reset" name="Reset" value="Reset" />
      </td>
    </tr>
  </table>
</form>
<?php
include_once("footer.php");
?>