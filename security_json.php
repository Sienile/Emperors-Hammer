<?php
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);

switch($_GET["func"]){
	case "getdocs":
		getdocs($_GET["id"]);
}

function getdocs($doc_id){
	
}