<?php
/*
 * Created by : VA Jedi Eclipse (Greg Gullett)
 * Description: Class to help the SO in their duties
 * 
 */
include_once("functions.php");
class Security{
	/**
	 * @var database connection
	 */
	private $db = false;

	/**
	 * function Security
	 * @param $db mysql connection object.
	 */
	function Security($db=false){
		if (is_array($db)){
			$this->db = mysql_connect($db["host"], $db["username"], $db["password"]);
			mysql_select_db($db["name"], $this->db);
		}else if(gettype($db) == "resource"){
			$this->db = $db;	
		}else{
			include_once("config.php");
			$this->db = mysql_connect($db_host, $db_username, $db_password);
			mysql_select_db($db_name, $this->db);
			if (!$this->db){
				die("No valid database resource available");
			}
		}
	}

	/**
	 * function addIP adds an item to the tracking database
	 * @param string $system_note A note from the system enterying item
	 * @param int $is_login Whether or not this is a login, true or false.
	 */
	public function addIP($system_note="null", $is_login=false){
		$id = (isset($_SESSION) && array_key_exists("EHID",$_SESSION)) ? $_SESSION["EHID"] : "null"; 
		$script = $_SERVER["SCRIPT_NAME"];
		$ip     = $_SERVER["REMOTE_ADDR"];
		$warning = "null";
		if ($is_login){
			$warning = $this->checkLogin($id);
		}else{
			$this->accessWarning($id, $name, $ip, $script, $system_note);
		}
		// If $is_login == true, make it 1, else 0
		$is_login = ($is_login) ? "1" : "0";
		
		$query_string = "INSERT INTO EH_IP_Tracker 
						(`Member_ID`,`IP`,`Script`,`Date`,`Is_Login`,
						 `Warning_Flag`,`System_Note`) 
						VALUES 
						('%d','%s','%s',NOW(),'%s','%s','%s')";
		$query = mysql_query(sprintf($query_string, $id, $ip, 
									 $script, $is_login, $warning,
									 $system_note), $this->db);
		if (!$query){
			$error = mysql_error($this->db);
			die("Could not add an IP log entry ".$error);
		}
		return true;
	}
	
	/**
	 * function queryIP, used to query the database for ip log table
	 * @param string $field Field to search
	 * @param string $search The query
	 * @param int $page The page to search
	 * @param string $order_by the order by field
	 * @param string $desc values (DESC, ASC)
	 */
	public function queryIP($field="", $search="", $page="1", $order_by="Date", $desc="DESC"){
		
		$query_string = "SELECT eip.Track_ID,eip.Member_ID,
								 eip.IP,eip.Script,eip.Date,
								 if (eip.Is_Login > 0, 'Yes', 'No') as Is_Login,
								 if (eip.Warning_Flag > 0, 'Yes','No') as Warning,
								 eip.System_Note, 
								 m.Name FROM `EH_IP_Tracker` as eip ";
		
		$join = "LEFT JOIN `EH_Members` as m ON eip.Member_ID = m.Member_ID ";
		$query_string .= $join;
		
		$where  = "";
		if (!empty($search)){
			$where = "WHERE ".mysql_real_escape_string($field)." LIKE '".mysql_real_escape_string($search)."'";
		}
		return $this->query($query_string, $where, $page, $order_by, $desc);
	}
	
	private function stripsl($item){
		return(stripslashes($item));
	}
	/**
     * This function is the end-point for all query requests in this class. 
     * query should not be called directly, see queryIP for an exmample of usage
     * @param string  $select   the select portion of your query
     * @param string  $where    the where condition of your query
     * @param int     $page     the page of data to retreive
     * @param string  $order_by is the field to order results by
     * @param string  $desc is  the direction to order by , (DESC, ASC)
     * @param boolean $all      If you want to pull back all records, remove limit, default false
     */
	private function query($select, $where, $page ,$order_by, $desc, $all=false){
		$return = array();
		$per_page = 50;
		$offset = ((int)$page-1) * $per_page;
		
		$query_string = $select;
		$order = "ORDER BY ".mysql_real_escape_string($order_by)." ".mysql_real_escape_string($desc);
		$query_string .= $where." ".$order;
		
		$row_query = mysql_query($query_string);
		$max_rows = ($row_query) ? mysql_num_rows($row_query) : 1;
		if (!$all){
			$limit = "LIMIT ".$offset.", ".$per_page;
			$query_string .= " ".$limit;
		}
		$query = @mysql_query($query_string, $this->db);
		$data = array();
		
		while ($row = mysql_fetch_assoc($query)){
			array_push($data, array_map(array('Security','stripsl'),$row));	
		}
		return array($data, $max_rows);
	}

