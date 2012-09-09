<?
session_start();
include_once("config.php");
include_once("functions.php");
include_once("nav.php");
include_once("table.class.php");
Access($_SESSION['EHID'], "security_docs");
$mysql_link = mysql_connect($db_host, $db_username, $db_password);
mysql_select_db($db_name, $mysql_link);
$response = "";
$db = array("host"=>$db_host, "name"=>$db_name, 
					"username"=>$db_username, "password"=>$db_password);
if (!empty($_POST)){
	if ($_POST["Submit"] == "Add"){
		$DOC = new SODocument("new",$db);
		$result = $DOC->add($_POST);
		if (!$result["status"]){
			$response = "<span style='color: red'>Error: ".$result["msg"]."</span><br/>";
		}else{
			$response = "<span style='color: green'>".$result["msg"]."</span><br/>";
		}
	}else if ($_POST["Submit"] == "Edit"){
		$DOC = new SODocument($_GET["id"], $db);
		$DOC->update($_POST);
	}
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
	
	.associated_profiles th {
		padding: 3px;
		background-color: grey;
	}
	.associated_profiles td {
		padding: 3px;
		background-color: white; 
		color: black;
	}
	
	.associated_profiles a {
		color: black;
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
    		     }
			});
		$("#members").result(function(func, data){
            if (data)
            {
                var value = $("form[name='addForm'] #member_id"); 
                value.val(data.id);                
       	    }
        }); 
		$("#add_profile").autocomplete("adminjson.php?func=memberautocomplete",{
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
		     }
		});
		$("#add_profile").result(function(func, data){
	        if (data)
	        {
	            var value = $("form[name='addForm'] #new_member_id"); 
	            value.val(data.id);        
	   	    }
	    }); 
		
	});

	function addRow(){
	    var table = $(".associated_profiles");
	    var id = $("#new_member_id").val();
	    var mem_name = $("#add_profile").val();

	    table.append("<tr id=\"row_"+id+"\">\
	                      <td>"+mem_name+"<input type=\"hidden\" id=\"add_id_"+id+"\" name=\"add_id[]\" value=\""+id+"\" /></td><td>"+id+"</td><td><a href=\"javascript:delRow("+id+");\" >remove</a></td>\
	                  </tr>");
	}

	function delRow(id){
		$("#row_"+id).remove();
	}
	
</script>

