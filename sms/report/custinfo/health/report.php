<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("customer.inc.php");
$robj = new report();
$custobj = new customer();
$date = $custobj->getParameter("date");
$begindate = $custobj->getParameter("begin");
$enddate= $custobj->getParameter("end");
	
// system's time period
$sql = "select * from p_timer";
$timeperiodrs = $obj->getResult($sql);
$timeperiod = array();
for($i=0;$i<$timeperiodrs["rows"];$i++){
		$timeperiod[$timeperiodrs[$i]["time_id"]] = $timeperiodrs[$i]["time_start"];
}

// system's hour period
$sql = "select * from l_hour";
$hourperiodrs = $obj->getResult($sql);
$hourperiod = array();
for($i=0;$i<$hourperiodrs["rows"];$i++){
		$hourperiod[$hourperiodrs[$i]["hour_id"]] = $hourperiodrs[$i]["hour_name"];
}

$branch_id = $custobj->getParameter("branchid",0);
if($branch_id==""){$branch_id=0;}
$today = date("Ymd");
$custobj->setDebugStatus();
$rs = $custobj->getcusthealthinfo($branch_id,$begindate,$enddate);
//die();
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $custobj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Health Department Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$chkrow=1;	//prevent divice by zero warning
if($export!="Excel"&&$export){
	$chkrow = $custobj->getParameter("chkrow",27);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$branchname = "";
if($branch_id>1){$branchname = $custobj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id");}
$reportname = "$branchname Health Department Report";
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>

<span class="pdffirstpage"/>	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="10%"></td><td width="18%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="17%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" colspan="8" class="reporth">
    		<b>
    		<p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p>
    			<b style='color:#ff0000;'>
    			<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
    			</b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>ลำดับ</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>ชื่อ - สกุล<br>ผู้รับบริการ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>สัญชาติ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>มาจาก</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>บริการที่ได้รับ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>วัน/เืดือน/ปี<br>ที่่รับบริการ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>เวลาที่รับบริการ<br>(เริ่ม - เสร็จสิ้น)</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>ผู้ให้บริการ</b></td>
	</tr>
<?
$total_cms=0;
$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
 	<tr height="20">
 		<td colspan="8">&nbsp;</td>
 	</tr>
	<tr height="20">
    	<td width="100%" align="center" colspan="8" ><br>
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="10%"></td><td width="18%"></td>
		<td width="10%"></td><td width="15%"></td>
		<td width="17%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
	</tr>
	<tr>
    	<td width="100%" class="reporth" align="center" colspan="8">
    		<b>
    		<p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p>
    			<b style='color:#ff0000;'>
    			<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
    			</b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>ลำดับ</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>ชื่อ - สกุล<br>ผู้รับบริการ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>สัญชาติ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>มาจาก</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>บริการที่ได้รับ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>วัน/เืดือน/ปี<br>ที่่รับบริการ</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>เวลาที่รับบริการ<br>(เริ่ม - เสร็จสิ้น)</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;white-space: nowrap;"><b>ผู้ให้บริการ</b></td>
	</tr>
    	
	
<?	
}	
$rowcnt++;

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$rs[$i]["bpds_id"];
if($export!=false){
	$id="<b>$bpdsid</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">$bpdsid</a>";
}	
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
$time_end = $obj->chkBlockTimeEnd($rs[$i]["hour_id"],$rs[$i]["appt_time_id"],$hourperiod);
	?>
		<tr height="20" <?=$bgcolor?>>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["nationality_name"]?>&nbsp;</td>
					<? $rs[$i]["acc_name"] = str_replace("--select--"," ",$rs[$i]["acc_name"]); ?>
					<td class="report" align="center"><?=str_replace("-- select --"," ",$rs[$i]["acc_name"])?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["package_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>		
					<td class="report" align="center"><?=substr($rs[$i]["time_start"],0,5)." - ".substr($timeperiod[max($time_end)],0,5)?>&nbsp;</td>
					<td class="report" align="center"><?=($rs[$i]["therapist_id"]==1)?"-":$rs[$i]["emp_code"]." ".$rs[$i]["emp_nickname"]?>&nbsp;</td>
		</tr>
<?
}
?>
 				<tr height="20">
 					<td colspan="8">&nbsp;</td>
 				</tr>
			    <tr height="20">
			    	<td width="100%" align="center" colspan="8" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>