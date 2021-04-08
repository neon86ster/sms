<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
$obj->setDebugStatus(false);

$ubranch_id = $obj->getIdToText($_SESSION["__user_id"],"s_user","branch_id","u_id");
$ubranch_name = strtolower($obj->getIdToText($ubranch_id,"bl_branchinfo","branch_name","branch_id"));
if($ubranch_name!="all"){
	$branch = $ubranch_id;
}
$cityid = $obj->getIdToText($ubranch_id,"bl_branchinfo","city_id","branch_id");
$querystr = "pageid=$pageid&cityid=$cityid";
$th_shiftone = $obj->getIdToText("$ubranch_id","bl_th_available","th_shiftone","branch_id","1 order by l_lu_date desc");
$th_shifttwo = $obj->getIdToText("$ubranch_id","bl_th_available","th_shifttwo","branch_id","1 order by l_lu_date desc");
$updateth=$obj->getParameter("update_th");
if($updateth) {
	$branchid = $ubranch_id;
	$th_shiftone = $obj->getParameter("th_shiftone",$th_shiftone);
	$th_shifttwo = $obj->getParameter("th_shifttwo",$th_shifttwo);
	$tmp = $obj->addThAvailable($branchid,$th_shiftone,$th_shifttwo);
	if(!$tmp){
		$errormsg = $obj->getErrorMsg();
	}
}
$signin = $obj->getParameter("signin");
$thid = $obj->getParameter("thid");
if($signin==" In "&&$thid) {
	$branchid = $ubranch_id;
	$tmp = $obj->addThList($branchid,$thid);
	if(!$tmp){
		$errormsg = $obj->getErrorMsg();
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="getReturnText('report.php','page=1','tableDisplay');">
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
    <td height="49px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>
</table> 
</div>
  	</td>
  </tr>
  	<? if($chkPageEdit){?>
 	<tr>
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/menu/logobg.png');">
			        <a href="javascript:;" onClick="gotoURL('addinfo.php?pageid=<?=$pageid?>')"><img src="../../images/addIcon.png" alt="Add" width="16" height="16" border="0"/></a>
			        &nbsp;&nbsp;<a href="javascript:;" onClick="gotoURL('addinfo.php?pageid=<?=$pageid?>')">Manage Company information</a>
			        </td>
			       </tr>
			      <tr bgcolor="#999999">
			        <td height="1" bgcolor="#CCCCCC"><img src="/images/blank.gif" width="1" height="1" /></td>
			      </tr>
    		</table>
  		</td>
	</tr>
	<? } ?>
  <tr>
		<td valign="top" style="margin-top:0px;margin-left:0px">
			<div id="tableDisplay"></div>
		</td>
  </tr>
</table> 
</form> 
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('../../images')"/></div>
</body>
</html>