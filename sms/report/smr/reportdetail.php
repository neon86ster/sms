<?php
/*
 * Created on Feb 19, 2009
 */
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");

$obj = new customer();
//
require_once("marketing.inc.php");
$robj = new marketing();

//
$hidden_begin = $obj->getParameter("begin");
$hidden_end = $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$pdcategoryid = $obj->getParameter("itemid");
$table=$obj->getParameter("table");
$payid = $obj->getParameter("payid");
$sexid= $obj->getParameter("sexid");


//
$cityid = $obj->getParameter("cityid");
$tbname = $obj->getParameter("tbname");
$mktype = $obj->getParameter("mktype");
$status = $obj->getParameter("status");
$mkcode = $obj->getParameter("mkid");
$mktypeid = $obj->getParameter("mktypeid");
////////////////




$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end" .
		"&branchid=$branch_id&itemid=$pdcategoryid&payid=$payid&table=$table&sexid=$sexid&mkid=$mkcode&mktypeid=$mktypeid&tbname=$tbname&mktype=$mktype&status=$status&cityid=$cityid";
	//	"&order=$order&sortby=$sort&cityid=$city";	
			

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/scripts/components.js"></script>
</head>
<body onLoad="getReturnText('rdetail.php','<?=$querystr?>','tableDisplay');">
<div id="loading">
<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<table class="main" cellspacing="0" cellpadding="0" width="100%">
 <tr>
    <td height="69px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" colspan="3" align="center" height="49">
<?
//$i = count($pageinfo["parent"]);
$print = "rdetail.php?$querystr&export=print";
//$pageinfo["parenturl"][$i] = "rdetail.php?$querystr";
//$pageinfo["parent"][$i] = $pageinfo["pagename"];
//$pageinfo["pagename"] = $pageinfo["pagename"]." Detail";
?>
				<?include("$root/rdetailheader.php");?>
	 	</td>
	  </tr>
 	<tr>
    	<td valign="top" height="30">
    	<form name="cpldetail" id="cpldetail" action='reportdetail.php' method='get' style="padding:0;margin:0">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> Export:&nbsp;&nbsp;
				      	<select id="export" name="export">
				            <option title="PDF" value="PDF">PDF</option>
				            <option title="Excel" value="Excel">Excel</option>
				        </select>          
			         	&nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('rdetail.php?<?=$querystr?>&export='+document.getElementById('export').value,'')"/>
			         	
						<input type="hidden" id="begin" name="begin" value="<?=$hidden_begin?>"/>
						<input type="hidden" id="end" name="end" value="<?=$hidden_end?>"/>
						<input type="hidden" id="branchid" name="branchid" value="<?=$branch_id?>"/>
						<input type="hidden" id="cityid" name="cityid" value="<?=$cityid?>"/>
					
			          </td>
			        </tr>
			      <tr bgcolor="#999999">
			        <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
			      </tr>
    		</table>
		</form> 
  		</td>
	</tr>
</table> 
</div>
  	</td>
  </tr>
  <tr>
		<td valign="top" style="margin-top:0px;margin-left:0px">
			<div id="tableDisplay"></div>
		</td>
  </tr>
</table> 
</body>
</html>