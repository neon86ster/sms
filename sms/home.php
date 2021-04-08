<?php
	session_start();
	include("include.php");
	require_once("secure.inc.php");

	$object = new secure();
	if(!$object->checkLogin()) {
		header("location: index.php");
	}
	$pagename = "home.php";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SMS System</title>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
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
  	<link rel="stylesheet" type="text/css" href="/css/styles.css" />
  	<?
  	}
?>
</head>
<frameset rows="0,*" border="0" framespacing="0" frameborder="0">
 <frame src="header.php" id="topFrame" name="topFrame" frameborder="0" border="0" framespacing="0" marginheight="0" marginwidth="0" style="overflow:auto" noresize>
 <frameset cols="0,*" border="0" frameborder="0" framespacing="0" id="MainFrameSet">
  <frame src="leftmenu.php" id="leftFrame" name="leftFrame" frameborder="0" border="0" noresize>
  <frame src="mainPage.php" id="mainFrame" name="mainFrame" frameborder="0" border="0" framespacing="0" marginheight="0" marginwidth="0" style="overflow:auto" noresize>
 </frameset>
</frameset><noframes><body></body></noframes>
</html>