<?php
/*
 * Created on Aug 3, 2009
 * 
 * Generate mainmenu table for all report detail
 * Detect parent name for each page
 */
  $browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    if ($browser == true){
    	$browser = 'iphone';
  	?>
  	<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=1.0" />
  	<?
  	}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="leftmenu">
	<tr>
		<td height="47" style="background-image: url('/images/<?=$theme?>/header.png');">
						 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
					         <tbody>
						         <tr><td><b>
						         <? for($i=0;$i<count($pageinfo["parent"]);$i++){ ?>
						        <a href="javascript:;" target="mainFrame"><?=$pageinfo["parent"][$i]?> &gt</a>
						         <? } ?>
						         </b></td></tr>
						         <tr><td><b><?=$pageinfo["pagename"]?></b></td></tr>
					         </tbody>
						 </table>
		</td>
		<td height="47" align="right" valign="middle" style="background-image: url('/images/<?=$theme?>/header.png');">
					<span align="right" class="menuheader">
						<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="javascript:;" onclick="window.open('<?=$print?>','','resizable=yes,menubar=no,scrollbars=yes')" target="_parent"><img src="/images/<?=$theme?>/print.png" border="0" title="Print" /></a>					
					</span>
		</td>
	</tr>
	<tr>
		<td colspan="2" height="2" background="#eae8e8"><img src="/images/blank.gif" height="2px"></td>
	</tr>
</table>