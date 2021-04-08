<?
session_start();
$root = $_SERVER["DOCUMENT_ROOT"];
require_once ("$root/administration/include/secure.inc.php");

$object = new secure();
$pagename = "login.php";
$object->setDebugStatus(false);

if ($object->checkLogin()) {
?>
	<script language="javascript">
		window.location = "home.php";
	</script>
<?
} else {
	if (isset ($_POST["go"])) {
		$user = $object->getParameter("user");
		$pass = $object->getParameter("pass");
		if ($object->login($user, $pass)) {
			header("location:home.php");
		}
	}
}
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Login</title>
<link href='/css/styles.css' rel='stylesheet' type='text/css' />
<body class="login">
<form action='<?=$pagename?>' method='post' width='100%' name='login'>
<table border="0" width="100%" height="100%">
  <tr>
    <td valign="top" align="center" width="100%"><br/><br/><br/>
    	<img src="/images/login/SMSlogo.gif"/><table border="0" align="center">
                              <tr>
                                <td align="center"><img src="/images/login/login.gif"/></td>
                              </tr>
                              <tr>
                                <td><img src="/images/login/username.gif"/></td>
                              </tr>
                              <tr>
                                <td><input type='text' name='user' value='<?=$object->getParameter("user")?>' size='30'/></td>
                              </tr>
                              <tr>
                                <td><img src="/images/login/password.gif"/></td>
                              </tr>
                              <tr>
                                <td><input type='password' name='pass' value='' size='30'></td>
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