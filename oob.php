<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
?>
<style>
h1 {
padding:0px;
margin:0px;
}
h2 {
padding:0px;
margin:0px;
}
h3 {
padding:0px;
margin:0px;
}
h4 {
padding:0px;
margin:0px;
}
h5 {
padding:0px;
margin:0px;
}
h6 {
padding:0px;
margin:0px;
}
</style>
<p>Order of Battle of the Emperor's Hammer Strke Fleet. Current Version Updated April 25, 2011.</p>
<p><b>Fleet Commander's Notes</b>
<ul>
  <li>Herein are presented the Capital Ships and Units of the Fleet as recognized by the Fleet Commander of the Emperor's Hammer.</li>
  <li>The Taskforce Capital Ships and Units that are lunked to their rosters (when available) are manned by the Emperor's Hammer TIE Corps Members as starfighter pilots or commanding officers.</li>
  <li>The Subgroup Capital Ships and Units that are linked to their rosters (when available) are also manned by their respective Emperor's Hammer Subgroup Members.</li>
  <li>Ship type icons link to their Fleet Manual entries (when available). Emperor's Hammer Members desiring more specific information of each of the Emperor's Hammer capital ships should review the Fleet Manual.</li>
  <li>TIE Corps Units without an assigned craft type have the TIE Fighter (T/F) assigned as default.</li>
