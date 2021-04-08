<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("sale.inc.php");
$obj = new sale();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$branchcategoryid = $obj->getParameter("categoryid");
$city = $obj->getParameter("cityid");
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");


if($branch_id==""){$branch_id=0;}
$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}

$rs=$obj->getsrdetail($begin_date,$end_date,$branch_id,$branchcategoryid,$city);

if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Sales Report Detail.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}


$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Sales Report Detail";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" class="reporth" colspan="11">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Received By</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
<?	$all_amount=0; $all_sc=0; $all_vat=0; $all_total=0;
	$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
for($i=0; $i<$rs["rows"]; $i++) {
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
	<tr>
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
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Received By</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
    	
	
<?	
}		///////// Discount tax or servicecharge /////////////////
		if(!$rs[$i]["plus_servicecharge"]&&!$rs[$i]["plus_vat"]){
			//echo "<br> dis tax sc";
			$rs[$i]["unit_price"]=(100*$rs[$i]["unit_price"])/(100+$rs[$i]["taxpercent"]+$rs[$i]["servicescharge"]+($rs[$i]["taxpercent"]*$rs[$i]["servicescharge"])/100);
		}else if(!$rs[$i]["plus_vat"]){
			//echo "<br>dis tax : $taxpercent %";
			$rs[$i]["unit_price"]=(100*$rs[$i]["unit_price"])/(100+$rs[$i]["taxpercent"]);
		}else if(!$rs[$i]["plus_servicecharge"]){
			//echo "<br>dis sc : $servicescharge";
			$rs[$i]["unit_price"]=(100*$rs[$i]["unit_price"])/(100+$rs[$i]["servicescharge"]);
		}

		$product["set_sc"]=1;//$rs[$i]["plus_servicecharge"];
		$product["set_tax"]=1;//$rs[$i]["plus_vat"];
		//$product["set_sc"]=$rs[$i]["plus_servicecharge"];
		//$product["set_tax"]=$rs[$i]["plus_vat"];
		$product["servicescharge"]=$rs[$i]["servicescharge"];
		////////////insert March 14,2009 for new calculate tax & service charge/////////
		$rs[$i]["amount"]=$rs[$i]["unit_price"]*$rs[$i]["quantity"];
		$product["total"]=$rs[$i]["amount"];
		$product["taxpercent"]=$rs[$i]["taxpercent"];
						
						$sc = $obj->getsSvc($product);
						$vat = $obj->getsTax($product,$sc);
						$rowcnt++;
						//echo "<br>togal : ".number_format($product["total"],2,".",",");
					
if($rs[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";

if($rs[$i]["pay_id"]>1)
	$payname = $rs[$i]["pay_name"];
else
	$payname = "-";

if($rs[$i]["reception_code"]>1)
	$reception = $rs[$i]["reception_code"]." ".$rs[$i]["reception_name"];
else
	$reception = "-";

		if($rs[$i]["pay_id"]!=1)
			$keyword = $rs[$i]["pay_name"];
		else
			$keyword = "Unknown";

		$key = array_search($keyword, $paytype["type"]);
	
		
		if(!$key) {	
			$key = $pay_index;
			$pay_index++;
		}
		if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}
		
		if($rs[$i]["pay_id"]!=1) {
			$paytype["type"][$key] = $rs[$i]["pay_name"];
			if($rs[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}	
		else {
			$paytype["type"][$key] = "Unknown";
			if($rs[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}

if($rs[$i]["paid_confirm"]==0){
	echo "<tr bgcolor=\"#ffb9b9\" class=\"paidconfirm\" height=\"20\">\n"; 
	if($rs[$i]["pos_neg_value"]==0){
		$all_amount-=$product["total"];
		$all_sc-=$sc;	
		$all_vat-=$vat;	
		$all_total-=($product["total"]+$sc+$vat);
		$rs[$i]["amount"] = "-".$rs[$i]["amount"];
		$sc = "-".$sc;
		$vat = "-".$vat;
	}
}else if($rs[$i]["pos_neg_value"]==0) {
	$all_amount-=$product["total"];
	$all_sc-=$sc;	
	$all_vat-=$vat;	
	$all_total-=($product["total"]+$sc+$vat);
	$bgcolor = "#eaeaea";
	if(!$export){$bgcolor = "#d3d3d3";}
	echo "<tr bgcolor=\"$bgcolor\" class=\"odd\" height=\"20\">\n";   
	$rs[$i]["amount"] = "-".$rs[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
}
else {
	$all_amount+=$product["total"];
	$all_sc+=$sc;	
	$all_vat+=$vat;	
	$all_total+=($product["total"]+$sc+$vat);	
	echo "<tr height=\"20\" class=\"even\">\n";
}	   

$giftno = ($rs[$i]["tb_name"]=="a_bookinginfo")?$obj->getIdToText($rs[$i]["book_id"],"g_gift","gift_number","book_id"):"-";
if(!$giftno){$giftno = "-";}
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
if($export!=false){
	$id="<b>".$rs[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";
}	?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rs[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$rs[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="center"><?=$cms?></td>
					<td class="report" align="left"><?=$reception?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="center"><?=$giftno?>&nbsp;</td>
 				</tr>
 				<?	} ?>
 				<tr height="24">
					<td align="center"></td>
					<td></td>
					<td align="right"></td>
					<td align="center"><b>Total:</b></td>
					<td align="right"><?=number_format($all_amount,2,".",",")?></td>
					<td align="right"><?=number_format($all_sc,2,".",",")?></td>
					<td align="right"><?=number_format($all_vat,2,".",",")?></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
					<td align="right"></td>
 				</tr>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
 <?
 if($export && $export!="Excel" && (count($paytype["type"])+($rowcnt%$chkrow)) > $chkrow){
?>
	<tr>
		<td width="100%" align="center" colspan="11" ><br>
			<b>Printed: <?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?></b>
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
		<td width="7%"></td><td width="26%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="9%"></td>
		<td width="7%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="11">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
<?
 }
 ?> 				
				<tr height="20">
					<td colspan="2" align="left" height="20" style="padding-right:7px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						<td width="70%"></td><td width="30%"></td>
						</tr>
						<? for($i=1; $i<=count($paytype["type"]); $i++) {?>
						<!--<tr height="20">
							<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
							<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
						</tr>-->
						<? } ?>
						</table>
					</td>
					<td colspan="9" align="left" valign="top" height="20" style="padding-right:7px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						<td width="75%"></td><td width="25%"></td>
						</tr>
						<tr height="20">
							<td align="right"><b>Total Revenue : &nbsp;&nbsp;</b></td>
							<td align="right"><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($all_total,2,".",",")?></b></td>
						</tr>
						</table>
					</td>
				</tr>
			    <tr height="20">
			    	<td width="100%" align="center" colspan="11" ><br>
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