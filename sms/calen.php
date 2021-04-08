<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("checker.inc.php");
$obj = new report();
$cobj = new checker();
$obj->setDebugStatus(false);


$date = $obj->getParameter("date",false);
if($date){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}else{
	$date = 1;
	$begin = $obj->getParameter("begin",$obj->getBegin($date,$sdateformat)); 
	$end = $obj->getParameter("end",$obj->getEnd($date,$sdateformat));
}
$branch = $obj->getParameter("branchid",false);

if($date==2){
	$hidden_begin = $obj->getParameter("hidden_begin");
	$hidden_end = $obj->getParameter("hidden_end");
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
$bpid = $obj->getParameter("bpid",1);
$pu_func = $obj->getParameter("pu_func",1);
$insert = $obj->getParameter("insert",false);
$errormsg = ""; $successmsg = "";
if($insert=="add" && $chkPageEdit){
	$cmsid = $obj->getParameter("cmsid");
	$cmsprice = $obj->getParameter("cmsprice");
	$cmsenvnumber = $obj->getParameter("cmsEnvnumber");
	if($cmsenvnumber>0 && $cmsprice>=0){
		$cmseid=$cobj->updateenvl($cmsenvnumber,$cmsprice,$cmsid);
		if($cmseid){$successmsg = "Update Success !!";}
		else{$errormsg = "Update Commission Envelopment Number Fail, Please try again later!!";}
	}
}
$cmsEnvnumber =$obj->getParameter("cmsEnvnumber");
$cmsprice = $obj->getParameter("cms");
$cmsid = $obj->getParameter("cmsid");
if($obj->getParameter("checkaddall")=="addenv" && $chkPageEdit){
	$chkupdate = 1;
	for($i=0;$i<count($cmsEnvnumber);$i++){
		if($cmsEnvnumber[$i]!=""){
			$cmseid=$cobj->updateenvl($cmsEnvnumber[$i],$cmsprice[$i],$cmsid[$i]);
			if(!$cmseid){$chkupdate=0;}
		}
	}
	if($chkupdate){$successmsg = "Update Success !!";}
	else{
		$errormsg = "Update Commission Envelopment Number Fail, Please try again later!!";
		for($i=0;$i<count($cmsEnvnumber);$i++){
			$cmseid=$cobj->updateenvl(0,0,$cmsid[$i]);
		}
	}
}
//undefined index
if(!isset($_SESSION["__user_id"])){$_SESSION["__user_id"]="";}
$ubranch_id = $obj->getIdToText($_SESSION["__user_id"],"s_user","branch_id","u_id");
$ubranch_name = strtolower($obj->getIdToText($ubranch_id,"bl_branchinfo","branch_name","branch_id"));
$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&bpid=$bpid";
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
<body onLoad="getReturnText('report.php','<?=$querystr?>&pu_func=<?=$pu_func?>','tableDisplay');">
<div id="loading">

<table cellspacing="0" cellpadding="0" class="preloading">
<tr>
    <td align="center" valign="middle">
		<img src="/images/sms preload.png">
	</td>
</tr>
</table>
</div> 
<form id="cen" name="cen" action='index.php' method='post' style="padding:0;margin:0">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
 <tr>
    <td width="8" height="100%" align="center" rowspan="4" class="hidden_bar">&nbsp;</td>
  <tr>
    <td height="99px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
				<?include("$root/menuheader.php");?>
	 	</td>
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
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
			        </td>
        			<td class="rheader" height="30" align="right" style="padding-right: 20px; background-image: url('/images/<?=$theme?>/appt/menubg.png');">
				        Booking Company:
						<span style="width: 115px;font-family:Tahoma; font-size: 12px;overflow:hidden;">
						<?=$obj->makeListbox("bpid","al_bookparty","bp_name","bp_id",$bpid,0,"bp_name","bp_active","1")?>
						</span>
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
				              -->
				          <?if($chkPageEdit){?>
				          <input type="button" name="add" id="add" value="Add All" onClick="addallEnv();" />
				          <input type="hidden" id="checkaddall" name="checkaddall" value="non">
				          <?}?>
				           <input type="submit" name="Refresh" id="Refresh" value="Refresh" />
				           <span id="errormsg" class="style1" ><? if($errormsg!=""){ ?><img src="/images/errormsg.png" /><? } ?>&nbsp;
				           <b class="errormsg"><?=$errormsg?></b></span>
				           <span style='color:#3875d7' id="successmsg"><? if($successmsg!=""){ ?><img src="/images/successmsg.png" /><? } ?>&nbsp;
				           <b class="successmsg"><?=$successmsg?></b></span>
         	 			</td>
        			</tr>
				    <tr bgcolor="#999999">
				        <td height="1" bgcolor="<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
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
</html>s