<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("commission.inc.php");

$robj = new report();
$obj = new commission();

$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$collapse = $obj->getParameter("Collapse","Collapse");
$search = $obj->getParameter("search");
$order = $obj->getParameter("order");
$sort= $obj->getParameter("sortby","A &gt Z");

$anotherpara = "and a_bookinginfo.c_bp_phone like '%".$search."%'";
$cmschk = $obj->getParameter("commission",false);
if($cmschk==""){$cmschk=false;}
$branch_id = $obj->getParameter("branchid");
$cityid = $obj->getParameter("cityid",false);
if($branch_id==""){$branch_id=$obj->getIdToText("All","bl_branchinfo","branch_id","branch_name");}
$today = date("Ymd");
$branch_id=($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")=="All")?false:$branch_id;
$rs = $obj->getbccms($branch_id,$begindate,$enddate,$cmschk,$collapse,0,$anotherpara,$cityid,$order,$sort);
//Get Total Sales
if($collapse=="Expand"){
	$sum_total_sale=0;
for($i=0;$i<$rs["rows"];$i++){
	$sum_total_sale+=$rs[$i]["total"];
}
}
$begindate = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Booking Phone Number Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($collapse=="Collapse"){
	$chkrow = 20;
}else{
	$chkrow = 40;
}
$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")."Booking Phone Number Report";
?>
<?
//Get All Branch
        $sql = "select branch_id, branch_name from bl_branchinfo where branch_id<>1 ";
        		if($cityid){$sql .= "and city_id=".$cityid." ";}else
        		if($branch_id){$sql .= "and branch_id=".$branch_id." ";}
        $sql.= "and branch_active=1 order by branch_name asc";
        $rsBranch = $obj->getResult($sql);
        

    			for($j=0; $j<$rsBranch["rows"]; $j++){
    				$nbranchdetail[$j] = $rsBranch[$j]["branch_name"];
    			}
    			if($nbranchdetail){
  	  				$NbranchSrdString = implode(", ", $nbranchdetail); 
  				}
?>
<script type="text/javascript" src="../scripts/component.js"></script>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style=<?=($collapse!="Collapse")?"padding:40 20 50 20;":"padding:10 20 50 20;"?> width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="11%"></td><td width="5%"></td>
		<td width="6%"></td>
		<td width="15%"></td><td width="11%"></td><td width="10%"></td>
		<td width="11%"></td><td width="11%"></td>
		<td width="6%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="17%"></td><td width="16%"></td>
		<td width="16%"></td><td width="16%"></td>
		<td width="16%"></td><td width="16%"></td>
	</tr>
<? } ?>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
	    <b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
<? if($collapse=="Collapse"){ ?>
				<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accomodations</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;
$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
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
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="11%"></td><td width="5%"></td>
		<td width="6%"></td>
		<td width="15%"></td><td width="11%"></td><td width="10%"></td>
		<td width="11%"></td><td width="11%"></td>
		<td width="6%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="17%"></td><td width="16%"></td>
		<td width="16%"></td><td width="16%"></td>
		<td width="16%"></td><td width="16%"></td>
	</tr>
<? } ?>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
	    <b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accomodations</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
	</tr>
<?	
}	
	
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$totalbookcnt++;$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];

if($export!=false){
	$id=$bpdsid;
	$phoneno = $rs[$i]["cms_phone"];
}else{
	$phoneno = $obj->hightLightChar($search,$rs[$i]["cms_phone"]);
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">$bpdsid</a>";
}

$class = " class=\"even\" height=\"20\" style=\"background-color:#eaeaea;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";
?>
			<tr <?=$class?> height="20">
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["customer_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rs[$i]["time_start"],0,5)?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["hotel_room"]?>&nbsp;</td>
					<td class="report" align="center"><?=(str_replace(" ","",$rs[$i]["bp_name"])=="--select--")?"-----":$rs[$i]["bp_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=(str_replace(" ","",$rs[$i]["acc_name"])=="--select--")?"-----":$rs[$i]["acc_name"]?>&nbsp;</td>
					<td class="report" align="right" style="padding-right:10px"><?=$phoneno?>&nbsp;</td>
					<td class="report" align="left" style="padding-left:10px"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?></td>		
			</tr>
<?
	if(!isset($rs[$i+1]["cms_phone"])||$rs[$i]["cms_phone"]!=$rs[$i+1]["cms_phone"]){
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
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
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="11%"></td><td width="5%"></td>
		<td width="6%"></td>
		<td width="15%"></td><td width="11%"></td><td width="10%"></td>
		<td width="11%"></td><td width="11%"></td>
		<td width="6%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="25%"></td><td width="25%"></td>
		<td width="25%"></td><td width="25%"></td>
	</tr>
<? } ?>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
	    <b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Accomodations</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. People</b></td>
	</tr>
<?	
}	
	
	?><? 
$chkphone=$obj->getIdToText($rs[$i]["cms_phone"],"al_bankacc_cms","bankacc_cms_id","c_bp_phone","bankacc_active=1");
$class = "class=\"odd\" height=\"20\"  style=\"background-color:#d3d3d3;\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";
if($export!=false){
	$phoneno = $rs[$i]["cms_phone"];
}else{
	$phoneno = $obj->hightLightChar($search,$rs[$i]["cms_phone"]);
}
?>
			<tr bgcolor="#eaeaea" <?=$class?> height="20">
					<td class="report" align="left" colspan="4"><b><?=($rs[$i]["cms_phone"]=="")?"":$phoneno?><? if($chkphone){ ?> - </b><b style="color:#ff0000">DDC<? } ?></b></td>
					<td class="report" align="right"><b>&nbsp;</b></td>
					<td class="report" align="right"><b>&nbsp;</b></td>
					<td class="report" align="right"><b>&nbsp;</b></td>
					<td class="report" align="right"><b>&nbsp;</b></td>
					<td class="report" align="left"><b>&nbsp;</b></td>	
					<td class="report" align="center"><b><?=$bookcnt?>&nbsp;bookings&nbsp;</b></td>	
					<td class="report" align="right" style="padding-right:10px" colspan="2">
					<b>&nbsp;&nbsp;<?=$eachqty?>&nbsp;customers</b></td>		
			</tr>
	<?
		$bookcnt=0;$eachqty=0;$rowcnt++;
	}
}
?>
 			<tr height="20">
 					<td colspan="11">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td colspan="9" align="right"><b>Total Bookings : </b></td>
					<td align="center"><b style="color:#ff0000;"><?=number_format($totalbookcnt,0,".",",")?></b></td>
			</tr>
			<tr height="20">
					<td colspan="9" align="right"><b>Total Customers : </b></td>
					<td align="center"><b style="color:#ff0000;"><?=number_format($totalqty,0,".",",")?></b></td>
			</tr>
