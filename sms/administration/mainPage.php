<?
error_reporting(E_ALL);
include("include.php");
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Home</title>
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <? include "jsdetect.php"; // all javascript detect 
  ?>
</head>
<body style="margin: 0px;">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td width="8px" height="100%" align="center" rowspan="2" style="background-image: url('images/body_bg.gif');">&nbsp;</td>
			<td valign="top" align="center" height="76">
			<div id="header">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" id="menuheader">
				<tr>
   					<td width="50%"></td>
   					<td width="38%"></td>
   					<td width="6%"></td>
   					<td width="6%"></td>
  				</tr>
				<tr>
					<td height="47" style="background-image: url('images/header.png');">
						 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
					         <tbody>
						         <tr><td><b>Home</b></td></tr>
					         </tbody>
						 </table>
 						<input type="hidden" id="parent" name="parent" value="<?=$parent?>">
					</td>
					<td height="47" align="right" style="background-image: url('images/header.png');">
					
						<img src="images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="logout.php" target="_parent"><img src="images/logout.png" border="0" title="Logout" /></a>
							&nbsp;&nbsp;&nbsp;&nbsp;
					<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td height="47" align="right" style="background-image: url('/images/<?=$theme?>/header.png');">
						<font style="font-size:11px;color:#444;">WELCOME 
						<br><?=strtoupper($_SESSION["__user"])?>
						<br>
						<a href="/logout.php" target="_parent" style="color:#666666;font-weight: bold;">
						logout
						</a>
						</font>
					</td>
					<td height="47" align="center" style="background-image: url('/images/<?=$theme?>/header.png');">
						<span>
						<img style="border:1px solid #5792a9;" src="<?=$customize_part?>/images/user/<?=$obj->getIdToText($_SESSION["__user_id"], "s_user", "upic", "u_id")?>" width="40px" height="40px">
						</span>
					</td>				
				
					
				</tr>
				<tr>
					<td colspan="4" height="2" background="#eae8e8"><img src="images/blank.gif" height="2px"></td>
				</tr>
			</table>
 			</div>
 		</td></tr>
 		<tr><td valign="top" align="center">
    		<br /><br /><img src="images/welcome.png"><br /><br />
			<b class="welcomecompany">SMS PANEL</b>
			<br /><br />
              <table border="0" cellpadding="0" cellspacing="0" style='overflow:auto;'>
                <tbody>
                  <tr>
                  <? for($i=0;$i<$pageinfo["rows"];$i++){ 
                  		$pageinfo[$i]["popup"] = "";
                  		if($i%2==0&&$i){?></tr><tr><?}?>
                    <td width="342" height="96" align="center"><table cellspacing="0" cellpadding="0" class="mainmenu">
                        <tr>
                          <td width="320" bgcolor="<?=$fontcolor?>" title="<?=$pageinfo[$i]["popup"]?>"
                          onclick="gotoURL('<?=$pageinfo[$i]["url"]?>')" 
                          onmouseover="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>-over.png')" 
                          onmouseout="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png')">
                          <img src="images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png" id="<?=strtolower($pageinfo[$i]["page_name"])?>" border="0">
                          <b><span>&nbsp;&nbsp;<?=$pageinfo[$i]["page_name"]?><br/><span class="menudesc"><?=$object->getIdToText($pageinfo[$i]["page_id"],"s_pagename","description","page_id")?></span></span></b></td>
                          <td class="endmenu" bgcolor="<?=$fontcolor?>">&nbsp;</td>
                        </tr>
                    </table>
                    </td>
                    <? } ?>
                  </tr>
                </tbody>
              </table>
			</td>
		</tr>
	</tbody>
</table>
<div class="hiddenbar"><img id="spLine" src="images/bar_close.gif" alt="" onclick="hiddenLeftFrame()"/></div>
</body>