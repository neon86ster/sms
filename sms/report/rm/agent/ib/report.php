<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("rm.inc.php");

$obj = new rm();

$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$search = $obj->getParameter("search",false);
$branch = $obj->getParameter("branchid",0);
$city = $obj->getParameter("cityid",0);
$today = date("Ymd");
$chksearch = $obj->convert_char($search);
$rs = $obj->getbookinfo($begindate,$enddate,$chksearch,$branch,$city);
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Individual Bookings Relationship Management.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$chkrow = $obj->getParameter("chkrow",25);
if($export!="Excel"&&$export){
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$reportname = "Individual Bookings Relationship Management";
if(!$branch){
	if($city){
		$cityname = $obj->getIdToText($city,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}else{
		$reportname = "All branch's ".$reportname;
	}
}else{
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
}
?>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="17%"></td><td width="18%"></td>
		<td width="8%"></td><td width="7%"></td>
		<td width="20%"></td><td width="10%"></td>
		<td width="15%"></td><td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="8">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accommodations</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if(!$chkrow){$chkrow=1;}
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
    <tr height="20">
    	<td width="100%" align="center" colspan="8" >&nbsp;</td>
	</tr>
	<tr height="20">
    	<td width="100%" align="center" colspan="8" >
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
		<td width="17%"></td><td width="18%"></td>
		<td width="8%"></td><td width="7%"></td>
		<td width="20%"></td><td width="10%"></td>
		<td width="15%"></td><td width="5%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="8" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>BP. Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accommodations</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
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
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
if($export!=false){
	$accname = $rs[$i]["acc_name"];
	$bpphone = $rs[$i]["c_bp_phone"];
	$csname = $rs[$i]["cs_name"];
	$bpname = $rs[$i]["company_name"];
	$bpperson = $rs[$i]["bp_person"];
}else{
	$accname = $obj->hightLightChar($search,$rs[$i]["acc_name"]);
	$bpphone = $obj->hightLightChar($search,$rs[$i]["c_bp_phone"]);
	$csname = $obj->hightLightChar($search,$rs[$i]["cs_name"]);
	$bpname = $obj->hightLightChar($search,$rs[$i]["company_name"]);
	$bpperson = $obj->hightLightChar($search,$rs[$i]["bp_person"]);
}
?>					
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="left" style="padding-left:7px;">&nbsp;<?=($rs[$i]["bp_id"]==1)?"-------":$bpname?></td>
					<td class="report" align="left">&nbsp;<?=$bpperson?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="left">&nbsp;<?=$csname?></td>
					<td class="report" align="center"><?=($bpphone=="")?"&nbsp;":$bpphone?></td>
					<td class="report" align="left">&nbsp;<?=($rs[$i]["acc_id"]==1)?"-":$accname?></td>
					<td class="report" align="center" style="padding-left:10px">&nbsp;<?=$rs[$i]["branch_name"]?></td>
			</tr>
<?
}
?>
    <tr height="20">
    	<td width="100%" align="center" colspan="8" >&nbsp;</td>
	</tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="8" ><br>
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