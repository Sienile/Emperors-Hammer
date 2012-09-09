<?php ini_set("display_errors", 1); ?>
<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
include_once("table.class.php");
Access($_SESSION['EHID'], "security_ip");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
?>
<style>
	.ip_table {
		border: 1px solid grey;
		width: 100%;
	}
	.ip_table td {
		padding: 5px
	}
</style>

<script type="text/javascript">
	$(function(){
		$("#ehtabs").tabs();
	});
</script>

<div style="padding: 5px;">
	<?php include_once("security.nav.php"); ?> 
	<br /><br />
	<div id="ehtabs">
		<ul>
			<li><a href="#log-tab">IP Tracker</a></li>
			<li><a href="#search-tab">Search</a></li>
		</ul>
		<div id="log-tab">
			<?php
			//$field="", $search="", $page="1", $order_by="Date", $desc="DESC"
			$field  = (isset($_GET["field"]))  ? $_GET["field"] : "";
			$search = (isset($_GET["search"])) ? $_GET["search"] : "";
			$page   = (isset($_GET["page"]))   ? $_GET["page"] : 1;
			$order  = (isset($_GET["order"]))  ? $_GET["order"] : "Date";
			$desc   = (isset($_GET["desc"]))   ? $_GET["desc"] : "DESC";
			
			list($data,$max_rows) = $SO->queryIP($field, $search, $page, $order, $desc);

			$tbl = new Table();
            $ip_link = "security_ip.php?search={IP}&field=IP&submit=Submit";
            $name_link = "security_ip.php?member={Member_ID}";
			$col_order = array("Date"=>array("label"=>"Date","link"=>""),
							   "IP"=>array("label"=>"IP Address","link"=>$ip_link),
							   "Script"=>array("label"=>"Script/Page","link"=>""),
							   "Warning"=>array("label"=>"Warning","link"=>""),
							   "Is_Login"=>array("label"=>"Is a Login","link"=>""),
							   "Name"=>array("label"=>"Member Name","link"=>""));

			$config = array("sortable"=>true,
							"style"=>array(
								"table"=>array("name"=>"","id"=>"","class"=>"ip_table","style"=>"width: 100%;","border"=>"1"),
							));
			$tbl->config($config);
			$tbl->showPagedTable($data, $max_rows ,$col_order);
			?>
			<?php if (isset($_GET["search"])){?>
				<br /><a href="security_ip.php" >Clear Search</a>
			<?php } ?>
		</div>
		<?php 
		$toggle = (array_key_exists("field",$_GET)) ? $_GET["field"] : "IP";
		?>
		<div id="search-tab">
			<form method="get" action="">
				<label for="search">Search :</label><br />
				<input type="text" id="search" name="search" value="<?php echo (array_key_exists("search",$_GET)) ? $_GET["search"] : ""; ?>" /><br />
				<br />
				Search Field:<br />
				<input type="radio" id="IP" name="field" value="IP" <?php echo ($toggle == "IP") ? "checked='checked'" : ""; ?> /> IP Search <br />
				-- OR -- <br />
				<input type="radio" id="IP" name="field" value="Name" <?php echo ($toggle == "Name") ? "checked='checked'" : ""; ?> /> Search By Name  <br />
				<br />
				<input type="submit" id="submit" name="submit" value="Submit" />
				
				<input type="reset" id="reset" name="reset" value="Reset" />
				
				<a href="security_ip.php" >Clear Search</a>
			</form>
		</div>
	</div>
</div>
<?php
include("footer.php");
?>