<?php
/*
 * Created on Feb 19, 2009
 */
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("therapist.inc.php");
require_once("report.inc.php");
$obj = new therapist();
$robj = new report();

$hidden_begin = $obj->getParameter("begin");
$hidden_end = $obj->getParameter("end");
$branchid = $obj->getParameter("branchid");
$empid = $obj->getParameter("empid");
//if($empid==1){$empid=0;}
$city = $obj->getParameter("cityid");
$branchid = $obj->getParameter("branchid");
$export = $obj->getParameter("export",false);
$order = $obj->getParameter("order","Default");
$sort= $obj->getParameter("sortby");
$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&order=$order&sortby=$sort&empid=$empid&cityid=$city&branchid=$branchid";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Therapist Hour Detail</title>
<script type="text/javascript" src="/scripts/components.js"></script>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
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
$i = count($pageinfo["parent"]);
$print = "rdetail.php?$querystr&export=print";
$pageinfo["parenturl"][$i] = "rdetail.php?$querystr";
$pageinfo["parent"][$i] = $pageinfo["pagename"];
$pageinfo["pagename"] = $pageinfo["pagename"]." Detail";
?>
				<?include("$root/rdetailheader.php");?>
	 	</td>
	  </tr>
 	<tr>
    	<td valign="top" height="30">
    	<form name="therapistdetail" id="therapistdetail" action='reportdetail.php' method='get' style="padding:0;margin:0">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> Export:&nbsp;&nbsp;
				      	<select id="export" name="export">
				            <option title="PDF" value="PDF">PDF</option>
				            <option title="Excel" value="Excel">Excel</option>
				        </select>          
			         	&nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('rdetail.php?<?=$querystr?>&export='+document.getElementById('export').value,'Therapist hour Detail')"/>
			         	&nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
						<input type="hidden" id="empid" name="empid" value="<?=$empid?>"/>
						<input type="hidden" id="begin" name="begin" value="<?=$hidden_begin?>"/>
						<input type="hidden" id="end" name="end" value="<?=$hidden_end?>"/>
						<input type="hidden" id="branchid" name="branchid" value="<?=$branchid?>"/>
						<input type="hidden" id="cityid" name="cityid" value="<?=$city?>"/>
						<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
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