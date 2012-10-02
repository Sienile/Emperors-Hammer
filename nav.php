<?
if (!isset($_SESSION)){
    session_start();
}
include_once("config.php");
include_once("functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>Emperor's Hammer Strike Fleet<? if(isset($page)) echo ": $page"; ?></title>
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <link rel="stylesheet" type="text/css" media="screen" href="style/global.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/dark-hive/jquery-ui.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="style/superfish.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="style/superfish-navbar.css" />
  <link rel="stylesheet" type="text/css" media="screen" href="style/jquery.autocomplete.css" />
<?
if(isset($stats)) {
?>  <link rel="stylesheet" type="text/css" href="style/jquery.jqplot.css" />
<?
}
?>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
  <script type="text/javascript" src="js/hoverIntent.js"></script>
  <script type="text/javascript" src="js/superfish.js"></script>
  <script type="text/javascript" src="js/jquery.form.js" ></script>
  <script type="text/javascript" src="js/global.js" ></script>
  <script type="text/javascript" src="js/wtooltip.min.js"></script>
  <script type="text/javascript" src="js/jquery.autocomplete.js"></script>
  <script type="text/javascript" src="js/jquery.bgiframe.min.js"></script>
  <script type="text/javascript" src="js/xhtml.js"></script>
  <script type="text/javascript" src="js/htmlbox.min.js"></script>

</head>
<?
flush();
?>
<body>
<table style="width:1024px; margin: 0 auto;">
  <tr>
    <td style="text-align: center;"><img src="images/nav/topbanner.png" alt="Emperor's Hammer Strike Fleet" style="width: 1024px; height: 320px" /></td>
  </tr>
  <tr>
    <td style="text-algin: left;">
      <ul id="sample-menu-4" class="sf-menu sf-navbar">
        <li><a href="index.php">Home</a>
          <ul>
            <li><a href="index.php">News</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="groups.php">Groups</a></li>
            <li><a href="alliances.php">Alliances</a></li>
            <li><a href="page.php?page=history">History</a></li>
          </ul>
        </li>
<?
//        <li><a href="blog">Blog</a></li>
?>
        <li><a href="http://www.cafepress.com/emperor/" target="_blank">Buy EH Gear</a></li>
        <li><a href="http://www.emperorshammer.net/" target="_blank">TOR Guild</a></li>
        <li><a href="#">Rosters</a>
          <ul>
            <li><a href="cs.php">Command Staff</a></li>
            <li><a href="roster.php?group=2">TIE Corps</a></li>
            <li><a href="roster.php?group=3">Dark Brotherhood</a></li>
            <li><a href="roster.php?group=6">Hammer's Fist</a></li>
            <li><a href="roster.php?group=4">Directorate</a></li>
            <li><a href="roster.php?group=5">Fringe</a></li>
            <li><a href="search.php">Personnel Search</a></li>
            <li><a href="stats.php">Statistics</a></li>
          </ul>
        </li>
        <li><a href="login.php">Administration</a></li>
        <li><a href="#">Training</a>
          <ul>
            <li><a href="trainingmain.php">Main</a></li>
            <li><a href="staff.php">Staff</a></li>
<?
if(!isset($mysql_link)) {
  $mysql_link = mysql_connect($db_host, $db_username, $db_password);
  mysql_select_db($db_name, $mysql_link);
  }

            $query = "SELECT TAc_ID, Abbr FROM EH_Training_Academies Order By SortOrder";
            $result = @mysql_query($query, $mysql_link);
            $rows = @mysql_num_rows($result);
            for($i = 1; $i <= $rows; $i++) {
              $values = @mysql_fetch_row($result);
?>
            <li><a href="#"><?=stripslashes($values[1])?></a>
              <ul>
                <li><a href="acadabout.php?id=<?=$values[0]?>">About</a></li>
                <li><a href="cat.php?id=<?=$values[0]?>">Course Categories</a></li>
                <li><a href="grads.php?id=<?=$values[0]?>">Graduates</a></li>
              </ul>
            </li>
<?
}
?>
          </ul>
        </li>
        <li><a href="#">Archives</a>
          <ul>
            <li><a href="newsarchive.php">News</a></li>
            <li><a href="medalboard.php">Medal Board</a></li>
            <li><a href="battlecenter.php">Battle Center</a></li>
            <li><a href="patcharchive.php">Gaming Patches</a></li>
            <li><a href="files.php">Files</a></li>
            <li><a href="newsletters.php">Newsletters</a></li>
            <li><a href="fiction.php">Fiction</a></li>
            <li><a href="#">Images</a></li>
            <li><a href="#">Ship/Unit Histories</a></li>
          </ul>
        </li>
        <li><a href="comps.php">Competitions</a>
          <ul>
            <li><a href="comps.php">Competitions</a></li>
            <li><a href="killboard.php">Kill Board</a></li>
          </ul>
        </li>
        <li><a href="join.php">Join</a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td>
    <table style="width: 100%">
      <tr>
        <td  style="background-image:url('images/nav/framecorner.png'); background-repeat:repeat; width: 17px; height: 17px;"></td>
        <td style="background-image:url('images/nav/framefiller.png'); background-repeat:repeat; width: 220px; height: 17px;"></td>
        <td  style="background-image:url('images/nav/framecorner.png'); background-repeat:repeat; width: 17px; height: 17px;"></td>
        <td style="background-image:url('images/nav/framefiller.png'); background-repeat:repeat; height: 17px;"></td>
        <td  style="background-image:url('images/nav/framecorner.png'); background-repeat:repeat; width: 17px; height: 17px;"></td>
      </tr>
      <tr>
        <td style="background-image:url('images/nav/framefiller.png'); background-repeat:repeat; width: 17px;"></td>
        <td style="width: 220px; vertical-align:top; text-align: left;">
          <table style="width:220px; margin: 0 auto;">
            <tr>
              <td style="background-image:url('images/nav/tbltophdr.png');background-repeat:no-repeat;height: 11px;border: 0px;"></td>
            </tr>
            <tr>
              <td style="background-image:url('images/nav/tblbkg.png');background-repeat:repeat-x;">
                <table width="100%">
                  <tr>
                    <td style="width: 15px"></td>
                    <td><b>Communications</b></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 14px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="csreports.php">Reports</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="messageboard" target="_blank">Message Boards</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="servers.php?type=0">Communications Servers</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="servers.php?type=1">Gaming Servers</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="meetings.php">Meeting Information</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="links.php">EH Links</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 17px"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <table style="width:220px; margin: 0 auto;">
            <tr>
              <td style="background-image:url('images/nav/tbltophdr.png');background-repeat:no-repeat;height: 11px;border: 0px"></td>
            </tr>
            <tr>
              <td style="background-image:url('images/nav/tblbkg.png');background-repeat:repeat-x;">
                <table width="100%">
                  <tr>
                    <td style="width: 15px"></td>
                    <td ><b>Concourse</b></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 14px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="benefactors.php">Benefactors</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="articles.php">Article Mentions</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="heroes.php">Heroes</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="logos.php">Logos</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="siteawards.php">Site Awards</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 17px"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <table style="width:220px; margin: 0 auto;">
            <tr>
              <td style="background-image:url('images/nav/tbltophdr.png');background-repeat:no-repeat;height: 11px;border: 0px;"></td>
            </tr>
            <tr>
              <td style="background-image:url('images/nav/tblbkg.png');background-repeat:repeat-x;">
                <table width="100%">
                  <tr>
                    <td style="width: 15px"></td>
                    <td ><b>Group Manuals</b></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 14px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://www.emperorshammer.org/tc/downloads/TCPM.pdf">TIE Corps Pilot Manual</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://www.emperorshammer.org/db/DSC/">Dark Side Compendium</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://www.emperorshammer.org/hf/HFFM/">Hammer's Fist Field Manual</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://www.emperorshammer.org/dir/dir/manual/index.php">Directorate Manual</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 17px"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <table style="width:220px; margin: 0 auto;">
            <tr>
              <td style="background-image:url('images/nav/tbltophdr.png');background-repeat:no-repeat;height: 11px;border: 0px;"></td>
            </tr>
            <tr>
              <td style="background-image:url('images/nav/tblbkg.png');background-repeat:repeat-x;">
                <table width="100%">
                  <tr>
                    <td style="width: 15px"></td>
                    <td ><b>Manuals/Information</b></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 14px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://wiki.emperorshammer.org/">Encyclopaedia Imperia</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://www.emperorshammer.org/dir/sysman/">Systems Manual</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px">Fleet Manual</td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://tac.emperorshammer.org/base.php?page=5">Tactical Manual</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px">Science Manual</td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="oob.php">Order of Battle</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 15px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="http://www.emperorshammer.org/trainingoffice/EHTM/index.php">Training Manual</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 17px"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <table style="width:220px; margin: 0 auto;">
            <tr>
              <td style="background-image:url('images/nav/tbltophdr.png');background-repeat:no-repeat;height: 11px;border: 0px;"></td>
            </tr>
            <tr>
              <td style="background-image:url('images/nav/tblbkg.png');background-repeat:repeat-x;">
                <table width="100%">
                  <tr>
                    <td style="width: 15px"></td>
                    <td ><b>Rules and Regulations</b></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 14px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=bylaws">Bylaws</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=aow">Articles of War</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=coc">IRC Codes of Conduct</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=cheat">Cheating Policy</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=copyright">Copyrights/Disclaimers</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=larules">Lucasarts Rules</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 5px"></td>
                  </tr>
                  <tr>
                    <td style="width: 13px"></td>
                    <td style="background-image:url('images/nav/btnbackground.png');background-repeat:no-repeat; padding-left: 15px; height: 27px"><a href="page.php?page=privacy">Privacy Policy</a></td>
                  </tr>
                  <tr>
                    <td colspan="2" style="height: 17px"></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
        <td style="background-image:url('images/nav/framefiller.png'); background-repeat:repeat; width: 17px;"></td>
        <td style="vertical-align:top; text-align: left;">
