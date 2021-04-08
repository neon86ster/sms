<?php
// Created on Feb 19, 2009
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("inventory.inc.php");

$obj = new inventory();

$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");

$categoryid = $obj->getParameter("categoryid");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$branch = $obj->getParameter("branchid",false);
$trmid = $obj->getParameter("trmid",false);
$total=0;
if($categoryid==6){
	$sql_s = "select d_indivi_info.book_id,bl_branchinfo.branch_name," .
			"a_bookinginfo.b_appt_date,db_trm_category.trm_category_name, " .
			"'-' as trm_name,count(d_indivi_info.book_id) as total " .
			"from d_indivi_info,a_bookinginfo,bl_branchinfo,db_trm_category where " .
			 "d_indivi_info.book_id=a_bookinginfo.book_id and d_indivi_info.stream=1 " .
			 "and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id " .
			 "and db_trm_category.trm_category_id=$categoryid"." ";
	$sql_s .= "and a_bookinginfo.b_appt_date>='".$begin_date."' and a_bookinginfo.b_appt_date<='".$end_date."' ";
	if($branch){
		$sql_s.="and a_bookinginfo.b_branch_id=$branch"." ";
	}
	$sql_s .="group by d_indivi_info.book_id";
	$rs = $obj->getResult($sql_s);
}else{
$rs = $obj->getinventory($begin_date,$end_date,$branch,$categoryid,$trmid);
}
$rscategory = $obj->gettrmcategory($order,$sort,$categoryid);

$reportname = "Inventory Report Detail";
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
		<td width="30%"></td><td width="20%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="6">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Treatment Type</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Treatment Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;$vistotal=0;$restotal=0;$unktotal=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="6" >
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
		<td width="15%"></td><td width="15%"></td>
		<td width="15%"></td><td width="15%"></td>
		<td width="30%"></td><td width="10%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="6" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Treatment Type</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Treatment Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
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
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";}
}
$total += $rs[$i]["total"];
?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["trm_category_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["trm_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["total"]?>&nbsp;</td>
			</tr>
<?
}
?>
 	<tr height="20">
 			<td colspan="6">&nbsp;</td>
 	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Total Treatment : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($total,0,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="6" ><br>
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