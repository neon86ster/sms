<?
/*
 * file for let customers reservation oasis booking pass website
 * @modified - 6-Oct-2009 by natt@tap10.net
 *           - change chiangmainoasis.com to oasisspa.net
 */
session_start();
/*
if($_SERVER["SERVER_NAME"]!=="sample.spaprogram.net"){
	die();
}*/
include("include/smseg.inc.php");
$root = $_SERVER["DOCUMENT_ROOT"];
require_once ("$root/include/config.php");

$smseg = new smseg();
$pagename = $_SERVER["PHP_SELF"];
$user = $smseg->getParameter("user");
$pass = $smseg->getParameter("pass");

if (isset ($_POST["Enter"])) {
		if ($smseg->login($user, $pass)) {
			header("location: online_book.php");
		}
}else{
	if ($smseg->online()){
		header("location: online_book.php");
	}
	
}
$companyname=$smseg->getCompanny();
$_SESSION["companyname"]=$companyname;

//----------To Set Member Password-------------------
/*
$sql="select * from m_membership";
$rs=$smseg->get_data($sql);

for($i=0;$i<$rs["rows"];$i++){
$sql_pw="UPDATE `m_membership` SET `member_pass` = '"."pw".$rs[$i]["member_code"]."' " .
		"WHERE `m_membership`.`member_id` ='".$rs[$i]["member_id"]."'";
$rs_pw=$smseg->set_data($sql_pw);	
}
*/
//----------------------------------------------------

?>
<html>
<head>
<title><?=$_SESSION["companyname"]?> Members Login</title>
<meta name="keywords" content="" />
<META NAME="Description" CONTENT="">
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
						<td colspan="2" align="center" class="b" height="25"><font color="#34698d">Member Log In </font></td>
					</tr>
					
					<?if($smseg->get_msg()){?>
					<tr>
						<td colspan="2" align="center" class="b" height="25"><font color="red"><?=$smseg->get_msg()?></font></td>
					</tr>	
					<?}?>
					
					<?if($_SESSION["email"]){$_SESSION["email"]="";?>
					<tr>
						<td colspan="2" align="center" class="b" height="25"><font color="green">Password is send to you email...</font></td>
					</tr>	
					<?}?>
					
					<tr align="center">
						<td align="left">Member Code</td>
						<td align="left"><input type="text" size="20" value="<?=$user?>" id="user" name="user" style="text-align:left;color:#000000;"></td>
					</tr>
					<tr align="center">
						<td align="left">Password</td>
						<td align="left"><input type="password" size="20" value="<?=$pass?>" id="pass" name="pass" style="text-align:left;color:#000000;"></td>
					</tr>
					
					<tr align="center">
						<td colspan ="2"><input type="submit" name="Enter" class="button" value="Enter" onClick=""></td>
					</tr>
					
					<tr align="right">
						<td colspan ="2"><a style="text-decoration: none" href="recover.php">Forgot your password?</a></td>
					</tr>        
                </table>
    </td>
  </tr>
 </table>

</form>
</body>
</html>