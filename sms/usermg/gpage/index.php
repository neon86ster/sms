<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("formdb.inc.php");
$obj = new formdb();
$obj->setDebugStatus(false);

$rs=$object->isPageView($_SERVER["PHP_SELF"],1,0);
$pageindex = $obj->getParameter("pageindex");
$querystr = "pageindex=$pageindex";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$rs["pagename"]?></title>
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
<form name="thavi" id="thavi" action="" method="post" style="padding:0;margin:0">
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
 	<tr>
    	<td valign="top" height="30px" colspan="2" class="rheader">
<?	$chksql = "select page_id,page_name from s_pagename " .
				"where page_parent_id=0 " .
				"and active=1 " .
				"and page_id!=78 " .
				"order by page_priority ";
	$chkrs = $obj->getResult($chksql);
	$textout = "<select name=\"pageindex\" id=\"pageindex\"  onChange=\"gotoURL('index.php?pageindex='+this.options[this.selectedIndex].value)\"> \n";
	$textout .= "<option value='0'>---select---</option> \n";
	for($i=0; $i<$chkrs["rows"];$i++) {
			$selected = ($pageindex==$chkrs[$i]["page_id"])?'selected':'';
			$textout .= "<option value=\"".$chkrs[$i]["page_id"]."\" $selected >".$chkrs[$i]["page_name"]."</option> \n";
			
	}
	$selected = "";
	if($pageindex=='cc'){$selected = 'selected';}
	$textout .= "<option value='cc' $selected>Booking financial accessibility</option> \n";
	$textout .= "</select> \n";
?>    	
    		<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="padding-left: 20px;padding-right: 10px;">
						&nbsp;&nbsp;Page index : <?=$textout?>
			        </td>
			       </tr>
    		</table>
  		</td>
	</tr>
	<tr>
		 <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 	<tr>
	 	<td height="30px" class="rheader" style="padding-left: 20px;">
	 	<?=$rs["pagename"]?> Information 
	 	&nbsp;<? if($successmsg!=""){ ?><b class="successmsg" style="font-size:10px;">-&nbsp;&nbsp;<?=$successmsg?></b>&nbsp;<img src="/images/successmsg.png" />&nbsp;<? } ?>
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
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('../../images')"/></div>
</body>
</html>