<?php
/*
 * Created on May 14, 2009
 * 
 * Generate mainmenu table
 * Detect parent name for each page
 */
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="mainheader">
	<tr>
   					<td width="50%"></td>
   					<td width="38%"></td>
   					<td width="6%"></td>
   					<td width="6%"></td>
  	</tr>
	<tr>
		<td height="47" style="background-image: url('/images/<?=$theme?>/header.png');">
						 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
					         <tbody>
						         <tr><td>
						         <? for($i=0;$i<count($pageinfo["parent"]);$i++){ ?>
						         <a href="javascript:;" onclick="gotoURL('<?=$pageinfo["parenturl"][$i]?>')" target="mainFrame"><?=$pageinfo["parent"][$i]?> &gt</a>
						         <? } ?>
						          </td></tr>
						         <tr><td><b><?=$pageinfo["pagename"]?></b></td></tr>
					         </tbody>
						 </table>
 			<input type="hidden" id="parent" name="parent" value="<?=$parent?>">
		</td>
		<td height="47" align="right" style="background-image: url('/images/<?=$theme?>/header.png');">
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" onclick="gotoURL('<?=$pageinfo["parenturl"][0]?>')" target="_parent"><img src="/images/<?=$theme?>/home.png" border="0" title="Home" /></a>&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" onclick="gotoURL('<?=$pageinfo["parenturl"][count($pageinfo["parent"])-1]?>')" target="_parent"><img src="/images/<?=$theme?>/up.png" border="0" title="Up" /></a>&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/logout.php" target="_parent"><img src="/images/<?=$theme?>/logout.png" border="0" title="Logout" /></a>
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
		<td colspan="4" height="2" background="#eae8e8"><img src="/images/blank.gif" height="2px"></td>
	</tr>
</table>