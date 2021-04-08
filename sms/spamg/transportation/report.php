<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("transport.inc.php");
$obj = new transport();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$city_id = $obj->getParameter("cityid");
$branch_id = $obj->getParameter("branchid");
if($city_id==""){$city_id=0;}
if($branch_id==""){$branch_id=0;}

$export = $obj->getParameter("export");

if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Transportation Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");


}
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}
$rs = $obj->gettrans($branch_id,$city_id,$begin_date,$end_date);
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",20);
	$chkpage = ceil($rs["rows"]/$chkrow);
}

$reportname = "Transportation Report";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><? } ?>
<span class="pdffirstpage"/>
<?if($export&&$export!="Excel"&&$rs["rows"]!=0){
		$all_sc=0; $all_vat=0;	$all_total=0;	
		for($a=0;$a<$chkpage;$a++){
			if(!isset($rowschk["end"][$a-1])){$rowschk["end"][$a-1]="";}
			if($a==0&&$a!=$chkpage-1){$rowschk["begin"][0]=0;$rowschk["end"][0]=$chkrow;}
			else if($a==$chkpage-1){
				$rowschk["begin"][$a]=$rowschk["end"][$a-1];
				$rowschk["end"][$a]=$rs["rows"];
			}else{
				$rowschk["begin"][$a]=$rowschk["end"][$a-1];
				$rowschk["end"][$a]=$rowschk["begin"][$a]+$chkrow;
			}
			$rowschk["begin"][0]=0;
			?>
<? if($a){?><hr style="page-break-before:always;border:0;color:#ffffff;" /><?}?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">	
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
					<td width="5%"></td><td width="6%"></td><td width="8%"></td><td width="6%"></td>
					<td width="8%"></td><td width="8%"></td><td width="6%"></td><td width="9%"></td>
					<td width="13%"></td><td width="7%"></td><td width="6%"></td><td width="9%"></td>
					<td width="7%"></td>
		</tr>
		<tr>
	    	<td class="reporth" width="100%" align="center" colspan="13">
	    		<b><p>Spa Management System</p>
	    		<?=$reportname?></b><br>
	    		<p><b style="color:#ff0000;"><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
	    	</td>
		</tr>
		<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Driver P/U</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>P/U Time</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>P/U Place</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Driver T/B</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T/B Time</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T/B Place</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Reservation</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Agent</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Agent Phone</b></td>
		</tr>
		<?	for($i=$rowschk["begin"][$a]; $i<$rowschk["end"][$a]; $i++) {

if(!isset($rs[$i]["reception_code"])){$rs[$i]["reception_code"]=0;}
if($rs[$i]["reception_code"]>1)
	$reception = $rs[$i]["reception_code"]." ".$rs[$i]["reception_name"];
else
	$reception = "-";



if($i%2==0){
	echo "<tr height=\"20\">\n";
}else{
	echo "<tr bgcolor=\"#eaeaea\" height=\"20\">\n";
}   

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
$pagename = "manageBooking";
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
	?>
					<td class="report" align="center"><?=$id?>&nbsp;</td>
					<td class="report"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$obj->getIdToText($rs[$i]["driver_pu_id"],"l_employee","emp_nickname","emp_id")?>&nbsp;</td>
					<td class="report" align="right"><?=$obj->getIdToText($rs[$i]["pu_time"],"p_timer","time_start","time_id")?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$rs[$i]["pu_place"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$obj->getIdToText($rs[$i]["driver_tb_id"],"l_employee","emp_nickname","emp_id")?>&nbsp;</td>
					<td class="report" align="right"><?=$obj->getIdToText($rs[$i]["tb_time"],"p_timer","time_start","time_id")?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$rs[$i]["tb_place"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$rs[$i]["acc_name"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["b_hotel_room"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$rs[$i]["rsvn_name"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:15px;"><?=$rs[$i]["c_bp_person"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["c_bp_phone"]?>&nbsp;</td>
 				</tr>
 		<?	} ?>
 		<tr height="20">
 					<td colspan="13" height="20">&nbsp;</td>
 		</tr>
	    <tr height="20">
	    	<td align="center" colspan="13">
    			<b>Printed: </b><?=date($ldateformat." H:i:s")?>
	    	</td>
		</tr>
	 </table>
		</td>
	</tr>
</table><? } 
}else{		// export to excel/not export function
	
?>
<table width="100%" border="0" <?($export=="Excel")?"x:str":""?> cellspacing="0" cellpadding="0">
	<tr>
    	<td class="reporth" width="100%" align="center">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style="color:#ff0000;"><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$enddate==$begindate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    	</td>
	</tr>
	<tr>
    	<td class="content" width="100%">
			<table cellspacing="0" border="0" cellpadding="0" width="100%">
				<tr height="32">
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Driver P/U</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>P/U Time</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>P/U Place</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Driver T/B</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T/B Time</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>T/B Place</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Reservation</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Agent</b></td>
					<td style="text-align:center;border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Agent Phone</b></td>
				</tr>
				<?	for($i=0; $i<$rs["rows"]; $i++) {

if(!isset($rs[$i]["reception_code"])){$rs[$i]["reception_code"]=1;}
if($rs[$i]["reception_code"]>1)
	$reception = $rs[$i]["reception_code"]." ".$rs[$i]["reception_name"];
else
	$reception = "-";



if($i%2==1){
	echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\">\n";
}else{
	echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
}   

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
$pagename = "manageBooking";
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
	?>
					<td class="report" align="center"><?=$id?>&nbsp;</td>
					<td class="report"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="right"><?=$obj->getIdToText($rs[$i]["driver_pu_id"],"l_employee","emp_nickname","emp_id")?>&nbsp;</td>
					<td class="report" align="right"><?=$obj->getIdToText($rs[$i]["pu_time"],"p_timer","time_start","time_id")?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["pu_place"]?>&nbsp;</td>
					<td class="report" align="right"><?=$obj->getIdToText($rs[$i]["driver_tb_id"],"l_employee","emp_nickname","emp_id")?>&nbsp;</td>
					<td class="report" align="right"><?=$obj->getIdToText($rs[$i]["tb_time"],"p_timer","time_start","time_id")?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["tb_place"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["acc_name"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["b_hotel_room"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["rsvn_name"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["c_bp_person"]?>&nbsp;</td>
					<td class="report" align="right"><?=$rs[$i]["c_bp_phone"]?>&nbsp;</td>
 				</tr>
 				<?	} ?>
 			</table><br>
		</td>
    </tr>
 	<tr height="20">
 					<td colspan="13" height="20">&nbsp;</td>
 	</tr>
    <tr height="20">
    	<td width="100%" align="center">
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table><br/><br/>
<?	} ?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>