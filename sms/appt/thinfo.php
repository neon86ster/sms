<? 
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("therapist.inc.php");
$thobj = new therapist(); 

$thobj->setDebugStatus(false);
$errormsg = "";
$successmsg = "";
$branchid = $thobj->getParameter("branchid");
$date = $thobj->getParameter("date",date('Ymd'));

///////////// End check user is admin or stay in branch ////////////////////
$sql = "select bl_th_list.th_id,l_employee.emp_code," .
		"l_employee.emp_nickname,l_employee.branch_id,bl_th_list.th_list_id,bl_th_list.leave " .
				"from bl_th_list,l_employee " .
				"where bl_th_list.th_id=l_employee.emp_id " .
				"and bl_th_list.branch_id=$branchid " .
				"and DATE_FORMAT(bl_th_list.l_lu_date,\"%Y%m%d\")=\"".$date."\"" .
				"order by bl_th_list.leave asc,bl_th_list.queue_order";
				//"and bl_th_list.leave=0 ";			// add bl_th_list.leave=0 for show only therapist who isn't leave, natt

$thrs = $thobj->getResult($sql);

if($thrs["rows"]){
for($i=0;$i<$thrs["rows"];$i++){
	$bl_list[$i] = $thrs[$i]["th_id"];
}
	$notin_list = implode(",", $bl_list); 
$sql_gth="select distinct da_mult_th.therapist_id as th_id,l_employee.emp_code," .
		"l_employee.emp_nickname,l_employee.branch_id,(0) as th_list_id,(0) as 'leave'" .
		"from da_mult_th,a_bookinginfo,l_employee " .
		"where da_mult_th.therapist_id=l_employee.emp_id " .
		"and DATE_FORMAT(a_bookinginfo.b_appt_date,\"%Y%m%d\")=\"".$date."\"" .
		"and a_bookinginfo.b_branch_id=$branchid " .
		"and a_bookinginfo.b_set_cancel=0 " .
		"and da_mult_th.book_id=a_bookinginfo.book_id " .
		"and da_mult_th.therapist_id<>1 " .
		"and da_mult_th.therapist_id not in " .
		"($notin_list)";

$gth = $thobj->getResult($sql_gth);

$sql_rgth="select da_mult_th.therapist_id,da_mult_th.start_id as b_appt_time_id,da_mult_th.end_id as time_end " .
		"from da_mult_th,a_bookinginfo " .
		"where DATE_FORMAT(a_bookinginfo.b_appt_date,\"%Y%m%d\")=\"".$date."\"" .
		"and a_bookinginfo.b_branch_id=$branchid " .
		"and a_bookinginfo.b_set_cancel=0 " .
		"and da_mult_th.book_id=a_bookinginfo.book_id " .
		"and da_mult_th.therapist_id<>1 " .
		"and da_mult_th.therapist_id not in " .
		"($notin_list)";

$rgth = $thobj->getResult($sql_rgth);
}
$th_signin = $thrs["rows"]+0;
$th_shiftone = $thobj->getIdToText("$branchid","bl_th_available","th_shiftone","branch_id","1 order by l_lu_date desc")+0;
$th_shifttwo = $thobj->getIdToText("$branchid","bl_th_available","th_shifttwo","branch_id","1 order by l_lu_date desc")+0;

// ################ branch parameter ###############
$starttime_id = $thobj->getIdToText($branchid,"bl_branchinfo","start_time_id","branch_id");
$closetime_id = $thobj->getIdToText($branchid,"bl_branchinfo","close_time_id","branch_id");
if($starttime_id%12!=1){
	$starttime_id = $starttime_id - $starttime_id%12 + 1;
}

// ################ time line of booking #########
$tp_id = $thobj->getIdToText("1","a_company_info","tp_id","company_id");
$chksql = "select * from l_timeperiod where tp_id=$tp_id";
$timeperiodrs = $thobj->getResult($chksql);
$time_period = $timeperiodrs[0]["tp_name"];
$time_period_distance = $timeperiodrs[0]["tp_distance"];
$chksql = "select * from p_timer where time_id between $starttime_id and $closetime_id";
$timeline = $thobj->getResult($chksql); // get time to use in appointment
$count_time = $timeline["rows"]; // count all record to use in first column
	
