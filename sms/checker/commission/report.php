<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();
$obj->setDebugStatus(0);


$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
if($branch_id==""){$branch_id=0;}
$export = $obj->getParameter("export",false);

$ddreport = $obj->getParameter("ddreport",false);
$chkddreport = $obj->getParameter("chkddreport",false);
$bank = $obj->getParameter("bank",false);

$order = $obj->getParameter("order");
$sort = $obj->getParameter("sortby","A &gt Z");
$bank_acc = $obj->getParameter("bank_acc","All");

//For debug undefined variable : total. By Ruck : 21-05-2009
$total=0;

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&ddreport=$ddreport");
}
//if($ddreport){$rs = $obj->getbankcms($branch_id,$begin_date,$end_date);}
if($chkddreport){$rs = $obj->getbankcms($branch_id,$begin_date,$end_date,false,$bank);}
else{$rs = $obj->getcms($branch_id,$begin_date,$end_date,false,$order,$sort);}
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",25);
	$chkpage = ceil($rs["rows"]/$chkrow);
}

if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Commission Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}


$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Commission Report";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>
<?if($export=="Excel"){?><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><?}?>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>	
<span class="pdffirstpage"/>
<?
//if($ddreport){
if($chkddreport){
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="13%"></td><td width="9%"></td>
		<td width="5%"></td><td width="10%"></td>
		<td width="12%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="7%">
	</tr>
	<tr>
    	<td width="100%" align="center" colspan="12" class="reporth">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Branch</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Account Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Account Number</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
	</tr>
<?
$total_cms=0;$ttamount=0;
$rowcnt=0; 
for($i=0; $i<$rs["rows"]; $i++) {
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
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
		<td width="7%"></td><td width="7%"></td>
		<td width="13%"></td><td width="9%"></td>
		<td width="5%"></td><td width="10%"></td>
		<td width="12%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="7%">
	</tr>
	<tr>
    	<td width="100%" class="reporth" align="center" colspan="12">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Branch</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Account Name</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Account Number</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
	</tr>
    	
	
<?	
}	
$total += $rs[$i]["total"];
$total_cms += $rs[$i]["cms"]+$rs[$i]["c_cms_value"];
$rowcnt++;

/*if($i%2==1)
	echo "<tr bgcolor=\"#EAEAEA\" height=\"20\">\n";
else*/
	echo "<tr class=\"even\" height=\"20\">\n";

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
if($export!=false){
	$id="<b>$bpdsid</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$bpdsid."</a>";
}	
	
?>
					<td class="report" align="left"><?=$rs[$i]["bank_Ename"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["bank_branch"]?></td>
					<td class="report" align="left"><?=$rs[$i]["bankacc_name"]?></td>		
					<td class="report" align="center"><?=$rs[$i]["bankacc_number"]?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=($rs[$i]["branch_name"]==" -- select --")?"-----":$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["cms_phone"]?></td>
					<td class="report" align="left"><?=$rs[$i]["hotel"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cms_company_name"]?>&nbsp;</td>
					<td class="report" align="right"><?=number_format($rs[$i]["cms"]+$rs[$i]["c_cms_value"],2,".",",")?></td>	
<?	$ttamount+=$rs[$i]["cms"]+$rs[$i]["c_cms_value"];
	echo "</tr>";
	if(!isset($rs[$i+1]["bank_Ename"]) || $rs[$i]["bank_Ename"]!=$rs[$i+1]["bank_Ename"]){
?>
		<tr height="22" class="odd"  bgcolor="#eaeaea">		
					<td class="report" colspan="11" style="text-align:right;"><b>Total in <?=$rs[$i]["bank_Ename"]?> : </b></td>
					<td class="report" colspan="1" style="text-align:right;"><b style='color:#ff0000'><?=number_format($ttamount,2,".",",")?></b></td>
		</tr>
<?
		$ttamount=0;
	}
}
?>
 				<tr height="20">
 					<td colspan="12">&nbsp;</td>
 				</tr>
				<tr height="20">
					<td colspan="11" align="right"><b>Total Amount : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($total,2,".",",")?></b></td>
				</tr>
				<tr height="20">
					<td colspan="11" align="right"><b>Total Commission : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($total_cms,2,".",",")?></b></td>
				</tr>
			    <tr>
			    	<td width="100%" align="center" colspan="12" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<? } else {?>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7%"></td><td width="7%"></td>
		<td width="13%"></td><td width="16%"></td>
		<td width="5%"></td><td width="10%"></td>
		<td width="9%"></td><td width="10%"></td>
		<td width="6%"></td><td width="6%"></td>
		<td width="7%"></td><td width="6%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" colspan="11" class="reporth">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Percent</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Account</b></td>
	</tr>
<?
$total_cms=0;
$rowcnt=0; 
for($i=0; $i<$rs["rows"]; $i++) {
if($i&&$export!="Excel"&&$export&&$rowcnt&&$rowcnt%$chkrow==0){
?>
	<tr height="20">
    	<td width="100%" align="center" colspan="11" ><br>
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
		<td width="7%"></td><td width="7%"></td>
		<td width="13%"></td><td width="16%"></td>
		<td width="5%"></td><td width="10%"></td>
		<td width="9%"></td><td width="10%"></td>
		<td width="6%"></td><td width="6%"></td>
		<td width="7%"></td><td width="6%"></td>
	</tr>
	<tr>
    	<td width="100%" class="reporth" align="center" colspan="11">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>		
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Hotel</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Phone Number</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Book Company</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Percents</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Bank Account</b></td>
	</tr>
    	
	
<?	
}	
$rs_bacc = $obj->getbankacc($rs[$i]["cms_phone"]);	
if($bank_acc=="All"){
$total += $rs[$i]["total"];
$total_cms += $rs[$i]["cms"]+$rs[$i]["c_cms_value"];
$rowcnt++;

if($rs_bacc["rows"])
	echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" >\n";
else
	echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" >\n";

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
if($export!=false){
	$id="<b>$bpdsid</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$bpdsid."</a>";
}		?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="left"><?=($rs[$i]["branch_name"]==" -- select --")?"-----":$rs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
					<td class="report" align="left"><?=($rs[$i]["hotel"]==" -- select --"||$rs[$i]["hotel"]==" --select--")?"-----":$rs[$i]["hotel"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["qty_pp"]?>&nbsp;</td>
					<td class="report" align="left"><?=$rs[$i]["cms_name"]?>&nbsp;</td>		
					<td class="report" align="center"><?=$rs[$i]["cms_phone"]?></td>
					<? $companyname = $obj->getIdToText($rs[$i]["cms_company"],"al_bookparty","bp_name","bp_id");?>
					<td class="report" align="center"><?=($rs[$i]["cms_company"]>1)?$companyname:"-----"?>&nbsp;</td>
					<td class="report" align="right"><?=number_format($rs[$i]["total"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["pcms_percent"],2,".",",")?>&nbsp;%</td>
					<td class="report" align="right"><?=number_format($rs[$i]["cms"]+$rs[$i]["c_cms_value"],2,".",",")?></td>	
<?
				if($rs_bacc["rows"]) {
					
					$bank_msg = "&lt;b/&gt;Bank Name : ".$rs_bacc[0]["bank_Ename"]."&lt;br/&gt;";
					$bank_msg .= "Bank Branch : ".$rs_bacc[0]["bank_branch"]."&lt;br/&gt;";
					$bank_msg .= "Bank Account Name : ".$rs_bacc[0]["bankacc_name"]."&lt;br/&gt;";
					$bank_msg .= "Bank Account Number : ".$rs_bacc[0]["bankacc_number"]."&lt;br/&gt;";
					$bank_msg .= "Comment : &lt;b/&gt;".$rs_bacc[0]["bankacc_comment"]." ";	
					if($export!=false){
						echo "<td class=\"report\" align=\"center\"><b style='color:#ff0000'>Yes</b></td>";
					}else{
						echo "<td class=\"report\" align=\"center\" title=\" cssbody=[cmspopup] header=[] body=[$bank_msg]\"><b style='color:#ff0000'>Yes</b></td>";
					}
				}
				else {
					echo "<td class=\"report\" align=\"center\"><b>No</b></td>";
				}
	echo "</tr>";
}else if($bank_acc=="Yes"){
		if($rs_bacc["rows"]){
		
		$total += $rs[$i]["total"];
		$total_cms += $rs[$i]["cms"]+$rs[$i]["c_cms_value"];
		$rowcnt++;

		$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
		$pagename = "manageBooking".$rs[$i]["book_id"];
		$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
		if($export!=false){
			$id="<b>$bpdsid</b>";
		}else{
			$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$bpdsid."</a>";
		}		?>
							<td class="report" align="center"><?=$id?></td>
							<td class="report" align="left"><?=($rs[$i]["branch_name"]==" -- select --")?"-----":$rs[$i]["branch_name"]?>&nbsp;</td>
							<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
							<td class="report" align="left"><?=($rs[$i]["hotel"]==" -- select --"||$rs[$i]["hotel"]==" --select--")?"-----":$rs[$i]["hotel"]?>&nbsp;</td>
							<td class="report" align="center"><?=$rs[$i]["qty_pp"]?></td>
							<td class="report" align="left"><?=$rs[$i]["cms_name"]?>&nbsp;</td>		
							<td class="report" align="center"><?=$rs[$i]["cms_phone"]?>&nbsp;</td>
							<? $companyname = $obj->getIdToText($rs[$i]["cms_company"],"al_bookparty","bp_name","bp_id");?>
							<td class="report" align="center"><?=($rs[$i]["cms_company"]>1)?$companyname:"-----"?>&nbsp;</td>
							<td class="report" align="right"><?=number_format($rs[$i]["total"],2,".",",")?></td>
							<td class="report" align="right"><?=number_format($rs[$i]["pcms_percent"],2,".",",")?>&nbsp;%</td>
							<td class="report" align="right"><?=number_format($rs[$i]["cms"]+$rs[$i]["c_cms_value"],2,".",",")?></td>	
		<?
						if($rs_bacc["rows"]) {
							
							$bank_msg = "&lt;b/&gt;Bank Name : ".$rs_bacc[0]["bank_Ename"]."&lt;br/&gt;";
							$bank_msg .= "Bank Branch : ".$rs_bacc[0]["bank_branch"]."&lt;br/&gt;";
							$bank_msg .= "Bank Account Name : ".$rs_bacc[0]["bankacc_name"]."&lt;br/&gt;";
							$bank_msg .= "Bank Account Number : ".$rs_bacc[0]["bankacc_number"]."&lt;br/&gt;";
							$bank_msg .= "Comment : &lt;b/&gt;".$rs_bacc[0]["bankacc_comment"]." ";	
							if($export!=false){
								echo "<td class=\"report\" align=\"center\"><b style='color:#ff0000'>Yes</b></td>";
							}else{
								echo "<td class=\"report\" align=\"center\" title=\" cssbody=[cmspopup] header=[] body=[$bank_msg]\"><b style='color:#ff0000'>Yes</b></td>";
							}
						}
			echo "</tr>";
		}
}else if($bank_acc=="No"){
	   if(!$rs_bacc["rows"]){
			
			$total += $rs[$i]["total"];
			$total_cms += $rs[$i]["cms"]+$rs[$i]["c_cms_value"];
			$rowcnt++;
		
			$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
			$pagename = "manageBooking".$rs[$i]["book_id"];
			$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
			if($export!=false){
				$id="<b>$bpdsid</b>";
			}else{
				$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$bpdsid."</a>";
			}		?>
								<td class="report" align="center"><?=$id?></td>
								<td class="report" align="left"><?=($rs[$i]["branch_name"]==" -- select --")?"-----":$rs[$i]["branch_name"]?>&nbsp;</td>
								<td class="report" align="left"><?=$rs[$i]["cs_name"]?>&nbsp;</td>
								<td class="report" align="left"><?=($rs[$i]["hotel"]==" -- select --"||$rs[$i]["hotel"]==" --select--")?"-----":$rs[$i]["hotel"]?>&nbsp;</td>
								<td class="report" align="center"><?=$rs[$i]["qty_pp"]?>&nbsp;</td>
								<td class="report" align="left"><?=$rs[$i]["cms_name"]?>&nbsp;</td>		
								<td class="report" align="center"><?=$rs[$i]["cms_phone"]?></td>
								<? $companyname = $obj->getIdToText($rs[$i]["cms_company"],"al_bookparty","bp_name","bp_id");?>
								<td class="report" align="center"><?=($rs[$i]["cms_company"]>1)?$companyname:"-----"?>&nbsp;</td>
								<td class="report" align="right"><?=number_format($rs[$i]["total"],2,".",",")?></td>
								<td class="report" align="right"><?=number_format($rs[$i]["pcms_percent"],2,".",",")?>&nbsp;%</td>
								<td class="report" align="right"><?=number_format($rs[$i]["cms"]+$rs[$i]["c_cms_value"],2,".",",")?></td>	
			<?
								echo "<td class=\"report\" align=\"center\"><b>No</b></td>";
				echo "</tr>";
	   }
}
}
?>
 				<tr height="20">
 					<td colspan="11">&nbsp;</td>
 				</tr>
				<tr height="20">
					<td colspan="9" align="right"><b>Total Amount : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($total,2,".",",")?></b></td>
					<td>&nbsp;</td>
				</tr>
				<tr height="20">
					<td colspan="9" align="right"><b>Total Commission : </b></td>
					<td align="right"><b style='color:#ff0000'><?=number_format($total_cms,2,".",",")?></b></td>
					<td>&nbsp;</td>
				</tr>
			    <tr>
			    	<td width="100%" align="center" colspan="11" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<? } ?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>