<?php
session_start();
include_once("config.php");
$so_info = $SO->fetch_so();
?>
<html>
	<head>
		<title>403: Access Forbidden</title>
		<style>
			body{
				background-color: black;
				color: red;
				font-size: large;
			}
		</style>
	</head>
	<body>
		<h1>Access to this site has been forbidden for this address</h1>
		<hr/>
		By order of the Security Office, the address you are attempting to access this site from has been<br/>
		ordered to be blocked from viewing this site. If you feel this is in error whether by a change of IP <br />
		or other method please contact the Security Officer at <?php echo $so["email"]?> .
		<br />
		<br />
		Your IP Address is : <?=$_SERVER["REMOTE_ADDR"]?>
	</body>
</html>