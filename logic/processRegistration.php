<?php
/* Have you got Christ?
 * TrunkSMS GPL project www.trunksms.com.
 * 
 * @author  Daser Solomon Sunday songofsongs2k5@gmail.com,  daser@trunksms.com
 * @version 0.1
 * @License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Library General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor Boston, MA 02110-1301,  USA
 */
session_start();
if(strlen($_SESSION['phoneNo']) == 0){
session_destroy();
require_once "../includes/trunk_config.php";
require_once "../includes/gatewayfunction.php";
include "misc_function.php";
$SMSUNIT = FREESMSUNIT; //FREE SMS FOR FIRST TIME TRUNKERS
	$conn =@mysql_connect(HOST,USER,PASS) or die("Skywalkers Nig: Cannot Connect To the Database Server");
	@mysql_select_db(DB,$conn) or die ("Skywalkers Nig: Cannot Select Database Please Try Again later");
	
	if(isset($_REQUEST['submitReg'])){
	
	$org = ucwords(strtolower(trim(addslashes($_REQUEST['name']))));
	$phoneNo  = trim(addslashes($_REQUEST['phone']));
	$address = trim(addslashes($_REQUEST['address']));
	$email  = trim(addslashes($_REQUEST['email']));
	$password  = trim(addslashes($_REQUEST['password']));
	$password2  = trim(addslashes($_REQUEST['password2']));
	$password2  = trim(addslashes($_REQUEST['password2']));
	$countryCode = trim(addslashes($_REQUEST['country']));
	//die($countryCode);
	$how = trim(addslashes($_REQUEST['how']));
	
	$error = null;
	$sql = "select * from TRUNKregistration WHERE org = '$org' ";
	$result = @mysql_query($sql) or die("kjhsdjshdjs" . __LINE__);
	$num = 0;
	$num = @mysql_num_rows($result);
	if($num == 1){
	$error .= "The Organization/name provided is in use<br/>";
	}
	
	@mysql_free_result($result);
	
	$sql = "select * from TRUNKregistration WHERE phoneNo = '$phoneNo' ";
	$result = @mysql_query($sql) or die("blah blah" . __LINE__);
	$num = 0;
	$num = @mysql_num_rows($result);
	if($num == 1){
	$error .= "The Phone Number provided is in use<br/>";
	}
	@mysql_free_result($result);
	
	$sql = "select * from TRUNKregistration WHERE email = '$email' ";
	$result = @mysql_query($sql) or die("knskdnksd" . __LINE__);
	$num = 0;
	$num = @mysql_num_rows($result);
	if($num == 1){
	$error .= "The Email Address provided is in use<br/>";
	}
	@mysql_free_result($result);
	
	
	
	if($password != $password2){
	$error = $error . "The Passwords provided did not match<br/>";
	}
	
	if(strlen($password) > 255 || strlen($password2) > 255){
	$error.= "the password is to long";
	}
	
	if(strlen($password) == 0 || strlen($password2) == 0){
	$error = $error . "Password cannot be empty<br/>";
	}
	
	if(strlen($address) == 0 || strlen($address) > 255){
	$error = $error . "Address cannot be empty<br/>";
	}
	
	if(strlen($countryCode) == 0 || strlen($countryCode) > 255){
	$error = $error . "Country field is compulsory<br/>";
	}
	
	if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)){
	$error = $error . "Invalid Email<br/>";
	}
	
	if(strlen($email) == 0 || strlen($email) > 255){
	$error = $error . "Email cannot be empty<br/>";
	}
	
	if(strlen($phoneNo) == 0 || strlen($phoneNo) > 255){
	$error = $error . "Phone Number cannot be empty<br/>";
	}
	
	if(strlen($how) > 255){
	$error = $error . "How you got to know us entry is to much<br/>";
	}
	
	
		if($error == null){
		//die("control got here about to enter");
		//insert record and send smscode to $phoneNo and emailcode to $email
		//first generate email code and phonecode before insertion
				for($x=1;$x<=10;$x++){
				$phoneCode[] = rand(0,9);
				}
				for($x=1;$x<=10;$x++){
				$emailCode[] = rand(0,9);
				}
				$phoneCode = "TSMS" . implode("", $phoneCode);
				$emailCode = "TSMS" . implode("", $emailCode);
$sql = "INSERT INTO TRUNKregistration (`phoneNo`, `org`, `password`, `email`, `activated`, `emailCode`, `phoneCode`, `countryCode`, `address`, `how`, `AccountNo`, `SMSunits`) VALUES ('$phoneNo', '$org', '$password', '$email', '0', '$emailCode', '$phoneCode', '$countryCode','$address', '$how', '0', '0')";
		$result = @mysql_query($sql) or die( __LINE__ . mysql_error());
			if($result){
			//free to send sms code and email code to the newly registerd user so they could activate thier account and have free sms
				if(sendMail($email, $emailCode, $org) && sendSMS($countryCode,$phoneNo, $phoneCode)){
				//if(1){//if online just comment this if(1) and uncoment the previous line. thats all
				echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"; 
				echo "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>";
				echo "Your account as been created but not activated. An Email containing your Email Code and And an SMS containing your SMS codes has been sent to your email address and phone respectively. </br>If you dont receive the SMS it means your Phone Number is not surported. check the coverage section for the list of surported Network otherwise contact us. Final Step To Start Sending SMS. Please activate your account using these codes Now or later from the home page.</p>";
				echo "</div></div>";
				/*
				echo "<div id = \"success\">";
				echo "Your account as been created but no activated. An Email containing your Email Code and And an SMS containing your SMS codes has been sent to your email address and phone respectively. </br>If you dont receive the SMS it means your Phone Number is not surported. check the coverage section for the list of surported Network otherwise contact us. Final Step To Start Sending SMS. Please activate your account using these codes Now or later from the home page.";
				echo "</div> <!-- end success -->";*/
				echo "<div id = \"activation\">";
				getActivationForm();
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "<div><!-- end activation-->";
				}else{
				echo "<div class=\"ui-widget\">";
echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong>Registered but Unable to send either SMS or Email. Please contact us for more information about this problem.</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
	/*			
				echo "<div id = \"failure\">";
				echo "Unable to send either SMS or Email";
				echo "</div> <!-- end failure -->";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";*/
				}
				
			}else{
			echo "<div class=\"ui-widget\">";
echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> The phone number may exist.</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
				/*
			echo "<div id = \"failure\">";
			echo "you cannot insert probably the phone number exists";
			echo "</div> <!-- end failure -->";
			echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";*/
			}
		}else{
				echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> $error</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
				/*
		echo "<div id = \"failure\">";
		echo $error;
		echo "</div> <!-- end failure -->";
		echo "<div class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";*/
		}
	
	
	
	
	}else
	if(isset($_REQUEST['submitAct'], $_REQUEST['phoneNo'], $_REQUEST['password'], $_REQUEST['EmailCode'])){
	$phoneNo = addslashes(trim($_REQUEST['phoneNo']));
	$password = addslashes(trim($_REQUEST['password']));
	$phoneCode = addslashes(trim($_REQUEST['phoneCode']));
	$emailCode = addslashes(trim($_REQUEST['EmailCode']));
	if(strlen($phoneNo) < 255 && strlen($password) < 255 && strlen($phoneCode) < 255 && strlen($emailCode) < 255){
	$sql = "SELECT * FROM TRUNKregistration WHERE phoneNo = '$phoneNo' AND password = '$password' AND phoneCode = '$phoneCode' AND emailCode = '$emailCode' AND activated = 1";
	$result = @mysql_query($sql);
	$num = 0; 
	$num = @mysql_num_rows($result);
	if($num == 1){
	echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> Your Account is active. <p>Do login to enjoy our services.</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
/*
	echo "<div id = \"failure\">";
		echo "Your Account is active. <p>Do login to enjoy our services</p>";
		echo "</div> <!-- end failure -->";
		echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";*/
	}else{
	mysql_free_result($result);
	$sql = "SELECT * FROM TRUNKregistration WHERE phoneNo = '$phoneNo' AND password = '$password' AND phoneCode = '$phoneCode' AND emailCode = '$emailCode' ";
	$result = @mysql_query($sql);
	$num = 0;
	$num = @mysql_num_rows($result);
	@mysql_free_result($result);
		if($num == 1){
		//activate account here + generate account number $accountNo
		////////////////acct number generation
			for($x=1;$x<=10;$x++){
				$accountNoGen[] = rand(0,9);
				}
				$accountNo = "ACCT" . implode("", $accountNoGen);
		$sql = "UPDATE TRUNKregistration SET `activated` = '1', `AccountNo` = '$accountNo', `SMSunits` = '$SMSUNIT' WHERE `TRUNKregistration`.`phoneNo` = '$phoneNo' ";
		$result = @mysql_query($sql);
			if($result){
			$sql = "select * from TRUNKregistration WHERE phoneNo = '$phoneNo' ";
			$result = @mysql_query($sql);
				while($row = @mysql_fetch_array($result)){
				$email = $row['email']; //get the users email with his $phone number here to send his acctnumber
				$named = $row['org']; //of cause you have to congratulate with a name
				}
				@mysql_free_result($result);
			//email user his account number and notify the user of such
			emailAccountNo($email, $accountNo,$named);
			
			echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"; 
				echo "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>";
				echo "Congratulations! Your Account has been Activated. And Your Account number has been sent to your E-mail address. You use your account number to purchase or transfer SMS units.<p> Thanks For choosing TrunkSMS</p>Enjoy!!!</p>";
				echo "</div></div>";
				/*
			echo "<div id = \"success\">";	
			echo "Congratulations! Your Account has been Activated. And Your Account number has been sent to your E-mail address. You use your account number to purchase or transfer SMS units.<p> Thanks For choosing TrunkSMS</p>Enjoy!!!";
			echo "</div> <!-- end success -->";*/
			//start session
			echo "<div><form action = \"./MyPage/\" method = \"REQUEST\" onSubmit =\"authenticateUser(this); return false;\">";
			echo "<input type = \"hidden\" name = \"username\" value = \"$phoneNo\"><input type = \"hidden\" name = \"password\" value = \"$password\">";
			echo "<input style = \"background-color: #FFFFFF; border: 0px; color: #006633;\" type = \"submit\" value = \"Continue..\" name = \"submit\"></form></div>";
			}else{
			echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> There was a problem processing your request. Please Email us for assistance or check our help section for guidiance. Thank you.</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
				/*
			echo "<div id = \"failure\">";
			echo "There was a problem processing your request. Please Email us for assistance or check our help section for guidiance. Thank you";
			echo "</div> <!-- end failure -->";
			echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</div>";*/
			}
	
		}else{
		echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> The Provided Credential does not exist in our Database.</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
				/*
		echo "<div id = \"failure\">";
		echo "The Provided Credential does not exist in our Database";
		echo "</div> <!-- end failure -->";
		echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
	*/
		}	
	
   }
	}else{
	echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> You Are trying to compromise our Database Integrity!.</p>";
				echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";
				echo "</div></div>";
				/*
	echo "<div id = \"failure\">";
		echo "You Are trying to compromise our Database Integrity!";
	echo "</div> <!-- end failure -->";
echo "<div class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";*/
	}
	
	}else{//end isset
	//die("control got here abou");
		echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> Please! All entries are required, Thank You.</p>";
					echo "<div><a style = \"text-decoration: none;\" href = \"./\">Click here</a></div>";
				echo "</div></div>";

/*
	echo "<div id = \"failure\">";
	echo "Please! All entries are required, Thank You.";
	echo "</div> <!-- end failure -->";
	echo "<div><a style = \"text-decoration: none;\" href = \"./\">Click here</a></div>";*/
	}
	
}else{

echo "<div class=\"ui-widget\">";
				echo "<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em; margin-top: 20px; \">";
				echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"; 
				echo "<strong>Alert:</strong> Another user with the username {$_SESSION['name']} is in already. click <a href = \"./\">here</a>.</p>";
					echo "<div><a style = \"text-decoration: none;\" href = \"./\">Click here</a></div>";
				echo "</div></div>";
	/*			
echo "<div id = \"failure\">";
	echo "Another user with the username {$_SESSION['name']} is in already. click <a href = \"./\">here</a> ";
	echo "</div> <!-- end failure -->";
	echo "<div  class = \"back\"><a style = \"text-decoration: none;\" href = \"./\">back</a></div>";*/
}
?>
