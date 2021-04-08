<?php
	include("include.php");
	
	$uname=$_SESSION["__user"];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>leftmenu</title>
<script type="text/javascript">
		try{
			window.parent.mainFrame.location;
			window.parent.leftFrame.location;
		}catch(e){
			document.location.href="home.php";
		}
</script>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
<script src="/scripts/AC_RunActiveContent.js" type="text/javascript"></script>
</head>
<!--<body>
<table width="100%" height="58" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="center">
		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="700" height="65" title="SPA MANAGEMENT SYSTEM">
		 		 <param name="wmode" value="transparent"> 
				  <param name="movie" value="flash/header sms.swf">
				  <param name="quality" value="high">
				  <embed src="/flash/header sms.swf" wmode="transparent" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="700" height="65"></embed>
		</object></td>
		 </td>
	  <td width="100" align="center">
      		<font color="#5197bb" class="mainheader"><b>WELCOME<br><?=strtoupper($uname)?></b></font>
      		<br/><b><a href="logout.php" target="_parent">Logout</a></b>
      </td>
      <td width="40" align="right">
      </td>
	</tr>
</table>
</body>-->