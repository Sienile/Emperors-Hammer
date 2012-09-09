<?
include_once("config.php");
include_once("functions.php");
session_start();
// Unset all of the session variables.
session_unset();
// Finally, destroy the session.
$del = session_destroy();
if($del)
   Redirect("login.php");
?>