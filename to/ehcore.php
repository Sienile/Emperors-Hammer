<?php include("header.php"); ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td height="313" class="main">
        <?php
        $errors = array();
        if ($_POST["Submit"]){
            // Make sure we have everything we want!
            foreach(array_keys($_POST) as $key){
	            // Loop through the post vars, make sure none of them are empty
	            if ($key != "found" && $key != "groups"){
		            if (!isset($_POST[$key]) || trim(strip_tags($_POST[$key])) == ""){
			            array_push($errors, $key);
		            }
	            }
            }

			if (isset($_POST["found"])){
				if (array_sum($_POST["found"]) != 4){
					// Someone didnt check that they completed all of their stuff!
					array_push($errors, "found");
				}
			}else{ array_push($errors, "found"); }

            if (count($_POST["groups"]) < 1){
	            // Someone hasnt chosen a group!
	            array_push($errors, "groups");
            }
        }
        if (count($errors) < 1 && $_POST["Submit"])
        {
            $message =  "ID: " . $_POST['callsign'] . "\n" . "\n";
            $message .= "Age: " . $_POST['age'] . "\n" . "\n";
            $message .= "Email Adress: " . $_POST['email'] . "\n" . "\n";
            $message .= "Full Name: " . $_POST['name'] . "\n" . "\n";
            $message .= "Gender: " . $_POST['gender'] . "\n" . "\n";
            $message .= "Recruiter: " . $_POST['recruiter'] . "\n" . "\n";

            $message .= "I have read and understand the Emperor's Hammer Training Manual" . "\n";
            $message .= "Answer: Yes\n";

            $message .= "I have found and reviewed the governing texts that bind all EH Members" . "\n";
            $message .= "Answer: Yes\n";

            $message .= "Group(s) I am interested in: " . implode(",",$_POST['groups']) . "\n" . "\n" ;

            $to  = $TO_Name.' <'.$TO_Address .'>, ';
            $to .= $_POST['callsign'].' <'.$_POST['email'].'>';
            $subject = 'EH CORE: ' . $_POST['callsign'];
            $headers = 'From: Office of the EH-TO <'. $_POST['email'] .">\r\n" .
            'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
            echo("<span align=\"center\">Thank you for you joining!</span>");
        }else{

        ?>


		<h2 align="center">Enter your information to join<br /> the Emperor's Hammer!</h2>
		<hr />
        <p><em>Note: You must be 13 years of age or older,
		have parental/guardian permission, <br />or provide a valid parental/guardian e-mail address to join.</em></p>

          <h4>Identification:</h4>
        <form name="contact" method="post" action="">
          <table width="100%" border="0">
              <tr>
	            <td colspan="2" width="25%" align="left">Persona/Nickname/Callsign:</td>
	            <td colspan="4" align="left">
		            <input name="callsign" type="text" id="callsign"
				            value="<?=(isset($_POST["callsign"]))? $_POST["callsign"] : "" ?>" size="50" />
		            <?=(in_array('callsign',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
	            </td>
              </tr>
              <tr>
	            <td colspan="2" align="left">E-Mail address:</td>
	            <td colspan="4" align="left">
		            <input name="email" type="text" id="email"
				            value="<?=(isset($_POST["email"]))? $_POST["email"] : "" ?>" size="50" />
		            <?=(in_array('email',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
	            </td>
              </tr>
              <tr>
	            <td colspan="2" align="left">Name:</td>
	            <td colspan="4" align="left">
		            <input name="name" type="text" id="name"
			            value="<?=(isset($_POST["name"]))? $_POST["name"] : "" ?>" size="50" />
		            <?=(in_array('name',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
	            </td>
              </tr>
              <tr>
	            <td width="5%" style="text-align:right">Age:</td>
	            <td width="5%" style="text-align:left">
		            <input name="age" type="text" id="age"
				            value="<?=(isset($_POST["age"]))? $_POST["age"] : "" ?>" size="3" />
		            <?=(in_array('age',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
	            </td>
	            <td width="25%" style="text-align:right">Recruited by:</td>
	            <td width="15%" style="text-align:left">
		            <input name="recruiter" type="text" id="recruiter"
				            value="<?=(isset($_POST["recruiter"]))? $_POST["recruiter"] : "" ?>" size="15" />
		            <?=(in_array('recruiter',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
	            </td>
	            <td width="15%" style="text-align:right">Gender:</td>
	            <td width="30%" style="text-align:left">
		            <?php
		            $male = ($_POST["gender"] == "male" || !isset($_POST["gender"]))? "checked=\"checked\"" : "" ;
		            $female = ($_POST["gender"] == "female") ? "checked=\"checked\"" : "";
		            ?>
		            <input type="radio" name="gender" id="gender_male" value="male" <?=$male?> />
		            <label for="gender_male"> Male</label><br />
		            <input type="radio" name="gender" id="gender_fem" value="female" <?=$female?> />
		            <label for="gender_fem"> Female</label>
		            <?=(in_array('gender',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
	            </td>
              </tr>

            </table>

            <p>I have completed the following:<br />
	            <?php $read_tm = ($_POST["read_tm"] == 1)? "checked=\"checked\"" : ""; ?>
	            <input type="checkbox" name="read_tm" id="read_tm" value="1" <?=$read_tm?> />
	            <label for="read_tm">Read and understand the Emperor's Hammer Training Manual</label>
	            <?=(in_array('read_tm',$errors)) ? "<br /><span style='color: red'>Please fill in this required field</span>" : ""?>
            </p>

            <p>I know where to find:<br />
            <?=(in_array('found',$errors)) ? "<span style='color: red'>You must complete All steps below!</span>" : ""?>
		            <input type="checkbox" name="found[]" value="1" id="found_eh" />
		            <label for="found_eh">The Emperor's Hammer Main page</label><br />
		            <input type="checkbox" name="found[]" value="1" id="found_blw" />
		            <label for="found_blw">The By Laws</label><br />
		            <input type="checkbox" name="found[]" value="1" id="found_coc" />
		            <label for="found_coc">The IRC Code of Conduct</label><br />
		            <input type="checkbox" name="found[]" value="1" id="found_aow" />
		            <label for="found_aow">The Articles of War</label></p>

            <p>Which Group(s) of the Emperor's Hammer you wish to be part of?<br />
	            <?=(in_array('groups',$errors)) ? "<span style='color: red'>You must choose at least one group to continue</span>" : ""?>
	            <?php
		            $tc = (isset($_POST["groups"]) && in_array("TIE Corps",$_POST["groups"])) ? "checked=\"checked\" ": "";
		            $db = (isset($_POST["groups"]) && in_array("Dark Brotherhood",$_POST["groups"])) ? "checked=\"checked\" ": "";
		            $hf = (isset($_POST["groups"]) && in_array("Hammers Fist",$_POST["groups"])) ? "checked=\"checked\" ": "";
		            $dir = (isset($_POST["groups"]) && in_array("Directorate",$_POST["groups"])) ? "checked=\"checked\" ": "";
		            $fr = (isset($_POST["groups"]) && in_array("Fringe",$_POST["groups"])) ? "checked=\"checked\" ": "";
	            ?>
		            <input type="checkbox" name="groups[]" value="TIE Corps" id="group_tc" <?=$tc?> />
		            <label for="group_tc">The TIE Corps</label><br />
		            <input type="checkbox" name="groups[]" value="Dark Brotherhood" id="group_db" <?=$db?> />
		            <label for="group_db">The Dark Brotherhood</label><br />
		            <input type="checkbox" name="groups[]" value="Hammers Fist" id="group_hf" <?=$hf?> />
		            <label for="group_hf">The Hammer's Fist</label><br />
		            <input type="checkbox" name="groups[]" value="Directorate" id="group_dir" <?=$dir?> />
		            <label for="group_dir">The Directorate</label><br />
		            <input type="checkbox" name="groups[]" value="Fringe" id="group_fr" <?=$fr?> />
		            <label for="group_fr">The Fringe</label></p>
          <p><strong>Please allow two (2) days for processing your information. If
          you have not received a reply within two (2) days, contact the Training Officer at
          <?=$TO_Address?> with the subject "Join the EH"</strong></p>

          <input type="submit" value="Submit" name="Submit" />
          <input type="reset" value="Clear" name="Reset" />
          </form>
        <?
            }
        ?>
         </td>
    </tr>

</table>

<?php include("footer.php"); ?>