	/**
	 * Function checkLogin checks to see if there are any other logins on the same IP address.
	 * @param int $id Member ID
	 */
	private function checkLogin($id){
		$ip    = $_SERVER["REMOTE_ADDR"];		
		$query = mysql_query("SELECT DISTINCT track.Member_ID as Member_ID, m.Name as Name, track.Date as Date
				  LEFT JOIN `EH_Members` as m ON track.Member_ID = m.Member_ID
		          FROM `EH_IP_Tracker` as track
				  WHERE `IP`='".$ip."' 
			  		AND `Is_Login`=='1' 
		  			AND `Member_ID` != '".$id."'
		  		  ORDER BY Date Desc");
		
		if (@mysql_num_rows($query) > 0){
			$data  = array();
			$user1  = $this->fetch_user($id);
			// appending the first user data
			array_push($data, $user1);
			while($row = mysql_fetch_assoc($query)){
				array_push($data, $row);
			}
			$this->loginWarning($data);
			return "1";
		}else{
			return "null";
		}
	}
	
	/**
	 * Function loginWarning sends the warning message to the SO that another member has logged in under
	 * the same IP address as another.
	 * @param array  $data Array of members, first item should be most recent.
	 * @param string $ip IP Address in question
	 */
	private function loginWarning(array $data, $ip){
		$message  = "This is a security warning.\n\n";
		
		$message .= "The EH website has noticed that a member has just logged in from the same address\n";
		$message .= "as another member.\n\n";
		
		$message .= "What we have found:\n";		
		$message .= "Member ".$data[0]["Name"]." logged in under the same IP as member ".$data[1]["Name"]."\n";
		$message .= "Member ".$data[1]["Name"]." was seen at this IP on ".$data[1]["Date"].".\n\n";
		
		$message .= "The IP Address is ".$ip."\n";
		$message .= "This occurred at: ".date("F j, Y, g:i a T")."\n\n";
		
		$message .= "The EH Database-";
		
		$recipient = "";
		$recipient_base = $this->fetch_by_position("60");
		
		if (!$recipient_base){
			$recipient_base = $this->fetch_by_position("49");
			if (!$recipient_base){
				die("Could not find an SO or XO to notify, script exiting");
			}
		}
		$recipient = $recipient_base["Email"];
		
		$this->sendMail("[SO-Alert] EH Login Warning", $message, $recipient);
	}

    /**
    * Function builds message portion of email to warn the SO that someone is attempting to gain access to 
    * restricted sections
    * @param int id is the member ID (if available)
    * @param string $name is the name of the member or "Unknown"
    * @param string $ip is the ip address of the person attempting access
    * @param string $page is the page they are accessing
    * @param string $note is a note that the system inputs
    */
	private function accessWarning($id=false, $name="Unknown", $ip, $page, $note=""){
		$message  = "This is a security warning.\n\n";
		
		$message .= "The EH website has noticed that someone has attempted to access a secured area without permission.\n\n";
		
		$message .= "What we have found:\n";		
		if ($id){
			$message .= "[Member ID]   : ".$id.".\n";
			$message .= "[Member Name] : ".$name.".\n";	
		}
		
		$message .= "[IP Address] : ".$ip."\n";
		$message .= "[Script Accessed] : ".$page."\n";
		if (!empty($note)){
			$message .= "[System Note] : ".$note."\n";
		}
		$message .= "This occurred at : ".date("F j, Y, g:i a T")."\n\n";
		
		$message .= "The EH Database-";
		
		$recipient = "";
		$recipient_base = $this->fetch_by_position("60");
		
		if (!$recipient_base){
			$recipient_base = $this->fetch_by_position("49");
			if (!$recipient_base){
				die("Could not find an SO or XO to notify, script executing");
			}
		}
		$recipient = $recipient_base["Email"];
		
		$this->sendMail("[SO-Alert] EH Access Warning", $message, $recipient);
	}
	
	/**
	 * function sendMail sends an email!
	 * @param string $subject
	 * @param string $message
	 * @param string $recipient
	 */
	private function sendMail($subject, $message, $recipient){
		$backtrace = debug_backtrace();
		if (count($backtrace) > 2 && $backtrace[3]["function"] =="test"){
			print "Sending mail to : ". $recipient."<br />";
			print "Subject : ".$subject."<br />";
			print "<pre>";
			print $message;
			print "</pre>";
		}
		
		$postmaster = "DO NOT REPLY <postmaster@emperorshammer.org>";
		$headers .= "From: $postmaster\n";
		$headers .= "X-Mailer: PHP\n"; // mailer
		$headers .= "Return-Path: $postmaster\n";  // Return path for errors
		storeEmail($recipient, '', '', $subject, $message);
		mail($recipient, $subject, $message, $headers);
		
		return true;
	}
	/**
     *Function fetches who the current SO is
     */
	public function fetch_so(){
		$so = array();
		$info = $this->fetch_by_position(60);
		$so["email"] = $info["Email"];
		$so["name"] = $info["Name"];
		return $so;
	} 
	
	/**
	 * Fetches user given the member ID as $id
	 * @param int $id Member ID
	 */
	private function fetch_user($id){
		$query_string = "SELECT `Member_ID`,`Name` FROM `EH_Members` WHERE `Member_ID` = '".$id."'";
		$query = mysql_query($query_string);
		if (!$query){
			return array("Member_ID"=>$id,"Name"=>"Lookup Failed");	
		}else{
			return mysql_fetch_array($query);
		}
	}

	/**
	 * Fetches the member record by position ID
	 * @param int $position_id
	 */
	private function fetch_by_position($position_id){
		$query_string = "SELECT m.Member_ID as Member_ID, m.Name as Name, m.Email as Email 
						 FROM `EH_Members` as m
						 INNER JOIN `EH_Members_Positions` as emp
						     ON m.Member_ID = emp.Member_ID  
						 WHERE emp.Position_ID = '".$position_id."'";
		$query = mysql_query($query_string);
		if (!$query){
			return false;
		}else{
			return mysql_fetch_array($query);
		}
	}

    public function checkAccess($member_id){
        $query = "SELECT `status` FROM `EH_IP_ACCESS` WHERE `Member_ID` = '{$member_id}' ORDER BY `status` DESC LIMIT 1";
        $result = mysql_query($query, $this->db);
        if ($result){
            $status = mysql_result($result, 0);
            return $status;        
        }else{
            return 0;
        }
    }

	/**
	 * Function addAccess adds an entry to the EH_IP_Access table
	 * @param int_type $ip
	 * @param int_type $member_id
	 * @param int_type $status
	 * @param string $note
	 * @return string
	 */
	public function addAccess($ip, $target, $member_id, $status, $doc_id, $note){
		$ip = mysql_real_escape_string($ip);
		$member_id = mysql_real_escape_string($member_id);
		$member_info = array();
		if (!empty($member_id)){
			$member_info = $this->fetch_user($member_id);
			if (empty($member_info)){
				$member_info["Name"] = "Unknown";
			}
		} 
		$target  = (empty($target)) ? $member_info["Name"] :  mysql_real_escape_string($target);
		$status = mysql_real_escape_string($status);
		$doc_id  = mysql_real_escape_string($doc_id);
		$note  = mysql_real_escape_string($note);
		$query_string = "INSERT INTO EH_IP_Access 
						(`IP`,`Member_ID`,`Document_ID`, `Target`, `Status`, `Date`,`Note`) 
						VALUES 
						('%s','%d','%d','%s','%d',NOW(),'%s')";
		$query = mysql_query(sprintf($query_string, $ip, $member_id, $doc_id, $target, 
									 $status, $notes), $this->db);
		if (!$query){
			$error = mysql_error($this->db);
			return array("status"=>false,"msg"=>$error);
		}
		$this->generateHTAccess();
		return array("status"=>true,"msg"=>"Access rule created successfully");
	}
	
	/**
	 * Function editAccess edits an entry to the EH_IP_Access table
	 * @param string $id
	 * @param string $status
	 * @param string $note 
	 * @return string
	 */
	public function editAccess($id, $status, $document_id, $note){
		$status = mysql_real_escape_string($status);
		$note = mysql_real_escape_string($note);
		$query_string = "UPDATE EH_IP_Access SET `Status`='%d', `Document_ID`='%d', `Note`='%s' 
						 WHERE `Access_ID` = '%d'";
		$query = mysql_query(sprintf($query_string, $status, $document_id ,$note, $id), $this->db);
		if (!$query){
			$error = mysql_error($this->db);
			return array("status"=>false,"msg"=>"Could not update access rule".$error);
		}
		$this->generateHTAccess();
		return array("status"=>true,"msg"=>"Access rule edited successfully");
	}
	
	/**
	 * Function builds query for the Access table and returns result of $this->query 
	 * @param string $field
	 * @param string $search
	 * @param int $page
	 * @param string $order_by
	 * @param string $desc
	 */
	public function queryAccess($field="", $search="", $page="1", $order_by="Date", $desc="DESC", $all=false){
		
		$query_string = "SELECT eia.Access_ID,eia.Member_ID,
								eia.Document_ID,eia.Target,
								eia.IP, eia.Status as Status_Code,
								CASE WHEN eia.Status = 0 THEN 'Allowed'
								  WHEN eia.Status = 1 THEN 'Login Banned'
								  WHEN eia.Status = 2 THEN 'Site Banned' END AS Status,
								eia.Date, eia.Note, m.Name FROM `EH_IP_Access` as eia ";
		
		$join = "LEFT JOIN `EH_Members` as m ON eia.Member_ID = m.Member_ID ";
		$query_string .= $join;
		
		$where  = "";
		if (!empty($search)){
			$where = "WHERE ".mysql_real_escape_string($field)." LIKE '".mysql_real_escape_string($search)."'";
		}		
		return $this->query($query_string, $where, $page, $order_by, $desc, $all);
	}
	
	public function generateHTAccess(){
		list($ban_rows,$count) = $this->queryAccess("Status",2);
		$file = "ErrorDocument 403 /403.php\n";
		$file .= "order allow,deny\n";

		foreach($ban_rows as $row){
			$file .= "deny from ".$row["IP"]."\n";
		}
		$file .= "allow from all\n";		
		$f = fopen(".htaccess","w+");
		fwrite($f,$file);
	}
	
	/**
	 * Function builds query for the Documents table and returns result of $this->query 
	 * @param unknown_type $field
	 * @param unknown_type $search
	 * @param unknown_type $page
	 * @param unknown_type $order_by
	 * @param unknown_type $desc
	 */
	public function queryDocs($field="", $search="", $page="1", $order_by="Date", $desc="DESC", $all=false){
		
		$query_string = "SELECT esd.*, m.Name FROM 
						 `EH_Security_Docs` as esd
						 INNER JOIN `EH_Members` as m
						 	ON m.Member_iD = esd.Member_ID
						 ";
		
		$where  = "";
		if (!empty($search)){
			$where = "WHERE ".mysql_real_escape_string($field)." LIKE '".mysql_real_escape_string($search)."'";
		}		
		return $this->query($query_string, $where, $page, $order_by, $desc, $all);
	}
	
	/**
	 * Function test, used to test any functions in the class.
	 * @param string $function
	 */
	public function test($function){
		$func_args = func_get_args();
		$function_args = array_slice($func_args,1);
		$result = call_user_func_array(array($this,$function), $function_args);
		
		echo "Function Test: ".$function."<br />";
		echo "Function Args: <br />";
		print_r($func_args);
		echo "<hr />";
		echo "Result : <br />";
		if (is_array($result)){
			print "<pre>";
			print_r($result);
			print "</pre>";	
		}else{
			echo $result."<br />";
		}
	}
}

/**
 * 
 * This is an object that represents a Security Office Document. 
 * Essentially it is a Dossier
 * @author VA Jedi Eclipse
 *
 */
class SODocument{
	private $db;
	private $id;
	public $DB_Errors = array();
	public $Document_ID;
	public $Member_ID;
	public $Name;
	public $Aliases;
	public $Last_IP;
	public $Last_Location;
	public $Previous_IP;
	public $Notes;
	public $Profiles;
	public $Submitter_ID;
	public $Submitter_Name;
	public $Date_Added;
	
