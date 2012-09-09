<?
include_once("config.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$id = mysql_real_escape_string($_GET['id'], $mysql_link);
$query = "select Email FROM EH_Members Where Member_ID=$id";
$result = mysql_query($query, $mysql_link);
$values = mysql_fetch_row($result);
$text = $values[0];
$size=8;
$angle=0;
$fontfile="fonts/Verdana.TTF";
$box = imagettfbbox ($size, $angle , $fontfile, $text);
$im = imagecreatetruecolor($box[2]+15, $box[3]+15);
$white=imagecolorallocate ($im, 0, 0, 0);
$black=imagecolorallocate ($im, 255, 255, 255);
imagefilledrectangle($im, 0, 0, $box[2]+15, $box[3]+15, $white);
imagettftext ($im, $size, $angle, 5, $box[1]+10, $black, $fontfile ,$text);

// Output the image to browser
header('Content-type: image/gif');

imagegif($im);
imagedestroy($im);
?>