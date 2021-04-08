<?php
// Created on Feb 19, 2009
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("customer.inc.php");
$obj = new customer();


$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");
$beginage = $obj->getParameter("beginage");
$endage = $obj->getParameter("endage");
$branchid = $obj->getParameter("branchid");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$chkrow="";
$rs=$obj->getcusperagedetail($begin_date,$end_date,$beginage,$endage,$branchid);
//$rsavg = $obj->getavgage($begin_date,$end_date);

$reportname = "Age of Customer Detail";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"$reportname.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",40);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="12%"></td><td width="8%"></td>
		<td width="15%"></td><td width="15%"></td>
		<td width="30%"></td><td width="10%"></td>
		<td width="10%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="7">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Time</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Age</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Gender</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;$malecnt=0;$femalecnt=0;$maleagecnt=0;$femaleagecnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if(!$chkrow){$chkrow=1;}
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="7" >
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
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
		<td width="12%"></td><td width="8%"></td>
		<td width="15%"></td><td width="15%"></td>
		<td width="30%"></td><td width="10%"></td>
		<td width="10%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="7" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Time</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Age</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Gender</b></td>
	</tr>
<?	
}	
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$rowcnt++;
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
$csname = $rs[$i]["cs_name"];
$csage = $rs[$i]["cs_age"];
$csgender = $rs[$i]["sex_type"];
if($csgender=="Male"){$malecnt++;$maleagecnt+=$csage;}
else if($csgender=="Female"){$femalecnt++;$femaleagecnt+=$csage;}
?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["time_start"]?>&nbsp;</td>
					<td class="report" align="left"><?=$csname?>&nbsp;</td>
					<td class="report" align="center"><?=$csage?>&nbsp;</td>
					<td class="report" align="center"><?=$csgender?>&nbsp;</td>
			</tr>
<?
}
?>
 	<tr height="20">
 			<td colspan="7">&nbsp;</td>
 	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Total Customers : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($rs["rows"],0,".",",")?></b></td>
 			<td>&nbsp;</td>
 			<td>&nbsp;</td>
	</tr>
	<?if($malecnt==0){$malecnt=1;}	// prevent warning divide by zero
	  if($femaleagecnt==0){$femaleagecnt=1;}?>
	<tr height="20">
			<td colspan="4" align="right"><b>AVG Male Age : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($maleagecnt/$malecnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>AVG Female Age : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=($femalecnt==0)?0.00:number_format($femaleagecnt/$femalecnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
 			<td>&nbsp;</td>
	</tr>
	<?
	/*for($i=0; $i<$rsavg["rows"]; $i++) {
	?>
	<tr height="20">
			<td colspan="4" align="right"><b>AVG <?=$rsavg[$i]["sex_type"]?> : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($rsavg[$i]["age"],2,".",",")?></b></td>
 			<td>&nbsp;</td>
 			<td>&nbsp;</td>
	</tr>
	<?
	}*/
	?>
    <tr height="20">
    	<td width="100%" align="center" colspan="7" ><br>
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
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