// system's time period
$sql = "select * from p_timer";
$timeperiodrs = $thobj->getResult($sql);
$timeperiod = array();
for($i=0;$i<$timeperiodrs["rows"];$i++){
		$timeperiod[$timeperiodrs[$i]["time_id"]] = $timeperiodrs[$i]["time_start"];
}
	
// system's hour period
$sql = "select * from l_hour";
$hourperiodrs = $thobj->getResult($sql);
$hourperiod = array();
for($i=0;$i<$hourperiodrs["rows"];$i++){
		$hourperiod[$hourperiodrs[$i]["hour_id"]] = $hourperiodrs[$i]["hour_name"];
}
// therapist working today
$rs = $thobj->getThQueue($branchid,$date);
for($i=0;$i<$rs["rows"];$i++){
	$rs[$i]["time_start"] = $obj->chkBlockTimeStart($rs[$i]["b_appt_time_id"],6,$timeline);
	$hr[0]=$rs[$i]["therapist_hour"];
	
	////Edit App Time to real
	//if(!isset($rs[$i-1]["indivi_id"])){$rs[$i-1]["indivi_id"]="";}
	//if($rs[$i-1]["indivi_id"]==$rs[$i]["indivi_id"]){
	//	$rs[$i]["b_appt_time_id"]=$rs[$i-1]["time_end"];
	//}
	////
	//$endtime = $obj->chkBlockTimeEnd($hr,$rs[$i]["b_appt_time_id"],$hourperiod,$timeline);
	//$rs[$i]["time_end"] = $endtime[0];
	$rs[$i]["time_end"]=$rs[$i]["end_id"];
}

$pageinfo["pageid"]="1";

$pageinfo = $object->get_pageinfo(1,$permissionrs);

$i = count($pageinfo["parent"]);
if(isset($pageinfo["pageurl"])){
$pageinfo["parenturl"][$i] = $pageinfo["pageurl"];
}
if(isset($pageinfo["pagename"])){
$pageinfo["parent"][$i] = $pageinfo["pagename"];
}
$pageinfo["pagename"] = "Therapist Queue";

$pagename = "currentcust.php";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
 $browser = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    if ($browser == true){
    	$browser = 'iphone';
  	?>
  	<meta name="viewport" content="width=device-width,minimum-scale=1.0, maximum-scale=1.0" />
  	<?
  	}
?>
<title><?=$pageinfo["pagename"]?></title>
<script type="text/javascript" src="scripts/ajax.js"></script>
<script type="text/javascript" src="scripts/tooltip/boxover.js"></script>
<link href="/css/styles.css" rel="stylesheet" type="text/css">
</head>
<body> 
<form name="appointment" id="appointment" action="" method="get">
<table class="main" cellspacing="0" cellpadding="0" width="100%">
    <td height="85px" valign="top">
<div id="header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainheader">
      <tr>
	    <td valign="top" align="center" height="49">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="leftmenu">
				<tr>
					<td height="47" style="background-image: url('/images/<?=$theme?>/header.png');">
									 <table height="47" border="0" cellpadding="0" cellspacing="0" style='overflow:auto;' class="dir">
								         <tbody>
									         <tr><td>
									         <? for($i=0;$i<count($pageinfo["parent"]);$i++){ ?>
									         <a href="javascript:;" target="mainFrame"><?=$pageinfo["parent"][$i]?> &gt</a>
									         <? } ?>
									          </td></tr>
									         <tr><td><b><?=$pageinfo["pagename"]?></b></td></tr>
								         </tbody>
									 </table>
			 			<?if(!isset($parent)){$parent="";}?>
			 			<input type="hidden" id="parent" name="parent" value="<?=$parent?>">
					</td>
				</tr>
				<tr>
					<td height="2" background="#eae8e8"><img src="/images/blank.gif" height="2px"></td>
				</tr>
			</table>
	 	</td>
	  </tr>
		<tr>
    	 <td valign="center" height="20px" style="padding-left: 20px;background-image: url('/images/<?=$theme?>/appt/menubg.png');">
    		<table border="0" cellspacing="0" cellpadding="0">