<? }else{?>
				<tr height="32">	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>DDC</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Sales</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Avg. Total Sales Per Cust.</b></td>
				</tr>
<?
$bookcnt=0;$totalbookcnt=0;
$totalqty=0;$eachqty=0;
$rowcnt=0;
for($i=0; $i<$rs["rows"]; $i++) {	
if($rowcnt%$chkrow==0&&$i&&$export!="Excel"&&$export){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
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
<? if($collapse=="Collapse"){ ?>
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="11%"></td><td width="5%"></td>
		<td width="6%"></td>
		<td width="15%"></td><td width="11%"></td><td width="10%"></td>
		<td width="11%"></td><td width="11%"></td>
		<td width="6%"></td>
	</tr>
<? }else{ ?>
	<tr>
		<td width="25%"></td><td width="25%"></td>
		<td width="25%"></td><td width="25%"></td>
	</tr>
<? } ?>
	<tr>
		<td class="reporth" width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" >
	    <b><p>Spa Management System</p>
    		<?=$obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")?> Phone Number Report</b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate=="")?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?></b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>DDC</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Bookings</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customers</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Sales</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Avg. Total Sales Per Cust.</b></td>
	</tr>
<?	
}		

$bookcnt+=$rs[$i]["cntbook"];$totalbookcnt+=$rs[$i]["cntbook"];$rowcnt++;
$eachqty+=$rs[$i]["qty_pp"];$totalqty+=$rs[$i]["qty_pp"];
	//if(!isset($rs[$i+1]["cms_phone"])||$rs[$i]["cms_phone"]!=$rs[$i+1]["cms_phone"]){

$bgcolor="";
//if($category!="Category" && $i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
//if($category!="Category" && !$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" ";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" ";}
//}
if($export!=false){
	$phoneno = $rs[$i]["cms_phone"];
}else{
	$phoneno = $obj->hightLightChar($search,$rs[$i]["cms_phone"]);
}
	?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="left"><?=$phoneno?>&nbsp;</td>
					<td class="report" align="center"><? $chkphone=$obj->getIdToText($rs[$i]["cms_phone"],"al_bankacc_cms","bankacc_cms_id","c_bp_phone","bankacc_active=1"); if($chkphone){ ?><b class="style1" style="color:#ff0000">DDC</b><? }else{ ?><b>-</b><? } ?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["cntbook"]?></td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["total"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["total"]/$rs[$i]["qty_pp"],2,".",",")?></td>				
			</tr>
	<?
		$bookcnt=0;$eachqty=0;
	//}
}?>
 			<tr height="20">
 					<td colspan="6" height="20">&nbsp;</td>
 			</tr>
			<tr height="20">
					<td colspan="4" align="right" height="20"><b>Total Bookings : </b></td>
					<td align="right"><b style="color:#ff0000;"><?=number_format($totalbookcnt,0,".",",")?></b></td>
			</tr>
			<tr height="20">
					<td colspan="4" align="right"><b>Total Sales : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($sum_total_sale,2,".",",")?></b></td>
			</tr>
			<tr height="20">
					<td colspan="4" align="right" height="20"><b>Total Customers : </b></td>
					<td align="right"><b style="color:#ff0000;"><?=number_format($totalqty,0,".",",")?></b></td>
			</tr>
			<tr height="20">
					<td colspan="4" align="right"><b>Avg. Total Sales Pes Cus. : </b></td>
					<td align="right"><b style='color:#ff0000'><?=($totalqty)?number_format($sum_total_sale/$totalqty,2,".",","):0?></b></td>
			</tr>
<? 
}?>
    <tr height="20">
    	<td width="100%" align="center" colspan="<?=($collapse=="Collapse")?"11":"6"?>" ><br>
    		<br><b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?><?=$rowcnt?>
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