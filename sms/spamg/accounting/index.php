<?
ini_set("memory_limit","-1");
?>
<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
$obj->setDebugStatus(false);
$obj = new report();

$date = $obj->getParameter("date");
$begin = "";
$end = "";
if($date){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}else{
	$date = 16;
	$begin = $obj->getParameter("begin",""); 
	$end = $obj->getParameter("end","");
}
if(!$begin&&!$end){
	$begin = $obj->getBegin($date,$sdateformat);
	$end = $obj->getEnd($date,$sdateformat);
}

$branch = $obj->getParameter("branchid",5);
$payid = $obj->getParameter("pay_id",false);

if(is_numeric($branch)&&$branch>0){$branch=$branch;}else{$branch=5;}
$acc_func = $obj->getParameter("acc_func",1);
if(is_numeric($acc_func)&&$acc_func>0){$acc_func=$acc_func;}else{$acc_func=1;}
if($date==2){
	$hidden_begin = $obj->getParameter("hidden_begin",date("Ymd"));
	$hidden_end = $obj->getParameter("hidden_end",date("Ymd"));
}else{
	$hidden_begin = $dateobj->convertdate($begin,$sdateformat,"Ymd");
	$hidden_end = $dateobj->convertdate($end,$sdateformat,"Ymd");
}
$ubranch_id = $obj->getIdToText($_SESSION["__user_id"],"s_user","branch_id","u_id");
$ubranch_name = strtolower($obj->getIdToText($ubranch_id,"bl_branchinfo","branch_name","branch_id"));
if($ubranch_name!="all"){
	$branch = $ubranch_id;
}
$querystr = "pageid=$pageid&begin=$hidden_begin&end=$hidden_end&branchid=$branch&acc_func=$acc_func&payid=$payid";
$print = "report.php?$querystr&export=print";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<script type="text/javascript" src="/scripts/date-functions.js"></script>
<script type="text/javascript" src="/scripts/datechooser.js"></script>
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
<form name="account" id="account" action="" method="post" style="padding:0;margin:0">
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
    		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			      <tr>
			        <td class="rheader" height="30" style="padding-left:10px;">
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
			        </td><td class="rheader" style="padding-right:0px;">
     				From:<input id='begin' name='begin' value="<?=$begin?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_begin" name="hidden_begin" value="<?=$hidden_begin?>"/>
			        <img src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'begin', 'date_begin', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_begin" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"> </div>
			        To:<input id='end' name='end' value="<?=$end?>" readonly="1" class="textbox" type="text" style="width:85px"/>
			        <input type="hidden" id="hidden_end" name="hidden_end" value="<?=$hidden_end?>"/>
			        <img src="/images/calendar.png" alt="Date Appointment" onClick="showChooser(this, 'end', 'date_end', 1900, 2100, '<?=$sdateformat?>', false,false);" />
			        <div id="date_end" class="dateChooser" style="display: none; visibility: hidden;background: #aea; padding-top: 5px; padding: 5 0 0 0;" align="center"></div>
			        </td>
			        <td class="rheader" align="left" style="padding-right:0px;">
					    Branch:<?=$obj->makeListbox("branchid","bl_branchinfo","branch_name","branch_id",$branch,true,"branch_name","branch_active","1","branch_name!='All'")?>
						&nbsp;&nbsp;Method of Payment:
						<?=$obj->makeListbox("pay_id","all_l_paytype","pay_name","pay_id",$payid,0,"pay_name","pay_active","1","")?>
						&nbsp;&nbsp;Accounting Function:
				        <!--<select id="acc_func" name="acc_func" onChange="this.form.submit()">-->
						<select id="acc_func" name="acc_func">
				          <option title="All Sales" value="1" <?=($acc_func=="1")?"selected":""?>>All Sales</option>
				          <option title="Selected Booking" value="2" <?=($acc_func=="2")?"selected":""?>>Selected Booking</option>
				        </select>
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
	 	<? if($chkPageEdit){?>
          Start New Sales Receipt Number:&nbsp;&nbsp;
          <input type="text" name="st_srnum" id="st_srnum" size="8" />
          &nbsp;&nbsp;<input type="button" name="new_srnumber" id="new_srnumber" value="Make Sales Receipt Number" onClick="make_srnum(document.getElementById('st_srnum').value);" />
          &nbsp;&nbsp;
          <?}?>
          &nbsp;&nbsp;<input type="button" name="p_sr" id="p_sr" value="Print Sales Receipt" onClick="window.open('abb_invoice.php?begin=<?=$hidden_begin?>&end=<?=$hidden_end?>&branchid=<?=$branch?>','p_cnx','resizable=1,menubar=1,scrollbars=1,status=1,width=285,height=700');" />
          &nbsp;&nbsp;<input type="submit" name="Refresh" id="Refresh" value="Refresh" />
		  <br/><span id="errormsg" class="style1" ></span>
        </td>
    	<td align="right" height="30px" class="rheader" style="padding-right: 20px;">
    	  <input type="text" name="new_srnum" id="new_srnum" size="8" />
          &nbsp;&nbsp;<input type="button" name="b_srnumber" id="b_srnumber" value="  Print Receipt " onClick="window.open('abb_invoice.php?pagenum='+document.getElementById('new_srnum').value,'p_cnx','resizable=1,menubar=1,scrollbars=1,status=1,width=285,height=700');"/>
		  &nbsp;&nbsp;<input type="button" name="b_srnumber" id="b_srnumber" value="  Print Report " onClick="window.open('<?=$print?>','','resizable=yes,menubar=no,scrollbars=yes')"/>
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