<?
//Get Start-End Time
$apptdate = $date;
$appttime = $obj->getParameter("time",date("H:i:00"));

if($obj->getParameter("time")){
	$appthour = $obj->getParameter("hour",date("00:$time_period")); 
}else{
$appthour = $obj->getParameter("hour",date("00:01")); 
}

list($hr,$min,$sec) = explode(":",$appttime);
list($phr,$pmin) = explode(":",$appthour);
$hr += $phr;
$min += $pmin;
$endtime = date("H:i:00",mktime($hr,$min,$sec,0,0,0));

?>
			      <tr>
        			<td height="30" class="rheader" style="background-image: url('/images/<?=$theme?>/appt/menubg.png');">
						&nbsp;&nbsp;Branch:&nbsp;&nbsp;&nbsp;<?=$obj->getIdToText($branchid,"bl_branchinfo","branch_name","branch_id")?>
						&nbsp;&nbsp;Date:&nbsp;&nbsp;&nbsp;<?=$dateobj->convertdate($date,'Ymd',$sdateformat)?>
						<?if($obj->getParameter("time")){?>&nbsp;&nbsp;Time:&nbsp;<?="<font color=red>".$appttime."-".$endtime."</font>"?>&nbsp;&nbsp;&nbsp;<?}?>
						&nbsp;&nbsp;TH sign-in:&nbsp;&nbsp;&nbsp;&nbsp;<?=$th_signin?>
						&nbsp;&nbsp;SCH:&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="hidden" id="bid" name="bid" value="<?=$branchid?>">
				        <?=$th_shiftone?> / <?=$th_shifttwo?>
						<div id="therapistavi" class="bg" style="display:none;"></div>
				    </td>
			        </tr>
    			</table>    
    	  </td>
  	   </tr>
				    <tr>
				        <td height="1" style="background:<?=$fontcolor?>"><img src="/images/blank.gif" width="1" height="1" /></td>
				    </tr>
</table> 
</div>
  	</td>
  </tr>
  <tr>  		
  	<td valign="top" style="margin-top:0px;margin-left:0px;padding-left:0px;">
			<div id="tableDisplay">
 <!-- begin div tableDisplay -->
<?/*if($obj->getParameter("type")!="chk_all"){*/?>			
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%" class="generalinfo">
				<tr height="32">
<?

//Check therapist from other branch in same city
$display=false;
for($i=0;$i<$thrs["rows"];$i++){
	if($branchid!=$thrs[$i]["branch_id"]){
		$display=true;
		break;
	}
}
if(!isset($gth["rows"])){$gth["rows"]="";}
for($i=0;$i<$gth["rows"];$i++){
	if($branchid!=$gth[$i]["branch_id"]){
		$display=true;
		break;
	}
}

$cnt=0;
if($display){
		//$arrFieldsname = array("Order","Therapist Name","Branch","Available",
		$arrFieldsname = array("Order","Therapist Name","Available",
				"Appointment Time");
				$cnt=5;
$chkarrFields = array("queue_order","emp_nickname","branch_id","available",
				"appt_time");
}else{
$arrFieldsname = array("Order","Therapist Name","Available",
				"Appointment Time");
				$cnt=4;
$chkarrFields = array("queue_order","emp_nickname","available",
				"appt_time");
}		

				
//start field name generate
for($i=0;$i<$cnt;$i++){
	if(!isset($arrFieldsname[$i])){$arrFieldsname[$i]="";}
		if($arrFieldsname[$i]=="Order"){ 
			$style = "background-color:#88afbe;" .
					  "background-image: url('/images/arrow_down.png');" .
					  "border-bottom: 3px solid #eae8e8";
		}else{
		 	$style = "background-color:#a8c2cb;";
		}
?>
					<td style="text-align:center;<?=$style?>" class="pagelink">
					<b><?=$arrFieldsname[$i]?></b>
					</td>
<? 	
} 
?>	
				</tr>
