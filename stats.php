<?
session_start();
include_once("config.php");
include_once("functions.php");
$stats=true;
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
echo "<p>Various statistics can be easily calculated for the membership of the Emperor's Hammer. Some of the stats are listed below</p>\n";
?>
<script type="text/javascript">

	$(function() {

		$("#accordion").accordion({

			collapsible: true

		});

	});

	</script>


<div id="accordion">

  <h3><a href="#">FCHG Statistics</a></h3>

  <div><div id="fchg" style="height:200px;width:640px; "></div> 
  </div>

  <h3><a href="#">Combat Rating Statistics</a></h3>

  <div><div id="cr" style="height:200px;width:640px; "></div>
  </div>

  <h3><a href="#">Rank Statistics</a></h3>

  <div><p>Rank Statistics</p></div>

  <h3><a href="#">Platform Statistics</a></h3>

  <div><p>Platform Statistics</p></div>

  <h3><a href="#">Medal Statistics</a></h3>

  <div><p>Medal Statistics</p></div>

  <h3><a href="#">Stormtrooper Types</a></h3>

  <div><p>Stormtrooper Types</p></div>

  <h3><a href="#">Courses Taken</a></h3>

  <div><p>Courses Taken</p></div>

</div>
<script type="text/javascript">
<?
$query = "SELECT Name, Points, StatsColor From EH_FCHG Order By Points DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
$prevtotal=0;
echo "fchgdata = [";
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $colors[]=$values[2];
  echo "['$values[0]: ";
  $query1 = "SELECT Count(EMSA_ID) From EH_Members_Special_Areas WHERE SA_ID=1 AND Value>=$values[1]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pt = $values1[0]-$prevtotal;
    echo $pt."', ".$pt;
    $prevtotal=$values1[0];
    }
  echo "]";
  if($i+1<$rows)
    echo ", ";
  }
  echo "];\n";
?>
$.jqplot('fchg', [fchgdata], {  
  title: 'Fleet Commander\'s Honor Guard',
  series:[{renderer:$.jqplot.BarRenderer}],
  axesDefaults: {
      tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
      tickOptions: { 
       angle: -30,
        fontSize: '10pt'
      }
  },  axes: {
    xaxis: {
      renderer: $.jqplot.CategoryAxisRenderer
    },
    yaxis: {
      autoscale:true
    }
  }});
<?
$query = "SELECT Name, Points, StatsColor From EH_Combat_Ratings Order By Points DESC";
$result = mysql_query($query, $mysql_link);
$rows = mysql_num_rows($result);
$prevtotal=0;
echo "crdata = [";
for($i=0; $i<$rows; $i++) {
  $values = mysql_fetch_row($result);
  $colors[]=$values[2];
  echo "['$values[0]: ";
  $query1 = "SELECT Count(EMSA_ID) From EH_Members_Special_Areas WHERE SA_ID=2 AND Value>=$values[1]";
  $result1 = mysql_query($query1, $mysql_link);
  $rows1 = mysql_num_rows($result1);
  if($rows1) {
    $values1 = mysql_fetch_row($result1);
    $pt = $values1[0]-$prevtotal;
    echo $pt."', ".$pt;
    $prevtotal=$values1[0];
    }
  echo "]";
  if($i+1<$rows)
    echo ", ";
  }
  echo "];\n";
?>
$.jqplot('cr', [crdata], {  
  title: 'Combat Ratings',
  series:[{renderer:$.jqplot.BarRenderer}],
  axesDefaults: {
      tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
      tickOptions: { 
       angle: -30,
        fontSize: '10pt'
      }
  },  axes: {
    xaxis: {
      renderer: $.jqplot.CategoryAxisRenderer
    },
    yaxis: {
      autoscale:true
    }
  }});
</script>
<?
include_once("footer.php");
?>