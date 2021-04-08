<?
/*
 * file for let customers reservation oasis booking pass website
 * @modified - 6-Oct-2009 by natt@tap10.net
 *           - change chiangmainoasis.com to oasisspa.net
 */
session_start();
include("include/smseg.inc.php");
include_once("class.phpmailer.php");

$smseg = new smseg();

$pagename = $_SERVER["PHP_SELF"];
$user = $smseg->getParameter("user");

function sendToCustomer($post) {
	$html = array();
	$html["body"] = "<body>".
			"<table width='600' cellpadding='2' cellspacing='0' border='0' style='font-family:tahoma; font-size:12px;'>".
				"<tr>".
					"<td colspan='2'><div align='left'><img src='http://www.oasisspa.net/images/zheader.gif' width='664' height='67'></div></td>".
				"</tr>".
				"<tr>".
					"<td colspan='2'><b>Dear ".$post[0]["fname"]." ".$post[0]["mname"]." ".$post[0]["lname"].",</b><br>&nbsp;</td>".
				"</tr>".
				"<tr>".
					"<td colspan='2'><b>Greetings from Oasis Spa! </b></td>".
				"</tr>".
				
				"<tr>".
					"<td colspan='2'><b>We are delighted to receive your information from our website:</b></td>".
				"</tr>";
					
	$html["body"] .="<tr>".
				"<td colspan='2' height='30' align='left' valign='bottom'>" .
					"<br><b>" .
					"Member Code: ".$post[0]["member_code"]."<br>Password: ".$post[0]["member_pass"]."<br><br>We look forward to pampering you soon.</td>".
				"</tr>".	
				
				"<tr>".
					"<td colspan='2'><div align='left'><img src='http://www.oasisspa.net/images/zfooter.gif' width='664' height='156'></div></td>".
				"</tr>";
	$html["body"] .= "</table>".
			"</body>";
		$html["name"] = $post[0]["fname"]." ".$post[0]["mname"]." ".$post[0]["lname"];
		$html["email"] = $post[0]["email"];
		//$html["email"] = "david@tap10.com";
		
	return $html;
	}
	
if (isset ($_POST["Send"])) {
		
		$sql_u = "select * from m_membership where member_code = '$user' ";
		$rs_id=$smseg->get_data($sql_u);
			if($rs_id["rows"] < 1) {
				$error="Member not found...";
			}
		if($rs_id){
			$_SESSION["email"]=true;
			$mail = new PHPMailer();
			$csEmail = "cs@oasisspa.net";

			$html = "";
			$html = sendToCustomer($rs_id);		
			$mail->From		= $csEmail;
			$mail->FromName = "Oasis Spa Customer Service";
			$mail->CharSet    = "utf-8";
			
			//$mail->Subject    = "Your order from website ".$_SERVER['SERVER_NAME']." ";			
			$mail->Subject    = "Oasis Spa website reservation.";			
			
			$mail->MsgHTML($html["body"]);
			$mail->AddAddress($html["email"], $html["name"]);
			
			$mail->Send();
			if($mail){
				header("location: login.php");
			}
		}
}else{
	if ($smseg->online()){
		header("location: online_book.php");
	}
}

?>
<html>
<head>
<title><?=$_SESSION["companyname"]?> Recover</title>
<meta name="keywords" content="spa,oasis,Bangkok,massage,treatments,body,complete,beauty,mai,treatment,guests,chiang,home,air,allow,senses,hand,customer,comment,scrubs,perfect,picked,specialists,tranquility,menu,genuine,ancient,operate,pleasure, Bangkokoasis" />

<META NAME="Description" CONTENT="">
<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
<link rel="icon" href="/images/favicon.ico" type="image/x-icon" />

<link href='style.css' rel='stylesheet' type='text/css' />
</head>
<body class="login">
<form action="<?=$pagename?>" method="post" name="memberlogin" id="memberlogin" enctype="multipart/form-data">
 
 <table border="0" width="100%" height="100%" style="margin-top:-20px">
  <tr>
    <td valign="center" align="center" width="100%"><br/><br/><br/>
    	<!--<img src="../images/login/SMSlogo.gif"/>-->
    			<table width="250" cellpadding="2" cellspacing="0" align="center">
					
					<tr>
						<td colspan="2" align="center" height="auto"><img src="viewPicture.php?name=company_logo" width="auto" height="auto" border="0"></td>
					</tr>
					
					<tr>
						<td colspan="2" align="center" class="b" height="25"><font color="#34698d">Enter your Member Code </font></td>
					</tr>
					
					<?if($error){?>
					<tr>
						<td colspan="2" align="center" class="b" height="25"><font color="red"><?=$error?></font></td>
					</tr>	
					<?}?>
					
					<tr align="center">
						<td width="40%" align="left">Member Code</td>
						<td width="40%" align="left"><input type="text" size="25" value="<?=$user?>" id="user" name="user" style="text-align:left;color:#000000;"></td>
					</tr>
			
					<tr align="center">
						<td colspan ="2"><input type="submit" name="Send" class="button" value="Send" onClick=""></td>
					</tr>
                </table>
    </td>
  </tr>
 </table>
 
</form>
</body>
</html>