	/**
	 * 
	 * Constructor for the document object
	 * @param int $doc_id
	 * @param mixed $db If an array will make connection, if db con instance, will use that.
	 */
	function SODocument($doc_id=false, $db=false){
		if (is_array($db)){
			$this->db = mysql_connect($db["host"], $db["username"], $db["password"]);
			mysql_select_db($db["name"], $this->db);
		}else if(gettype($db) == "resource"){
			$this->db = $db;	
		}else{
			include_once("config.php");
			$this->db = mysql_connect($db_host, $db_username, $db_password);
			mysql_select_db($db_name, $this->db);
			if (!$this->db){
				die("No valid database resource available");
			}
		}
		if ($doc_id == "new"){
			return;
		}else{
			$this->id = $doc_id;
			$this->loadDocument();
			$this->loadProfiles();
			$this->System_IP = (array)$this->loadIPs($this->Member_ID);
		}
		
	}
	
	/**
	 * 
	 * Loads the current state of the document into the object
	 */
	private function loadDocument(){
		$query_string = "SELECT ESD.*, 
						 EM.Name as Submitter_Name,
						 EM2.Name as Name
						 FROM `EH_Security_Docs` as ESD
					 	 INNER JOIN `EH_Members` as EM
					 	 	ON EM.Member_ID = ESD.Submitter_ID 
					 	 INNER JOIN `EH_Members` as EM2
					 	 	ON EM2.Member_ID = ESD.Member_ID
					 	 WHERE `Document_ID`='%s'";
		$query = mysql_query(sprintf($query_string, $this->id), $this->db);
		//print $query_string;
		if (!$query){
			print mysql_error($this->db);
		}
		$row = mysql_fetch_row($query,MYSQL_ASSOC);
		//print_r($row);
		foreach((array)$row as $key=>$value){
			$this->$key = $value; 
		}
	}
	
