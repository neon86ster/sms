<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");


$obj = new report(); 
$errormsg ="";
$thid = $obj->getParameter("id");
// for return to the same page 
$showinactive = $obj->getParameter("showinactive");
$method = $obj->getParameter("method");
$order = $obj->getParameter("order");
$page = $obj->getParameter("page",1);
$sort=$obj->getParameter("sort","asc");
$branchid=$obj->getParameter("branchid",0);
$cityid=$obj->getParameter("cityid",0);
$search = $obj->getParameter("where");
$searchstr=str_replace("+","%2B",$search);
$searchstr=str_replace("&","%26",$searchstr);
$querystr = "&pageid=$pageid&search=$searchstr&order=$order&page=$page&cityid=$cityid&branchid=$branchid&sort=$sort&showinactive=".$showinactive;


$i = count($pageinfo["parent"]);
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = "Manage Approved Treatments";
$parent = "$parent";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script src="../scripts/datechooser/date-functions.js" type="text/javascript"></script>
<script src="../scripts/datechooser/datechooser.js" type="text/javascript"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

<!--[if IE]>
<style>
span.fix select.ctrDropDown{
    width:115px;
    font-size:11px;
}
span.fix select.ctrDropDownClick{
    font-size:11px;

    width:auto;

}
span.fix select.plainDropDown{
    width:115px;
    font-size:11px;
}

</style>
<![endif]-->

</head>
<body onLoad="loadtreatmentapp(<?=$thid?>);">
<form name="thavi" id="thavi" action="" method="post" style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="6px" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="79px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
	  </tr>
	  <tr>
		 	<td height="30px" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
		 	<?=$obj->getIdToText($thid,"l_employee","emp_code","emp_id")." ".$obj->getIdToText($thid,"l_employee","emp_nickname","emp_id")?> 
		 	- Treatment Approve
		 	</td>
	 </tr>
	 <tr>
			 <td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	 </tr>
</table> 
</div>
  	</td>
  </tr>  
  <tr>
<td valign="top">

<table width="95%" border="0" cellspacing="0" cellpadding="0" align="left" style="padding-top:10px; padding-left:20px;">
	<tr>
    	<td width="100%" align="center">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Package</b>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Massage</b>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Facial</b>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Bath</b>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Scrub</b>
					</td>
					<td style="text-align:center;background-color:#a8c2cb;">
					<b>Wrap</b>
					</td>
				</tr>
				<tr height="24">
					<td class="report" align="center">
					<span class="fix" style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
					<?=$obj->makeListbox("package","db_package","package_name","package_id",$packageid,0,"package_name",0,0,0,0,0,"settreatmentapp('$thid','0',this.options[this.selectedIndex].value,'','add','packagediv');this.selectedIndex=0;")?></td>
					</span>
					</td>
					<td class="report" align="center">
					<span class="fix" style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
					<?=$obj->makeListbox("massage","db_trm","trm_name","trm_id",$massageid,0,"trm_name","trm_active",1,"trm_category_id=3","trm_id=1",0,"settreatmentapp('$thid','3',this.options[this.selectedIndex].value,'','add','massagediv');this.selectedIndex=0;")?>
					</span>
					</td>
					<td class="report" align="center">
					<span class="fix" style="width: 115px;font-family:Tahoma; font-size: 11px;overflow:hidden;">
					<?=$obj->makeListbox("facial","db_trm","trm_name","trm_id",$facial,0,"trm_name","trm_active",1,"trm_category_id=2","trm_id=1",0,"settreatmentapp('$thid','2',this.options[this.selectedIndex].value,'','add','facialdiv');this.selectedIndex=0;")?>
					</span>
					</td>
					<td class="report" align="center">
					<div id="bathdiv">
					<a href="javascript:;" onClick="settreatmentapp('<?=$thid?>','1','0','<?=$chkrs[$i]["thapp_id"]?>','delete','<?=$divname?>');" >
					<img src="/images/active.png" border="0" title="approve" />
					</a></div>
					</td>
					<td class="report" align="center">
					<div id="scrubdiv">
					<a href="javascript:;" onClick="settreatmentapp('<?=$thid?>','4','0','<?=$chkrs[$i]["thapp_id"]?>','add','<?=$divname?>');" >
					<img src="/images/active.png" border="0" title="approve" />
					</a></div>
					</td>
					<td class="report" align="center">
					<div id="wrapdiv">
					<a href="javascript:;" onClick="settreatmentapp('<?=$thid?>','5','0','<?=$chkrs[$i]["thapp_id"]?>','add','<?=$divname?>');" >
					<img src="/images/active.png" border="0" title="approve" />
					</a></div>
					</td>
				</tr>
				<tr height="20">
					<td class="report" align="center" valign="top"><div id="packagediv"></div></td>
					<td class="report" align="center" valign="top"><div id="massagediv"></div></td>
					<td class="report" align="center" valign="top"><div id="facialdiv"></div></td>
					<td class="report" align="center" valign="top">&nbsp;</td>
					<td class="report" align="center" valign="top">&nbsp;</td>
					<td class="report" align="center" valign="top">&nbsp;</td>
				</tr>
			</table>
		</td>
    </tr>
</table>

</td>
  </tr>
</table> 
<input type="hidden" name="querystr" id="querystr" value="<?=$querystr?>"/>
<input name="id" id="id" type="hidden" value="<?=$thid?>">
</form>
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</div>
</body>
</html>