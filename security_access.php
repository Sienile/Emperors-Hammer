<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
include_once("table.class.php");
Access($_SESSION['EHID'], "security_access");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$response = "";
if (array_key_exists("add_submit",$_POST)){
	// $ip, $target, $member_id, $status, $doc_id, $note
	$result = $SO->addAccess($_POST["IP"], $_POST["members"], $_POST["member_id"], $_POST["status"], $_POST["document_id"], $_POST["note"]);
	if (!$result["status"]){
		$response = "<span style='color: red'>Error: ".$result["msg"]."</span><br/>";
	}else{
		$response = "<span style='color: green'>".$result["msg"]."</span><br/>";
	}
}elseif(array_key_exists("edit_submit",$_POST)){
	$result = $SO->editAccess($_GET["id"], $_POST["status"], $_POST["document_id"], $_POST["note"]);
	if (!$result["status"]){
		$response = "<span style='color: red'>Error: ".$result["msg"]."</span><br/>";
	}else{
		$response = "<span style='color: green'>".$result["msg"]."</span><br/>";
	}	
}elseif(array_key_exists("regen_submit",$_POST)){
	$SO->generateHTAccess();
	$response = ".htaccess regenerated";
}
?>
<style>
	.ip_table {
		border: 1px solid grey;
		width: 100%;
	}
	.ip_table td {
		padding: 5px
	}
	.desc , label {
		color: white
	}
</style>

<script type="text/javascript">
	$(document).ready(function(){
		$("#ehtabs").tabs();
		$("#members").autocomplete("adminjson.php?func=memberautocomplete",{
				dataType:"json",
				parse: function(data) {
		            var array = new Array();
		            for(var i=0;i<data.length;i++)
		            {
                    	array[array.length] = { data: data[i], value: data[i].value, result: data[i].value };
		            }
		            return array;
	    		},
	    		formatItem: function(row, i, n) {
    		       return row.value + ', ' + row.id;
    		     },	
			});
		$("#members").result(function(func, data){
            if (data)
            {
                var value = $("form[name='addForm'] #member_id"); 
                value.val(data.id);                
       	    }
        }); 
		$("#documents").autocomplete("security_docs.php?func=getdocs",{
			dataType:"json",
			parse: function(data) {
	            var array = new Array();
	            for(var i=0;i<data.length;i++)
	            {
                	array[array.length] = { data: data[i], value: data[i].value, result: data[i].value };
	            }
	            return array;
    		},
    		formatItem: function(row, i, n) {
		       return row.value + ', ' + row.id;
		     },	
		});
		$("#documents").result(function(func, data){
	        if (data)
	        {
	            var value = $("form[name='addForm'] #document_id"); 
	            value.val(data.id);        
	   	    }
	    }); 
		
	});
</script>

