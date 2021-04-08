<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("csi.inc.php");
$obj = new csi();
$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");

$branch = $obj->getParameter("branchid",1);
$recid = $obj->getParameter("recid",0);
$today = date("Ymd");

$rs = $obj->getrecreport($begin_date,$end_date,$branch,$recid);

$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);

$branchname=$obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
if($branch==0 || strtolower($branchname)=="all"){
	$reportname = "All Customer Recommendation Report Detail";
}else{
	$reportname = $branchname."'s Customer Recommendation Report Detail";
}
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
$chkrow = $obj->getParameter("chkrow",40);
if($export!="Excel"&&$export){
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Time</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Recommendation</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if(!$chkrow){$chkrow=1;}
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
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
		<td width="12%"></td><td width="8%"></td>
		<td width="15%"></td><td width="15%"></td>
		<td width="30%"></td><td width="20%"></td>
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Time</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Recommendation</b></td>
	</tr>
<?	
}	
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$rs[$i]["bpds_id"];

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
$csname = $rs[$i]["cs_name"];

?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["time_start"]?>&nbsp;</td>
					<td class="report" align="left"><?=$csname?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:10px"><?=$obj->checkParameter($rs[$i]["rec_name"],"No Recommend")?>&nbsp;</td>
			</tr>
<?
}
?>
 	<tr height="20">
 			<td colspan="6">&nbsp;</td>
 	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Total Customers : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($rs["rows"],0,".",",")?></b></td>
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