	/**
	 * 
	 * Loads the associated profiles into the document.
	 */
	private function loadProfiles(){
		$toReturn = array();
		$query_string = "SELECT ESDP.*, EM.Name 
						 FROM `EH_Security_Docs_Profiles` as ESDP
					 	 INNER JOIN `EH_Members` as EM
					 	 	ON EM.Member_ID = ESDP.Member_ID 
						 WHERE `Document_ID`='%s'";
		$query = mysql_query(sprintf($query_string, $this->id));
		if (!$query){
			print mysql_error($this->db);
		}
		while ($row = mysql_fetch_assoc($query)){
			$row["System_IP"] = (array)$this->loadIPs($row["Member_ID"]);
			array_push($toReturn, $row);
		}
		$this->Profiles = $toReturn;
		return $toReturn;
	}
	
	/**
	 * 
	 * Retrieves the IP information for the member, last 5 entries
	 * @param int $id is the Member ID to load IP info for
	 */
	private function loadIPs($id){
		$toReturn = array();
		$query_string = "SELECT EIT.*, EM.Name 
						 FROM `EH_IP_Tracker` as EIT
					 	 INNER JOIN `EH_Members` as EM
					 	 	ON  EM.Member_ID = EIT.Member_ID
						 WHERE EM.`Member_ID`='%s'
						 ORDER BY Track_ID DESC
						 LIMIT 0, 5";
		$query = mysql_query(sprintf($query_string, $id), $this->db);
		if (!$query){
			print mysql_error($this->db);
		}
		while($row = mysql_fetch_assoc($query, MYSQL_ASSOC)){
			array_push($toReturn, $row);
		}
		return $toReturn;
	}
	
