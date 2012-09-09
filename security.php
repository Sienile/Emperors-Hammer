<?
session_start();
include_once("config.php");
include_once("functions.php");
Access($_SESSION['EHID'], "security");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
?>
<style>
	strong {as 
		color: #0c0;
	}
</style>
<div style="padding: 5px;">
	<img src="images/Secoffice-small.jpg" height="220" width="200" style="float: right;" /><br />
	<?php if(has_access($_SESSION['EHID'], "security_ip")){ ?>
	<strong>IP Tracking Tools</strong>
	<ul type="circle">
		<li><a href="security_ip.php" >View IP Log</a></li>
		<li><a href="security_ip.php#search-tab" >Search IP Log</a></li>
	</ul>
	<?php }
	if(has_access($_SESSION['EHID'], "security_docs")){
	?>
	<strong>Member Documents</strong>
	<ul type="circle">
		<li><a href="security_docs.php">View Documents</a></li>
		<li><a href="security_docs.php#search-tab">Search Documents</a></li>
		<li><a href="security_docs.php#add-tab">Add Document</a></li>
	</ul>
	<?php }
	if(has_access($_SESSION['EHID'], "security_access")){?>
	<strong>Access Controls</strong>
	<ul type="circle">
		<li><a href="security_access.php">View IP Controls</a></li>
		<li><a href="security_access.php#search-tab">Search IP Controls</a></li>
		<li><a href="security_access.php#add-tab">Add IP control</a></li>
	</ul>
	<?php } ?>
</div>
<?php 
include("footer.php");
?>