<div style="padding: 5px;">
	<?php include_once("security.nav.php"); ?>
	<br /><br />
	<?php if (!isset($_GET["id"])) { ?>
	<div id="ehtabs">
		<ul>
			<li><a href="#log-tab">Access List</a></li>
			<li><a href="#search-tab">Search</a></li>
			<li><a href="#add-tab">Add</a></li>
			<li><a href="#adv-tab">Advanced</a></li>
		</ul>
		<div id="log-tab">
			<?php
			//$field="", $search="", $page="1", $order_by="Date", $desc="DESC"
			$field  = (isset($_GET["field"]))  ? $_GET["field"] : "";
			$search = (isset($_GET["search"])) ? $_GET["search"] : "";
			$page   = (isset($_GET["page"]))   ? $_GET["page"] : 1;
			$order  = (isset($_GET["order"]))  ? $_GET["order"] : "Date";
			$desc   = (isset($_GET["desc"]))   ? $_GET["desc"] : "DESC";
			
			list($data,$max_rows) = $SO->queryAccess($field, $search, $page, $order, $desc);
			$config = array("sortable"=>true,"style"=>array(
							"table"=>array("name"=>"","id"=>"","class"=>"ip_table","style"=>"width: 100%;","border"=>"1"),
				));
			$tbl = new Table($config);
			$col_order = array("Date"=>array("label"=>"Date Updated","link"=>"security_access.php?id={Access_ID}"),
							   "IP"=>array("label"=>"IP Address","link"=>"security_access.php?id={Access_ID}"),
							   "Status"=>array("label"=>"Access Status","link"=>"security_access.php?id={Access_ID}"),
							   "Name"=>array("label"=>"Member Name","link"=>"security_access.php?id={Access_ID}"),
							   "Target"=>array("label"=>"Target","link"=>"security_access.php?id={Access_ID}")
							  );
			$tbl->showPagedTable($data, $max_rows ,$col_order);
			?>
		</div>
		<?php 
		$toggle = (array_key_exists("field",$_GET)) ? $_GET["field"] : "IP";
		?>
		<div id="search-tab">
			<form method="get" action="">
				<label for="search">Search :</label><br />
				<div id="div_search_string" style="">
					<input type="text" id="search" name="search" value="" /><br />	
				</div>
				<div id="div_search_status" style="display:none;">
					<select id="search_status" name="search_disabled" >
					    <option value="0">Allowed</option>
					    <option value="1">Login Banned</option>
					    <option value="2">Site Banned</option>
					</select>
				</div><br />
				Search Field:<br />
				<input type="radio" id="IP" name="field" value="IP" onChange="switchSearch();" checked="checked" /> IP Search<br />
				<!-- - OR - <br /> -->
				<input type="radio" id="name" name="field" value="Name" onChange="switchSearch();" /> Search By Name  <br />
				<!-- OR - <br /> -->
				<input type="radio" id="member_id" name="field" value="Member_ID" onChange="switchSearch();" /> Search By Member ID  <br />
				<!-- OR - <br /> -->
				<input type="radio" id="status" name="field" value="Status" onChange="switchSearch();" /> Search By Status  <br />
				<br />
				<input type="submit" id="submit" name="submit" value="Submit" />
				
				<input type="reset" id="reset" name="reset" value="Reset" />
				
				<a href="/security_access.php" >Clear Search</a>
			</form>
		</div>
		<div id="add-tab">
			<?php echo (!empty($response)) ? $response: ""; ?>
			<form id="addForm" name="addForm" method="POST" action="#add-tab" >
				<input type="hidden" id="add_func" name="func" value="add" />
				<label for="add_ip">IP Address</label><br />
				<input type="text" id="add_ip" name="IP" value="" /><br />
				<label for="add_member">Member / Target *</label><br />
				<input type="text" id="members" name="members" value="" /><br />
				<input type="hidden" id="member_id" name="member_id" value="" />

				<label for="add_member">Related Document</label><br />
				<input type="text" id="documents" name="documents" value="" /><br />
				<input type="hidden" id="document_id" name="document_id" value="" />

				<label for="add_status">Status</label><br />
				<select id="add_status" name="status" >
				    <option value="0">Allowed to View / Login</option>
				    <option value="1">Login Banned</option>
				    <option value="2">Site Banned</option>
				</select><br />
				<label for="add_note">Notes</label><br />
				<textarea id="add_note" name="note" cols="60" rows="10"></textarea>
				<br />
				<input type="submit" id="add_submit" name="add_submit" value="Submit" />
				
				<input type="reset" id="add_reset" name="reset" value="Reset" />
				<br />
				* If a member is not found using the autocomplete it will set the "Target" field
			</form>
		</div>
		<div id="adv-tab">
			<div style="padding: 5px;">
				<form id="advform" name="advform" method="POST" action="#adv-tab" >
					<label for="advSubmit">Rebuild Access Control .htaccess file</label><br />
					<input type="submit" id="advSubmit" name="regen_submit" value="Rebuild Access Control" /><br /><br />
					This should never be needed but is available just in case
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript" >
		var last_status = "<?php echo (isset($_GET["IP"])) ? $_GET["IP"] : "IP"; ?>";
		function switchSearch(){
			var new_status = $("input[name='field']:checked").val();
			if (new_status == "Status" || (new_status != "Status" && last_status == "Status")){
				$("#div_search_string").toggle();
				$("#div_search_status").toggle();
				
				text = $("input[type='text']");
				select = $("select");
				if (text.attr("name") == "search"){
					text.attr("name", "search_disabled");
					text.attr("disabled", true);
				}else{
					text.attr("name", "search");
					text.removeAttr("disabled");
				}
				if (select.attr("name") == "search"){
					select.attr("name", "search_disabled");
					select.attr("disabled", true);
				}else{
					select.attr("name", "search");
					select.removeAttr("disabled");
				}
			}
			last_status = $("input[name='field']:checked").val();
		}
	</script>
	<?php 
	}elseif(!array_key_exists("edit",$_GET)){ // End if id is not set 
		$access_id = mysql_real_escape_string($_GET["id"]);
		list($data, $max_rows) = $SO->queryAccess("Access_ID", $access_id);
		$data = $data[0];
	?>
		<div id="description">
			<span class="desc">IP Address:</span> <?=$data["IP"]?><br />
			<span class="desc">Member Name:</span> <?=$data["Name"]?><br />
			<span class="desc">Target Name:</span> <?=$data["Target"]?><br />
			<span class="desc">Access Level :</span> <?=$data["Status"]?><br />
			<br />
			<span class="desc">Notes:</span>
			<hr />
			<?=nl2br($data["Note"])?>
			<hr />
			<a href="/security_access.php?edit&id=<?=$access_id?>">Edit Entry</a>
			|
			<a href="/security_access.php">Back to List</a>
		</div>
	<?php 
	}else{ 
		$access_id = mysql_real_escape_string($_GET["id"]);
		list($data, $max_rows) = $SO->queryAccess("Access_ID", $access_id);
		$data = $data[0];		
		?>
		<?php echo (!empty($response)) ? $response: ""; ?>
		<div id="description">
			<form id="addForm" name="addForm" method="POST" action="?edit&id=<?=$_GET["id"]?>" >
				<input type="hidden" id="add_func" name="func" value="add" />
				<label for="edit_ip">IP Address</label>
				<br />
				<input type="text" id="edit_ip" name="IP" value="<?=$data["IP"]?>" readonly="readonly" />
				<br />
				<label for="edit_member">Member / Target *</label>
				<br />
				<input type="text" id="edit_member" name="members" value="<?=$data["Target"]?>" readonly="readonly" />
				<br />
				<input type="hidden" id="member_id" name="member_id" value="<?=$data["Member_ID"]?>" readonly="readonly" />
	
				<label for="edit_member">Related Document</label>
				<br />
				<input type="text" id="documents" name="documents" value="<?=$data["Document_ID"]?>" />
				<br />
				<input type="hidden" id="document_id" name="document_id" value="<?=$data["Document_ID"]?>" />
	
				<label for="edit_status">Status</label>
				<br />
				<select id="edit_status" name="status" >
				    <option value="0" <?=($data["Status_Code"] <  1) ? "selected=\"selected\"": ""; ?> >Allowed to View / Login</option>
				    <option value="1" <?=($data["Status_Code"] == 1) ? "selected=\"selected\"": ""; ?>>Login Banned</option>
				    <option value="2" <?=($data["Status_Code"] == 2) ? "selected=\"selected\"": ""; ?>>Site Banned</option>
				</select>
				<br />
				<label for="edit_note">Notes</label><br />
				<textarea id="edit_note" name="note" cols="60" rows="10"><?=nl2br($data["Note"])?></textarea>
				<br />
				<input type="submit" id="edit_submit" name="edit_submit" value="Submit" />
				
				<input type="reset" id="edit_reset" name="reset" value="Reset" />
				
				<button type="reset" id="edit_cancel" name="cancel" value="Cancel" >Cancel</button>
				<br />
				* If a member is not found using the autocomplete it will set the "Target" field
			</form>
		</div>
	<?php } ?>
</div>
<?php 
include("footer.php");
?>