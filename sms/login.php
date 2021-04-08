<?
session_start();
$root = $_SERVER["DOCUMENT_ROOT"];
require_once ("$root/include/config.php");
require_once ("$root/include/secure.inc.php");
require_once ("$root/include/protect.inc.php");

$object = new secure();
$pagename = "login.php";
$object->setDebugStatus(false);
/*
$deny_login = Services::bruteCheck();
if($deny_login) {
	$object->setErrorMsg("Login locked. Try again in 15 minutes.");
}
*/
if ($object->checkLogin()) {
if(!isset($bookid)){$bookid="";}
if(!isset($pdsid)){$pdsid="";}
if(!isset($rmid)){$rmid="";}
?>
	<script language="javascript">
		alert("Another user is already logged on to SMS....");
		window.location = "home.php";
	</script>
	<?

} else {
	$bookid = $object->getParameter("bookid", 0);
	$pdsid = $object->getParameter("pdsid", 0);
	$rmid = $object->getParameter("rmid", 0);

	// force other user from other location log out.
	$force = $object->getParameter("force", 0);
	if ($force == 1) {
		$_SESSION["__user_id"] = $_SESSION["checkUser"];
		$object->setUser("logout", $_SESSION["checkUser"]);
		unset ($_SESSION["checkUser"]);
	}

	if (isset ($_POST["go"]) || $force == 1) {
		$user = $object->getParameter("user");
		$pass = $object->getParameter("pass");
		$user = mysql_escape_string($user);
		$pass = mysql_escape_string($pass);
		
		$deny_login = Services::bruteCheck(false,$user);
		if($deny_login){
		$object->setErrorMsg("Username locked...<br>Try again in 10 minutes.");
		}
		if(!$deny_login){
			if ($object->login($user, $pass)) {
				if ($bookid > 0) {
					header("location:/appt/manage_booking.php?bookid=$bookid&newLogin=1");
				} else
					if ($pdsid > 0) {
						header("location:/appt/manage_pdforsale.php?pdsid=$pdsid&newLogin=1");
					} else
						if ($rmid > 0) {
							header("location:/appt/manage_mroom.php?rmid=$rmid&newLogin=1");
						} else {
							header("location:home.php");
						}
			}else{
				Services::bruteCheck(true,$user);
				///////Lock Login more than 5 times
				/*if($deny_login) {
				   $object->setErrorMsg("Login locked. Try again in 15 minutes.");
				}else{
					   Services::bruteCheck(true);
				}*/
				///////
			}
		}
	}
}
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png"/>
<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-precomposed.png"/>
<title>Login</title>
<link href='css/styles.css' rel='stylesheet' type='text/css' />
<?
  $isiPad = strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
  	if ($isiPad == true){
  		$isiPad='ipad';
  	?>
  	<style>
  	<!--
	@media only screen and (device-width: 768px) {
	  /* For general iPad layouts */
	}
	
	@media only screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait) {
	  /* For portrait layouts only */
	}
	
	@media only screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape) {
	  /* For landscape layouts only */
	}
  	-->
  	</style>
  	<?
  	}
  $browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    if ($browser == true){
    	$browser = 'iphone';
  	?>
  	<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=1.0" />
  	<?
  	}
?>
</head>
<body class="login">
<form action='<?=$pagename?>' method='post' width='100%' name='login'>
<table border="0" width="100%" height="100%">
  <tr>
    <td valign="top" align="center" width="100%"><br/><br/><br/>
    	<img src="images/login/SMSlogo.gif"/><table border="0" align="center">
                              <tr>
                                <td align="center"><img src="images/login/login.gif"/></td>
                              </tr>
							  <tr>
                                <td><img src="images/login/username.gif"/></td>
                              </tr>
                              <tr>
                                <td><input type='text' name='user' size='30' value='<?=$object->getParameter("user")?>'/>
                                <input type='hidden' id='bookid' name='bookid' value='<?=$bookid?>'/>
                                <input type='hidden' id='rmid' name='rmid' value='<?=$rmid?>'/>
                                <input type='hidden' id='pdsid' name='pdsid' value='<?=$pdsid?>'/>
                                <input type='hidden' id='force' name='force' value='0'/>
                                </td>
                              </tr>
                              <tr>
                                <td><img src="images/login/password.gif"/></td>
                              </tr>
                              <tr>
                                <td><input type='password' name='pass' size='30' autocomplete="off" value='<?=$object->getParameter("pass")?>'></td>
                              </tr>
                              <tr>
                                <td align="center"><b><?=$object->getErrorMsg()?></b>&nbsp;</td>
                              </tr>
                              <tr>
                                <td align="center"><input type='submit' name='go' value=' ' class="lbutton"/></td>
                              </tr>
                          </table>
    </td>
  </tr>
</table>
</form>
</body>
<?if(isset($_SESSION["checkUser"])){?>
<script type="text/javascript">
	if(confirm('User Account <?=$object->getParameter("user")?> is already sign in, Do you want to log them out from the other location?')==true){
		document.getElementById("force").value=1;
		document.login.submit();
	}
</script>
<?}?>