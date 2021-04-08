<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("report.inc.php");
$obj = new report();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$cshotel= $obj->getParameter("cshotel");
$export = $obj->getParameter("export",false);
$payid = $obj->getParameter("payid");
if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf(0,0,1);
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}

///Get Data
	
	
	$date_start = substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2);
	$date_end = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
	$sql1="select bl_branchinfo.branch_name, c_bpds_link.*, a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date, " .
			"a_bookinginfo.b_branch_id as branch_id, a_bookinginfo.b_customer_name, " .
			"a_bookinginfo.b_qty_people,a_bookinginfo.b_accomodations_id,a_bookinginfo.b_hotel_room , l_hour.hour_name, " .
			"c_salesreceipt.salesreceipt_id, c_salesreceipt.salesreceipt_number, " .
			"c_salesreceipt.paid_confirm, c_salesreceipt.pay_id,c_salesreceipt.sr_total, " .
			"al_percent_cms.pcms_percent , a_bookinginfo.b_set_cancel as set_cancel, " .
			"(select sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) " .
				"from c_srdetail,cl_product,cl_product_category " .
				"where c_srdetail.salesreceipt_id=c_salesreceipt.salesreceipt_id and " .
				"c_srdetail.pd_id=cl_product.pd_id and " .
				"cl_product.pd_category_id=cl_product_category.pd_category_id ) as total ," .
				"bl_branchinfo.servicescharge,l_tax.tax_percent " .
			"from c_bpds_link, a_bookinginfo, l_hour, c_salesreceipt,bl_branchinfo,l_tax,al_percent_cms " .
			"where c_bpds_link.tb_name='a_bookinginfo' " .
			"and c_bpds_link.tb_id=a_bookinginfo.book_id " .
			"and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
			"and bl_branchinfo.tax_id=l_tax.tax_id " .
			"and l_hour.hour_id=a_bookinginfo.b_book_hour " .
			"and a_bookinginfo.book_id=c_salesreceipt.book_id " .
			"and bl_branchinfo.branch_cms=al_percent_cms.pcms_id " .
			"and c_salesreceipt.paid_confirm=1 " .
			"and al_percent_cms.pcms_percent>0 ";
			if($payid){$sql1 .= "and c_salesreceipt.pay_id=$payid ";}
			if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
			if($date_end==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$date_start." ";}
			else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$date_start."' and a_bookinginfo.b_appt_date<='".$date_end."' ";}
			//$sql1 .= "order by a_bookinginfo.b_appt_date,a_bookinginfo.b_branch_id,a_bookinginfo.book_id," .
			//		"c_salesreceipt.salesreceipt_id ";
	
	$sql2="select  bl_branchinfo.branch_name, c_bpds_link.*, c_saleproduct.pds_id, c_saleproduct.pds_date as appt_date, " .
			"c_saleproduct.branch_id as branch_id, '-' as b_customer_name, " .
			"'-' as b_qty_people,'0' as b_accomodations_id,'-' as b_hotel_room , '-' as hour_name, " .
			"c_salesreceipt.salesreceipt_id, c_salesreceipt.salesreceipt_number, " .
			"c_salesreceipt.paid_confirm, c_salesreceipt.pay_id,c_salesreceipt.sr_total, " .
			"al_percent_cms.pcms_percent , c_saleproduct.set_cancel as set_cancel, " .
			"(select sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) " .
				"from c_srdetail,cl_product,cl_product_category " .
				"where c_srdetail.salesreceipt_id=c_salesreceipt.salesreceipt_id and " .
				"c_srdetail.pd_id=cl_product.pd_id and " .
				"cl_product.pd_category_id=cl_product_category.pd_category_id ) as total ," .
				"bl_branchinfo.servicescharge,l_tax.tax_percent " .
			"from c_bpds_link, c_saleproduct, c_salesreceipt,bl_branchinfo,l_tax,al_percent_cms " .
			"where c_bpds_link.tb_name='c_saleproduct' " .
			"and c_bpds_link.tb_id=c_saleproduct.pds_id " .
			"and c_saleproduct.branch_id=bl_branchinfo.branch_id " .
			"and bl_branchinfo.tax_id=l_tax.tax_id " .
			"and c_saleproduct.pds_id=c_salesreceipt.pds_id " .
			"and bl_branchinfo.branch_cms=al_percent_cms.pcms_id " .
			"and c_salesreceipt.paid_confirm=1 " .
			"and al_percent_cms.pcms_percent>0 ";
			if($payid){$sql2 .= "and c_salesreceipt.pay_id=$payid ";}
			if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
			if($date_end==false){$sql2 .= "and c_saleproduct.pds_date=".$date_start." ";}
			else{$sql2 .= "and c_saleproduct.pds_date>='".$date_start."' and c_saleproduct.pds_date<='".$date_end."' ";}
			//$sql2 .= "order by c_saleproduct.pds_date,c_saleproduct.branch_id,c_saleproduct.pds_id," .
			//		"c_salesreceipt.salesreceipt_id ";



	$sql = "($sql1) union ($sql2) order by appt_date ,branch_name,salesreceipt_number ";		
	
	//echo $sql;
	$rs=$obj->getResult($sql);
