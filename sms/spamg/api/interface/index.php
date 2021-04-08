<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
$obj->setDebugStatus(false);

// for return to the same page 
$search=$obj->getParameter("search",""); 
$showinactive=$obj->getParameter("showinactive",0);
//$showdetail=$obj->getParameter("showdetail",0);
$order=$obj->getParameter("order","c_bp_person");
$sort=$obj->getParameter("sort","asc");
$page = $obj->getParameter("page",1);
$successmsg = $obj->getParameter("msg","");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$save=$obj->getParameter("save",false);
//$querystr = "pageid=$pageid&search=$searchstr&sort=$sort&order=$order&page=$page&showdetail=$showdetail&showinactive=".$showinactive;
$querystr = "pageid=$pageid&search=$searchstr&sort=$sort&order=$order&page=$page&showinactive=".$showinactive;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=(isset($pageinfo["pagename"]))?$pageinfo["pagename"]:""?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="getReturnText('report.php','<?=$querystr?>','tableDisplay');">
<div id="loading">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form name="bankacccms" id="bankacccms" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="109px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" colspan="2" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>
 	<!--<tr>
    	<td valign="top" height="30px" colspan="2" class="rheader">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="padding-left: 20px;">
			        <input type="hidden" name="page" id="page" value="<?=$page?>">
			        <input type="hidden" name="sort" id="sort" value="<?=$sort?>">
			        <input type="hidden" name="order" id="order" value="<?=$order?>">
			        Search: &nbsp;
						<input type="text" name="search" id="search" <?=($search)?"value='".$search."'":""?>/>
					</td><td class="rheader">
     					&nbsp;&nbsp;
        			    <a href="javascript:;" onClick="searchInfo('1')"><img src="/images/<?=$theme?>/search.png" alt="search" border="0"/></a>
        			    <a href="javascript:;" onClick="document.getElementById('search').value='';searchInfo('1')"><img src="/images/<?=$theme?>/view.png" alt="view all" border="0"/></a> 
        			    <a href="javascript:;" onClick="searchInfo('<?=$page?>')"><img src="/images/<?=$theme?>/refresh.png" alt="refresh" border="0"/></a>&nbsp; 
        			</td><td class="rheader">
        				&nbsp;&nbsp; 
        				<input id='showinactive' type='checkbox' name='showinactive' value='1' onClick="showInactive('report.php')" <? echo ($showinactive)?"checked":""?> /> Show Inactive
        				&nbsp;&nbsp; 
        			</td>
			       </tr>
    		</table>-->
  		</td>
	</tr>
	<tr>
		 <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 	<tr>
	 	<td height="30px" class="rheader" style="padding-left: 20px;">
	 	<?=$pageinfo["pagename"]?> Information 
	 	&nbsp;<? if($successmsg!=""){ ?><b class="successmsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$successmsg?></b>&nbsp;<img src="/images/successmsg.png" />&nbsp;<? } ?>
	 	</td>
    	<td align="right" height="30px" class="rheader">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="margin-buttom: 5px;">
			        <?	
			        	$chksql = "select bankacc_cms_id from al_bankacc_cms ";
			        	$search = strtolower($search);
						$searchsql = $obj->convert_char($search);
			        	$chksql .= "where bankacc_cms_id=bankacc_cms_id ";
			        	if(!$showinactive){$chksql .= "and bankacc_active=1 ";}
			        	$chksql .= "and (lower(bankacc_number) like '%$searchsql%' " .
			        				"or lower(c_bp_person) like '%$searchsql%' " .
			        				"or lower(bankacc_comment) like '%$searchsql%' " .
			        				"or lower(bankacc_name) like '%$searchsql%' " .
			        				"or REPLACE(c_bp_phone,\"-\",\"\") like '%$searchsql%') ";
			        	$chkrs = $obj->getResult($chksql);
			        	//echo $chkrs["rows"]+0;
			        ?> 
			        </td><td class="rheader">
						<img src="/images/<?=$theme?>/appt/separate.png">&nbsp;
					</td>
 	<? if($chkPageEdit){?>
						<td class="rheader" style="padding: 2 2 2 0;">
						<table border="0" cellspacing="0" cellpadding="0" style="cursor: pointer;">
						<tr>
							<td onClick="gotoURL('addinfo.php?pageid=<?=$pageid?>')" height="30px" class="rheader" style="padding-left: 20px;color: <?=$fontcolor?>;background-color:#a8c2cb;">
								Add To PC
							</td>
							<td onClick="gotoURL('addinfo.php?pageid=<?=$pageid?>')" style="padding-left: 20px;padding-right: 10px;background-color:#a8c2cb;"><img src="/images/<?=$theme?>/add.png"></td>
							<td onClick="gotoURL('addinfos.php?pageid=<?=$pageid?>')" height="30px" class="rheader" style="padding-left: 20px;color: <?=$fontcolor?>;background-color:#a8c2cb;">
								Add To Bank
							</td>
							<td onClick="gotoURL('addinfos.php?pageid=<?=$pageid?>');" style="padding-left: 10px;padding-right: 20px;background-color:#a8c2cb;"><img src="/images/<?=$theme?>/add.png"></td>
						</tr>
						</table> 
					</td>
	<? } ?>
					<td class="rheader">&nbsp; </td>
			       </tr>
    		</table>
  		</td>
	</tr>
	<tr>
		 <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 </table> 
</div>
  	</td>
  </tr>
  <tr>
  		<td valign="top" style="margin-top:0px;margin-left:0px;padding-left:0px;">
			<div id="tableDisplay"></div>
		</td>
   </tr>
</table>
</form> 
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</body>
</html>