<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
$obj = new report();
$obj->setDebugStatus(false);

$date = $obj->getParameter("date",false);
if($date){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}else{
	$date = 16;
	$begin = $obj->getParameter("begin",$obj->getBegin($date,$sdateformat)); 
	$end = $obj->getParameter("end",$obj->getEnd($date,$sdateformat));
}
if($date==2){
	$hidden_begin = $obj->getParameter("hidden_begin");
	$hidden_end = $obj->getParameter("hidden_end");
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
$column = $obj->getParameter("column","Total only");
$order = $obj->getParameter("order","Default");
$percentchk = $obj->getParameter("percent",false);
$showallchk = $obj->getParameter("showall",false);
$branch = $obj->getParameter("branchid",0);
$cityid = $obj->getParameter("cityid",0,1);

if($cityid){
	$branch=0;	
}

$collapse = $obj->getParameter("chkCollapse","Expand");
if($order=="Total CSI"||$order=="Total Customer"){
	$collapse = "Expand";
}
$sortby = $obj->getParameter("chksortby","Z &gt A");
$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&date=$date&column=$column&order=$order&Collapse=$collapse&cityid=$cityid&branchid=$branch&showall=$showallchk&sortby=$sortby";
$print = "report.php?$querystr&export=print";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script src="/scripts/date-functions.js" type="text/javascript"></script>
<script src="/scripts/datechooser.js" type="text/javascript"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

<!--[if IE]>
<style>
td.rheader span.date select.ctrDropDown{
    width:115px;
    font-size:12px;
}
td.rheader span.date select.ctrDropDownClick{
    font-size:12px;

    width:auto;

}
td.rheader span.date select.plainDropDown{
    width:115px;
    font-size:12px;
}
</style>
<![endif]-->

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
<form name="health" id="health" action='' method='post' style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="99px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" colspan="3" align="center" height="49">
				<?include("$root/rmenuheader.php");?>
	 	</td>
	  </tr>
 	<tr>
    	<td valign="top" height="30px" colspan="2" class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			<table border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td class="rheader" height="30" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			         Dates:<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
			        <span class="date" style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
					<select id="date" name="date" class="ctrDropDown" onBlur="this.className='ctrDropDown';" onMouseDown="this.className='ctrDropDownClick';" onChange="this.className='ctrDropDown';">
						  <option title="All" value="1" <?=($date=="1")?"selected":""?>>All</option>
			              <option title="Custom" value="2" <?=($date=="2")?"selected":""?>>Custom</option>
			              <option title="Last Fiscal Quarter" value="3" <?=($date=="3")?"selected":""?>>Last Fiscal Quarter</option>
			              <option title="Last Fiscal Quarter to date" value="4" <?=($date=="4")?"selected":""?>>Last Fiscal Quarter to date</option>
			              <option title="Last Fiscal Year" value="5" <?=($date=="5")?"selected":""?>>Last Fiscal Year</option>
			              <option title="Last Fiscal Year to date" value="6" <?=($date=="6")?"selected":""?>>Last Fiscal Year to date</option>
			              <option title="Last Month" value="7" <?=($date=="7")?"selected":""?>>Last Month</option>
			              <option title="Last Month to date" value="8" <?=($date=="8")?"selected":""?>>Last Month to date</option>
			              <option title="Last Week" value="9" <?=($date=="9")?"selected":""?>>Last Week</option>
			              <option title="Last Week to date" value="10" <?=($date=="10")?"selected":""?>>Last Week to date</option>
			              <option title="This Fiscal Quarter" value="11" <?=($date=="11")?"selected":""?>>This Fiscal Quarter</option>
			              <option title="This Fiscal Quarter to date" value="12" <?=($date=="12")?"selected":""?>>This Fiscal Quarter to date</option>
			              <option title="This Fiscal Year" value="13" <?=($date=="13")?"selected":""?>>This Fiscal Year</option>
			              <option title="This Fiscal Year to date" value="14" <?=($date=="14")?"selected":""?>>This Fiscal Year to date</option>
			              <option title="This Month" value="15" <?=($date=="15")?"selected":""?>>This Month</option>
			              <option title="This Month to date" value="16" <?=($date=="16")?"selected":""?>>This Month to date</option>
			              <option title="Today" value="17" <?=($date=="17")?"selected":""?>>Today</option>
			              <option title="Yesterday" value="18" <?=($date=="18")?"selected":""?>>Yesterday</option>
					</select>
				</span>
       		 		</td><td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        &nbsp;From: <input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
			        To: <input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
        			 </td>
			        <td class="rheader" height="30" align="right" style="padding-left: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				    	Columns:
				        <select id="column" name="column">
				          <option title="Total only" value="Total only" <?=($column=="Total only")?"selected":""?>>Total only</option>
				          <option title="Day" value="Day" <?=($column=="Day")?"selected":""?>>Day</option>
				          <option title="Week" value="Week" <?=($column=="Week")?"selected":""?>>Week</option>
				          <option title="Half month" value="Half month" <?=($column=="Half month")?"selected":""?>>Half month</option>
				          <option title="Month" value="Month" <?=($column=="Month")?"selected":""?>>Month</option>
				          <option title="Quarter" value="Quarter" <?=($column=="Quarter")?"selected":""?>>Quarter</option>
				          <option title="Year" value="Year" <?=($column=="Year")?"selected":""?>>Year</option>
				        </select>
						Sort by:
				        <select id="order" name="order">
				          <option title="Category" value="Category" <?=($order=="Category")?"selected":""?>>Alpha</option>
				          <option title="Default" value="Default" <?=($order=="Default")?"selected":""?>>Default</option>
				          <option title="Total CSI" value="Total CSI" <?=($order=="Total CSI")?"selected":""?>>Total CSI</option>
				          <option title="Total Customer" value="Total Customer" <?=($order=="Total Customer")?"selected":""?>>Total Customer</option>
				         </select>
				        <input type="submit" name="sort" id="sort" value="<?=$sortby?>" onClick="changesbValue(this)"/>
				          <input type="hidden" name="chksortby" id="chksortby" value="<?=$sortby?>"/>
				        </td>
		  		</tr>
    		</table>  
    	</td>
  	</tr>
	<tr bgcolor="#999999">
		        	<td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
	</tr>
 	<tr>
    	<td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');"> Export:&nbsp;&nbsp;
			      	  <select id="export" name="export">
			            <option title="PDF" value="PDF">PDF</option>
			            <option title="Excel" value="Excel">Excel</option>
			          </select>          
			          &nbsp;&nbsp;<input type="button" name="Export" id="Export" value="Export" onClick="window.open('report.php?<?=$querystr?>&export='+document.getElementById('export').value,'')"/>
			          &nbsp;&nbsp;<input type="submit" name="Collapse" id="Collapse" value="<?=$collapse?>" onClick="changeValue(this)"/>
			          <input type="hidden" name="chkCollapse" id="chkCollapse" value="<?=$collapse?>"/>
			          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
			          &nbsp;&nbsp;Branch:&nbsp;&nbsp;
						<?=$obj->makeListbox("branchid","all_bl_branchinfo","branch_name","branch_id",$branch,0,"branch_name","branch_active","1","branch_name!='All'")?>
			          &nbsp;&nbsp;City:&nbsp;&nbsp;
						<?=$obj->makeListbox("cityid","all_al_city","city_name","city_id",$cityid,0,"city_name")?>
			          <!--
			          &nbsp;&nbsp;<input type="checkbox" name="percent" id="percent" value="checked" onClick="this.form.submit()" <?=$percentchk?>/> 
			          &nbsp;&nbsp;percent
			          &nbsp;&nbsp;<input type="checkbox" name="showall" id="showall" value="checked" onClick="this.form.submit()" <?=$showallchk?>/> 
			          &nbsp;&nbsp;show all
			          -->
					  </td>
			        </tr>
			      <tr bgcolor="#999999">
			        <td height="1" colspan="2" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
			      </tr>
    		</table>
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
</form> 
	<div class="hiddenbar"><img id="spLine" src="/images/bar_close.gif" alt="" width="6px" height="60px" onClick="hiddenLeftFrame('/images')"/></div>
</body>
</html>