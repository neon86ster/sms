<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?=(isset($rs["pagename"]))?$rs["pagename"]:""?></title>
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
						<td height="49">
						<?include("$root/menuheader.php");?>
						</td>
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
                  <? for($i=0;$i<$pageinfo["rows"];$i++){ 
                  		if($i%3==0&&$i){?></tr><tr><?}?>
                    <td width="172" height="137" align="center" valign="top">
                    <table width="96" height="89" cellspacing="0" cellpadding="0">
                        <tr>
                          <td style="background-image: url('/images/<?=$theme?>/dirbg.png');background-repeat: repeat-x;cursor: pointer;" 
                          onclick="gotoURL('<?=$pageinfo[$i]["url"]?>');" align="center" 
                          onmouseover="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>-over.png')" 
                          onmouseout="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png')">
                          <img src="/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png" id="<?=strtolower($pageinfo[$i]["page_name"])?>" border="0">
                          </td>
                        </tr>
                    </table>
                    <br style="margin-top:-10px;"/>
                    <a href="javascript:;" onClick="gotoURL('<?=$pageinfo[$i]["url"]?>')" target="mainFrame" style="color:<?=$fontcolor?>; font-weight:bold;"><?=$pageinfo[$i]["page_name"]?></a>
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