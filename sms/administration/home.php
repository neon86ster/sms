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
</head>
<frameset rows="0,*" border="0" framespacing="0" frameborder="0">
<frame src="header.php" id="topFrame" name="topFrame" frameborder="0" border="0" framespacing="0" marginheight="0" marginwidth="0" style="overflow:auto" noresize>
 <frameset cols="220,*" border="0" frameborder="0" framespacing="0" id="MainFrameSet">
  <frame src="leftmenu.php" id="leftFrame" name="leftFrame" frameborder="0" border="0" noresize>
  <frame src="mainPage.php" id="mainFrame" name="mainFrame" frameborder="0" border="0" framespacing="0" marginheight="0" marginwidth="0" style="overflow:auto" noresize>
 </frameset>
</frameset><noframes><body></body></noframes>
</html>