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
require_once "./includes/trunk_config.php";
require_once "./includes/gatewayfunction.php";
require_once "./includes/sheduling.php";

$time = getdate();
$time = $time['weekday'] . " " . $time['month'] . " " . $time['year'] . " at " . $time['hours'].":".$time['minutes'] . ":" . $time['seconds'];
file_put_contents("crondebug/scron.txt","scron.php last run on " . $time . "\n");


$conn =@mysql_connect(HOST,USER,PASS) or die("Skywalkers Nig: Cannot Connect To the Database Server");
	@mysql_select_db(DB,$conn) or die ("Skywalkers Nig: Cannot Select Database Please Try Again later");

$sql = "SELECT * FROM TRUNKsheduleSingle WHERE sent = 0";
$num = 0;
$result = @mysql_query($sql);
$num = @mysql_num_rows($result);

if($num == 0 ){
print "no message in quee";
exit(1);
}

$gotMatch = 0;
					while($row = mysql_fetch_array($result)){
					$phoneNo = $row['phoneNo'];
					$mesg = $row['mesg'];
					$mesgid = $row['id'];
					$smsName =  $row['smsName'];
					$to = $row['tophone'];
					
					$time = $row['times']; //this should convert it to days
					$now = time();
					print "current time:" . $now . " future time:" . $time;
						if($now < $time){
						print "not now";
						}else{
						$gotMatch = 1;
						break; //one transaction at a time
						}
					
					}
					//die("<br/>die now");
		
		if($gotMatch == 1){
		print "now";
		}else{
		exit(1);
		}
					

	$phoneNumber = $to;
	
	$smsname = $smsName;
	
	
	
	$sql = "SELECT * FROM TRUNKregistration WHERE phoneNo = $phoneNo";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$units = $row['SMSunits'];
	$cronorg = $row['org'];
	
	
		if($units > 1 || $units == 1){
		sendSMS($phoneNumber,$smsname,$mesg);
		}else{
		$_SESSION['CRONmessage'] .= "<p>Insuficient SMS credit! Please Recharge your Account. Your Balance is $units</p><br/>";	
		}
	

$sql = "UPDATE TRUNKsheduleSingle SET `statusMessages` = '{$_SESSION['CRONmessage']}', `sent` = '1' WHERE `id` = '$mesgid' ";

mysql_query($sql);

unset($_SESSION['CRONmessage']);


function sendSMS($phone,$smsname,$smsMessage){ //a recursive function that sends more that one pages to one user
global $conn, $units, $cronorg,$phoneNo;


$message = substr($smsMessage, 0, 160);//send 1 message


	//$sql = "INSERT INTO sent (`id`, `org`, `toNum`, `fromNa`, `date`, `sent`) VALUES (NULL, '{$_SESSION['name']}', '$phone', '$smsname', NOW(), '1')";
	
	//$result = mysql_query($sql);
	
	$result = CRONsendToTrunkSMS($smsname,$phone,$smsMessage); //returns bool
	
		if($result){ //this section represents clickAtel
		$units = $units - 1;
$sql = "UPDATE TRUNKregistration SET `SMSunits` = '$units' WHERE `TRUNKregistration`.`phoneNo` = '$phoneNo' ";
if(!mysql_query($sql)){
$_SESSION['CRONmessage'] .= "<p>Unable to process your account information Please contact Us</p><br/>";
}
		$_SESSION['CRONmessage'] .= "<p>Sending SMS to " . urldecode($phone) . " " . "Was a Success" . "</p>"; //session based log
		$sentStatus = 1;
		}else{
		//log error message. Cannot connect to www.trunksms.com
		$_SESSION['CRONmessage'] .= "<p>Sending SMS to " . $phone . " Failed </p><br/>";	
		$sentStatus = 0;
		}
		
		////////////////////////////////////////////////////////////////////////
		$status_msg = htmlentities($_SESSION['CRONmessage']);

		$sql = "INSERT INTO TRUNKsent (`id`, `org`, `phoneNo`, `toNum`, `mesg`, `fromNa`, `units`, `statusMsg`, `date`, `sent`) VALUES (NULL, '$cronorg', '$phoneNo', '$phone', '$smsMessage', '$smsname', '$units', '$status_msg', NOW(), '$sentStatus')";

	
		$result = @mysql_query($sql);
		/////////////////////////////////////////////////////////////////
		$nextMesg = trim(str_replace($message, "", $smsMessage));
		$count = strlen($nextMesg);
		if($count > 0 && $units > 0){
		sendSMS($phone,$smsname,$nextMesg); //a recursive call
		}
		
		unset($result,$count,$smsMessage,$message);
}//endFunc 


?>