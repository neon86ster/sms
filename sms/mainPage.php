<?
include("include.php");

$errormsg="";$successmsg="";
//if($object->checkAdmin($obj->getUserIdLogin())){
	if(isset($_REQUEST["Unlock"])){
		$sql = "select * from c_bpds_link where bpds_id=".$_REQUEST["bookid"];
		$chkrs=$obj->getResult($sql);
		if(!$chkrs["rows"]){
			$errormsg="Unlock booking ID: ".$_REQUEST["bookid"]." failt!!";
		}else{
			if($chkrs[0]["tb_name"]=="a_bookinginfo"){$sql = "update a_bookinginfo set l_set_use=0 where book_id=".$chkrs[0]["tb_id"];}
			else{$sql = "update c_saleproduct set l_set_use=0 where pds_id=".$chkrs[0]["tb_id"];}
			$rs = $obj->setResult($sql);
			if(!$rs){
				$errormsg="Unlock booking ID: ".$_REQUEST["bookid"]." failt!!";
			}else{
				$successmsg="Unlock booking Success!!";
			}
		}
	}
	if(isset($_REQUEST["UnlockAll"])){
		$sql = "update a_bookinginfo set l_set_use=0";
		$id1 = $obj->setResult($sql);
		$sql = "update c_saleproduct set l_set_use=0";
		$id2 = $obj->setResult($sql);
		if(!$id1&&!$id2){
			$errormsg="Unlock booking failt!!";
		}else{
			$successmsg="Unlock booking Success!!";
		}
	}
//}	
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="Cache-Control" content="public">
  <meta http-equiv="pragma" content="public">
  <title>Home</title>
  <link rel="stylesheet" type="text/css" href="/css/styles.css" />
  <? include "jsdetect.php"; // all javascript detect 
  ?>
