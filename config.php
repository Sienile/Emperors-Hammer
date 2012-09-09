<?
/* *****************************************************
*Emperor's Hammer: Hammer's Fist Site		*
*Written by: FA/OBM/FM Zsinj (aka Scott Lookabill)		*
*File: Config File					*
*******************************************************/
//MySQL Database info
$db_host = ""; // Redacted for security
$db_name = ""; // Redacted for security
$db_username = ""; // Redacted for security
$db_password = ""; // Redacted for security


$base_path  = ""; // Redacted for security
$site_host = ""; // Redacted for security			//URL to the site ommiting the http:// and the final /

//Image Headers Section, Coming soon
$adminrankname="HA Zsinj";
$adminemail="scott.lookabill@gmail.com";
$adminlink = "<a href=\"mailto:$adminemail\">$adminrankname</a>";

$postmaster = "DO NOT REPLY <postmaster@emperorshammer.org>";

include_once("security.class.php");
$SO = new Security(array("host"=>$db_host,"username"=>$db_username,"password"=>$db_password,"name"=>$db_name));
?>
