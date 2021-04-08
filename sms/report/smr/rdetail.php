<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("sale.inc.php");
require_once("checker.inc.php");
require_once("customer.inc.php");

$objcc = new customer();
$objc = new checker();
$obj = new sale();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");

$today = date("Ymd");
$branch = $obj->getParameter("branchid",0);
$cityid = $objcc->getParameter("cityid");
$pdcategoryid = $obj->getParameter("itemid");
$table=$obj->getParameter("table");

$payid = $obj->getParameter("payid");
$sexid = $objcc->getParameter("sexid");

$today = date("Ymd");

if($branch_id==""){$branch_id=0;}
$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Items Report Detail.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
$rrrs=$objcc->getcustpersexdetail($begin_date,$end_date,$branch_id,false,$cityid,false,$sexid);		//gender
$res=$objcc->getcustlocaldetail($begin_date,$end_date,$branch_id,false,$cityid,"Resident");				//resident
$vis=$objcc->getcustlocaldetail($begin_date,$end_date,$branch_id,false,$cityid,"Visitor");				//visitor 
$rrs=$objcc->getcustlocaldetail($begin_date,$end_date,$branch_id,false,$cityid);				//resident and visitor

$rs = $obj->getitemsaledetail($branch_id,$begin_date,$end_date,false,$cityid);									//overview
$rss = $obj->getitemsaledetail($branch_id,$begin_date,$end_date,false,$cityid,$pdcategoryid);	//sale
$pos = $obj->getitemsaledetail($branch_id,$begin_date,$end_date,false,$cityid,$pdcategoryid,"pos");	//sale total pos
$poss = $obj->getitemsaledetail($branch_id,$begin_date,$end_date,false,$cityid,$pdcategoryid,"neg");	//sale total neg
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}

$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
////
?>

<!--start overview here-->

<?if($table=="Overview"){
if($branch_id){
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")."'s ".$obj->getIdToText($branch_id,"cl_product","pd_name","branch_id")." Overview Report Detail";
}else{
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname."Overview Report Detail";
	}
	$reportname = "Total Overview Report Detail";
	}	
?>
	<?if($export!="Excel"){?><script type="text/javascript" src="../scripts/ajax.js"></script><?}?>
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
<?	$all_sc=0; $all_vat=0; $all_total=0;	
	$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srdetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
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
						$all_sc+=$sc;	
						$all_vat+=$vat;	
						$all_total+=($product["total"]+$sc+$vat);	
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
		$all_total-=($product["total"]+$sc+$vat)*2;
		$rs[$i]["amount"] = "-".$rs[$i]["amount"];
		$sc = "-".$sc;
		$vat = "-".$vat;
	}
}else if($rs[$i]["pos_neg_value"]==0) {
	$all_total-=($product["total"]+$sc+$vat)*2;
	$bgcolor = "#eaeaea";
	if(!$export){$bgcolor = "#d3d3d3";}
	echo "<tr bgcolor=\"$bgcolor\" class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";   
	$rs[$i]["amount"] = "-".$rs[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
}
else {
	$sqlcMp = "select * from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
	$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		echo "<tr height=\"20\" bgcolor=\"#eaf7cc\" class=\"multipay\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaf7cc'\" height=\"20\">\n";
	}else{
		echo "<tr height=\"20\" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	}
}	   

$giftno = ($rs[$i]["tb_name"]=="a_bookinginfo")?$obj->getIdToText($rs[$i]["book_id"],"g_gift","gift_number","book_id"):" ";
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
 		<?				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $rs[$i]["salesreceipt_id"];			
 	
 	$sqlMp = "select * from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
		$mpId = $obj->getResult($sqlMp);
		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$mpId){			
 	$sqlSr = "select `c_salesreceipt`.* , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$rs[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 		
 				
 				} 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	$bookSrdOld="";
 	$bookPayId="";
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 

 		?>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
 <?
 if($export&& $export!="Excel" && (count($paytype["type"])+$rowcnt) > $chkrow){
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
					
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);


		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
				<!--	<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
	}else{
			for($i=1; $i<=count($paytype["type"]); $i++) {?>
				<!--<tr height="20">
						<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
					</tr>-->
			<? } 
	}
	
	
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			"and `c_srpayment`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 //	echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
?>					
					</table>
					</td>
					<td colspan="8" align="left" valign="top" height="20" style="padding-right:7px;">
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
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    		<br>Method of payment column shows the higest pay price in that sales receipt
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>