//
	
	
if($export!="Excel"&&$export){
	$chkrow = $obj->getParameter("chkrow",30);
	$chkpage = ceil($rs["rows"]/$chkrow);
}
if($export=="Excel" && $chkPageView){
	// This line will stream the file to the user rather than spray it across the screen
	header("Content-type: application/octet-stream");
	// Internet Explorer support
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Disposition: attachment; filename=\"Daily Report Specific.xls\"");
	header("Pragma: public");
	header("Expires: 0");
}

$bCMS="";
$getCMSid = $obj->getIdToText($branch_id,"bl_branchinfo","branch_cms","branch_id");
if($getCMSid){
	$getCMS = $obj->getIdToText($getCMSid,"al_percent_cms","pcms_percent","pcms_id");
	$bCMS=number_format($getCMS,2,".",",")."%";
}else{
	$sqlCMS="select branch_name,pcms_percent " .
			"from bl_branchinfo,al_percent_cms " .
			"where branch_active=1 and branch_name!='All' " .
			"and branch_cms=pcms_id and pcms_percent>0 " .
			"order by branch_name";
	$rsCMS=$obj->getResult($sqlCMS);
	for($i=0;$i<$rsCMS["rows"];$i++){
		if($i==0){
			$bCMS.= $rsCMS[$i]["branch_name"]." ".$rsCMS[$i]["pcms_percent"]."%";
		}else{
			$bCMS.= ", ".$rsCMS[$i]["branch_name"]." ".$rsCMS[$i]["pcms_percent"]."%";
		}
	}
}


$reportname = $obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id")." Monthly Report Details "."<br><br>CMS Percent : $bCMS";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>

<?
$header = "\t<tr>\n";
$header .= "\t\t<td width=\"100%\" align=\"center\" colspan=\"8\" ><br>\n";
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
$header .= "\t\t<td width=\"15%\"></td><td width=\"15%\"></td>\n";
$header .= "\t\t<td width=\"15%\"></td><td width=\"5%\"></td>\n";
$header .= "\t\t<td width=\"15%\"></td><td width=\"10%\"></td>\n";
$header .= "\t\t<td width=\"15%\"></td><td width=\"10%\"></td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr>\n";
$header .= "\t\t<td class=\"reporth\" width=\"100%\" align=\"center\" colspan=\"8\">\n";
$header .= "\t\t\t<b><p>Spa Management System</p>\n";
$header .= "\t\t\t$reportname</b><br>\n";
$header .= "\t\t\t<p><b style='color:#ff0000'>";
$header .= $dateobj->convertdate($begindate,$sdateformat,$ldateformat);
$header .= ($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat);
$header .= "<b><br><br></p>\n";
$header .= "\t\t</td>\n";
$header .= "\t</tr>\n";
$header .= "\t<tr height=\"32\">\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Date</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Payment Type</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>No. of people</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Sub Total</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Service Charge</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Vat</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;\"><b>Total</b></td>\n";
$header .= "\t\t<td align=\"center\" style=\"border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;border-left:1px solid #6c6c6c;\"><b>CMS Amount</b></td>\n";
$header .= "\t</tr>\n";
?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="15%"></td><td width="15%"></td>
		<td width="15%"></td><td width="5%"></td>
		<td width="15%"></td><td width="10%"></td>
		<td width="15%"></td><td width="10%"></td>


	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="8">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Date</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Payment Type</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>No. of people</b></td>
					<td align="right" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sub Total</b></td>
					<td align="right" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Service Charge</b></td>
					<td align="right" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat</b></td>
					<td align="right" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
					<td align="right" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;border-left:1px solid #6c6c6c;"><b>CMS Amount</b></td>
	</tr>
