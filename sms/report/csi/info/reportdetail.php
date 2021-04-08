<?php
/*
 * Created on Feb 19, 2009
 */
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();


$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate = $obj->getParameter("end");

$hidden_begin = $obj->getParameter("begin");
$hidden_end = $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid",1);

$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&branchid=$branch_id";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Therapist Massage Customer CSI Report Detail</title>
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
    	<form name="cpldetail" id="cpldetail" action='reportdetail.php' method='get' style="padding:0;margin:0">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> Export:&nbsp;&nbsp;
				      	<select id="export" name="export">
				            <option title="PDF" value="PDF">PDF</option>
				            <option title="Excel" value="Excel">Excel</option>
				        </select>          
			         	&nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('rdetail.php?<?=$querystr?>&export='+document.getElementById('export').value,'Therapist hour Detail')"/>
			         	&nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
						<input type="hidden" id="begin" name="begin" value="<?=$hidden_begin?>"/>
						<input type="hidden" id="end" name="end" value="<?=$hidden_end?>"/>
						<?
						if(!isset($beginage)){$beginage="";}
						if(!isset($endage)){$endage="";}
						if(!isset($pageid)){$pageid="";}
						if(!isset($order)){$order="";}
						if(!isset($sort)){$sort="";}
						?>
						<input type="hidden" id="beginage" name="beginage" value="<?=$beginage?>"/>
						<input type="hidden" id="endage" name="endage" value="<?=$endage?>"/>
						<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
						<input type="hidden" id="order" name="order" value="<?=$order?>"/>
						<input type="hidden" id="sortby" name="sortby" value="<?=$sort?>"/>
			          	<input type="hidden" id="branchid" name="branchid" value="<?=$branch_id?>"/>
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