<?
}
//end overview
//sale table
if($table=="Sale"){
	
	$reportname = "Sale Category Report Detail";
if(!$branch){
	if($pdcategoryid){
		$pdcategoryid= $obj->getIdToText($pdcategoryid,"cl_product_category","pd_category_name","pd_category_id");
		$reportname = $pdcategoryid." ".$reportname;
	}
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}
	
}else{
	if($pdcategoryid){
		$pdcategoryid= $obj->getIdToText($pdcategoryid,"cl_product_category","pd_category_name","pd_category_id");
		$reportname = $pdcategoryid." ".$reportname;
	}
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" class="reporth" colspan="12">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
<?	$all_sc=0; $all_vat=0; $all_total=0;	
	$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srdetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
for($i=0; $i<$rss["rows"]; $i++) {
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Received By</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
    	
	
<?	
}
//
///
	///////// Discount tax or servicecharge /////////////////
		if(!$rss[$i]["plus_servicecharge"]&&!$rss[$i]["plus_vat"]){
			//echo "<br> dis tax sc";
			$rss[$i]["unit_price"]=(100*$rss[$i]["unit_price"])/(100+$rss[$i]["taxpercent"]+$rss[$i]["servicescharge"]+($rss[$i]["taxpercent"]*$rss[$i]["servicescharge"])/100);
		}else if(!$rss[$i]["plus_vat"]){
			//echo "<br>dis tax : $taxpercent %";
			$rss[$i]["unit_price"]=(100*$rss[$i]["unit_price"])/(100+$rss[$i]["taxpercent"]);
		}else if(!$rss[$i]["plus_servicecharge"]){
			//echo "<br>dis sc : $servicescharge";
			$rss[$i]["unit_price"]=(100*$rss[$i]["unit_price"])/(100+$rss[$i]["servicescharge"]);
		}

		$product["set_sc"]=1;//$rs[$i]["plus_servicecharge"];
		$product["set_tax"]=1;//$rs[$i]["plus_vat"];
		//$product["set_sc"]=$rs[$i]["plus_servicecharge"];
		//$product["set_tax"]=$rs[$i]["plus_vat"];
		$product["servicescharge"]=$rss[$i]["servicescharge"];
		////////////insert March 14,2009 for new calculate tax & service charge/////////
		$rss[$i]["amount"]=$rss[$i]["unit_price"]*$rss[$i]["quantity"];
		$product["total"]=$rss[$i]["amount"];
		$product["taxpercent"]=$rss[$i]["taxpercent"];
						
						$sc = $obj->getsSvc($product);
						$vat = $obj->getsTax($product,$sc);
						$rowcnt++;
						$all_sc+=$sc;	
						$all_vat+=$vat;	
						$all_total+=($product["total"]+$sc+$vat);	
						//echo "<br>togal : ".number_format($product["total"],2,".",",");
					
if($rss[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";

if($rss[$i]["pay_id"]>1)
	$payname = $rss[$i]["pay_name"];
else
	$payname = "-";

if($rss[$i]["reception_code"]>1)
	$reception = $rss[$i]["reception_code"]." ".$rss[$i]["reception_name"];
else
	$reception = "-";

		if($rss[$i]["pay_id"]!=1)
			$keyword = $rss[$i]["pay_name"];
		else
			$keyword = "Unknown";

		$key = array_search($keyword, $paytype["type"]);
	
		
		if(!$key) {	
			$key = $pay_index;
			$pay_index++;
		}
		if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}
		
		if($rss[$i]["pay_id"]!=1) {
			$paytype["type"][$key] = $rss[$i]["pay_name"];
			if($rss[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}	
		else {
			$paytype["type"][$key] = "Unknown";
			if($rss[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}
		
if($rss[$i]["paid_confirm"]==0){
	echo "<tr bgcolor=\"#ffb9b9\" class=\"paidconfirm\" height=\"20\">\n"; 
	if($rss[$i]["pos_neg_value"]==0){
		$all_total-=($product["total"]+$sc+$vat)*2;
		$rss[$i]["amount"] = "-".$rss[$i]["amount"];
		$sc = "-".$sc;
		$vat = "-".$vat;
	}
}else if($rss[$i]["pos_neg_value"]==0) {
	$all_total-=($product["total"]+$sc+$vat)*2;
	$bgcolor = "#eaeaea";
	if(!$export){$bgcolor = "#d3d3d3";}
	echo "<tr bgcolor=\"$bgcolor\" class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";   
	$rss[$i]["amount"] = "-".$rss[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
}
else {
	$sqlcMp = "select * from c_srpayment where salesreceipt_id=".$rss[$i]["salesreceipt_id"]."";
	$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		echo "<tr height=\"20\" bgcolor=\"#eaf7cc\" class=\"multipay\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaf7cc'\" height=\"20\">\n";
	}else{
		echo "<tr height=\"20\" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	}
}	   

$giftno = ($rss[$i]["tb_name"]=="a_bookinginfo")?$obj->getIdToText($rss[$i]["book_id"],"g_gift","gift_number","book_id"):" ";
if(!$giftno){$giftno = "-";}
$url = ($rss[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rss[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rss[$i]["book_id"]."";
$pagename = ($rss[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rss[$i]["book_id"]:"managePds".$rss[$i]["book_id"];
if($export!=false){
	$id="<b>".$rss[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$rss[$i]["bpds_id"]."</a>";
}
//

//	
?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rss[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($rss[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$rss[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($rss[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="center"><?=$cms?></td>
					<td class="report" align="left"><?=$reception?></td>
					<td class="report" align="center"><?=$rss[$i]["branch_name"]?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="center"><?=$giftno?>&nbsp;</td>
 				</tr>
 		<?				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $rss[$i]["salesreceipt_id"];			
 	
 	$sqlMp = "select * from c_srpayment where salesreceipt_id=".$rss[$i]["salesreceipt_id"]."";
		$mpId = $obj->getResult($sqlMp);
		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$mpId){			
 	$sqlSr = "select `c_salesreceipt`.* , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$rss[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 		
 				
 				} 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	$bookSrdOld="";
 	$bookPayId="";
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 

 		?>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
 <?
 if($export&& $export!="Excel" && (count($paytype["type"])+$rowcnt) > $chkrow){
?>
	<tr>
		<td width="100%" align="center" colspan="12" ><br>
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
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
					
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);


		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
	}else{
			for($i=1; $i<=count($paytype["type"]); $i++) {?>
				<!--<tr height="20">
						<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
					</tr>-->
			<? } 
	}
	
	
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			"and `c_srpayment`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 	//echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
?>					
					</table>
					</td>
					<td colspan="8" align="left" valign="top" height="20" style="padding-right:7px;">
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
			    	<td width="100%" align="center" colspan="12" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="12" ><br>
			    		<br>Method of payment column shows the higest pay price in that sales receipt
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>

<?
}
//end sale
//start payment

if($table=="payment"){
	

	$reportname = "Payment Type Report Detail";
if(!$branch){
	if($payid){
		$payid = $obj->getIdToText($payid,"l_paytype","pay_name","pay_id");
		$reportname = $payid." ".$reportname;
	}
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}
	
}else{
	if($payid){
		$payid = $obj->getIdToText($payid,"l_paytype","pay_name","pay_id");
		$reportname = $payid." ".$reportname;
	}
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
}
$payid = $obj->getParameter("payid");
$branch_id = $obj->getParameter("branchid");
$cityid = $objcc->getParameter("cityid");
?>
<?
$header = "\t<tr>\n";
$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"11\" ><br>\n";
$header .= "\t\t\t<b>Printed: </b>".$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")."\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "</table></td>\n";
$header .= "\t</tr>\n";
$header .= "</table>\n";
$header .= "<hr style=\"page-break-before:always;border:0;color:#ffffff;\" />\n";
$header .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td valign=\"top\" style=\"padding:10 20 50 20;\" width=\"100%\" align=\"center\">\n";
$header .= "\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td width=\"8%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"5%\"></td>\n";
$header .= "\t\t<td width=\"12%\"><td width=\"7%\"></td><td width=\"7%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"5%\"></td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td class=\"reporth\" width=\"100%\" align=\"center\" colspan=\"11\">\n";
$header .= "\t\t\t<b><p>Spa Management System</p>\n";
$header .= "\t\t\t$reportname</b><br>\n";
$header .= "\t\t\t<p><b style='color:#ff0000'>";
$header .= $dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= ($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat);
$header .= "<b><br><br></p>\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr height=\"32\">\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Receipt No.</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Booking ID</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Customer Name</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Branch</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Room</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Date</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Time</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Method of Payment</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Total</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Cashier</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Reprinted</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>CMS</b></td>\n";
$header .= "\t</tr>\n";
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="8%"></td><td width="8%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="12%"></td><td width="7%"></td>
		<td width="7%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="8%"></td><td width="5%"></td>
		
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receipt No.</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cashier</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Reprinted</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
	</tr>
<?
//
//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm,";
		$sql1 .= "c_bpds_link.tb_name as tb_name,";
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date,bl_branchinfo.branch_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.b_customer_name as customer_name,";
		$sql1 .= "a_bookinginfo.c_set_cms as cms,";
		$sql1 .= "c_salesreceipt.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_number,";
		$sql1 .= "c_salesreceipt.sr_total,";
		$sql1 .= "l_paytype.pay_name as pay_name, ";
		$sql1 .= "l_hour.hour_name,max(log_c_srprint_tmp.reprint_times) as reprint_times,s_user.u ";
		
		//for cancel book
		$sql1 .=",a_bookinginfo.b_set_cancel as set_cancel ";
			
		$sql1 .= "from c_salesreceipt left join " .
				"(SELECT log_c_srprint.* 
					FROM log_c_srprint
					order by log_c_srprint.reprint_times desc)" .
				" as log_c_srprint_tmp " .
				"on log_c_srprint_tmp.salesreceipt_id=c_salesreceipt.salesreceipt_id " .
				"left join s_user on log_c_srprint_tmp.l_lu_user=s_user.u_id," .
				"a_bookinginfo,l_hour,bl_branchinfo,c_srpayment,l_paytype,c_bpds_link ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and l_hour.hour_id=a_bookinginfo.b_book_hour ";
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srpayment.salesreceipt_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		if($cityid){$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($payid){
		$sql1 .= "and c_srpayment.pay_id=$payid ";
		}
		$sql1 .= "and c_srpayment.pay_id=l_paytype.pay_id ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$begin_date." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$begin_date."' and a_bookinginfo.b_appt_date<='".$end_date."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		//$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "group by c_salesreceipt.salesreceipt_id ";
		
		//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_date as appt_date,bl_branchinfo.branch_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "\"-\" as customer_name,";
		$sql2 .= "\"0\" as cms,";
		$sql2 .= "c_salesreceipt.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_number,";
		$sql2 .= "c_salesreceipt.sr_total,";
		$sql2 .= "l_paytype.pay_name as pay_name, ";
		$sql2 .= "\"-\" as hour_name,max(log_c_srprint_tmp.reprint_times) as reprint_times,s_user.u ";
		
		//for cancel product
		$sql2 .=",c_saleproduct.set_cancel as set_cancel ";
		
		$sql2 .= "from c_salesreceipt left join " .
				"(SELECT log_c_srprint.* 
					FROM log_c_srprint
					order by log_c_srprint.reprint_times desc)" .
				" as log_c_srprint_tmp " .
				"on log_c_srprint_tmp.salesreceipt_id=c_salesreceipt.salesreceipt_id " .
				"left join s_user on log_c_srprint_tmp.l_lu_user=s_user.u_id," .
				"c_saleproduct,bl_branchinfo,c_srpayment,l_paytype,c_bpds_link ";
		$sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srpayment.salesreceipt_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		if($cityid){$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($payid){
		$sql2 .= "and c_srpayment.pay_id=$payid ";
		}
		$sql2 .= "and c_srpayment.pay_id=l_paytype.pay_id ";
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date='".$begin_date."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$begin_date."' and c_saleproduct.pds_date<='".$end_date."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		//$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "group by c_salesreceipt.salesreceipt_id ";
		
		$sql = "($sql1) union ($sql2) order by paid_confirm desc,branch_id,salesreceipt_number,bpds_id ";
		//echo $sql;
		$rsss = $obj->getResult($sql);
//


$all_total=0;		
$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srddetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
$rsunknown_value=0;

$bookSrdString="";
$bookSrdOld="";
$bookPayId="";
for($i=0; $i<$rsss["rows"]; $i++) {
// separate page when export
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
	echo $header;	$rowcnt=0;
}	
if($rsss[$i]["set_cancel"]==0 && $rsss[$i]["paid_confirm"]==1){
// summary each payment's total
$keyword = ($rsss[$i]["pay_id"]!=1)?$rsss[$i]["pay_name"]:"Unknown";
$key = array_search($keyword, $paytype["type"]);

if(!$key) {	
		$key = $pay_index;
		$pay_index++;
}


if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}

if($rsss[$i]["pay_id"]!=1){
$paytype["type"][$key] = $keyword;
$paytype["value"][$key] += $rsss[$i]["sr_total"];
}

if($rsss[$i]["pay_id"]==1){
	$rsunknown = $keyword; 
	$rsunknown_value += $rsss[$i]["sr_total"]; 
}
}
if($rsss[$i]["set_cancel"]==0){
// each rows' color
$bgcolor = "#eaeaea"; $class = "even";
if($i%2==0){
	$bgcolor = "#d3d3d3"; $class = "odd";
}
if($rsss[$i]["paid_confirm"]==0){
	$bgcolor = "#ffb9b9"; $class = "paidconfirm";
}
}

// define booking id links
$url = ($rsss[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rsss[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rsss[$i]["book_id"]."";
$pagename = ($rsss[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rsss[$i]["book_id"]:"managePds".$rsss[$i]["book_id"];
if($export!=false){
	$id="<b>".$rsss[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rsss[$i]["bpds_id"]."</a>";
}	

// define room in each room 
if($rsss[$i]["tb_name"]=="a_bookinginfo"){
	$roomname=array();
	$sqlRoom = "select distinct room_name from d_indivi_info,bl_room " .
				"where d_indivi_info.room_id=bl_room.room_id " .
				"and book_id =".$rsss[$i]["book_id"] ;
	$rsRoom = $obj->getResult($sqlRoom);
	
	
	for($j=0; $j<$rsRoom["rows"]; $j++){
		$roomname[$j]=$rsRoom[$j]["room_name"];
	}
	sort($roomname);
	$rname = implode(", ",array_filter($roomname));
}else{
	$rname = "-";
}

// define another value
$payname = ($rsss[$i]["pay_id"]>1)?$rsss[$i]["pay_name"]:"-";
if($rsss[$i]["set_cancel"]==0 && $rsss[$i]["paid_confirm"]==1){
$all_total+=$rsss[$i]["sr_total"];
}

$sr_id = $rsss[$i]["salesreceipt_id"];
$cashier =($rsss[$i]["u"]==null)?"-": $rsss[$i]["u"];
$reprint = ($rsss[$i]["reprint_times"]==0)?"-":$rsss[$i]["reprint_times"];
$cms = ($rsss[$i]["cms"])?"<span style='color:#ff0000'>yes</span>":"<span style='color:#ff0000'>no</span>";

$rowcnt++;		

	$sqlcMp = "select salesreceipt_id from c_srpayment where salesreceipt_id=".$rsss[$i]["salesreceipt_id"]."";
		$cmpId = $obj->getResult($sqlcMp);
		
		
		
	if($cmpId["rows"]>1){
		$bgcolor = "#eaf7cc"; $class = "multipay";
	}	
	
$style="";
if($rsss[$i]["set_cancel"]==1){
		
		$bgcolor = "#eaeaea"; $class = "even";
		if($i%2==0){
			$bgcolor = "#d3d3d3"; $class = "odd";
		}
		//$bgcolor = "#707070"; $class="cancel";
?>			
			<tr style="text-decoration:line-through;" bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=($rsss[$i]["salesreceipt_number"])?$rsss[$i]["salesreceipt_number"]:"-"?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rsss[$i]["customer_name"]?></td>
					<td class="report"><?=$rsss[$i]["branch_name"]?></td>
					<td class="report"><?=$rname?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rsss[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rsss[$i]["hour_name"],0,5)?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="right"><?=number_format($rsss[$i]["sr_total"],2,".",",")?></td>
					<td class="report" align="center"><?=$cashier?></td>
					<td class="report" align="center"><?=$reprint?></td>
					<td class="report" align="center"><?=$cms?></td><s>
 			</tr>
 		
 		<?
}else{
?>
			<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=($rsss[$i]["salesreceipt_number"])?$rsss[$i]["salesreceipt_number"]:"-"?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rsss[$i]["customer_name"]?></td>
					<td class="report"><?=$rsss[$i]["branch_name"]?></td>
					<td class="report"><?=$rname?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rsss[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rsss[$i]["hour_name"],0,5)?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="right"><?=number_format($rsss[$i]["sr_total"],2,".",",")?></td>
					<td class="report" align="center"><?=$cashier?></td>
					<td class="report" align="center"><?=$reprint?></td>
					<td class="report" align="center"><?=$cms?></td>
 			</tr>
<?	
}
if($rsss[$i]["set_cancel"]==0 && $rsss[$i]["paid_confirm"]){				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $rsss[$i]["salesreceipt_id"];			
 	
 	//$sqlMp = "select salesreceipt_id from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
		//$mpId = $obj->getResult($sqlMp);

		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$cmpId){			
 	$sqlSr = "select `c_salesreceipt`.`pay_id` ,`c_salesreceipt`.`sr_total` ,`c_salesreceipt`.`salesreceipt_id` , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$rsss[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 				
 				} 
 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 
 	
 	$sqlPd = "SELECT `c_srpayment`.`pay_id` FROM c_srpayment WHERE `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) group by `c_srpayment`.`pay_id`";
		$srPd = $obj->getResult($sqlPd);
		for ($k = 0; $k < $srPd["rows"]; $k++) {
				$PayId[$k] = $srPd[$k]["pay_id"];

		}	
 	
 	if($PayId){
    	$bookPayId = implode(",", $PayId);
 	} 
}
 		?>

 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
<?
 if($export && (count($paytype["type"])+$rowcnt) > $chkrow){
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
		<td width="8%"></td><td width="8%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="12%"></td><td width="7%"></td>
		<td width="7%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="8%"></td><td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
<?
 }
 ?>
				<tr height="20">
					<td colspan="3" align="left" height="20" style="padding-right:7px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="70%"></td><td width="30%"></td>
					</tr>
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);

		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
					<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
	}else{
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
?>
								
					
					<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
	}
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			//"and `c_srpayment`.`pay_id` != 1 " .
 			"and `c_salesreceipt`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 	//echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>
<? 
		}
?>	
		<?if($rsunknown_value){?>
					<tr height="20">
						<td align="right"><b><?=$rsunknown?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($rsunknown_value,2,".",",")?></b></td>
					</tr>
		<?}?>	
					</table>
					</td>
					<td colspan="8" align="right" height="20" valign="top" style="padding-right:7px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td width="75%"></td><td width="25%"></td>
							</tr>
<?
$sql = "select tax_id,servicescharge from bl_branchinfo where branch_active=1 and branch_id=".$branch_id." order by branch_name limit 0,1";
	$rsss = $obj->getResult($sql);
	//echo "<br>$sql";
	
		$servicescharge = $rsss[0]["servicescharge"];
		$taxpercent=$obj->getIdToText($rsss[0]["tax_id"],"l_tax","tax_percent","tax_id");
		
$sql_st = "select * from c_srdetail where salesreceipt_id in (".$bookSrdString.")";
	$rs_st = $obj->getResult($sql_st);
//echo "<br>$sql_st";
$total_sc=0;
$total_tax=0;
for ($s = 0; $s < $rs_st["rows"]; $s++) {
 	
 $sql_p = "select cl_product_category.pos_neg_value from cl_product,cl_product_category " .
 		"where cl_product.pd_id=".$rs_st[$s]["pd_id"]." " .
 		"and cl_product.pd_category_id=cl_product_category.pd_category_id";	
 $rs_p = $obj->getResult($sql_p);
 	//echo"<br>$sql_p";
 if($rs_p[0]["pos_neg_value"]){
 	if($rs_st[$s]["set_sc"]){
 		$total_sc+=(($rs_st[$s]["unit_price"]*$servicescharge)/100)*$rs_st[$s]["qty"];
 		if($rs_st[$s]["set_tax"]){
 		$total_tax+=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}else{
 		if($rs_st[$s]["set_tax"]){
 		$total_tax+=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}
 }else{
 		if($rs_st[$s]["set_sc"]){
 		$total_sc-=(($rs_st[$s]["unit_price"]*$servicescharge)/100)*$rs_st[$s]["qty"];
 		if($rs_st[$s]["set_tax"]){
 		$total_tax-=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}else{
 		if($rs_st[$s]["set_tax"]){
 		$total_tax-=((($rs_st[$s]["unit_price"]+(($rs_st[$s]["unit_price"]*$servicescharge)/100))*$taxpercent)/100)*$rs_st[$s]["qty"];
 		}
 	}
 }
}
?>


						<?if($payid==0){?>	<tr height="20">
								<td align="right"><b>Total Revenue : &nbsp;&nbsp;</b></td>
								<td align="right"><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($all_total,2,".",",")?></b></td>
							</tr><?}?>
			
						</table>
					</td>
				</tr>
				
				<tr height="50">
			    	<td width="100%" align="center" colspan="11" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				
<?
$c_sdate = substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2);
		$c_edate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$c_sql1="select c_bpds_link.*,a_bookinginfo.book_id as book_id " .
				"from a_bookinginfo,c_bpds_link " .
				"where a_bookinginfo.book_id=c_bpds_link.tb_id " .
				"and c_bpds_link.tb_name='a_bookinginfo' " .
				"and a_bookinginfo.b_set_cancel<>1 " .
				"";
		$c_sql1.="and a_bookinginfo.book_id not in ( select c_salesreceipt.book_id from c_salesreceipt where c_salesreceipt.book_id=a_bookinginfo.book_id)";
				//"and IFNULL((select count(c_salesreceipt.salesreceipt_id) from c_salesreceipt where c_salesreceipt.book_id=a_bookinginfo.book_id),0)=0 ";
		if($end_date==false){$c_sql1 .= "and a_bookinginfo.b_appt_date=".$c_sdate." ";}
		else{$c_sql1 .= "and a_bookinginfo.b_appt_date>='".$c_sdate."' and a_bookinginfo.b_appt_date<='".$c_edate."' ";}
		if($branch_id){$c_sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		
		$c_sql2="select c_bpds_link.*,c_saleproduct.pds_id as book_id " .
				"from c_saleproduct,c_bpds_link " .
				"where c_saleproduct.pds_id=c_bpds_link.tb_id " .
				"and c_bpds_link.tb_name='c_saleproduct' " .
				"and c_saleproduct.set_cancel<>1 " .
				"";
		$c_sql2.="and c_saleproduct.pds_id not in ( select c_salesreceipt.pds_id from c_salesreceipt where c_salesreceipt.pds_id=c_saleproduct.pds_id)";
				//"and IFNULL((select count(c_salesreceipt.salesreceipt_id) from c_salesreceipt where c_salesreceipt.pds_id=c_saleproduct.pds_id),0)=0 ";
		if($end_date==false){$c_sql2 .= "and c_saleproduct.pds_date =".$c_sdate." ";}
		else{$c_sql2 .= "and c_saleproduct.pds_date >='".$c_sdate."' and c_saleproduct.pds_date <='".$c_edate."' ";}
		if($branch_id){$c_sql2 .= "and c_saleproduct.branch_id =".$branch_id." ";}
		
		$c_sql = "($c_sql1) union ($c_sql2) order by bpds_id ";	
	
		$rscbook = $obj->getResult($c_sql);
		$cbook = "";
		for ($i = 0; $i < $rscbook["rows"]; $i++) {
			if ($cbook != '') {
				$cbook .= ", ";
			}	
			
			$curl = ($rscbook[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rscbook[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rscbook[$i]["book_id"]."";
			$cpagename = ($rscbook[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rscbook[$i]["book_id"]:"managePds".$rscbook[$i]["book_id"];

				if(!$export){
				$cbook .= "<a href='javascript:;;' onClick=\"newwindow('/appt/$curl','$cpagename')\" class=\"menu\">".$rscbook[$i]["bpds_id"]."</a>";
				}else{
				$cbook .= $rscbook[$i]["bpds_id"];
				}
		}
?>
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    			<br><b>Booking Not Finish : </b><br>
<div></div><br />
<div></div><p><?=($cbook)?$cbook:"No Booking"?></p><br />
<br /> 
<div></div><br />

			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    			<br><b>Notation : </b><br>
<div></div><br />
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#EAF7CC;"></div> &nbsp;- Green line, Multi method of payment in sale receipt.<br />
<br /> 
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#FFB9B9;"></div> &nbsp;- Red line, This sale receipt is not paid yet.<br />
<br />
<!--<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#707070;"></div> &nbsp;- Gray line, This Cancel Booking.<br />
<br />-->
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#707070;"></div> &nbsp;- Strikethrough Text, This Cancel Booking.<br />
<br />
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>

<?}
if($table=="gender")
{
	$reportname = "Gender Report Detail";
if(!$branch){
	if($sexid){
		$sexid= $obj->getIdToText($sexid,"dl_sex","sex_type","sex_id");
		$reportname = $sexid." ".$reportname;
	}
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}
	
}else{
	if($sexid){
		$sexid= $obj->getIdToText($sexid,"dl_sex","sex_type","sex_id");
		$reportname = $sexid." ".$reportname;
	}
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
}
$chkrow = $obj->getParameter("chkrow",40);
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
$bookcnt=0;$rowcnt=0;$malecnt=0;$femalecnt=0;
for($i=0; $i<$rrrs["rows"]; $i++) {
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
$url = "manage_booking.php?chkpage=1&bookid=".$rrrs[$i]["book_id"];
$pagename = "manageBooking".$rrrs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rrrs[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$rowcnt++;
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#d3d3d3\"";}
else{$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\"";}
	else{$bgcolor="class=\"even\"";}
}
$csname = $rrrs[$i]["cs_name"];
$csage = $rrrs[$i]["cs_age"];
$csgender = $rrrs[$i]["sex_type"];
if($csgender=="Male"){$malecnt++;}
else if($csgender=="Female"){$femalecnt++;}
?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rrrs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($rrrs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$rrrs[$i]["time_start"]?>&nbsp;</td>
					<td class="report" align="left"><?=$csname?>&nbsp;</td>
					<td class="report" align="center"><?=($csage>0)?$csage:"-"?>&nbsp;</td>
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
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($rrrs["rows"],0,".",",")?></b></td>
 			<td>&nbsp;</td>
 			<td>&nbsp;</td>
	</tr>
	<? $totalcnt = $rrrs["rows"];
		if($totalcnt==0){$totalcnt=1;}	// prevent warning divide by zero
	?>
	<tr height="20">
			<td colspan="4" align="right"><b>AVG Male : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format(100*$malecnt/$totalcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>AVG Female : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format(100*$femalecnt/$totalcnt,2,".",",")?></b></td>
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


<?}
if($table=="resident")
{
	
	$reportname = "Report Detail";
if(!$branch){
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s Resident ".$reportname;
	}else{
		$reportname = "Resident ".$reportname;
	}
}else{
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s Resident ".$reportname;
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Status</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;$vistotal=0;$restotal=0;$unktotal=0;
for($i=0; $i<$res["rows"]; $i++) {
//if(!$chkrow){$chkrow=1;}
if(!isset($chkrow)){$chkrow=1;}
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Status</b></td>
	</tr>
<?	
}	
//

//
$url = "manage_booking.php?chkpage=1&bookid=".$res[$i]["book_id"];
$pagename = "manageBooking".$res[$i]["book_id"];
$bpdsid=$obj->getIdToText($res[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$rowcnt++;
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\"";}
	else{$bgcolor="class=\"even\"";}
}
$csname = $res[$i]["cs_name"];
if($res[$i]["visitor"]){
	$csstatus = "Visitor";
	$vistotal++;
}else if($res[$i]["resident"]){
	$csstatus = "Resident";
	$restotal++;
}else{
	$csstatus = "Unknown";
	$unktotal++;
}
?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$res[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($res[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$res[$i]["time_start"]?>&nbsp;</td>
					<td class="report" align="left"><?=$csname?>&nbsp;</td>
					<td class="report" align="center"><?=$csstatus?>&nbsp;</td>
			</tr>
<?
}
?>
 	<tr height="20">
 			<td colspan="6">&nbsp;</td>
 	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Total Customers : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($res["rows"],0,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Visitor (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($vistotal*100/$bookcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Resident (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($restotal*100/$bookcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Unknown (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($unktotal*100/$bookcnt,2,".",",")?></b></td>
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

<?}
if($table=="visitor"){
	
		$reportname = "Report Detail";
if(!$branch){
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s Visitor ".$reportname;
	}else{
		$reportname = "Visitor ".$reportname;
	}
}else{
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s Visitor ".$reportname;
}

if($export!="Excel"&&$export){
	$chkrow=0;
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Appointment Time</b></td>	
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Status</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;$vistotal=0;$restotal=0;$unktotal=0;
for($i=0; $i<$vis["rows"]; $i++) {
//if(!$chkrow){$chkrow=1;}
if(!isset($chkrow)){$chkrow=1;}
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Status</b></td>
	</tr>
<?	
}	
//

//
$url = "manage_booking.php?chkpage=1&bookid=".$vis[$i]["book_id"];
$pagename = "manageBooking".$vis[$i]["book_id"];
$bpdsid=$obj->getIdToText($vis[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$rowcnt++;
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\"";}
	else{$bgcolor="class=\"even\"";}
}
$csname = $vis[$i]["cs_name"];
if($vis[$i]["visitor"]){
	$csstatus = "Visitor";
	$vistotal++;
}else if($vis[$i]["resident"]){
	$csstatus = "Resident";
	$restotal++;
}else{
	$csstatus = "Unknown";
	$unktotal++;
}
?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$vis[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($vis[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$vis[$i]["time_start"]?>&nbsp;</td>
					<td class="report" align="left"><?=$csname?>&nbsp;</td>
					<td class="report" align="center"><?=$csstatus?>&nbsp;</td>
			</tr>
<?
}
?>
 	<tr height="20">
 			<td colspan="6">&nbsp;</td>
 	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Total Customers : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($vis["rows"],0,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Visitor (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($vistotal*100/$bookcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Resident (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($restotal*100/$bookcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Unknown (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($unktotal*100/$bookcnt,2,".",",")?></b></td>
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
<?}
if($table=="place"){
		$reportname = "Report Detail";
if(!$branch){
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s Visitor and Resident ".$reportname;
	}else{
		$reportname = "All branch's Visitor and Resident ".$reportname;
	}
}else{
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s Visitor and Resident ".$reportname;
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Status</b></td>
	</tr>
<?
$bookcnt=0;$rowcnt=0;$vistotal=0;$restotal=0;$unktotal=0;
for($i=0; $i<$rrs["rows"]; $i++) {
//if(!$chkrow){$chkrow=1;}
if(!isset($chkrow)){$chkrow=1;}
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Status</b></td>
	</tr>
<?	
}	
//

//
$url = "manage_booking.php?chkpage=1&bookid=".$rrs[$i]["book_id"];
$pagename = "manageBooking".$rrs[$i]["book_id"];
$bpdsid=$obj->getIdToText($rrs[$i]["book_id"],"a_appointment","bpds_id","book_id");

$bookcnt++;$rowcnt++;
if($export!=false){
	$id=$bpdsid;
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$bpdsid."</a>";
}
$bgcolor="";
if($i%2!=0){$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\"";}
	else{$bgcolor="class=\"even\"";}
}
$csname = $rrs[$i]["cs_name"];
if($rrs[$i]["visitor"]){
	$csstatus = "Visitor";
	$vistotal++;
}else if($rrs[$i]["resident"]){
	$csstatus = "Resident";
	$restotal++;
}else{
	$csstatus = "Unknown";
	$unktotal++;
}
?>
			<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rrs[$i]["branch_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$dateobj->convertdate($rrs[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=$rrs[$i]["time_start"]?>&nbsp;</td>
					<td class="report" align="left"><?=$csname?>&nbsp;</td>
					<td class="report" align="center"><?=$csstatus?>&nbsp;</td>
			</tr>
<?
}
?>
 	<tr height="20">
 			<td colspan="6">&nbsp;</td>
 	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Total Customers : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($rrs["rows"],0,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Visitor (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($vistotal*100/$bookcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Resident (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($restotal*100/$bookcnt,2,".",",")?></b></td>
 			<td>&nbsp;</td>
	</tr>
	<tr height="20">
			<td colspan="4" align="right"><b>Unknown (%) : </b></td>
			<td align="left" style="padding-left:10px"><b style='color:#ff0000'> <?=number_format($unktotal*100/$bookcnt,2,".",",")?></b></td>
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

<?}

if($table=="market"){
?>
<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("marketing.inc.php");
require_once("report.inc.php");
$robj = new report();
$obj = new marketing();

$begin_date = $obj->getParameter("begin");
$end_date = $obj->getParameter("end");
$branchid = $obj->getParameter("branchid");
$cityid = $obj->getParameter("cityid");
$tbname = $obj->getParameter("tbname");
$mktype = $obj->getParameter("mktype");
$export = $obj->getParameter("export",false);
if($export=="Excel"){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Marketing Report Detail.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}
if($export=="PDF"){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&gmt=".$_SESSION["__gmt"]);
}
$status = $obj->getParameter("status");
// $status = 0   -  show all information
// $status = 1   -  show only gift certificate information
 //$status = 2   -  show only marketing code information
if($tbname=="l_marketingcode"){
	$mkcode = $obj->getParameter("mkid");
	$mktypeid = $obj->getParameter("mktypeid");
	if($mktype=="amount"){
		$rs=$obj->getmkdetail($begin_date,$end_date,$branchid,$cityid,$mkcode,$mktypeid);
		$rsp=$obj->getsaledetail($begin_date,$end_date,$branchid,$cityid,$mkcode,$mktypeid);
	}else{
		$rs=$obj->getmkqtydetail($begin_date,$end_date,$branchid,$cityid,$mkcode,$mktypeid);
		$rsp=$obj->getsaleqtydetail($begin_date,$end_date,$branchid,$cityid,$mkcode,$mktypeid);
	}
	$reportname = $obj->getIdToText($mkcode,"l_marketingcode","sign","mkcode_id")." - Marketing Report Detail";
	if($mkcode==0){$reportname = $obj->getIdToText($mktypeid,"l_mkcode_category","category_name","category_id")." - Marketing Report Detail";}
}else{
	if($mktype=="issue"){
		$gift = $obj->getParameter("mktypeid");
		$rs=$obj->getgiftissuedetail($begin_date,$end_date,$gift);
	}else{
		$gift = $obj->getParameter("mktypeid");
		$rs=$obj->getgiftdetail($begin_date,$end_date,$branchid,$cityid,$gift);
	}
	$reportname = $obj->getIdToText($gift,"gl_gifttype","gifttype_name","gifttype_id")." - Marketing Report Detail";
	if($gift==0){$reportname = "All Gift Certificate - Marketing Report Detail";}
}
if($export!="Excel"&&$export){
	if($tbname=="g_gift"){ 
	$chkrow = $obj->getParameter("chkrow",20);
	}else{ 
		if($mktype=="qty"){
			$chkrow = $obj->getParameter("chkrow",40);
		}else{
			$chkrow = $obj->getParameter("chkrow",35);
		}
	}
	$chkpage = ceil($rs["rows"]/$chkrow);
}

//echo $tbname." ".$mktype;
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);


?>
<?if($export!="Excel"){?>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" align="center"><div id="companyinfo">
			<table cellspacing="0" border="0" cellpadding="0" width="100%">
			
<? if($tbname=="g_gift"){ 
		$colspan=12;	
?>
				<tr>
					<td width="5%"></td><td width="11%"></td>
					<td width="12%"></td><td width="7%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="20%"></td><td width="7%"></td>
					<td width="5%"></td><td width="5%"></td>
				</tr>
<? }else{ 
		if($mktype=="qty"){
			$colspan=7;	
?>
				<tr>
					<td width="12%"></td><td width="12%"></td>
					<td width="12%"></td><td width="12%"></td>
					<td width="20%"></td><td width="20%"></td>
					<td width="12%"></td>
				</tr>
<? 		}else{
			$colspan=10;	
?>	
				<tr>
					<td width="7%"></td><td width="26%"></td>
					<td width="10%"></td><td width="5%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="10%"></td><td width="12%"></td>
				</tr>

	  <?}
   } ?>
				<tr>
			    	<td class="reporth" align="center" colspan="<?=$colspan?>">
			    		<b>
			    		<p>Spa Management System</p>
			    		<?=$reportname?></b><br>
			    		<p>
			    			<b style='color:#ff0000;'>
			    			<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    			<?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    			</b><br><br></p>
			    	</td>
				</tr>
			
<? if($tbname=="g_gift"){ ?>
				<tr height="32">
					<td style="text-align:center; padding-left: 10px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift no.</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Give To</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receive From</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Type</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Issue</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Expired</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Used</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receive By</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td style="text-align:center; padding-left: 2px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Value</b></td>
					<td style="text-align:center; padding-left: 2px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; padding-left: 7px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>ID Sold</b></td>
				</tr>
<? }else{ 
		if($mktype=="qty"){?>
				<tr height="32">
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customer</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
				</tr>
<? 		}else{?>
				<tr height="32">
					<td style="text-align:left; padding-left: 10px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receive By</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
				</tr>
	  <?}
   } ?>
			
			
<?
	 
$all_total=0;
$all_sc=0;	
$all_vat=0;	
$total=0;	
$total_customer=0;
$rowcnt = 0;
$total_book=0;
for($i=0;$i<$rs["rows"];$i++){
			
if($i&&$export!="Excel"&&$export&&$rowcnt%$chkrow==0){
?>
 				<tr height="20">
 					<td colspan="<?=$colspan?>">&nbsp;</td>
 				</tr>
				<tr>
					<td height="20" align="center" colspan="<?=$colspan?>">
						   <b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
					</td>
				</tr>
			</table>
			</div>
    	</td>
    </tr>
</table>
<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td class="content" align="center"><div id="companyinfo">
			<table cellspacing="0" border="0" cellpadding="0" width="100%">
			
<? if($tbname=="g_gift"){ 
		$colspan=12;	
?>
				<tr>
					<td width="5%"></td><td width="11%"></td>
					<td width="12%"></td><td width="7%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="20%"></td><td width="7%"></td>
					<td width="5%"></td><td width="5%"></td>
				</tr>
<? }else{ 
		if($mktype=="qty"){
			$colspan=7;	
?>
				<tr>
					<td width="12%"></td><td width="12%"></td>
					<td width="12%"></td><td width="12%"></td>
					<td width="20%"></td><td width="20%"></td>
					<td width="12%"></td>
				</tr>
<? 		}else{
			$colspan=10;	
?>	
				<tr>
					<td width="7%"></td><td width="26%"></td>
					<td width="10%"></td><td width="5%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="7%"></td><td width="7%"></td>
					<td width="10%"></td><td width="12%"></td>
				</tr>

	  <?}
   } ?>
				<tr>
			    	<td class="reporth" align="center" colspan="<?=$colspan?>">
			    		<b>
			    		<p>Spa Management System</p>
			    		<?=$reportname?></b><br>
			    		<p>
			    			<b style='color:#ff0000;'>
			    			<?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?>
			    			<?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?>
			    			</b><br><br></p>
			    	</td>
				</tr>
			
<? if($tbname=="g_gift"){ ?>
				<tr height="32">
					<td style="text-align:center; padding-left: 10px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift no.</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Give To</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receive From</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Type</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Issue</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Expired</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Used</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receive By</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td style="text-align:center; padding-left: 2px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Value</b></td>
					<td style="text-align:center; padding-left: 2px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; padding-left: 7px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>ID Sold</b></td>
				</tr>
<? }else{ 
		if($mktype=="qty"){?>
				<tr height="32">
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total Customer</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Company</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking Person</b></td>
					<td style="text-align:center; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
				</tr>
<? 		}else{?>
				<tr height="32">
					<td style="text-align:left; padding-left: 10px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td style="text-align:right; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receive By</b></td>
					<td style="text-align:center; padding-left: 20px; border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
				</tr>
	  <?}
	}
}			
$rowcnt++;
			
			
			
			
			
			
			
if($tbname=="g_gift"){ 
$idsold = 0;	
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["id_sold"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["id_sold"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["id_sold"]:"managePds".$rs[$i]["id_sold"];
if(!$obj->getIdToText($rs[$i]['id_sold'],"c_bpds_link","bpds_id","tb_id","tb_name like '".$rs[$i]["tb_name"]."'")){
	$idsold=$rs[$i]['id_sold'];
}else{
	$idsold="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$obj->getIdToText($rs[$i]['id_sold'],"c_bpds_link","bpds_id","tb_id","tb_name like '".$rs[$i]["tb_name"]."'")."</a>";
}
$bookid = 0;	
$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
if($export!=false||!$obj->getIdToText($rs[$i]['book_id'],"c_bpds_link","bpds_id","tb_id","tb_name like 'a_bookinginfo'")){
	$bookid=$rs[$i]['book_id'];
}else{
	$bookid="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$obj->getIdToText($rs[$i]['book_id'],"c_bpds_link","bpds_id","tb_id","tb_name like 'a_bookinginfo'")."</a>";
}
$all_total += $rs[$i]["value"];
$bgcolor = "";
if($i%2!=0){$bgcolor="bgcolor=\"#d3d3d3\"";}
else{$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";}
}
?>
	<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$rs[$i]["gift_number"]?></td>
					<td class="report"><?=$rs[$i]["give_to"]?></td>
					<td class="report" align="center"><?=$rs[$i]["receive_from"]?></td>
					<td class="report" align="center"><?=$obj->getIdToText($rs[$i]['gifttype_id'],"gl_gifttype","gifttype_name","gifttype_id")?>&nbsp;</td>
					<td class="report" align="center"><?=($rs[$i]["issue"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["issue"],'Y-m-d',$sdateformat)?></td>
					<td class="report" align="center"><?=($rs[$i]["expired"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["expired"],'Y-m-d',$sdateformat)?></td>
					<td class="report" align="center"><?=($rs[$i]["used"]=="0000-00-00")?"-":$dateobj->convertdate($rs[$i]["used"],'Y-m-d',$sdateformat)?></td>
					<td class="report" align="center"><?=($rs[$i]['receive_by_id']==0)?"-":$obj->getIdToText($rs[$i]['receive_by_id'],"l_employee","emp_nickname","emp_id")?>&nbsp;</td>
					<td class="report" align="left">&nbsp;&nbsp;<?=$rs[$i]["product"]?></td>
					<td class="report" align="center"><?=number_format($rs[$i]["value"],2,".",",")?></td>
					<td class="report" align="center"><?=$bookid?></td>
					<td class="report" align="center"><?=$idsold?>&nbsp;</td>
	</tr>
<? }else{  
$chkmk = 0;
if($status!=2){
	$chkmk = $obj->getIdToText($rs[$i]["book_id"],"g_gift","gift_id","book_id");
}

if($mktype=="qty"&&!$chkmk){

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
if($export!=false){
	$id=$rs[$i]["bpds_id"];
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";
}

if($rs[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";
	
$total_customer+=$rs[$i]["qty_person"];
$bgcolor = "";
if($total_book%2!=0){$bgcolor="bgcolor=\"#d3d3d3\"";}
else{$bgcolor="bgcolor=\"#eaeaea\"";}
if(!$export){
	if($total_book%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";}
	else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";}
}
$total_book++;
?>
				<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$id?></td>
					<td class="report" align="center"><?=$rs[$i]["branch_name"]?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?></td>
					<td class="report" align="center"><?=$rs[$i]["qty_person"]?></td>
					<td class="report" align="center"><?=$rs[$i]["cms_company"]?>&nbsp;</td>
					<td class="report" align="center"><?=$rs[$i]["cms_name"]?>&nbsp;</td>
					<td class="report" align="center"><?=$cms?></td>
				</tr>
<? 		}else{
if(!isset($rs[$i+1]["srdetail_id"])){$rs[$i+1]["srdetail_id"]=0;}
if(!isset($rs[$i]["srdetail_id"])){$rs[$i]["srdetail_id"]=0;}
if($rs[$i]["srdetail_id"]!=$rs[$i+1]["srdetail_id"]){
				///////// Discount tax or servicecharge /////////////////
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
				$product["servicescharge"]=$rs[$i]["servicescharge"];
				////////////insert March 14,2009 for new calculate tax & service charge/////////
				$rs[$i]["amount"]=$rs[$i]["unit_price"]*$rs[$i]["quantity"];
				$product["total"]=$rs[$i]["amount"];
				$product["taxpercent"]=$rs[$i]["taxpercent"];
				
				$sc = $robj->getsSvc($product);
				$vat = $robj->getsTax($product,$sc);
				
				$all_sc+=$sc;	
				$all_vat+=$vat;	
			
if($rs[$i]["pos_neg_value"]==0) {
	$all_total=$all_total-($rs[$i]["amount"]+$sc+$vat);
	$total=$total-($rs[$i]["amount"]+$sc+$vat);
	echo "<tr bgcolor=\"#d3d3d3\"class=\"odd\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";  
	$rs[$i]["amount"] = "-".$rs[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
} else {
	echo "<tr height=\"20\" bgcolor=\"#eaeaea\" class=\"even\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	$all_total=$all_total+($rs[$i]["amount"]+$sc+$vat);
	$total=$total+($rs[$i]["amount"]+$sc+$vat);
}	

$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"];
$pagename = "manageBooking".$rs[$i]["book_id"];
if($export!=false){
	$id=$rs[$i]["bpds_id"];
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";
}
if($rs[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";

if($rs[$i]["pay_id"]>1)
	$paytype = $rs[$i]["pay_name"];
else
	$paytype = "-";

if($rs[$i]["reception_code"]>1)
	$reception = $rs[$i]["reception_code"]." ".$rs[$i]["reception_name"];
else
	$reception = "-";
$chk=$rs[$i]["amount"]+$sc+$vat;
if(!$i||$rs[$i]["book_id"]!=$rs[$i-1]["book_id"]){
	$total_customer+=$rs[$i]["qty_person"];
	$total_book++;
}
			?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rs[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$rs[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($rs[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="right"><?=$cms?></td>
					<td class="report" align="center"><?=$reception?></td>
					<td class="report" align="center"><?=$paytype?></td>
				</tr>
			<?
 		} // end check srdetail_id repeat
	} // end check $mktype of l_marketing qty/amount
  }	// end check gift or mk code detail
}	// end for loop
?>		



<?
//mkcode for product
if(!isset($rsp["rows"])){$rsp["rows"]=0;}
	for($i=0;$i<$rsp["rows"];$i++){
	
	if($mktype=="qty"&&!$chkmk){
		
		$url = "manage_pdforsale.php?pdsid=".$rsp[$i]["pds_id"];
		$pagename = "managePdforsale".$rsp[$i]["pds_id"];
		if($export!=false){
			$pid=$rsp[$i]["bpds_id"];
		}else{
			$pid="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rsp[$i]["bpds_id"]."</a>";
		}
	if($total_book%2!=0){
		if($i%2==0){$bgcolor="bgcolor=\"#d3d3d3\"";}
		else{$bgcolor="bgcolor=\"#eaeaea\"";}
			if(!$export){
		if($i%2==0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";}
		else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";}
		}
	}else{
	    if($i%2!=0){$bgcolor="bgcolor=\"#d3d3d3\"";}
		else{$bgcolor="bgcolor=\"#eaeaea\"";}
			if(!$export){
		if($i%2!=0){$bgcolor="class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\"";}
		else{$bgcolor="class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\"";}
		}
	}
	$total_sale=1;
	?>
				<tr <?=$bgcolor?> height="20">
					<td class="report" align="center"><?=$pid?></td>
					<td class="report" align="center"><?=$rsp[$i]["branch_name"]?></td>
					<td class="report" align="center"><?=$rsp[$i]["pds_date"]?></td>
					<td class="report" align="center"><?=$total_sale?></td>
					<td class="report" align="center"><?="-"?>&nbsp;</td>
					<td class="report" align="center"><?="-"?>&nbsp;</td>
					<td class="report" align="center"><?="-"?></td>
				</tr>
<?
	}else{
if(!isset($rsp[$i+1]["srdetail_id"])){$rsp[$i+1]["srdetail_id"]=0;}
if(!isset($rsp[$i]["srdetail_id"])){$rsp[$i]["srdetail_id"]=0;}
if($rsp[$i]["srdetail_id"]!=$rsp[$i+1]["srdetail_id"]){
				///////// Discount tax or servicecharge /////////////////
				if(!$rsp[$i]["plus_servicecharge"]&&!$rsp[$i]["plus_vat"]){
					//echo "<br> dis tax sc";
					$rsp[$i]["unit_price"]=(100*$rsp[$i]["unit_price"])/(100+$rsp[$i]["taxpercent"]+$rsp[$i]["servicescharge"]+($rsp[$i]["taxpercent"]*$rsp[$i]["servicescharge"])/100);
				}else if(!$rsp[$i]["plus_vat"]){
					//echo "<br>dis tax : $taxpercent %";
					$rsp[$i]["unit_price"]=(100*$rsp[$i]["unit_price"])/(100+$rsp[$i]["taxpercent"]);
				}else if(!$rsp[$i]["plus_servicecharge"]){
					//echo "<br>dis sc : $servicescharge";
					$rsp[$i]["unit_price"]=(100*$rsp[$i]["unit_price"])/(100+$rsp[$i]["servicescharge"]);
				}
				
				$product["set_sc"]=1;//$rs[$i]["plus_servicecharge"];
				$product["set_tax"]=1;//$rs[$i]["plus_vat"];
				$product["servicescharge"]=$rsp[$i]["servicescharge"];
				////////////insert March 14,2009 for new calculate tax & service charge/////////
				$rsp[$i]["amount"]=$rsp[$i]["unit_price"]*$rsp[$i]["quantity"];
				$product["total"]=$rsp[$i]["amount"];
				$product["taxpercent"]=$rsp[$i]["taxpercent"];
				
				$sc = $robj->getsSvc($product);
				$vat = $robj->getsTax($product,$sc);
				
				$all_sc+=$sc;	
				$all_vat+=$vat;	
			
if($rsp[$i]["pos_neg_value"]==0) {
	$all_total=$all_total-($rsp[$i]["amount"]+$sc+$vat);
	$total=$total-($rsp[$i]["amount"]+$sc+$vat);
	echo "<tr bgcolor=\"#d3d3d3\"class=\"odd\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";  
	$rsp[$i]["amount"] = "-".$rsp[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
} else {
	echo "<tr height=\"20\" bgcolor=\"#eaeaea\" class=\"even\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	$all_total=$all_total+($rsp[$i]["amount"]+$sc+$vat);
	$total=$total+($rsp[$i]["amount"]+$sc+$vat);
}	

$url = "manage_pdforsale.php?pdsid=".$rsp[$i]["pds_id"];
$pagename = "managePdforsale".$rsp[$i]["pds_id"];

if($export!=false){
	$pid=$rsp[$i]["bpds_id"];
}else{
	$pid="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rsp[$i]["bpds_id"]."</a>";
}

if($rsp[$i]["pay_id"]>1){
	$paytype = $rsp[$i]["pay_name"];
}else{
	$paytype = "-";
}
$chk=$rs[$i]["amount"]+$sc+$vat;

if(!$i||$rsp[$i]["pds_id"]!=$rsp[$i-1]["pds_id"]){
	$total_customer++;
	$total_book++;
}
	?>
					<td class="report" align="center"><?=$pid?></td>
					<td class="report"><?=$rsp[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($rsp[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$rsp[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($rsp[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="right"><?="-"?></td>
					<td class="report" align="center"><?="-"?></td>
					<td class="report" align="center"><?=$paytype?></td>
				</tr>
	<?
		}
	}
}
?>



	
<? if($tbname=="g_gift"){ ?>
 				<tr height="20">
 					<td colspan="<?=$colspan?>">&nbsp;</td>
 				</tr>
				<tr>
					<td colspan="9" align="right" height="20"><b>Total Value:</b>&nbsp;</td>
					<td colspan="3" align="left"><b style='color:#ff0000;'><?=number_format($all_total,2,".",",")?>&nbsp;</b></td>
				</tr>
 				<tr height="20">
 					<td colspan="<?=$colspan?>">&nbsp;</td>
 				</tr>
<? } else { 
if($mktype=="qty"){
	

?>
 				<tr height="20">
 					<td colspan="<?=$colspan?>">&nbsp;</td>
 				</tr>
				<tr height="20">
					<td align="right" colspan="3"><b>Total Customers:</b>&nbsp;</td>
					<td align="center"><b style='color:#ff0000;'><?=number_format($total_customer+$rsp["rows"],0,".",",")?></b></td>
					<td align="left" colspan="3">&nbsp;</td>
				</tr>
				<tr height="24">
					<td align="right" colspan="3"><b>Total Booking:</b>&nbsp;</td>
					<td align="center"><b style='color:#ff0000;'><?=number_format($total_book+$rsp["rows"],0,".",",")?></b></td>
					<td align="left" colspan="3">&nbsp;</td>
				</tr>
 				<tr height="20">
 					<td colspan="<?=$colspan?>">&nbsp;</td>
 				</tr>
<?
	
}else{
?>
 				<tr height="20">
 					<td colspan="<?=$colspan?>">&nbsp;</td>
 				</tr>
				<tr height="20">
					<td align="right" colspan="3"><b>Total Booking:</b>&nbsp;</td>
					<td colspan="2" align="right"><b style='color:#ff0000;'><?=number_format($total_book,0,".",",")?></b></td>
					<td align="left" colspan="5">&nbsp;</td>
				</tr>
				<tr height="20">
					<td align="right" colspan="3"><b>Total Customers:</b>&nbsp;</td>
					<td colspan="2" align="right"><b style='color:#ff0000;'><?=number_format($total_customer,0,".",",")?></b></td>
					<td align="left" colspan="5">&nbsp;</td>
				</tr>				<tr height="20">
					<td colspan="3" align="right"><b>Total Revenue:</b>&nbsp;</td>
					<td colspan="2" align="right"><b style='color:#ff0000;'><?=number_format($all_total,2,".",",")?></b></td>
					<td colspan="5">&nbsp;</td>
				</tr>
<? }
}?>
				<tr height="20">
			    	<td align="center" colspan="<?=$colspan?>">
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
			</table>
			</div>
    	</td>
    </tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>


<?}
if($table=="csi"){
?>
<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
require_once("csi.inc.php");
$robj = new report();
$obj = new csi();
$date = $obj->getParameter("date");
$begindate = $obj->getParameter("begin");
$enddate = $obj->getParameter("end");

$branch_id = $obj->getParameter("branchid",1);
$cityid = $obj->getParameter("cityid",false);
$order= $obj->getParameter("order");
$sort= $obj->getParameter("sortby");
$today = date("Ymd");
$category = $obj->getParameter("category");
$category = (!$category)?"All":$obj->getIdToText($category,"fl_csi_index","csii_name","csii_id");
$branch_name = strtolower($obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id"));
/*
if($branch_id){
	$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")."'s Customer ";
	if($category != "All"){$reportname .= "- $category ";}
	$reportname .= "CSI index Report";
}else{
	$reportname = "All Customer ";
	if($category != "All"){$reportname .= "- $category ";}
	$reportname .= "CSI index Report";
}*/

$reportname = "Customer CSI index Report ";
if($category != "All"){$reportname .= "- $category ";}
	
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
	$pdf=new convert2pdf(false,true);
	//$pdf->convertFromFile("manage_cplinfo3.htm");
	$pdf->convertGraphFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print&chkrow=55");
}
// function for initial array for customer comment rows
function InitA($rscsiv,$rscsii) {
	$tmp = array();
	$db = array();
	$n = array();
	for($i=0; $i<$rscsii["rows"]; $i++){
		$db[$i] = $rscsii[$i]["csii_column_name"];
		$n[$i] = $rscsii[$i]["csii_name"];
	}
	//$db = array("at_fac","at_temp","at_m","at_aroma","at_clean","q_value","q_tr","q_mg","s_driver","s_friendly","s_greeting","s_attentive","s_manner");
	//$n = array("Facilities","Temperature","Music","Aroma","Cleanlines","Value for Money","Body Treatments","Massage","Driver","Friendly & Cheerful","Greeting","Attentiveness","Manner / Courtesy");
	
	for($i=0; $i<=$rscsii["rows"]; $i++) {
		if($i) {
			$tmp["rowsname"][$i] = $db[$i-1];		// set colume name from database
			$tmp["formname"][$i] = $n[$i-1];		// set table 1st row header 
			for($j=0; $j<$rscsiv["rows"]; $j++) {
				$tmp["value"][$i][$rscsiv[$j]["csiv_id"]] = 0;	// initial csi value to zero
			}
		}
		else {
			$tmp["formname"][$i] = " ";				// 1st rows(header) in show table
			$tmp["percent"][$i] = "CSI Percent(%)";	
		}
	}
	
	$tmp["rows"]=$i;
	return $tmp;
}

// function for initial array for customer comment rows
function allcsitoArray($init,$rs,$rscsiv) {
	$c = $init;
	for($i=1; $i<$init["rows"]; $i++) {				
		for($j=0; $j<$rs["rows"]; $j++) {
			++$c["value"][$i][$rs[$j][$init["rowsname"][$i]]];
			//++$c["value"][$i]["total"];
		}	
		$c["ptotal"][$i] = $c["value"][$i][5]+$c["value"][$i][4]+$c["value"][$i][3]+$c["value"][$i][2];
		//$c["value"][$i]["total"] = $c["value"][$i]["total"]-$c["value"][$i][1];	// $c["value"][$i]["total"] except no-recommended
		
		$c["ppercent"][$i] = 0;
		for($j=0; $j<$rscsiv["rows"]; $j++) {
			if($rscsiv[$j]["csiv_name"]){
				$c["ppercent"][$i] +=$c["value"][$i][$rscsiv[$j]["csiv_id"]]*$rscsiv[$j]["csiv_value"];
			}
		}
		
		if($c["ptotal"][$i]==0){$c["ptotal"][$i]=1;}
		$c["percent"][$i] = $c["ppercent"][$i]/$c["ptotal"][$i];
	}
	//unset($c["ptotal"][6]);
	if(array_sum($c["ptotal"])==0){$allcnt=1;}else{$allcnt=array_sum($c["ptotal"]);}
	
	$tmp["ppercent"] = $c["ppercent"];
	//unset($tmp["ppercent"][6]);
	$c["percenttotal"] = array_sum($tmp["ppercent"])/$allcnt;
	return $c;
}

$rs = $obj->getcsinfo($begindate,$enddate,$branch_id,false,$cityid);
$rscsiv = $obj->getcsivalue();
$rscsii = $obj->getcsiindex();
$init = InitA($rscsiv,$rscsii);
//print_r($init);
$csi = allcsitoArray($init,$rs,$rscsiv);

$begin_date = $dateobj->convertdate(substr($begindate,0,4)."-".substr($begindate,4,2)."-".substr($begindate,6,2),"Y-m-d",$sdateformat);
$end_date = $dateobj->convertdate(substr($enddate,0,4)."-".substr($enddate,4,2)."-".substr($enddate,6,2),"Y-m-d",$sdateformat);

$column= $obj->getParameter("column","Total only");
if($column==""){$column="Total only";}
$rsdate = $obj->getdatecol($column,$begindate,$enddate);



$dataset=array();$yaxis = array();

if($category=="All"){
	if($order=="Total"){
		array_multisort($csi["percent"],$csi["formname"]);
			if($sort=="Z > A"){
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][1+$i];
					$dataset[$i] = $csi["percent"][1+$i];
				}
			}else{
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][13-$i];
					$dataset[$i] = $csi["percent"][13-$i];
				}
			}
	}else{
	array_multisort($csi["formname"],$csi["percent"]);
	for($i=0;$i<count($csi["formname"])-1;$i++){
		if($sort=="Z > A"){
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][1+$i];
					$dataset[$i] = $csi["percent"][1+$i];
				}
			}else{
				for($i=0;$i<count($csi["formname"])-1;$i++){
					$yaxis[$i] = $csi["formname"][13-$i];
					$dataset[$i] = $csi["percent"][13-$i];
				}
			}
	}
	}
	$allcsi = $csi["percenttotal"];
}else{
	$tmpkey = array_keys($init["formname"], "$category");
	$columnname = $init["rowsname"][$tmpkey[0]];
	$rs = $obj->getcsinfo($begindate,$enddate,$branch_id,$columnname,$cityid);
	//$rsdate = $obj->getdatecol($column,$begindate,$enddate-1);
	$yaxis = $rsdate["header"];
	for($d=0;$d<$rsdate["rows"];$d++){
		$csid["percent"][$d] = 0;
		$ptotal[$d] = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			for($j=0; $j<$rscsiv["rows"]; $j++) {
				if(!isset($csid["value"][$d][$rscsiv[$j]["csiv_id"]])){$csid["value"][$d][$rscsiv[$j]["csiv_id"]]=0;}
				if($appt_date>=$rsdate["begin"][$d]&&$appt_date<=$rsdate["end"][$d]
				&&$rscsiv[$j]["csiv_id"]==$rs[$i]["$columnname"]){
						$csid["value"][$d][$rscsiv[$j]["csiv_id"]] += 1;
				}
			}
		}
		
		for($j=0; $j<$rscsiv["rows"]; $j++) {
			if(!isset($csid["value"][$d][$rscsiv[$j]["csiv_id"]])){$csid["value"][$d][$rscsiv[$j]["csiv_id"]]=0;}
			if($rscsiv[$j]["csiv_name"]){
				$csid["percent"][$d] +=$csid["value"][$d][$rscsiv[$j]["csiv_id"]]*$rscsiv[$j]["csiv_value"];
				$ptotal[$d] += $csid["value"][$d][$rscsiv[$j]["csiv_id"]];
			}
		}
		if($ptotal[$d]==0){$ptotal[$d]=1;}
		$csipd["percent"][$d]=$csid["percent"][$d] / $ptotal[$d] ;
	}
	
	if(array_sum($ptotal)==0){$allcnt=1;}else{$allcnt=array_sum($ptotal);}
	$allcsi = array_sum($csid["percent"])/$allcnt;
	$dataset = $csipd["percent"];
	
}
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
<script type="text/javascript" src="../scripts/ajax.js"></script>
<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50%"></td><td width="50%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="2">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begin_date,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($end_date,$sdateformat,$ldateformat)?><b></p>
    		<p><b style='color:#ff0000'><?="Branch : "?><?=$NbranchSrdString?></b></p>
    	</td>
	</tr>

	<tr>
    	<td width="100%" align="center" colspan="2">
    		
<div class="graph">
			<?php require 'graph.php' ?> 
</div>
 
    	</td>
	</tr>
<!--
	<tr height="32">
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Quality</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Excellent</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Good</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Average</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Poor</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No CM</b></td>
			<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
-->
	<tr height="30">
    	<td width="100%" align="center" colspan="2"><br>
    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
    	</td>
	</tr>
</table></td>
	</tr>
</table>

<?}
if($table=="total"){
		$reportname = "Total Marketing Report Detail";
		if(!$branch){
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}else{
		$reportname = "All branch's ".$reportname;
	}
}else{
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
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
<?	
/////
			//table a_bookinginfo
		$sql1 = "select a_appointment.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.c_set_cms as cms,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "cl_product.pd_category_id as pd_category_id,";/////category id
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql1 .= "l_employee.emp_nickname as reception_name,";
		$sql1 .= "l_employee.emp_code as reception_code, ";	
		$sql1 .= "c_salesreceipt.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "bl_branchinfo.city_id as city_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "l_paytype.pay_id as pay_id,";
		$sql1 .= "l_marketingcode.mkcode_id as code_id,";//marketing code
		$sql1 .= "l_paytype.pay_name as pay_name ";
		
		
		$sql1 .= "from a_bookinginfo,bl_branchinfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_paytype,l_employee,a_appointment,l_tax,l_marketingcode ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.b_branch_id = bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.book_id = a_appointment.book_id ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		
		
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$begin_date." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$begin_date."' and a_bookinginfo.b_appt_date<='".$end_date."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($cityid){$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";}
		//if($pd_id){$sql1 .= "and c_srdetail.pd_id=".$pd_id." ";}
		//if($pdcategoryid){$sql1 .= "and cl_product.pd_category_id=$pdcategoryid ";}
		
		//$sql1 .= "and c_saleproduct.mkcode_id=l_marketingcode.mkcode_id ";//
		//$sql1 .= "and c_saleproduct.mkcode_id!=1 ";//
		$sql1 .= "and a_bookinginfo.mkcode_id=l_marketingcode.mkcode_id ";
		$sql1 .= "and a_bookinginfo.mkcode_id!=1 ";
		
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";
		$sql1 .= "and l_employee.emp_id = a_bookinginfo.b_receive_id ";
		$sql1 .= "order by a_bookinginfo.book_id,a_bookinginfo.b_branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		
				//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "\"0\" as cms,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "cl_product.pd_category_id as pd_category_id,";/////
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql2 .= "\"-\" as reception_name,";
		$sql2 .= "\"\" as reception_code, ";	
		$sql2 .= "c_salesreceipt.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "bl_branchinfo.city_id as city_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "l_paytype.pay_id as pay_id,";
		$sql2 .= "l_marketingcode.mkcode_id as code_id,";//marketing code
		$sql2 .= "l_paytype.pay_name as pay_name ";
		
		
		$sql2 .= "from c_saleproduct,bl_branchinfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_paytype,c_bpds_link,l_tax,l_marketingcode ";
	    $sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
	    $sql2 .= "and c_saleproduct.branch_id = bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";//
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";//
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		
		 
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date='".$begin_date."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$begin_date."' and c_saleproduct.pds_date<='".$end_date."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		if($cityid){$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";}
		//if($pd_id){$sql2 .= "and c_srdetail.pd_id=".$pd_id." ";}
		//if($pdcategoryid){$sql2 .= "and cl_product.pd_category_id=$pdcategoryid ";}
		
		$sql2 .= "and c_saleproduct.mkcode_id=l_marketingcode.mkcode_id ";//
		$sql2 .= "and c_saleproduct.mkcode_id!=1 ";//
		
		
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1 ";
		$sql2 .= "order by c_bpds_link.tb_id,c_saleproduct.branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";	

		$sql = "($sql1) union ($sql2) order by paid_confirm desc,bpds_id,branch_id,salesreceipt_id,srdetail_id ";
///////
		

//echo $sql;
		$mk = $obj->getResult($sql);
///
$all_sc=0; $all_vat=0; $all_total=0;	
	$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srdetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
for($i=0; $i<$mk["rows"]; $i++) {
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
}		


		///////// Discount tax or servicecharge /////////////////
		if(!$mk[$i]["plus_servicecharge"]&&!$mk[$i]["plus_vat"]){
			//echo "<br> dis tax sc";
			$mk[$i]["unit_price"]=(100*$mk[$i]["unit_price"])/(100+$mk[$i]["taxpercent"]+$mk[$i]["servicescharge"]+($mk[$i]["taxpercent"]*$mk[$i]["servicescharge"])/100);
		}else if(!$mk[$i]["plus_vat"]){
			//echo "<br>dis tax : $taxpercent %";
			$mk[$i]["unit_price"]=(100*$mk[$i]["unit_price"])/(100+$mk[$i]["taxpercent"]);
		}else if(!$mk[$i]["plus_servicecharge"]){
			//echo "<br>dis sc : $servicescharge";
			$mk[$i]["unit_price"]=(100*$mk[$i]["unit_price"])/(100+$mk[$i]["servicescharge"]);
		}

		$product["set_sc"]=1;//$rs[$i]["plus_servicecharge"];
		$product["set_tax"]=1;//$rs[$i]["plus_vat"];
		//$product["set_sc"]=$rs[$i]["plus_servicecharge"];
		//$product["set_tax"]=$rs[$i]["plus_vat"];
		$product["servicescharge"]=$mk[$i]["servicescharge"];
		////////////insert March 14,2009 for new calculate tax & service charge/////////
		$mk[$i]["amount"]=$mk[$i]["unit_price"]*$mk[$i]["quantity"];
		$product["total"]=$mk[$i]["amount"];
		$product["taxpercent"]=$mk[$i]["taxpercent"];
						
						$sc = $obj->getsSvc($product);
						$vat = $obj->getsTax($product,$sc);
						$rowcnt++;
						$all_sc+=$sc;	
						$all_vat+=$vat;	
						$all_total+=($product["total"]+$sc+$vat);	
						//echo "<br>togal : ".number_format($product["total"],2,".",",");
					
if($mk[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";

if($mk[$i]["pay_id"]>1)
	$payname = $mk[$i]["pay_name"];
else
	$payname = "-";

if($mk[$i]["reception_code"]>1)
	$reception = $mk[$i]["reception_code"]." ".$mk[$i]["reception_name"];
else
	$reception = "-";

		if($mk[$i]["pay_id"]!=1)
			$keyword = $mk[$i]["pay_name"];
		else
			$keyword = "Unknown";

		$key = array_search($keyword, $paytype["type"]);
	
		
		if(!$key) {	
			$key = $pay_index;
			$pay_index++;
		}
		if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}
		
		if($mk[$i]["pay_id"]!=1) {
			$paytype["type"][$key] = $mk[$i]["pay_name"];
			if($mk[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}	
		else {
			$paytype["type"][$key] = "Unknown";
			if($mk[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}
		
if($mk[$i]["paid_confirm"]==0){
	echo "<tr bgcolor=\"#ffb9b9\" class=\"paidconfirm\" height=\"20\">\n"; 
	if($mk[$i]["pos_neg_value"]==0){
		$all_total-=($product["total"]+$sc+$vat)*2;
		$mk[$i]["amount"] = "-".$mk[$i]["amount"];
		$sc = "-".$sc;
		$vat = "-".$vat;
	}
}else if($mk[$i]["pos_neg_value"]==0) {
	$all_total-=($product["total"]+$sc+$vat)*2;
	$bgcolor = "#eaeaea";
	if(!$export){$bgcolor = "#d3d3d3";}
	echo "<tr bgcolor=\"$bgcolor\" class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";   
	$mk[$i]["amount"] = "-".$mk[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
}
else {
	$sqlcMp = "select * from c_srpayment where salesreceipt_id=".$mk[$i]["salesreceipt_id"]."";
	$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		echo "<tr height=\"20\" bgcolor=\"#eaf7cc\" class=\"multipay\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaf7cc'\" height=\"20\">\n";
	}else{
		echo "<tr height=\"20\" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	}
}	   

$giftno = ($mk[$i]["tb_name"]=="a_bookinginfo")?$obj->getIdToText($mk[$i]["book_id"],"g_gift","gift_number","book_id"):" ";
if(!$giftno){$giftno = "-";}
$url = ($mk[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$mk[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$mk[$i]["book_id"]."";
$pagename = ($mk[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$mk[$i]["book_id"]:"managePds".$mk[$i]["book_id"];
if($export!=false){
	$id="<b>".$mk[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$mk[$i]["bpds_id"]."</a>";
}	?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$mk[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($mk[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$mk[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($mk[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="center"><?=$cms?></td>
					<td class="report" align="left"><?=$reception?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="center"><?=$giftno?>&nbsp;</td>
 				</tr>
 		<?				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $mk[$i]["salesreceipt_id"];			
 	
 	$sqlMp = "select * from c_srpayment where salesreceipt_id=".$mk[$i]["salesreceipt_id"]."";
		$mpId = $obj->getResult($sqlMp);
		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$mpId){			
 	$sqlSr = "select `c_salesreceipt`.* , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$mk[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 		
 				
 				} 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	$bookSrdOld="";
 	$bookPayId="";
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 

 		?>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
 <?
 if($export&& $export!="Excel" && (count($paytype["type"])+$rowcnt) > $chkrow){
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
					
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);


		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
				<!--	<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
	}else{
			for($i=1; $i<=count($paytype["type"]); $i++) {?>
				<!--<tr height="20">
						<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
					</tr>-->
			<? } 
	}
	
	
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			"and `c_srpayment`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 //	echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>

					<!--<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
?>					

					</table>
					</td>
					<td colspan="8" align="left" valign="top" height="20" style="padding-right:7px;">
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
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    		<br>Method of payment column shows the higest pay price in that sales receipt
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<?}
if($table=="pos"){
	
	$reportname = "Sale Category Report Detail";
if(!$branch){
	if($pdcategoryid){
		$pdcategoryid= $obj->getIdToText($pdcategoryid,"cl_product_category","pd_category_name","pd_category_id");
		$reportname = $pdcategoryid." ".$reportname;
	}
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}
	
}else{
	if($pdcategoryid){
		$pdcategoryid= $obj->getIdToText($pdcategoryid,"cl_product_category","pd_category_name","pd_category_id");
		$reportname = $pdcategoryid." ".$reportname;
	}
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" class="reporth" colspan="12">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
<?	$all_sc=0; $all_vat=0; $all_total=0;	
	$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srdetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
for($i=0; $i<$pos["rows"]; $i++) {
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Received By</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
    	
	
<?	
}
//
///
	///////// Discount tax or servicecharge /////////////////
		if(!$pos[$i]["plus_servicecharge"]&&!$pos[$i]["plus_vat"]){
			//echo "<br> dis tax sc";
			$pos[$i]["unit_price"]=(100*$pos[$i]["unit_price"])/(100+$pos[$i]["taxpercent"]+$pos[$i]["servicescharge"]+($pos[$i]["taxpercent"]*$pos[$i]["servicescharge"])/100);
		}else if(!$pos[$i]["plus_vat"]){
			//echo "<br>dis tax : $taxpercent %";
			$pos[$i]["unit_price"]=(100*$pos[$i]["unit_price"])/(100+$pos[$i]["taxpercent"]);
		}else if(!$pos[$i]["plus_servicecharge"]){
			//echo "<br>dis sc : $servicescharge";
			$pos[$i]["unit_price"]=(100*$pos[$i]["unit_price"])/(100+$pos[$i]["servicescharge"]);
		}

		$product["set_sc"]=1;//$rs[$i]["plus_servicecharge"];
		$product["set_tax"]=1;//$rs[$i]["plus_vat"];
		//$product["set_sc"]=$rs[$i]["plus_servicecharge"];
		//$product["set_tax"]=$rs[$i]["plus_vat"];
		$product["servicescharge"]=$pos[$i]["servicescharge"];
		////////////insert March 14,2009 for new calculate tax & service charge/////////
		$pos[$i]["amount"]=$pos[$i]["unit_price"]*$pos[$i]["quantity"];
		$product["total"]=$pos[$i]["amount"];
		$product["taxpercent"]=$pos[$i]["taxpercent"];
						
						$sc = $obj->getsSvc($product);
						$vat = $obj->getsTax($product,$sc);
						$rowcnt++;
						$all_sc+=$sc;	
						$all_vat+=$vat;	
						$all_total+=($product["total"]+$sc+$vat);	
						//echo "<br>togal : ".number_format($product["total"],2,".",",");
					
if($pos[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";

if($pos[$i]["pay_id"]>1)
	$payname = $pos[$i]["pay_name"];
else
	$payname = "-";

if($pos[$i]["reception_code"]>1)
	$reception = $pos[$i]["reception_code"]." ".$pos[$i]["reception_name"];
else
	$reception = "-";

		if($pos[$i]["pay_id"]!=1)
			$keyword = $pos[$i]["pay_name"];
		else
			$keyword = "Unknown";

		$key = array_search($keyword, $paytype["type"]);
	
		
		if(!$key) {	
			$key = $pay_index;
			$pay_index++;
		}
		if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}
		
		if($pos[$i]["pay_id"]!=1) {
			$paytype["type"][$key] = $pos[$i]["pay_name"];
			if($pos[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}	
		else {
			$paytype["type"][$key] = "Unknown";
			if($pos[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}
		
if($pos[$i]["paid_confirm"]==0){
	echo "<tr bgcolor=\"#ffb9b9\" class=\"paidconfirm\" height=\"20\">\n"; 
	if($pos[$i]["pos_neg_value"]==0){
		$all_total-=($product["total"]+$sc+$vat)*2;
		$pos[$i]["amount"] = "-".$pos[$i]["amount"];
		$sc = "-".$sc;
		$vat = "-".$vat;
	}
}else if($pos[$i]["pos_neg_value"]==0) {
	$all_total-=($product["total"]+$sc+$vat)*2;
	$bgcolor = "#eaeaea";
	if(!$export){$bgcolor = "#d3d3d3";}
	echo "<tr bgcolor=\"$bgcolor\" class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";   
	$pos[$i]["amount"] = "-".$pos[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
}
else {
	$sqlcMp = "select * from c_srpayment where salesreceipt_id=".$pos[$i]["salesreceipt_id"]."";
	$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		echo "<tr height=\"20\" bgcolor=\"#eaf7cc\" class=\"multipay\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaf7cc'\" height=\"20\">\n";
	}else{
		echo "<tr height=\"20\" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	}
}	   

$giftno = ($pos[$i]["tb_name"]=="a_bookinginfo")?$obj->getIdToText($pos[$i]["book_id"],"g_gift","gift_number","book_id"):" ";
if(!$giftno){$giftno = "-";}
$url = ($pos[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$pos[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$pos[$i]["book_id"]."";
$pagename = ($pos[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$pos[$i]["book_id"]:"managePds".$pos[$i]["book_id"];
if($export!=false){
	$id="<b>".$pos[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$pos[$i]["bpds_id"]."</a>";
}
//

//	
?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$pos[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($pos[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$pos[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($pos[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="center"><?=$cms?></td>
					<td class="report" align="left"><?=$reception?></td>
					<td class="report" align="center"><?=$pos[$i]["branch_name"]?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="center"><?=$giftno?>&nbsp;</td>
 				</tr>
 		<?				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $pos[$i]["salesreceipt_id"];			
 	
 	$sqlMp = "select * from c_srpayment where salesreceipt_id=".$pos[$i]["salesreceipt_id"]."";
		$mpId = $obj->getResult($sqlMp);
		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$mpId){			
 	$sqlSr = "select `c_salesreceipt`.* , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$pos[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 		
 				
 				} 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	$bookSrdOld="";
 	$bookPayId="";
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 

 		?>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
 <?
 if($export&& $export!="Excel" && (count($paytype["type"])+$rowcnt) > $chkrow){
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
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
					
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);


		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
	}else{
			for($i=1; $i<=count($paytype["type"]); $i++) {?>
				<!--<tr height="20">
						<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
					</tr>-->
			<? } 
	}
	
	
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			"and `c_srpayment`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 	//echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
?>					
					</table>
					</td>
					<td colspan="8" align="left" valign="top" height="20" style="padding-right:7px;">
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
			    	<td width="100%" align="center" colspan="12" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="12" ><br>
			    		<br>Method of payment column shows the higest pay price in that sales receipt
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<?}
if($table=="neg"){
	
	$reportname = "Sale Category Report Detail";
if(!$branch){
	if($pdcategoryid){
		$pdcategoryid= $obj->getIdToText($pdcategoryid,"cl_product_category","pd_category_name","pd_category_id");
		$reportname = $pdcategoryid." ".$reportname;
	}
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}
	
}else{
	if($pdcategoryid){
		$pdcategoryid= $obj->getIdToText($pdcategoryid,"cl_product_category","pd_category_name","pd_category_id");
		$reportname = $pdcategoryid." ".$reportname;
	}
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td width="100%" align="center" class="reporth" colspan="12">
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
<?	$all_sc=0; $all_vat=0; $all_total=0;	
	$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srdetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
for($i=0; $i<$poss["rows"]; $i++) {
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
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
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Unit Price</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Qty</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Amount</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>SC</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Received By</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Gift NO.</b></td>
	</tr>
    	
	
<?	
}
//
///
	///////// Discount tax or servicecharge /////////////////
		if(!$poss[$i]["plus_servicecharge"]&&!$poss[$i]["plus_vat"]){
			//echo "<br> dis tax sc";
			$poss[$i]["unit_price"]=(100*$poss[$i]["unit_price"])/(100+$poss[$i]["taxpercent"]+$poss[$i]["servicescharge"]+($poss[$i]["taxpercent"]*$poss[$i]["servicescharge"])/100);
		}else if(!$poss[$i]["plus_vat"]){
			//echo "<br>dis tax : $taxpercent %";
			$poss[$i]["unit_price"]=(100*$poss[$i]["unit_price"])/(100+$poss[$i]["taxpercent"]);
		}else if(!$poss[$i]["plus_servicecharge"]){
			//echo "<br>dis sc : $servicescharge";
			$poss[$i]["unit_price"]=(100*$poss[$i]["unit_price"])/(100+$poss[$i]["servicescharge"]);
		}

		$product["set_sc"]=1;//$rs[$i]["plus_servicecharge"];
		$product["set_tax"]=1;//$rs[$i]["plus_vat"];
		//$product["set_sc"]=$rs[$i]["plus_servicecharge"];
		//$product["set_tax"]=$rs[$i]["plus_vat"];
		$product["servicescharge"]=$poss[$i]["servicescharge"];
		////////////insert March 14,2009 for new calculate tax & service charge/////////
		$poss[$i]["amount"]=$poss[$i]["unit_price"]*$poss[$i]["quantity"];
		$product["total"]=$poss[$i]["amount"];
		$product["taxpercent"]=$poss[$i]["taxpercent"];
						
						$sc = $obj->getsSvc($product);
						$vat = $obj->getsTax($product,$sc);
						$rowcnt++;
						$all_sc+=$sc;	
						$all_vat+=$vat;	
						$all_total+=($product["total"]+$sc+$vat);	
						//echo "<br>togal : ".number_format($product["total"],2,".",",");
					
if($poss[$i]["cms"])
	$cms = "<span style='color:#ff0000'>yes</span>";
else
	$cms = "<span style='color:#ff0000'>no</span>";

if($poss[$i]["pay_id"]>1)
	$payname = $poss[$i]["pay_name"];
else
	$payname = "-";

if($poss[$i]["reception_code"]>1)
	$reception = $poss[$i]["reception_code"]." ".$poss[$i]["reception_name"];
else
	$reception = "-";

		if($poss[$i]["pay_id"]!=1)
			$keyword = $poss[$i]["pay_name"];
		else
			$keyword = "Unknown";

		$key = array_search($keyword, $paytype["type"]);
	
		
		if(!$key) {	
			$key = $pay_index;
			$pay_index++;
		}
		if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}
		
		if($poss[$i]["pay_id"]!=1) {
			$paytype["type"][$key] = $poss[$i]["pay_name"];
			if($poss[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}	
		else {
			$paytype["type"][$key] = "Unknown";
			if($poss[$i]["pos_neg_value"]==0){$paytype["value"][$key] -= $product["total"]+$sc+$vat;}
			else{$paytype["value"][$key] += $product["total"]+$sc+$vat;}
		}
		
if($poss[$i]["paid_confirm"]==0){
	echo "<tr bgcolor=\"#ffb9b9\" class=\"paidconfirm\" height=\"20\">\n"; 
	if($poss[$i]["pos_neg_value"]==0){
		$all_total-=($product["total"]+$sc+$vat)*2;
		$poss[$i]["amount"] = "-".$poss[$i]["amount"];
		$sc = "-".$sc;
		$vat = "-".$vat;
	}
}else if($poss[$i]["pos_neg_value"]==0) {
	$all_total-=($product["total"]+$sc+$vat)*2;
	$bgcolor = "#eaeaea";
	if(!$export){$bgcolor = "#d3d3d3";}
	echo "<tr bgcolor=\"$bgcolor\" class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" height=\"20\">\n";   
	$poss[$i]["amount"] = "-".$poss[$i]["amount"];
	$sc = "-".$sc;
	$vat = "-".$vat;
}
else {
	$sqlcMp = "select * from c_srpayment where salesreceipt_id=".$poss[$i]["salesreceipt_id"]."";
	$cmpId = $obj->getResult($sqlcMp);
		
	if($cmpId["rows"]>1){
		echo "<tr height=\"20\" bgcolor=\"#eaf7cc\" class=\"multipay\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaf7cc'\" height=\"20\">\n";
	}else{
		echo "<tr height=\"20\" class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\">\n";
	}
}	   

$giftno = ($poss[$i]["tb_name"]=="a_bookinginfo")?$obj->getIdToText($poss[$i]["book_id"],"g_gift","gift_number","book_id"):" ";
if(!$giftno){$giftno = "-";}
$url = ($poss[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$poss[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$poss[$i]["book_id"]."";
$pagename = ($poss[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$poss[$i]["book_id"]:"managePds".$poss[$i]["book_id"];
if($export!=false){
	$id="<b>".$poss[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','".$pagename."')\" class=\"menu\">".$poss[$i]["bpds_id"]."</a>";
}
//

//	
?>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$poss[$i]["pd_name"]?></td>
					<td class="report" align="right"><?=number_format($poss[$i]["unit_price"],2,".",",")?></td>
					<td class="report" align="center"><?=$poss[$i]["quantity"]?></td>
					<td class="report" align="right"><?=number_format($poss[$i]["amount"],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($sc,2,".",",")?></td>
					<td class="report" align="right"><?=number_format($vat,2,".",",")?></td>
					<td class="report" align="center"><?=$cms?></td>
					<td class="report" align="left"><?=$reception?></td>
					<td class="report" align="center"><?=$poss[$i]["branch_name"]?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="center"><?=$giftno?>&nbsp;</td>
 				</tr>
 		<?				
 	//Get all salesreceipt_id in report	
 	$Srddetail[$i] = $poss[$i]["salesreceipt_id"];			
 	
 	$sqlMp = "select * from c_srpayment where salesreceipt_id=".$poss[$i]["salesreceipt_id"]."";
		$mpId = $obj->getResult($sqlMp);
		//echo $sqlMp."<br>".$mpId["rows"]."<br>";
	if(!$mpId){			
 	$sqlSr = "select `c_salesreceipt`.* , `l_paytype`.`pay_name` from c_salesreceipt, l_paytype where salesreceipt_id=".$poss[$i]["salesreceipt_id"]." AND `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`";
		//echo $sqlSr; 
		$srId = $obj->getResult($sqlSr);	
		for ($k = 0; $k < $srId["rows"]; $k++) {
				$oldSrd[$srcound]["pay_id"] = $srId[$k]["pay_id"];
				$oldSrd[$srcound]["paytype"] = $srId[$k]["pay_name"];
				$oldSrd[$srcound]["pay_price"] = $srId[$k]["sr_total"];
				$Srdold[$srcound] = $srId[$k]["salesreceipt_id"];
			}	
	$srcound++;
	}
 		
 				
 				} 
 	if($Srddetail){
    	$bookSrdString = implode(",", $Srddetail); 
 	}
 	$bookSrdOld="";
 	$bookPayId="";
 	if($Srdold){
    	$bookSrdOld = implode(",", $Srdold); 
 	} 

 		?>
 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
 <?
 if($export&& $export!="Excel" && (count($paytype["type"])+$rowcnt) > $chkrow){
?>
	<tr>
		<td width="100%" align="center" colspan="12" ><br>
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
		<td width="7%"></td><td width="15%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="7%"></td><td width="7%"></td>
		<td width="8%"></td><td width="11%"></td>
		<td width="9%"></td><td width="7%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
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
					
<?
	//Get c_srpayment to show result
		
		$newPd = array ();
		$sqlPd = "SELECT `l_paytype`.`pay_name` , SUM( `c_srpayment`.`pay_total` ) AS total_price
FROM c_srpayment, l_paytype
WHERE `c_srpayment`.`pay_id` = `l_paytype`.`pay_id`
AND `c_srpayment`.`salesreceipt_id` IN ( ".$bookSrdString." ) AND `c_srpayment`.`pay_id` != 1 GROUP BY `l_paytype`.`pay_name`";
		//echo $sqlPd; 
		$srPd = $obj->getResult($sqlPd);


		if($srPd){
		for ($i = 0; $i < $srPd["rows"]; $i++) {
			$newPd[$i]["mp_type"] = $srPd[$i]["pay_name"];
			$newPd[$i]["mp_price"] = $srPd[$i]["total_price"];
		
		for ($k = 0; $k < $srcound; $k++) {
				if($newPd[$i]["mp_type"]==$oldSrd[$k]["paytype"]){
					$newPd[$i]["mp_price"]=$newPd[$i]["mp_price"]+$oldSrd[$k]["pay_price"];	
				}
		}
	
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newPd[$i]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newPd[$i]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
	}else{
			for($i=1; $i<=count($paytype["type"]); $i++) {?>
				<!--<tr height="20">
						<td align="right"><b><?=$paytype["type"][$i] ?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($paytype["value"][$i],2,".",",")?></b></td>
					</tr>-->
			<? } 
	}
	
	
	$newSr = array ();
	$sqlSr = "select `c_salesreceipt`.pay_id , `l_paytype`.`pay_name`, sum(`c_salesreceipt`.`sr_total`) as sr_total from " .
 			"`c_salesreceipt`, `l_paytype` where `c_salesreceipt`.`pay_id` = `l_paytype`.`pay_id`" .
 			"and `c_salesreceipt`.`salesreceipt_id` in (".$bookSrdOld.") " .
 			"and `c_salesreceipt`.`pay_id` not in (".$bookPayId.") " .
 			"and `c_srpayment`.`pay_id` != 1 " .
 			"group by `c_salesreceipt`.`pay_id`";
 	//echo $sqlSr;
		$srSd = $obj->getResult($sqlSr);
		for ($k = 0; $k < $srSd["rows"]; $k++) {	
					$newSr[$k]["mp_type"]=$srSd[$k]["pay_name"];	
					$newSr[$k]["mp_price"]=$srSd[$k]["sr_total"];
					
?>
					<!--<tr height="20">
						<td align="right"><b><?=$newSr[$k]["mp_type"]?> : &nbsp;&nbsp;</b></td>
						<td><b style='color:#ff0000'>&nbsp;&nbsp;<?=number_format($newSr[$k]["mp_price"],2,".",",")?></b></td>
					</tr>-->
<? 
		}
?>					
					</table>
					</td>
					<td colspan="8" align="left" valign="top" height="20" style="padding-right:7px;">
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
			    	<td width="100%" align="center" colspan="12" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="12" ><br>
			    		<br>Method of payment column shows the higest pay price in that sales receipt
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>
<?}
if($table=="freecust"){

$reportname = "Free customer & Hour Report Detail";
if(!$branch){
	if($payid){
		$payid = $obj->getIdToText($payid,"l_paytype","pay_name","pay_id");
		$reportname = $payid." ".$reportname;
	}
	if($cityid){
		$cityname = $obj->getIdToText($cityid,"al_city","city_name","city_id");
		$reportname = $cityname."'s ".$reportname;
	}
	
}else{
	if($payid){
		$payid = $obj->getIdToText($payid,"l_paytype","pay_name","pay_id");
		$reportname = $payid." ".$reportname;
	}
	$branchname = $obj->getIdToText($branch,"bl_branchinfo","branch_name","branch_id");
	$reportname = $branchname."'s ".$reportname;
}
$payid = $obj->getParameter("payid");
$branch_id = $obj->getParameter("branchid");
$cityid = $objcc->getParameter("cityid");

?>
<?
$header = "\t<tr>\n";
$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"11\" ><br>\n";
$header .= "\t\t\t<b>Printed: </b>".$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")."\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "</table></td>\n";
$header .= "\t</tr>\n";
$header .= "</table>\n";
$header .= "<hr style=\"page-break-before:always;border:0;color:#ffffff;\" />\n";
$header .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td valign=\"top\" style=\"padding:10 20 50 20;\" width=\"100%\" align=\"center\">\n";
$header .= "\t<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td width=\"8%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"5%\"></td>\n";
$header .= "\t\t<td width=\"12%\"><td width=\"7%\"></td><td width=\"7%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"10%\"></td><td width=\"8%\"></td>\n";
$header .= "\t\t<td width=\"5%\"></td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td class=\"reporth\" width=\"100%\" align=\"center\" colspan=\"11\">\n";
$header .= "\t\t\t<b><p>Spa Management System</p>\n";
$header .= "\t\t\t$reportname</b><br>\n";
$header .= "\t\t\t<p><b style='color:#ff0000'>";
$header .= $dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= ($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat);
$header .= "<b><br><br></p>\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr height=\"32\">\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Receipt No.</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Booking ID</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Customer Name</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Branch</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Room</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Date</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Time</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Method of Payment</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Total</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Cashier</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Reprinted</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>CMS</b></td>\n";
$header .= "\t</tr>\n";
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="8%"></td><td width="8%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="12%"></td><td width="7%"></td>
		<td width="7%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="8%"></td><td width="5%"></td>
		
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Receipt No.</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Booking ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Customer Name</b></td>
					<td align="left" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Room</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Time</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Method of Payment</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Cashier</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Reprinted</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>CMS</b></td>
	</tr>
<?
//
//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm,";
		$sql1 .= "c_bpds_link.tb_name as tb_name,";
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date,bl_branchinfo.branch_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.b_customer_name as customer_name,";
		$sql1 .= "a_bookinginfo.c_set_cms as cms,";
		$sql1 .= "c_salesreceipt.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_number,";
		$sql1 .= "(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.book_id = a_bookinginfo.book_id and c_salesreceipt.paid_confirm=1) as sr_total ,";
		$sql1 .= "l_paytype.pay_name as pay_name, ";
		$sql1 .= "l_hour.hour_name,max(log_c_srprint_tmp.reprint_times) as reprint_times,s_user.u ";
		
		//for cancel book
		$sql1 .=",a_bookinginfo.b_set_cancel as set_cancel ";
			
		$sql1 .= "from c_salesreceipt left join " .
				"(SELECT log_c_srprint.* 
					FROM log_c_srprint
					order by log_c_srprint.reprint_times desc)" .
				" as log_c_srprint_tmp " .
				"on log_c_srprint_tmp.salesreceipt_id=c_salesreceipt.salesreceipt_id " .
				"left join s_user on log_c_srprint_tmp.l_lu_user=s_user.u_id," .
				"a_bookinginfo,l_hour,bl_branchinfo,c_srpayment,l_paytype,c_bpds_link ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and l_hour.hour_id=a_bookinginfo.b_book_hour ";
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srpayment.salesreceipt_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		if($cityid){$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($payid){
		$sql1 .= "and c_srpayment.pay_id=$payid ";
		}
		$sql1 .= "and c_srpayment.pay_id=l_paytype.pay_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		//$sql1 .= "and c_salesreceipt.sr_total=0 ";
		$sql1 .= "and a_bookinginfo.a_member_code=0 ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$begin_date." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$begin_date."' and a_bookinginfo.b_appt_date<='".$end_date."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
	//	$sql1 .= "group by c_salesreceipt.book_id ";
				$sql1 .= "group by c_salesreceipt.salesreceipt_id ";
		//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_date as appt_date,bl_branchinfo.branch_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "\"-\" as customer_name,";
		$sql2 .= "\"0\" as cms,";
		$sql2 .= "c_salesreceipt.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_number,";
		$sql2 .= "(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.pds_id = c_saleproduct.pds_id and c_salesreceipt.paid_confirm=1) as sr_total ,";
		$sql2 .= "l_paytype.pay_name as pay_name, ";
		$sql2 .= "\"-\" as hour_name,max(log_c_srprint_tmp.reprint_times) as reprint_times,s_user.u ";
		
		//for cancel product
		$sql2 .=",c_saleproduct.set_cancel as set_cancel ";
		
		$sql2 .= "from c_salesreceipt left join " .
				"(SELECT log_c_srprint.* 
					FROM log_c_srprint
					order by log_c_srprint.reprint_times desc)" .
				" as log_c_srprint_tmp " .
				"on log_c_srprint_tmp.salesreceipt_id=c_salesreceipt.salesreceipt_id " .
				"left join s_user on log_c_srprint_tmp.l_lu_user=s_user.u_id," .
				"c_saleproduct,bl_branchinfo,c_srpayment,l_paytype,c_bpds_link ";
		$sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srpayment.salesreceipt_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		if($cityid){$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($payid){
		$sql2 .= "and c_srpayment.pay_id=$payid ";
		}
		$sql2 .= "and c_srpayment.pay_id=l_paytype.pay_id ";
		//$sql2 .= "and c_salesreceipt.sr_total=0 ";
		$sql2 .= "and c_saleproduct.a_member_code=0 ";
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date='".$begin_date."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$begin_date."' and c_saleproduct.pds_date<='".$end_date."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		//$sql2 .= "group by c_salesreceipt.book_id ";
		$sql2 .= "group by c_salesreceipt.salesreceipt_id ";
		$sql = "($sql1) union ($sql2) order by paid_confirm desc,branch_id,salesreceipt_number,bpds_id ";
		//echo $sql;
		$rsss = $obj->getResult($sql);
//

$all_total=0;		
$rowcnt=0;
$paytype["type"] = array();
$paytype["value"] = array();
$payvalue = array();
$pay_index = 1;
$Srddetail = array ();
$oldSrd = array ();
$Srdold = array ();
$PayId = array ();
$srcound = 0;
$rsunknown_value=0;
$ii=0;
$bookSrdString="";
$bookSrdOld="";
$bookPayId="";
for($i=0; $i<$rsss["rows"]; $i++) {
// separate page when export
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
	echo $header;	$rowcnt=0;
}	
if($rsss[$i]["set_cancel"]==0 && $rsss[$i]["paid_confirm"]==1){
// summary each payment's total
$keyword = ($rsss[$i]["pay_id"]!=1)?$rsss[$i]["pay_name"]:"Unknown";
$key = array_search($keyword, $paytype["type"]);

if(!$key) {	
		$key = $pay_index;
		$pay_index++;
}


if(!isset($paytype["value"][$key])){$paytype["value"][$key]=0;}

if($rsss[$i]["pay_id"]!=1){
$paytype["type"][$key] = $keyword;
$paytype["value"][$key] += $rsss[$i]["sr_total"];
}

if($rsss[$i]["pay_id"]==1){
	$rsunknown = $keyword; 
	$rsunknown_value += $rsss[$i]["sr_total"]; 
}
}
if($rsss[$i]["set_cancel"]==0){
// each rows' color
$bgcolor = "#eaeaea"; $class = "even";
if($ii%2==0){
	$bgcolor = "#d3d3d3"; $class = "odd";
}
if($rsss[$i]["paid_confirm"]==0){
	$bgcolor = "#ffb9b9"; $class = "paidconfirm";
}
}

// define booking id links
$url = ($rsss[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rsss[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rsss[$i]["book_id"]."";
$pagename = ($rsss[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rsss[$i]["book_id"]:"managePds".$rsss[$i]["book_id"];
if($export!=false){
	$id="<b>".$rsss[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rsss[$i]["bpds_id"]."</a>";
}	

// define room in each room 
if($rsss[$i]["tb_name"]=="a_bookinginfo"){
	$roomname=array();
	$sqlRoom = "select distinct room_name from d_indivi_info,bl_room " .
				"where d_indivi_info.room_id=bl_room.room_id " .
				"and book_id =".$rsss[$i]["book_id"] ;
	$rsRoom = $obj->getResult($sqlRoom);
	
	
	for($j=0; $j<$rsRoom["rows"]; $j++){
		$roomname[$j]=$rsRoom[$j]["room_name"];
	}
	sort($roomname);
	$rname = implode(", ",array_filter($roomname));
}else{
	$rname = "-";
}

// define another value
$payname = ($rsss[$i]["pay_id"]>1)?$rsss[$i]["pay_name"]:"-";
if($rsss[$i]["set_cancel"]==0 && $rsss[$i]["paid_confirm"]==1){
$all_total+=$rsss[$i]["sr_total"];
}

$sr_id = $rsss[$i]["salesreceipt_id"];
$cashier =($rsss[$i]["u"]==null)?"-": $rsss[$i]["u"];
$reprint = ($rsss[$i]["reprint_times"]==0)?"-":$rsss[$i]["reprint_times"];
$cms = ($rsss[$i]["cms"])?"<span style='color:#ff0000'>yes</span>":"<span style='color:#ff0000'>no</span>";

$rowcnt++;		

	$sqlcMp = "select salesreceipt_id from c_srpayment where salesreceipt_id=".$rsss[$i]["salesreceipt_id"]."";
		$cmpId = $obj->getResult($sqlcMp);
		
		
		
	if($cmpId["rows"]>1){
		$bgcolor = "#eaf7cc"; $class = "multipay";
	}	
	
$style="";

if($rsss[$i]["sr_total"]==0){
?>
			<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=($rsss[$i]["salesreceipt_number"])?$rsss[$i]["salesreceipt_number"]:"-"?></td>
					<td class="report" align="center"><?=$id?></td>
					<td class="report"><?=$rsss[$i]["customer_name"]?></td>
					<td class="report"><?=$rsss[$i]["branch_name"]?></td>
					<td class="report"><?=$rname?></td>
					<td class="report" align="center"><?=$dateobj->convertdate($rsss[$i]["appt_date"],"Y-m-d",$sdateformat)?>&nbsp;</td>
					<td class="report" align="center"><?=substr($rsss[$i]["hour_name"],0,5)?></td>
					<td class="report" align="center"><?=$payname?></td>
					<td class="report" align="right"><?=number_format($rsss[$i]["sr_total"],2,".",",")?></td>
					<td class="report" align="center"><?=$cashier?></td>
					<td class="report" align="center"><?=$reprint?></td>
					<td class="report" align="center"><?=$cms?></td>
 			</tr>
<?	
$ii++;
}
}
 		?>

 				<tr height="20">
 					<td colspan="11" height="20">&nbsp;</td>
 				</tr>
<?
 if($export && (count($paytype["type"])+$rowcnt) > $chkrow){
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
		<td width="8%"></td><td width="8%"></td>
		<td width="10%"></td><td width="5%"></td>
		<td width="12%"></td><td width="7%"></td>
		<td width="7%"></td><td width="10%"></td>
		<td width="10%"></td><td width="10%"></td>
		<td width="8%"></td><td width="5%"></td>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="12">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
<?
 }
 ?>
				<tr height="20">
					<td colspan="3" align="left" height="20" style="padding-right:7px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					<td width="70%"></td><td width="30%"></td>
					</tr>

				


					</table>
					</td>
					<td colspan="8" align="right" height="20" valign="top" style="padding-right:7px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							<td width="75%"></td><td width="25%"></td>

		
					
					
						</table>
					</td>
				</tr>
				
				<tr height="50">
			    	<td width="100%" align="center" colspan="11" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>
				



			    	</td>
				</tr>
				<tr height="100">
			    	<td width="100%" align="left" colspan="11" ><br>
			    			
			    	</td>
				</tr>
		</table></td>
	</tr>
</table>

<?}?>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>