	/**
	 * 
	 * This function loads Access control 
	 * @param int $id is the member ID
	 */
	private function loadAccess($id){
		$toReturn = array();
		$query_string = "SELECT * FROM `EH_IP_Access` 
						 WHERE `Member_ID` = '%s'";
		$query = mysql_query(sprintf($query_string,$this->Member_ID), $this->db);
		if (!$query){
			print mysql_error($this->db);
		}
		while($row = mysql_fetch_assoc($query)){
			array_push($toReturn, $row);
		}
	}
	
	/**
	 * 
	 * This function is used to return a "clean" version of the requested data member
	 * @param string $key
	 */
	public function get($key){
		return nl2br(stripslashes($this->$key));
	}
		
	/**
	 * 
	 * Used to update the document
	 * @param int $id is the Document ID to update
	 * @param array $args is an array of arguments that are attributes of the document
	 */
	public function update($args){
		$args = (array) $args;
		$query_string = "UPDATE `EH_Security_Docs` SET ";
		if (!array_key_exists("add_id", $args)){
			$args["add_id"] = array();
		} 
		foreach($args as $key=>$value){
			switch($key){
				case "add_profile":					
					break;
				case "add":
					break;
				case "new_member_id":					
					break;
				case "Reset":					
					break;
				case "Submit":					
					break;
				case "add_id":
					print_r($value);
					$this->updateProfiles($value);
					break;
				default:
					$query_string .= sprintf("`%s`='%s', ", 
									mysql_real_escape_string($key, $this->db), 
									mysql_real_escape_string($value, $this->db));
			}			
		}
		$query_string = substr($query_string, 0, strlen($query_string)-2);
		$query_string .= " WHERE `Document_ID`='".$this->id."'";
		$update = mysql_query($query_string);
	}
	