</head>
<body style="margin: 0px 0px 0px 0px;">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td width="8px" height="100%" align="center" rowspan="2" class="hidden_bar">&nbsp;</td>
			<td valign="top" align="center" height="76">
			<div id="header">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" menuheader>
				<tr>
   					<td width="50%"></td>
   					<td width="38%"></td>
   					<td width="6%"></td>
   					<td width="6%"></td>
  				</tr>
				<tr>
					<td height="47" align="left" style="background-repeat:repeat-x;background-image: url('/images/<?=$theme?>/header.png');">
						 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
					         <tbody>
					         <!--
						         <tr><td>
						         <a href="javascript:;" onclick="gotoURL('checker/index.php')" target="mainFrame">Home ></a>
						          </td></tr>
						       -->
						         <tr><td><b>Home</b></td></tr>
					         </tbody>
						 </table>
 						 <input type="hidden" id="parent" name="parent" value="<?=$parent?>">
				  </td>
					<td height="47" align="right" style="background-image: url('/images/<?=$theme?>/header.png');">
					<img src="/images/separate.png" />&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="logout.php" target="_parent"><img src="/images/<?=$theme?>/logout.png" border="0" title="Logout" /></a>
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
<? if($object->checkUserOnline($obj->getUserIdLogin())){ 
		$useronliners = $object->getUser();
		$useronline = $useronliners["rows"];
?>
				<!--<tr>
					<td height='29' align='right' style='border-top: 2px #eae8e8 solid;background-image: url("/images/<?=$theme?>/home/unlock.jpg");' class="rheader">
					<span id="online">
					online users: <?=$useronline?>&nbsp;&nbsp;
					</span>
						<input type="buttom" name="Detail" value=" Detail " class="button" style="height: 22px;width: 72px;background:no-repeat url('/images/<?=$theme?>/detail.png');" 
						onClick="newwindow('currentuser.php','online_users')"/>&nbsp;&nbsp;
					</td>
					<td height='29' align='left' style='border-top: 2px #eae8e8 solid;background-image: url("/images/<?=$theme?>/home/unlock.jpg");' class="rheader">
					<form name='appointment' action='<?=$pagename?>' method='get'>
						&nbsp;<input type="text" name="bookid" value="" size="5" class="text"/>
						&nbsp;<input type="submit" name="Unlock" value=" Unlock " class="button" style="height: 22px;width: 72px;background:no-repeat url('/images/<?=$theme?>/unlock.png');"/>
						&nbsp;<input type="submit" name="UnlockAll" value=" Unlock All " class="button"  style="height: 22px;width: 72px;background:no-repeat url('/images/<?=$theme?>/unlockall.png');"/>
						&nbsp;<? if($errormsg!=""){ ?><img src="/images/errormsg.png" />&nbsp;<b class="errormsg"><?=$errormsg?></b><? } ?>
						&nbsp;<? if($successmsg!=""){ ?><img src="/images/successmsg.png" />&nbsp;<b class="successmsg"><?=$successmsg?></b><? } ?>
 					</form>
					</td>
				</tr>-->
				
				<tr>
					<td colspan="4" height='29' align='center' style='border-top: 2px #eae8e8 solid;background-image: url("/images/<?=$theme?>/home/unlock.jpg");' class="rheader">
					<form name='appointment' action='<?=$pagename?>' method='get'>
						<span id="online">
					online users: <?=$useronline?>&nbsp;&nbsp;
					</span>
						<input type="buttom" name="Detail" value=" Detail " class="button" style="height: 22px;width: 72px;background:no-repeat url('/images/<?=$theme?>/detail.png');" 
						onClick="newwindow('currentuser.php','online_users')"/>&nbsp;&nbsp;
						&nbsp;<input type="text" name="bookid" value="" size="5" class="text"/>
						&nbsp;<input type="submit" name="Unlock" value=" Unlock " class="button" style="height: 22px;width: 72px;background:no-repeat url('/images/<?=$theme?>/unlock.png');"/>
						&nbsp;<input type="submit" name="UnlockAll" value=" Unlock All " class="button"  style="height: 22px;width: 72px;background:no-repeat url('/images/<?=$theme?>/unlockall.png');"/>
						&nbsp;<? if($errormsg!=""){ ?><img src="/images/errormsg.png" />&nbsp;<b class="errormsg"><?=$errormsg?></b><? } ?>
						&nbsp;<? if($successmsg!=""){ ?><img src="/images/successmsg.png" />&nbsp;<b class="successmsg"><?=$successmsg?></b><? } ?>
 					</form>
					</td>
				</tr>
<? } ?>
			</table>
 			</div>
 		</td></tr>
 		<tr><td valign="top" align="center">
    		<br /><br /><img src="/images/<?=$theme?>/welcome.png"><br /><br />
			<b class="welcomecompany"><?=strtoupper($obj->getIdToText("1","a_company_info","company_name","company_id"))?></b>
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
                  		$pageinfo[$i]["popup"] = "";
       
                  		if($i%$disrow==0&&$i){?></tr><tr><?}
                  ?>
                    <td width="342" height="96" align="center"><table cellspacing="0" cellpadding="0" class="mainmenu">
                        <tr>
                          <td width="320" bgcolor="<?=$fontcolor?>" title="<?=$pageinfo[$i]["popup"]?>"
                          onclick="gotoURL('<?=$pageinfo[$i]["url"]?>')" 
                          onmouseover="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>-over.png')" 
                          onmouseout="changeimg('<?=strtolower($pageinfo[$i]["page_name"])?>','/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png')">
                          <img src="/images/icon/<?=strtolower($pageinfo[$i]["page_name"])?>.png" id="<?=strtolower($pageinfo[$i]["page_name"])?>" border="0">
                          <b><span>&nbsp;&nbsp;<?=$pageinfo[$i]["page_name"]?><br/><span class="menudesc"><?=$obj->getIdToText($pageinfo[$i]["page_id"],"s_pagename","description","page_id")?></span></span></b></td>
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
<div class="hiddenbar"><img id="spLine" src="/images/bar_open.gif" alt="" onClick="hiddenLeftFrame('/images')"/></div>
</body>