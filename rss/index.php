<? header("Content-Type: application/xml; charset=ISO-8859-1");
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
include_once("config.php");
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <atom:link href="http://www.emperorshammer.org/rss/" rel="self" type="application/rss+xml" />
    <title>Emperor's Hammer News</title>
    <link>http://www.emperorshammer.org</link>
    <description>The Emperor's Hammer is an organization of online Star Wars gaming fans founded in December 1994 and is dedicated to recreating the excitement and adventure of the Star Wars saga online. The Emperor's Hammer is primarily based on the TIE Fighter (LucasArts, 1994 and 1995), Dark Forces (LucasArts, 1996), X-Wing (LucasArts, 1993 and 1994), X-Wing vs. TIE Fighter (Lucasarts, 1997), Jedi Knight (LucasArts, 1997), Rebellion (LucasArts, 1998) and XWing Alliance (LucasArts, 1999) game platforms and takes place in the Star Wars Universe following the Battle of Endor. However, we also have several Groups which allow interested Imperial Citizens to join our cause without owning any computer game simulations or playing EH-Member designed games (i.e. Conquest, Diplomacy, etc.).

The Emperor's Hammer is an UNOFFICIAL Star Wars related organization, supported entirely by its Members which has been the primary reason for its continued phenomenal growth rate. The goal of the Emperor's Hammer is to foster interactive participation from its Members to create an "online" Star Wars experience unprecedented on the Internet. Through the distribution of regular Newsletters, the Emperor's Hammer provides a publishing and distribution platform for its Members' submissions...As a result of this policy, the organization has rapidly become the premiere Star Wars organization on the World Wide Web (WWW).

The Emperor's Hammer is owned by William P. Call ("GA Ronin")
The Board of Trustees of Emperor's Hammer, Inc. consists of: William P. Call, John Roscoe, Charles Calvey, David Blakely, Matt Williams, and R. Mark Davis. No other person or persons shall be allowed to represent or speak for the club.
The Emperor's Hammer is an organization of online Star Wars® gaming fans founded in December 1994 and is dedicated to recreating the excitement and adventure of the Star Wars® saga online.</description>
    <copyright>Copyright Protected 1994-<?=date("Y"); ?> Emperor's Hammer, Inc.</copyright>
    <category>Star Wars</category>
    <image>
      <url>http://www.emperorshammer.org/eh-org-splash-d.jpg</url>
      <title>Emperor's Hammer News</title>
      <link>http://www.emperorshammer.org</link>
    </image>
    <language>en-us</language>
    <?

$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$daysback=7;
$startdate = mktime(23,59,59,date("m"),date("d")-$q,  date("Y"));
for($q=1; $q<=$daysback; $q++) {
  $lastold = mktime (23,49,59,date("m"),date("d")-$q,  date("Y"));
  $query = "select News_ID, Topic, Poster, Poster_ID, DatePosted, Body from EH_News where DatePosted>=$lastold AND DatePosted<=$startdate Order By DatePosted DESC";
  $result = mysql_query($query, $mysql_link);
  $rows = mysql_num_rows($result);
  for($i=1; $i<=$rows; $i++) {
    $values = mysql_fetch_row($result);
?>
    <item>
      <title><?=stripslashes($values[1])?></title>
      <link>http://www.emperorshammer.org</link>
      <guid>http://www.emperorshammer.org/index.php?newsid=<?=$values[0]?></guid>
      <description><?=strip_tags(stripslashes(html_entity_decode($values[5])))?></description>
      <pubDate><?=date("r", $values[4])?></pubDate>
    </item>
<?
    }
  $startdate=$lastold;
  }
?>
  </channel>
</rss>