<div style="padding: 5px;">
	<?php include_once("security.nav.php"); ?>  
	<br /><br />
	<?php if (!isset($_GET["id"])) { ?>
	<div id="ehtabs">
		<ul>
			<li><a href="#log-tab">Documents</a></li>
			<li><a href="#search-tab">Search</a></li>
			<li><a href="#add-tab">Add Document</a></li>
		</ul>
		<div id="log-tab">
			<?php
			//$field="", $search="", $page="1", $order_by="Date", $desc="DESC"
			$field  = (isset($_GET["field"]))  ? $_GET["field"] : "";
			$search = (isset($_GET["search"])) ? $_GET["search"] : "";
			$page   = (isset($_GET["page"]))   ? $_GET["page"] : 1;
			$order  = (isset($_GET["order"]))  ? $_GET["order"] : "Date_Added";
			$desc   = (isset($_GET["desc"]))   ? $_GET["desc"] : "DESC";
			
			list($data,$max_rows) = $SO->queryDocs($field, $search, $page, $order, $desc);
			$config = array("sortable"=>true,"style"=>array(
							"table"=>array("name"=>"","id"=>"","class"=>"ip_table","style"=>"width: 100%;","border"=>"1"),
				));
			$tbl = new Table($config);
			$col_order = array("Date_Added"=>array("label"=>"Date","link"=>"security_docs.php?id={Document_ID}"),
							   "Name"=>array("label"=>"Member","link"=>"security_docs.php?id={Document_ID}")
							  );
			$tbl->showPagedTable($data, $max_rows ,$col_order);
			?>
		</div>
		<div id="search-tab">
			To be completed later
		</div>
		<div id="add-tab">
			<?php echo $response; ?>
			<form id="addForm" name="addForm" method="POST" action="#add-tab" >
			    <table >
			        <tr>
			            <td style="vertical-align: top">
				            <label for="member">Primary Profile</label>
			                <br />
				            <input type="text" id="members" name="members" value="" />
			                <br />
			                <input type="hidden" id="member_id" name="Member_ID" value="" />
				            <label for="Aliases">Non-EH Aliases</label>
			                <br />
				            <textarea id="aliases" name="Aliases"></textarea>
			            	<br />
				            <label for="Last_IP">Last Known IP</label>
			                <br />
				            <input type="text" id="last_ip" name="Last_IP" value="" />
			                <br />
				            <label for="Last_Location">Last Known Location</label>
			                <br />
				            <input type="text" id="location" name="Last_Location" value="" />
			                <br />
				            <label for="Previous_IP">Previous IPs</label>
			                <br />
				            <textarea id="previous_ips" name="Previous_IP"></textarea>
			            </td>
			            <td style="vertical-align: top; padding-left: 10px;">
				            <label for="add_profile">Add Another Profile</label>
			                <br />
				            <input type="text" id="add_profile" name="add_profile" value="" />
				            
				            <input type="button" id="add" name="add" value="Add" onClick="addRow();" />
			                <br />
			                <input type="hidden" id="new_member_id" name="new_member_id" value="" />
			                <br />
			                <table class="associated_profiles" style="width: 400px;">
			                    <tr>
			                        <th>Name</th><th style="width: 100px">Pin#</th><th style="width: 30px">Action</th>
			                    </tr>
			                </table>
			                <br />
			                <label for="Notes"></label>
			                <br />
			                <textarea id="notes" name="Notes" cols="50" rows="10" ></textarea>
			            </td>
			        </tr>
			        <tr>
			        	<td colspan="2">
			        		<span>
				        		<input type="Submit" id="Submit" name="Submit" value="Add" />
				        		
				        		<input type="Reset" id="Reset" name="Reset" value="Reset" />
			        		</span>
			        	</td>
			        </tr>
				</table>
			</form>
		</div>
	</div>
	<?php 
	}else if(array_key_exists("edit",$_GET)){ 
		$DOC = new SODocument($_GET["id"], $db);
		?>
		<div  id="edit">
            <?php // echo $response; ?>
			<form id="addForm" name="addForm" method="POST" >
			    <table >
			        <tr>
			            <td style="vertical-align: top">
				            <label for="member">Primary Profile</label>
			                <br />
                            <?=$DOC->Name; ?>
			                <br />
				            <label for="Aliases">Non-EH Aliases</label>
			                <br />
				            <textarea id="aliases" name="Aliases"><?=$DOC->Aliases?></textarea>
			            	<br />
				            <label for="Last_IP">Last Known IP</label>
			                <br />
				            <input type="text" id="last_ip" name="Last_IP" value="<?=$DOC->Last_IP?>" />
			                <br />
				            <label for="Last_Location">Last Known Location</label>
			                <br />
				            <input type="text" id="last_location" name="Last_Location" value="<?=$DOC->Last_Location?>" />
			                <br />
				            <label for="Previous_IP">Previous IPs</label>
			                <br />
				            <textarea id="previous_ip" name="Previous_IP"><?=$DOC->Previous_IP?></textarea>
			            </td>
			            <td style="vertical-align: top; padding-left: 10px;">
				            <label for="add_profile">Add Another Profile</label>
			                <br />
				            <input type="text" id="add_profile" name="add_profile" value="" />
				            
				            <input type="button" id="add" name="add" value="Add" onClick="addRow();" />
			                <br />
			                <input type="hidden" id="new_member_id" name="new_member_id" value="" />
			                <br />
			                <table class="associated_profiles" style="width: 400px;">
			                    <tr>
			                        <th>Name</th><th style="width: 100px">Pin#</th><th style="width: 30px">Action</th>
			                    </tr>
                                <?php foreach($DOC->Profiles as $profile){ ?>
                                    <tr id="row_<?=$profile["Member_ID"]?>">
                                      <td><?=$profile["Name"]?>
                                            <input type="hidden" id="add_id_" name="add_id[]" value="<?=$profile["Member_ID"]?>" />
                                      </td>
                                      <td><?=$profile["Member_ID"]?></td>
                                      <td>
                                          <a href="javascript:delRow(<?=$profile["Member_ID"]?>);" >remove</a>
                                      </td>
                                    </tr>
                                <?php } ?>
			                </table>
			                <br />
			                <label for="Notes"></label>
			                <br />
			                <textarea id="notes" name="Notes" cols="50" rows="10" ><?=$DOC->Notes?></textarea>
			            </td>
			        </tr>
			        <tr>
			        	<td colspan="2">
			        		<span>
				        		<input type="Submit" id="Submit" name="Submit" value="Edit" />
				        		
				        		<input type="Reset" id="Reset" name="Reset" value="Reset" />
			        		</span>
			        	</td>
			        </tr>
				</table>
			</form>
		</div>
	<?php 
	}else{
		$id = $_GET["id"];
		$DOC = new SODocument($id, $db);
		?>
	<style>
		.title{
			color: white;
			font-weight: strong;
		}
	</style>
	<div id="SODocument">
		<span class="title">Dossier for:</span> [<a href="profile.php?pin=<?php echo $DOC->Member_ID; ?>"><?php echo $DOC->Name; ?></a>]
		<br />
		<br />
		<span class="title">Submitted By:</span> <?php echo $DOC->Submitter_Name;?>
		<br />
		<span class="title">Submitted On:</span> <?php echo $DOC->Date_Added;?>
		<br />
		<hr />
		<span class="title">Aliases:</span>
		<br />
		<?php echo $DOC->get("Aliases");?>
		<hr />
		<span class="title">Last Known Location:</span>
		<br />
		<?php echo $DOC->get("Last_Location");?>
		<hr />
		<span class="title">Last Known IP:</span>
		<br />
		<?php echo $DOC->get("Last_IP");?>
		<br />
		<span class="title">Previous IPs:</span>
		<br />
		<?php echo $DOC->get("Previous_IP");?>
		<br />
		<span class="title">System IPs:</span>
		<br />
		<?php 
		foreach($DOC->System_IP as $IP){
			echo $IP["IP"]." on ".$IP["Date"]."<br />";
		} ?>
		<br />
		<hr />
		<span class="title">Associated Profiles:</span>
		<br />
		<?php 
		foreach($DOC->Profiles as $profile){
		?>
			<hr />
			<span class="title">Profile ID:</span> <?php echo $profile["Member_ID"]?>
			<br />
			<span class="title">Profile:</span>
			
			[<a href="profile.php?pin=<?php echo $profile["Member_ID"]; ?>"><?php echo $profile["Name"]?></a>]
			<br />
			<span class="title">Date Added:</span> <?php echo $profile["Date_Added"]?>
			<br />
			<hr />
		<?php } ?>
		<hr />
		| <a href="?edit&id=<?php echo $_GET["id"]; ?>">Edit Document</a> | 
	</div>
<?php
	}
	?>
</div>
<?php 
include("footer.php");
?>