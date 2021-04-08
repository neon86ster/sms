<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
$object->setDebugStatus(false);

// for return to the same page 
$successmsg = $object->getParameter("msg","");
$errormsg = $object->getParameter("errormsg","");
$pagepermission = $object->getParameter("pagepermission","");
$querystr = "pageid=$pageid&pagepermission=$pagepermission";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajaxs.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="loadPage('<?=$querystr?>');">
<div id="loading">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form name="pageSetup" id="pageSetup" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="80px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" colspan="2" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>	
 	<tr>
		 <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 	<tr>
	 	<td height="30px" class="rheader" style="padding-left: 20px;white-space:nowrap;">
	 	<?=$pageinfo["pagename"]?> Information &nbsp;&nbsp;
	 	   (<a href="javascript:;;" onClick="collapse_all()">collapse all</a> , 
            <a href="javascript:;;" onClick="expand_all()">expand all</a>)
         </b>
	 	&nbsp;<? if($successmsg!=""){ ?><b class="successmsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$successmsg?></b>&nbsp;<img src="/images/successmsg.png" />&nbsp;<? } ?>
	 	<? if($errormsg!=""){ ?><b class="errormsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$errormsg?></b>&nbsp;<img src="/images/errormsg.png" />&nbsp;<? } ?>
	 	</td>
    	<td align="right" height="30px" class="rheader">
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="margin-buttom: 5px;">
			        <?	
			        	$sql = "select page_id from s_pagename ";
			        	$rs = $object->getResult($sql);
			        	echo $rs["rows"]+0;
			        ?> Total Records &nbsp;
			        </td>
			        <td class="rheader">
						<img src="/images/<?=$theme?>/appt/separate.png">&nbsp;
					</td>
			<? if($chkPageEdit){?>
					<td class="rheader" style="padding: 2 2 2 0;">
						<table border="0" cellspacing="0" cellpadding="0" >
						<tr>
							<td style="padding-left: 20px;padding-right: 20px;background-color:#a8c2cb;">
								<input name="add" id="add" type="button" size="" value=" save change " 
								style="cursor: pointer;" onClick="set_editData('s_pagename')">
								<input name="querystr" id="querystr" type="hidden" value="<?=$querystr?>">
							</td>
						</tr>
						</table> 
					</td>
			<? } ?>
					<td class="rheader">
						<img src="/images/<?=$theme?>/appt/separate.png">&nbsp;
					</td>
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