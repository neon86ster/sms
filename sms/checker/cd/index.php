<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();
$obj->setDebugStatus(false);


if($obj->getParameter("date")){
	$date = $obj->getParameter("date");
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}else{
	$date = 1;
	$begin = $obj->getParameter("begin"); $end = $obj->getParameter("end");
}
if(!$obj->getParameter("begin")&&!$obj->getParameter("end")){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}
$page = $obj->getParameter("page",1,1);
$cdfunc = $obj->getParameter("cdfunc",3,1);
if($obj->getParameter("date")==2){
	$hidden_begin = $obj->getParameter("hidden_begin");
	$hidden_end = $obj->getParameter("hidden_end");
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
$errormsg = ""; $successmsg = "";
if(isset($_REQUEST["insert"])&&$_REQUEST["insert"]=="add"&&$chkPageEdit){
	$cmsid = $obj->getParameter("cmsid");
	$cmsEnvdatepu = $obj->getParameter("dispdate");
	$cmsGofst_id = $obj->getParameter("gaveby");
	if($cmsEnvdatepu>0 && $cmsGofst_id>0){
		$cmseid=$obj->updatedisp($cmsEnvdatepu,$cmsGofst_id,$cmsid);
		if($cmseid){$successmsg = "Update Success !!";}
		else{$errormsg = "Update Commission Payment Faild, Please try again later!!";}
	}
	
}
$search = $obj->getParameter("where",false);
$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&cdfunc=$cdfunc&date=$date&where='+document.getElementById('search').value.replace(/\+/g,'%2B')+'";
$querystr = htmlspecialchars($querystr);
$print = "report.php?$querystr";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=(isset($pageinfo["pagename"]))?$pageinfo["pagename"]:""?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script type="text/javascript" src="/scripts/date-functions.js"></script>
<script type="text/javascript" src="/scripts/datechooser.js"></script>
  <?include("$root/jsdetect.php");?>
<link href="/css/styles.css" rel="stylesheet" type="text/css">

<!--[if IE]>
<style>
td.rheader select.ctrDropDown{
    width:115px;
    font-size:12px;
}
td.rheader select.ctrDropDownClick{
    font-size:12px;

    width:auto;

}
td.rheader select.plainDropDown{
    width:115px;
    font-size:12px;
}
</style>
<![endif]-->

</head>
<body onLoad="getReturnText('report.php','<?=$querystr?>&page=<?=$page?>','tableDisplay');">
<div id="loading" style="height:110%;">

<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form id="cd" name=="cd" action='index.php' method='post' style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
 <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="99px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="right" height="49">
				<?include("$root/menuheader.php");?>	 	</td>
	  </tr>
  	  <tr>
	    <td valign="top" align="center" height="10">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
			     <td class="rheader" style="padding-left: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        Dates:<input type="hidden" id="pageid" name="pageid" value="<?=$pageid?>"/>
			        <span style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
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
			        </td>
			        <td class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
			        &nbsp;From: <input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
			        <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
			        To: <input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
			       <img align="top" src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px;" align="center"></div>
			        <input type="submit" name="Refresh" id="Refresh" value="Refresh" />
			        </td>
        			<td class="rheader" height="30" align="right" style="padding-right: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Commission Dispersed:
						<select id="cdfunc" name="cdfunc">
							<option title="All" value="1" <?=($cdfunc=="1")?"selected":""?>>All</option>
			              	<option title="Picked Up" value="2" <?=($cdfunc=="2")?"selected":""?>>Picked Up</option>
			              	<option title="Not Picked Up" value="3" <?=($cdfunc=="3")?"selected":""?>>Not Picked Up</option>
						</select>
					</td>
      			  </tr>
			      <tr bgcolor="#999999">
			        <td height="1" colspan="3" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
			      </tr>
    			</table>  
    		</td>
  		</tr>
  		<tr>
    	 <td valign="top" height="20px">
    		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
        			<td height="30" class="rheader" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				       <!--
				          Export:&nbsp;&nbsp;
				         <select id="export" name="export">
				            <option title="PDF" value="PDF">PDF</option>
				            <option title="Excel" value="Excel">Excel</option>
				          </select>          
				          &nbsp;&nbsp;<input type="submit" name="Export" id="Export" value="Export" />
				          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
				          &nbsp;&nbsp;<span id="errormsg" class="style1" ><?=$errormsg?></span><span style='color:#3875d7'><?=$successmsg?></span>
				          </td>
				          <td bgcolor="#F1F3F5" height="30" align="right" style="padding-right:30px"> 
				              -->
					       <input type="text" name="search" id="search" <?=(isset($_GET["where"])&&$_GET["where"])?"value='".$_GET["where"]."'":""?> title="Search by Booking Person, BP Phone, Customer Name and Envelope Number."/>
					       <a href="javascript:;" onClick="getReturnText('report.php','begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&date=<?=$date?>&cdfunc=<?=$cdfunc?>&where='+document.getElementById('search').value,'tableDisplay');" title="Search by Booking Person, BP Phone, Customer Name and Envelope Number.">
					        <img src="/images/btn_search_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
					        <a href="javascript:;" onClick="getReturnText('report.php','<?=$querystr?>&page=<?=$page?>','tableDisplay');" title="Search by Booking Person, BP Phone, Customer Name and Envelope Number.">Search</a> &nbsp;
					        <a href="javascript:;" onClick="document.getElementById('search').value='';getReturnText('report.php','<?=$querystr?>&page=<?=$page?>','tableDisplay');" title="View all commissions disbursed.">
					        <img src="/images/btn_show-all_bg.gif" alt="search" width="16" height="16" border="0"/></a>&nbsp; 
					        <a href="javascript:;" onClick="document.getElementById('search').value='';getReturnText('report.php','<?=$querystr?>&page=<?=$page?>','tableDisplay');" title="View all commissions disbursed.">View All</a>
					        &nbsp;&nbsp;<span class="errormsg"><? if($errormsg!=""){ ?><img src="/images/errormsg.png" /><? } ?>&nbsp;
				           <b class="errormsg"><?=$errormsg?></b></span>
				           <span style='color:#3875d7'><? if($successmsg!=""){ ?><img src="/images/successmsg.png" /><? } ?>&nbsp;<b class="successmsg"><?=$successmsg?></b></span>
						</td>
			        </tr>
				    <tr bgcolor="#999999">
				        <td colspan="2" height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
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