<?	
$rowcnt=0;
$totalPeople=0;
$totalAmount=0;
$totalSc=0;
$totalVat=0;
$totalCms=0;
$totalGrand=0;
$Srddetail = array ();
$bookSrdString="";
$cnt_sum=0;
$total_dsub=array ();
$total_dsr=array ();
$total_dsc=array ();
$total_dvat=array ();
$total_dcm=array ();
$total_dp=array ();
for($i=0; $i<$rs["rows"]; $i++) {
$rowcnt++;	
		
// separate page when export
if($export!="Excel"&&$export&&$rowcnt%$chkrow==0&&$i){
	echo $header;	$rowcnt=0;
}	

// define booking id links
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking".$rs[$i]["book_id"]:"managePds".$rs[$i]["book_id"];
if($export!=false){
	$id="<b>".$rs[$i]["bpds_id"]."</b>";
}else{
	$id="<a href='javascript:;;' onClick=\"newwindow('/appt/$url','$pagename')\" class=\"menu\">".$rs[$i]["bpds_id"]."</a>";
}

$bgcolor = "#eaeaea"; $class = "even";
		if($cnt_sum%2==0){
			$bgcolor = "#d3d3d3"; $class = "odd";
		}	
		
$sqlScVat = "select * from c_srdetail where salesreceipt_id=".$rs[$i]["salesreceipt_id"]."";
$rsScVat = $obj->getResult($sqlScVat);

$total_sc=array();	
$total_vat=array();
$cms_amount=array();
	for($j=0;$j<$rsScVat["rows"];$j++){
	if(!isset($total_sc[$i])){$total_sc[$i]=0;}
	if(!isset($total_vat[$i])){$total_vat[$i]=0;}
		$pd_category_id=$obj->getIdToText($rsScVat[$j]["pd_id"],"cl_product","pd_category_id","pd_id");
   		$pos_neg=$obj->getIdToText($pd_category_id,"cl_product_category","pos_neg_value","pd_category_id");		
		   
		   if($rsScVat[$j]["set_sc"]!=0){
		   		if($pos_neg==1){
		   			$total_sc[$i]+=(($rsScVat[$j]["unit_price"]*$rsScVat[$j]["qty"])*$rs[$i]["servicescharge"]/100);
			   	}else{
			   		$total_sc[$i]-=(($rsScVat[$j]["unit_price"]*$rsScVat[$j]["qty"])*$rs[$i]["servicescharge"]/100);
			   	}
		   }else{
		   		$total_sc[$i]=0;
		   }
		   
		   if($rsScVat[$j]["set_tax"]!=0){
		   		if($pos_neg==1){
		   			$total_vat[$i]+=((($rsScVat[$j]["unit_price"]*$rsScVat[$j]["qty"])+(($rsScVat[$j]["unit_price"]*$rsScVat[$j]["qty"])*$rs[$i]["servicescharge"]/100))*$rs[$i]["tax_percent"]/100);
			   	}else{
			   		$total_vat[$i]-=((($rsScVat[$j]["unit_price"]*$rsScVat[$j]["qty"])+(($rsScVat[$j]["unit_price"]*$rsScVat[$j]["qty"])*$rs[$i]["servicescharge"]/100))*$rs[$i]["tax_percent"]/100);
			   	}
		   }else{
		   		$total_vat[$i]=0;
		   }
	}
?>
	<?
	if(($cshotel && $rs[$i]["b_accomodations_id"]==$cshotel) || !$cshotel){
	if($rs[$i]["set_cancel"]!=1){	
		if(!isset($rs[$i+1]["bpds_id"])){$rs[$i+1]["bpds_id"]="";}	
		if($rs[$i]["bpds_id"]!=$rs[$i+1]["bpds_id"]){
		if(!isset($total_dp[$cnt_sum])){$total_dp[$cnt_sum]="";}
			$total_dp[$cnt_sum]+=$rs[$i]["b_qty_people"];
		}
	$cms_amount[$i]=($rs[$i]["total"]*$rs[$i]["pcms_percent"])/100;

	if(!isset($total_dsub[$cnt_sum])){$total_dsub[$cnt_sum]=0;}
	$total_dsub[$cnt_sum]+=$rs[$i]["total"];
	if(!isset($total_dsr[$cnt_sum])){$total_dsr[$cnt_sum]=0;}
	$total_dsr[$cnt_sum]+=$rs[$i]["sr_total"];
	if(!isset($total_dsc[$cnt_sum])){$total_dsc[$cnt_sum]=0;}
	$total_dsc[$cnt_sum]+=$total_sc[$i];
	if(!isset($total_dvat[$cnt_sum])){$total_dvat[$cnt_sum]=0;}
	$total_dvat[$cnt_sum]+=$total_vat[$i];
	if(!isset($total_dcm[$cnt_sum])){$total_dcm[$cnt_sum]=0;}
	$total_dcm[$cnt_sum]+=$cms_amount[$i];
	if(!isset($rs[$i+1]["appt_date"])){$rs[$i+1]["appt_date"]="";}	
	if($rs[$i]["appt_date"]!=$rs[$i+1]["appt_date"]){?>
			<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo $bgcolor; ?>'">   			
					<td class="report" align="center"><?=$dateobj->convertdate($rs[$i]["appt_date"],"Y-m-d",$sdateformat)?></td>
					
					<?if($payid){?>	
					<td class="report" align="center"><?=$obj->getIdToText($rs[$i]["pay_id"],"l_paytype","pay_name","pay_id")?></td>
					<?}else{?>
					<td class="report" align="center"><?="All"?></td>
					<?}?>
					<td class="report" align="center"><?=$total_dp[$cnt_sum]?></td>
					<td class="report" align="right"><?=number_format($total_dsub[$cnt_sum],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($total_dsc[$cnt_sum],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($total_dvat[$cnt_sum],2,".",",")?></td>
					<td class="report" align="right"><?=number_format($total_dsr[$cnt_sum],2,".",",")?></td>
					<td class="report" align="right" style="border-left:1px solid #6c6c6c;"><?=number_format(	$total_dcm[$cnt_sum],2,".",",")?></td>
 			</tr>
 	<?
 	$cnt_sum++;
 	}
	}?>

<?
/*
if($class=="multipay" && $order=="Payment Type"){
		$sql_getpayment="select pay_id,pay_total from c_srpayment where salesreceipt_id=".$rs[$i]["salesreceipt_id"];
		$rs_payment=$obj->getResult($sql_getpayment);
	for($k=0;$k<$rs_payment["rows"];$k++){
?>
	<tr bgcolor="<?=$bgcolor?>" class="<?=$class?>" height="20">
		<td style="font-size : 11px;color: red;" align="right" colspan="<?=(!$branch_id)?"12":"11"?>">
		<?=$obj->getIdToText($rs_payment[$k]["pay_id"],"l_paytype","pay_name","pay_id")." : ";?>	
		</td>
		<td style="font-size : 11px;color: red;" align="right"><?=number_format($rs_payment[$k]["pay_total"],2,".",",")?></td>
		<td>&nbsp;</td>
	</tr>
<?
	}
}*/
?>			
<?
	if($rs[$i]["set_cancel"]!=1){	
		if(!isset($rs[$i+1]["bpds_id"])){$rs[$i+1]["bpds_id"]="";}	
		if($rs[$i]["bpds_id"]!=$rs[$i+1]["bpds_id"]){
			$totalPeople+=$rs[$i]["b_qty_people"];
		}
		$totalAmount+=$rs[$i]["total"];
		$totalSc+=$total_sc[$i];
		$totalVat+=$total_vat[$i];
		$totalCms+=$cms_amount[$i];
		$totalGrand+=$rs[$i]["sr_total"];
	}
	}
}

?>
 			<tr bgcolor="" class="total" height="20" onmouseover="this.style.backgroundColor='#b0dfde'"  onmouseout="this.style.backgroundColor='<?php echo ""; ?>'">
 				<td class="report" align="center" colspan="2"><b>Total</b></td>
 				<td class="report" align="center"><b><?=$totalPeople?></b></td>
 				<td class="report" align="right"><b><?=number_format($totalAmount,2,".",",")?></b></td>
 				<td class="report" align="right"><b><?=number_format($totalSc,2,".",",")?></b></td>
 				<td class="report" align="right"><b><?=number_format($totalVat,2,".",",")?></b></td>
 				<td class="report" align="right"><b><?=number_format($totalGrand,2,".",",")?></b></td>
 				<td class="report" align="right" style="border-left:1px solid #6c6c6c;"><b><?=number_format($totalCms,2,".",",")?></b></td>
 			</tr>
 			
 		
 			<tr height="20">
 				<td colspan="8" height="20">&nbsp;</td>
 			</tr>
 		
<?
 if($export && $rowcnt>$chkrow){
?>
	<tr>
		<td width="100%" align="center" colspan="8" ><br>
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
		<td width="15%"></td><td width="15%"></td>
		<td width="15%"></td><td width="5%"></td>
		<td width="15%"></td><td width="10%"></td>
		<td width="15%"></td><td width="10%"></td>

	</tr>
	</tr>
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="8">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
<?
 }
 ?>

			    <tr height="20">
			    	<td width="100%" align="center" colspan="8" ><br>
			    		<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
			    	</td>
				</tr>	
				
				<tr height="100">
			    	<td width="100%" align="left" colspan="8" ><br>
			    		<!--	<br><b>Notation : </b><br>-->
<!--<div></div><br />
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#EAF7CC;"></div> &nbsp;- Green line, Multi method of payment in sale receipt.<br />
<br /> 
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#FFB9B9;"></div> &nbsp;- Red line, This sale receipt is not paid yet.<br />
<br />
<div style="float:left; width:18px; height:17px; border:1px solid #ffffff; background-color:#707070;"></div> &nbsp;- Strikethrough Text, This Cancel Booking.<br />
<br />-->

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