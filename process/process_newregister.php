<?php
include('../connect/connect.php');
include('../config/config.php');

	foreach($_POST as $key => $val)
	{
		$$key=addslashes(trim($val));
		$_SESSION['register'][$key]=$val;
	}

	
	$password = md5($password);
	$userId = $userId;
	
	$query = "UPDATE ".tbl_user." set password='".$password."' WHERE userId = '".$userId."'";	
	$insert = mysql_query($query);
	
	$insrt_points = mysql_query("insert into ".tbl_userpoints." set points = '50',userId = '".$userId."'");
	
	if($insert)
	{
		$get_user = mysql_fetch_array(mysql_query("SELECT email from ".tbl_user." where userId = '".$userId."'"));
		$email = $get_user['email'];
	
		$to = $email;
		$subject="[iftGift] Your iftGift Registration is Confirmed!";
		$from = "iftGift [Info@iftgift.com]";
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from. "\r\n";
		$headers .= "Content-type: text/html\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		
		//$message = "Thank you for registering with iftGift.com!"."<br /><br />";
		$activationlink = ru .'process/confirmemail.php?userId='.$userId;
		//$message .= "<a href='".$activationlink."'>".$activationlink."</a>";
		$message	= '<div style="width:630px;border:#c5c4c4 2px solid;  margin:0 auto;background-color:#f6f0fa; font-family:Arial, Helvetica, sans-serif; font-size:12.5px; color:#000;">
  <div style="border:#ede2f5 thin solid; ">
    <div style="height:764px;font-size:15px !important; line-height:0.95">
    <img src="'.ru_resource.'images/header-email-new.png" alt="iftgift" />
    <div style="padding-left: 13px; padding-top: 15px;">
      <div style="-moz-border-radius:6px;-webkit-border-radius:6px;background: #fff;margin-top: 20px;border: 1px solid #cccccc;width: 602px;padding-top: 15px;height: 632px;" >
      <center>
        <p style="font-weight: bold">Thank you for registering with <span style="font-weight:bold;color:#ff69ff">i</span><span style="font-weight:bold;color:#3399cc">f</span><span style="font-weight:bold;color:#ff9900">t</span>Gift.com! </p>
        <p></p>


        <p><a href="'.$activationlink.'" target="_blank"><img src="'.ru_resource.'images/btn_email_confirmation.png" /></a></p>
        <img src="'.ru_resource.'images/jester-emails-confirmation.png" />
      </center>
      </div>
      </div>
    </div>
  </div>
  <center>
      <p>&nbsp;</p>
      <table style="color:#726f6f;">
      <tr>
      <td><a href="#" style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">Home</a></td>
      <td>|</td>
      <td><a href="whatisiftgift.php"  style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">What is <span style="font-weight:bold;color:#ff69ff">i</span><span style="font-weight:bold;color:#3399cc">f</span><span style="font-weight:bold;color:#ff9900">t</span>Gift?</a></td>
      <td>|</td>
      <td><a href="#"  style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">Schedule of <span style="font-weight:bold;color:#ff69ff">i</span><span style="font-weight:bold;color:#3399cc">f</span><span style="font-weight:bold;color:#ff9900">t</span>Points</a></td>
      <td>|</td>
      <td><a href="#"  style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">FAQ</a></td>
      <td>|</td>
      <td><a href="#"  style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">Contact</a></td>
      <td>|</td>
      <td><a href="#"  style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">Terms</a></td>
      <td>|</td>
      <td><a href="#"  style="text-decoration:none; color: #726f6f;padding-left: 5px;padding-right: 5px;">Privacy</a></td>
     </tr>
      </table>
      <p style="color:#726f6f;">Protected by one or more the following US Patents and Patents Pending: 8,280,825 and 8,589,314.<br />
        Copyright � 2011, 2012, 2013 Morris Fritz Friedman � All Rights Reserved � iftGift</p>
      <p style="color:#726f6f;">This message was sent from a notification-only email address that does not accept incoming email. <br />
        Please do not reply to this message.</p>
      <p><a style="color: #726f6f;font-weight: bold; text-decoration: none" href="http://www.iftGift.com" target="_blank">www.iftGift.com</a></p>
  </center>
  <div style=" height:250px;"></div>
</div>';
		$mailsent = mail($to,$subject,$message,$headers);
		header("location:".ru."thankyou");
	}
?>