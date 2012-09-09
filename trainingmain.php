<?
session_start();
session_register('EHID');
include_once("config.php");
include_once("functions.php");
$page="Main";
include_once("nav.php");
?>
<h3 align="center">Welcome to the Emperor's Hammer Academies Site</h3>
<p>This site is the central portal to all the Emperor's Hammer Academies. From here you can select courses, take tests, view course notes, and see graduates of the various courses.</p>
<? include_once("footer.php"); ?>