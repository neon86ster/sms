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
$order = $obj->getParameter("order","b_appt_date");
$sortby = $obj->getParameter("sortby","Z &gt; A");
$today = date("Ymd");
$chksearch = $obj->convert_char($search);
$memchk = $obj->getParameter("memchk");

$rs = $obj->getcustinfo($begindate,$enddate,$chksearch,$branch,$city,$order,$sortby);
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Customer Relationship Management.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",20);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
$reportname = "Customer Relationship Management";
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
    	<td valign="top" style="padding:40 20 50 20;" width="100%" align="center">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="20%"></td><td width="7%"></td>
		<td width="7%"></td><td width="10%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Nationality</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Birthday</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>E-mail</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Member Code</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gender</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Resident/Non-Resident</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>City</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Age</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
?>
	<tr height="20">
    	<td align="center" colspan="12" >&nbsp;</td>
    </tr>
	<tr height="20">
    	<td width="100%" align="center" colspan="12" >
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
		<td width="7%"></td><td width="7%"></td>
		<td width="20%"></td><td width="7%"></td>
		<td width="7%"></td><td width="10%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
	</tr>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="12" >
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Nationality</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Birthday</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>E-mail</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Member Code</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gender</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Resident/Non-Resident</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>City</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Age</b></td>
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
if(isset($rs[$i]["has_cms"])&&$rs[$i]["has_cms"]==1){
	$hascms = "<span style=\"color:#ff0000\">Yes</span>";
}else{
	$hascms = "<span>No</span>";}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
}
if($export!=false){
	$csname = $rs[$i]["cs_name"];
	$csphone = $rs[$i]["cs_phone"];
	$csemail = $rs[$i]["cs_email"];
}else{
	$csname = $obj->hightLightChar($search,$rs[$i]["cs_name"]);
	$csphone = $obj->hightLightChar($search,$rs[$i]["cs_phone"]);
	$csemail = $obj->hightLightChar($search,$rs[$i]["cs_email"]);
}
$csstatus = "";
if($rs[$i]["visitor"]){
	$csstatus = "Visitor";
}else if($rs[$i]["resident"]){
	$csstatus = "Resident";
}else{
	$csstatus = "Unknown";
}

	if(!$memchk or ($memchk && !$rs[$i]["a_member_code"])){
?>					
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="left">&nbsp;<?=$csname?></td>
					<td class="report" align="center">&nbsp;<?=$rs[$i]["cs_nation"]=="--select--"?"n/a":$rs[$i]["cs_nation"]?></td>
					<td class="report" align="center"><?=($rs[$i]["cs_birthday"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["cs_birthday"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="left" style="padding-left:10px"><?=($csemail=="")?"&nbsp;":$csemail?></td>
					<td class="report" align="center">&nbsp;<?=($rs[$i]["a_member_code"]>0)?$rs[$i]["a_member_code"]:"-"?></td>
					<td class="report" align="left" style="padding-left:10px"><?=($csphone=="")?"&nbsp;":$csphone?></td>
					<td class="report" align="center"><?=$rs[$i]["cs_gender"]?>&nbsp;</td>
					<td class="report" align="center">&nbsp;<?=$csstatus?></td>
					<td class="report" align="center">&nbsp;<?=$rs[$i]["city_name"]?></td>
					<td class="report" align="center"><?=($rs[$i]["cs_age"]>0)?$rs[$i]["cs_age"]:"-"?></td>
			</tr>
<?
	}
}
?>
	<tr height="20">
    	<td align="center" colspan="12" >&nbsp;</td>
    </tr>
    <tr height="20">
    	<td width="100%" align="center" colspan="12" ><br>
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