<?
//end field name generate
//start field element generate
$data = "&nbsp;";
for($i=0;$i<$thrs["rows"];$i++){
	$trclass = ($i%2==0)?"odd":"even";
	if($thrs[$i]["leave"]==1){
			$csstatus = "paidconfirm";
	}else{
			$csstatus = "$trclass";
	}
// check therapist available at this appointment time

$available = "<b style='color:#ff0000;'>n/a</b>";
if($apptdate==date('Ymd')){
		$available = $thobj->chkThAvailable($rs,$thrs[$i]["th_id"],$appttime,$endtime,$timeperiod);
}
$thblock = $obj->getParameter("thblock");

?>
	<tr class="<?=$csstatus?>" height="20">
			<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;"><?=$i+1?>&nbsp;</td>
			<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;">
			<? if($thblock){ ?>
			<a href="javascript:;;" onClick="thChoose('<?=$thblock?>','<?=$thrs[$i]["th_id"]?>')">
			<? }?>
			<?=$thrs[$i]["emp_code"]." ".$thrs[$i]["emp_nickname"]?>
			<? if($thblock){ ?></a><? } ?>
			&nbsp;</td>
			
			<?if($display){?>
			<!--<td align="center" height="21px" class="report" style="border-bottom:1px #eaeaea solid;"><?=$obj->getIdToText($thrs[$i]["branch_id"],"bl_branchinfo","branch_name","branch_id")?></td>-->
			<?}?>
			
			<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;"><?=$available?>&nbsp;</td>
			<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;">
			<?=$thobj->chkThTime($rs,$thrs[$i]["th_id"],$timeperiod)?>
			&nbsp;</td>
	</tr>
<?
}

for($i=0;$i<$gth["rows"];$i++){
	//$trclass = ($i%2==0)?"odd":"even";
	//if($thrs[$i]["leave"]==1){
			//$csstatus = "paidconfirm";
	//}else{
	//		$csstatus = "$trclass";
	//}
// check therapist available at this appointment time
$available = "<b style='color:#ff0000;'>n/a</b>";
if($apptdate==date('Ymd')){
	$available = $thobj->chkThAvailable($rgth,$gth[$i]["th_id"],$appttime,$endtime,$timeperiod);
}
$thblock = $obj->getParameter("thblock");
?>
		<tr bgcolor="#eaf7cc" height="20">
					<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;">&nbsp;</td>
					<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;">
					<? if($thblock){ ?>
					<a href="javascript:;;" onClick="thChoose('<?=$thblock?>','<?=$gth[$i]["th_id"]?>')">
					<? }?>
					<?=$gth[$i]["emp_code"]." ".$gth[$i]["emp_nickname"]?>
					<? if($thblock){ ?></a><? } ?>
					&nbsp;</td>
					
					<?if($display){?>
					<!--<td align="center" height="21px" class="report" style="border-bottom:1px #eaeaea solid;"><?=$obj->getIdToText($gth[$i]["branch_id"],"bl_branchinfo","branch_name","branch_id")?></td>-->
					<?}?>
					
					<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;"><?=$available?>&nbsp;</td>
					<td height="21px" class="report" style="border-bottom:1px #eaeaea solid;">
					<?=$thobj->chkThTime($rgth,$gth[$i]["th_id"],$timeperiod)?>
					&nbsp;</td>
			</tr>
<?}?>
 			</table><br/>
		</td>
    </tr>
</table>
<?/*}*/?>	
			
 <!-- end div tableDisplay -->			
			</div>
		</td>
   </tr>
</table>
</form> 
</body>
</html>