</ul>
</p>
<?
$query = "SELECT Base_ID, Name, Types, Link From EH_Bases WHERE BT_ID=7 AND Master_ID=0";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  echo "<table style=\"background-color:#344765;color:#000000;width:100%\">\n";
  echo "  <tr>\n";
  echo "    <td width=\"38px\"><img src=\"images/Icons/ships/scoicon.gif\" width=\"38\" height=\"38\" alt=\"Fleet Icon Image\" /></td>\n";
  echo "    <td colspan=\"3\"><h1>";
  if($values[3])
    echo "<a href=\"$values[3]\">";
  echo stripslashes($values[1]);
  if($values[3])
    echo "</a>";
  echo "</h1></td>\n";
  echo "  </tr>\n";
  $query1 = "SELECT EH_Bases.Base_ID, EH_Bases.Name, EH_Bases.Types, EH_Bases.Link, EH_Bases.Notes From EH_Bases, EH_Bases_Types WHERE EH_Bases.Master_ID=$values[0] AND EH_Bases.BT_ID=5 AND EH_Bases.BT_ID=EH_Bases_Types.BT_ID Order By EH_Bases_Types.SortOrder";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  for($j=0; $j<$rows1; $j++) {
    $values1 = mysql_fetch_row($result1);
    echo "  <tr>\n";
    echo "    <td colspan=\"4\"><h2>";
    if($values1[3])
      echo "<a href=\"$values1[3]\">";
    if($values1[2])
      echo VesselTypeAbbr($values1[2], 1). " ";
    echo stripslashes($values1[1]);
    if($values1[3])
      echo "</a>";
    echo "</h2></td>\n";
    echo "  </tr>\n";
    if($values1[4]) {
      echo "  <tr>\n";
      echo "    <td colspan=\"4\"><h6>".stripslashes($values1[4])."</h6></td>\n";
      echo "  </tr>\n";
      }
    $query2 = "SELECT EH_Bases.Base_ID, EH_Bases.Name, EH_Bases.Types, EH_Bases.Link, EH_Bases.Notes From EH_Bases, EH_Bases_Types WHERE EH_Bases.Master_ID=$values1[0] AND EH_Bases.BT_ID=6 AND EH_Bases.BT_ID=EH_Bases_Types.BT_ID Order By EH_Bases_Types.SortOrder";
    $result2 = mysql_query($query2, $mysql_link);
    $rows2 = mysql_num_rows($result2);
    for($k=0; $k<$rows2; $k++) {
      $values2 = mysql_fetch_row($result2);
      $img="";
      $query3 = "SELECT Filename FROM EH_Ships_Images WHERE SIT_ID=3 AND Ship_ID=$values2[2]";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      if($rows3) {
        $values3 = mysql_fetch_row($result3);
        $img = $values3[0];
        }
      echo "  <tr>\n";
      echo "    <td><img src=\"images/Icons/ships/$img\" alt=\"".VesselTypeAbbr($values2[2], 0)."\" /></td>\n";
      echo "    <td><h3>";
      if($values2[3])
        echo "<a href=\"$values2[3]\">";
      if($values2[2])
        echo VesselTypeAbbr($values2[2], 1) ." ";
      echo stripslashes($values2[1]);
      if($values2[3])
        echo "</a>";
      echo "</h3>";
      if($values2[4])
        echo "<h6>".stripslashes($values2[4])."</h6><br />\n";
      $query3 = "SELECT EH_Bases.Base_ID, EH_Bases.Name, EH_Bases.Types, EH_Bases.Link, EH_Bases.Notes From EH_Bases, EH_Bases_Types WHERE EH_Bases.Master_ID=$values2[0] AND EH_Bases.BT_ID=EH_Bases_Types.BT_ID Order By EH_Bases_Types.SortOrder";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      for($l=0; $l<$rows3; $l++) {
        $values3 = mysql_fetch_row($result3);
        echo "<table>";
        $imgs=array();
        $ships=explode(";", $values3[2]);
        foreach($ships as $ship) {
          $query4 = "SELECT Filename FROM EH_Ships_Images WHERE SIT_ID=2 AND Ship_ID=$ship";
          $result4 = mysql_query($query4, $mysql_link);
          $rows4 = mysql_num_rows($result4);
          if($rows4) {
            $values4 = mysql_fetch_row($result4);
            $imgs[] = $values4[0];
            }
          }
        echo "  <tr>\n";
        echo "    <td>";
        for($q=0; $q<count($imgs); $q++)
          echo "<img src=\"images/Icons/ships/$imgs[$q]\" alt=\"".VesselTypeAbbr($ships[$q], 0)."\" />";
        echo "</td>\n";
        echo "    <td><h3>";
        if($values3[3])
          echo "<a href=\"$values3[3]\">";
        echo stripslashes($values3[1]);
        if($values3[3])
          echo "</a>";
        echo "</h3>";
        if($values3[4])
          echo "<h6>".stripslashes($values3[4])."</h6>";
        echo "</td>\n";
        echo "  </tr>\n";
        $query4 = "SELECT EH_Bases.Base_ID, EH_Bases.Name, EH_Bases.Types, EH_Bases.Link, EH_Bases.Notes From EH_Bases, EH_Bases_Types WHERE EH_Bases.Master_ID=$values3[0] AND EH_Bases.BT_ID=EH_Bases_Types.BT_ID Order By EH_Bases_Types.SortOrder";
        $result4 = mysql_query($query4, $mysql_link);
        $rows4 = mysql_num_rows($result4);
        for($m=0; $m<$rows4; $m++) {
          $values4 = mysql_fetch_row($result4);
          $imgs=array();
          $ships=explode(";", $values4[2]);
          foreach($ships as $ship) {
            $query5 = "SELECT Filename FROM EH_Ships_Images WHERE SIT_ID=2 AND Ship_ID=$ship";
            $result5 = mysql_query($query5, $mysql_link);
            $rows5 = mysql_num_rows($result5);
            if($rows5) {
              $values5 = mysql_fetch_row($result5);
              $imgs[] = $values5[0];
              }
            }
          echo "  <tr>\n";
          echo "    <td>";
          for($q=0; $q<count($imgs); $q++)
            echo "<img src=\"images/Icons/ships/$imgs[$q]\" alt=\"".VesselTypeAbbr($ships[$q], 0)."\" />";
          echo "</td>\n";
          echo "    <td><h3>";
          if($values4[3])
            echo "<a href=\"$values4[3]\">";
          echo stripslashes($values4[1]);
          if($values4[3])
            echo "</a>";
          echo "</h3>";
          if($values4[4])
            echo "<h6>".stripslashes($values4[4])."</h6>";
          echo "</td>\n";
          echo "  </tr>\n";
          }
        echo "</table>\n";
        }
      echo "</td>\n<td>";
      $query3 = "SELECT EH_Bases.Base_ID, EH_Bases.Name, EH_Bases.Types, EH_Bases.Link, EH_Bases.Notes From EH_Bases, EH_Bases_Types WHERE EH_Bases.Master_ID=$values1[0] AND EH_Bases.BT_ID=1 AND EH_Bases.BT_ID=EH_Bases_Types.BT_ID Order By EH_Bases_Types.SortOrder";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      for($l=0; $l<$rows3; $l++) {
        $values3 = mysql_fetch_row($result3);
        echo "<table>\n";
        $imgs=array();
        $ships=explode(";", $values3[2]);
        foreach($ships as $ship) {
          $query4 = "SELECT Filename FROM EH_Ships_Images WHERE SIT_ID=2 AND Ship_ID=$ship";
          $result4 = mysql_query($query4, $mysql_link);
          $rows4 = mysql_num_rows($result4);
          if($rows4) {
            $values4 = mysql_fetch_row($result4);
            $imgs[] = $values4[0];
            }
          }
        echo "  <tr>\n";
        echo "    <td>";
        for($q=0; $q<count($imgs); $q++)
          echo "<img src=\"images/Icons/ships/$imgs[$q]\" alt=\"".VesselTypeAbbr($ships[$q], 0)."\" />";
        echo "</td>\n";
        echo "    <td><h3>";
        if($values3[3])
          echo "<a href=\"$values3[3]\">";
        if(count($ships)==1)
          echo VesselTypeAbbr($ships[0], 1) ." ";
        echo stripslashes($values3[1]);
        if($values3[3])
          echo "</a>";
        echo "</h3>";
        if($values3[4])
          echo "<h6>".stripslashes($values3[4])."</h6>";
        echo "</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        }
      echo "</td>\n<td>";
      $query3 = "SELECT EH_Bases.Base_ID, EH_Bases.Name, EH_Bases.Types, EH_Bases.Link, EH_Bases.Notes From EH_Bases, EH_Bases_Types WHERE EH_Bases.Master_ID=$values1[0] AND (EH_Bases.BT_ID=2 OR EH_Bases.BT_ID=8 OR EH_Bases.BT_ID=9) AND EH_Bases.BT_ID=EH_Bases_Types.BT_ID Order By EH_Bases_Types.SortOrder";
      $result3 = mysql_query($query3, $mysql_link);
      $rows3 = mysql_num_rows($result3);
      for($l=0; $l<$rows3; $l++) {
        $values3 = mysql_fetch_row($result3);
        echo "<table>\n";
        $imgs=array();
        $ships=explode(";", $values3[2]);
        foreach($ships as $ship) {
          $query4 = "SELECT Filename FROM EH_Ships_Images WHERE SIT_ID=2 AND Ship_ID=$ship";
          $result4 = mysql_query($query4, $mysql_link);
          $rows4 = mysql_num_rows($result4);
          if($rows4) {
            $values4 = mysql_fetch_row($result4);
            $imgs[] = $values4[0];
            }
          }
        echo "  <tr>\n";
        echo "    <td>";
        for($q=0; $q<count($imgs); $q++)
          echo "<img src=\"images/Icons/ships/$imgs[$q]\" alt=\"".VesselTypeAbbr($ships[$q], 0)."\" />";
        echo "</td>\n";
        echo "    <td><h3>";
        if($values3[3])
          echo "<a href=\"$values3[3]\">";
        if(count($ships)==1)
          echo VesselTypeAbbr($ships[0], 1) ." ";
        echo stripslashes($values3[1]);
        if($values3[3])
          echo "</a>";
        echo "</h3>";
        if($values3[4])
          echo "<h6>".stripslashes($values3[4])."</h6>";
        echo "</td>\n";
        echo "  </tr>\n";
        echo "</table>\n";
        }
      echo "  </tr>\n";
      }
    }
  echo "</table>\n";
  }
include_once("footer.php");
?>