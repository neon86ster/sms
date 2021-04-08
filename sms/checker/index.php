<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?=(isset($pageinfo["pagename"]))?$pageinfo["pagename"]:""?></title>
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <?include("$root/jsdetect.php");?>
</head>
<body style="margin: 0px 0px 0px 0px;">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td width="8px" height="100%" align="center" rowspan="2" class="hidden_bar">&nbsp;</td>
			<td valign="top" align="center" height="49">
			<div id="header">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
					<tr>
						<td height="49" align="right">
						<?include("$root/menuheader.php");?>						</td>
					</tr>
				</table>
 			</div>
 			</td>
 		</tr><tr>
 			<td valign="top" align="center">
    		<br /><br />
			<b class="welcomecompany"><?=$pageinfo["pagename"]?></b>
			<br /><br />
              <table border="0" cellpadding="0" cellspacing="0" style='overflow:auto;'>
                <tbody>
                  <tr>
                  <? 
    $browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    if ($browser == true){
    	$disrow=1;
    }else{
    	$disrow=2;
    }
                  for($i=0;$i<$pageinfo["rows"];$i++){ 
                  		if($i%$disrow==0&&$i){?></tr><tr><?}?>
                    <td width="342" height="96" align="center"><table cellspacing="0" cellpadding="0" class="mainmenu">
                        <tr>
                          <td width="320" bgcolor="<?=$fontcolor?>" 
                          onclick="gotoURL('<?=$pageinfo[$i]["url"]?>')" 
                          onmouseover="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>-over.png')" 
                          onmouseout="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png')">
                          <img src="/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png" id="<?=strtolower($pageinfo[$i]["page_name"])?>" border="0">
                          <b><span>&nbsp;&nbsp;<?=$pageinfo[$i]["page_name"]?><br/><span class="menudesc"></span></span></b></td>
                           <td class="endmenu" bgcolor="<?=$fontcolor?>">&nbsp;</td>
                          <!--<td class="endmenu" style="background-image: url('/images/<?=$theme?>/endmenu.png');">&nbsp;</td>-->
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
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" onClick="hiddenLeftFrame('/images')"/></div>
</body>