	/**
	 * 
	 * This function is used to update the profile listing for a document
	 * @param array $profiles
	 */
	private function updateProfiles($profiles){
		$status = true;
		$msg = "";
		$del_query = "DELETE FROM `EH_Security_Docs_Profiles` WHERE `Document_ID` = '%s' 
					  AND `Member_ID` NOT IN (%s)";
		$id_list = array();
		foreach ($profiles as $profile){
			array_push($id_list,"'".$profile."'");
		}
		$del_query = sprintf($del_query, $this->id, implode(",",$id_list));
		if (mysql_error($this->db)){
			$status = false;
			$msg .=" ".mysql_error($this->db);
		}
		$del = mysql_query($del_query);
		
		$ins_query = "INSERT INTO `EH_Security_Docs_Profiles` 
					  (`Document_ID`,`Member_ID`, `Date_Added`)
					  VALUES
					  ('%s','%s',NOW())
					  ";
		$sel_query = "SELECT count(*) as thecount FROM `EH_Security_Docs_Profiles` 
					  WHERE `Document_ID` = '%s' AND `Member_ID` = '%s'";
		foreach($profiles as $profile){
			$result = mysql_query(sprintf($sel_query,$this->id, $profile));
			$row = mysql_fetch_array($result);
			if ($row["thecount"] > 0){
				continue;
			}else{
				$insert = mysql_query(sprintf($ins_query,$this->id, $profile));
				if (mysql_error($this->db)){
					$status = false;
					$msg .=" ".mysql_error($this->db);
				}		
			}
		}
		$this->loadProfiles();
		return array("status"=>$status,"msg"=>$msg);
	}
	
	/**
	 * 
	 * Adds a new document to the database
	 * @param array $args
	 * Expected keys:
	 * Key Member_ID 
	 * Key Aliases
	 * Key Last_Location
	 * Key Last_IP
	 * Key Previous_IP
	 * Key Notes - String / notes
	 * Key add_id - array of member_ids 
	 */
	public function add($args){
		$submitter = $_SESSION["EHID"];
		
		$required = array("Member_ID"=>"Primary Profile");
		foreach($required as $required_key=>$label){
			if (!array_key_exists($required_key, $args)){
				return array("status"=>false,"msg"=>$label." must be specified!");
			}else if (empty($args[$required_key])){
				return array("status"=>false,"msg"=>$label." must be specified!");
			}
		}
		
		$member_id = mysql_real_escape_string($args["Member_ID"]);
		$aliases = mysql_real_escape_string($args["Aliases"]);
		$last_location = mysql_real_escape_string($args["Last_Location"]);
		$last_ip = mysql_real_escape_string($args["Last_IP"]);
		$previous_ip = mysql_real_escape_string($args["Previous_IP"]);
		$notes = mysql_real_escape_string($args["Notes"]);
		$associated_profiles = $args["add_id"];
		
		$query_string = sprintf("INSERT INTO `EH_Security_Docs`
						(`Member_ID`,`Submitter_ID`, `Date_Added`, `Aliases`, 
						 `Last_IP`,`Last_Location`,`Previous_IP`,`Notes`) 
						VALUES 
						('%d','%d',NOW(),'%s','%s','%s','%s','%s')", 
						$member_id, $submitter, $aliases, $last_ip,$last_location, 
						$previous_ip, $notes);

		$sel_query = sprintf("SELECT count(*) thecount FROM `EH_Security_Docs` 
					  		  WHERE `Member_ID`='%s'", $member_id);
		//print $sel_query;
		//print "<br />";
		$sel = mysql_fetch_array(mysql_query($sel_query,$this->db));
		$query = false;
		$error = "";
		if ($sel["thecount"] < 1){
			//print "<br />";
			//print $query_string;
			//print "<br />";
			$query = mysql_query($query_string, $this->db);
			if (!$query){
				$error = mysql_error($this->db);
			}else{
				$id = mysql_insert_id($this->db);
				$this->id = $id;
				$this->updateProfiles($associated_profiles);
			}
		}else{
			$error = "Document for this member already exists";
		}
		$this->loadDocument();
		if (!$query){
			return array("status"=>false,"msg"=>$error);
		}
		return array("status"=>true,"msg"=>"Document created successfully");
	}
}
