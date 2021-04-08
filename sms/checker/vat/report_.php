<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("checker.inc.php");
$obj = new checker();

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");

$export = $obj->getParameter("export",false);

if($export=="PDF" && $chkPageView){
	require('convert2pdf.inc.php');
	$pdf=new convert2pdf();
	$pdf->convertFromUrl($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."&export=print");
}

$sql="select bl_branchinfo.branch_id,bl_branchinfo.branch_name " .
		"from bl_branchinfo where " .
		"bl_branchinfo.branch_id<>1 " .
		"and branch_active=1 ";
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
	header("Content-Disposition: attachment; filename=\"VAT Report.xls\"");
	header("Pragma: public");
	header("Expires: 0");
	
}

$reportname = "VAT Report";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
$totaldate = ( strtotime($end_date) - strtotime($begin_date) ) / ( 60 * 60 * 24 )+1;

?>

<?if($export!="Excel"){?><link href="/css/style.css" rel="stylesheet" type="text/css"><?}?>
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
  <td valign="top" style="padding:10 20 20 20;" width="100%" align="center">
	<table width="70%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="25%"></td>
		<td width="25%"></td>
		<td width="25%"></td>
		<td width="25%"></td>
	</tr>
	
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="4">
    		<b><p>Spa Management System</p></b>
    		<b><?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br></p>
    	</td>
	</tr>
    

    <tr height="30">
        <td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Branch</b></td>
        <td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sub Total</b></td>
        <td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>VAT</b></td>
        <td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
    </tr>
	
   
   <?
   
$sdate = substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2);
$edate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);

   for($i=0;$i<$rs["rows"];$i++){
   	
   
   $sql1="select c_bpds_link.bpds_id, bl_branchinfo.branch_id,bl_branchinfo.branch_name,a_bookinginfo.book_id as a,c_srdetail.*" .
		",if(c_srdetail.set_tax=1,if(c_srdetail.set_sc=1,((c_srdetail.qty*c_srdetail.unit_price*(100+bl_branchinfo.servicescharge))/100),c_srdetail.qty*c_srdetail.unit_price)*((select tax_percent from l_tax where tax_id=bl_branchinfo.tax_id)/100),0)as vat " .
		"from a_bookinginfo,c_salesreceipt,c_srdetail,bl_branchinfo,c_bpds_link where " .
		"a_bookinginfo.book_id=c_salesreceipt.book_id " .
		"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id " .
		"and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and a_bookinginfo.b_set_cancel<>1 " .
		"and c_salesreceipt.paid_confirm=1 " .
		"and bl_branchinfo.branch_id=".$rs[$i]["branch_id"]." " .
		"and c_bpds_link.tb_id=a_bookinginfo.book_id ";
if($edate==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$sdate." ";}
else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$sdate."' and a_bookinginfo.b_appt_date<='".$edate."' ";}

$sql2="select c_bpds_link.bpds_id,bl_branchinfo.branch_id,bl_branchinfo.branch_name,c_saleproduct.pds_id as a,c_srdetail.*" .
		",if(c_srdetail.set_tax=1,if(c_srdetail.set_sc=1,((c_srdetail.qty*c_srdetail.unit_price*(100+bl_branchinfo.servicescharge))/100),c_srdetail.qty*c_srdetail.unit_price)*((select tax_percent from l_tax where tax_id=bl_branchinfo.tax_id)/100),0)as vat " .
		"from c_saleproduct,c_salesreceipt,c_srdetail,bl_branchinfo,c_bpds_link where " .
		"c_saleproduct.pds_id=c_salesreceipt.pds_id " .
		"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id " .
		"and c_saleproduct.branch_id=bl_branchinfo.branch_id " .
		"and c_saleproduct.set_cancel<>1 " .
		"and c_salesreceipt.paid_confirm=1 ".
		"and bl_branchinfo.branch_id=".$rs[$i]["branch_id"]." " .
		"and c_bpds_link.tb_id=c_saleproduct.pds_id ";
if($edate==false){$sql2 .= "and c_saleproduct.pds_date=".$sdate." ";}
else{$sql2 .= "and c_saleproduct.pds_date>='".$sdate."' and c_saleproduct.pds_date<='".$edate."' ";}

$sql_vat="($sql1) union ($sql2) order by bpds_id,srdetail_id ";
//echo $sql_vat."<br><br><br>";
$rs_vat=$obj->getResult($sql_vat);
$total_vat=0;
	for($j=0;$j<$rs_vat["rows"];$j++){
		
		$pd_category_id=$obj->getIdToText($rs_vat[$j]["pd_id"],"cl_product","pd_category_id","pd_id");
   		$pos_neg=$obj->getIdToText($pd_category_id,"cl_product_category","pos_neg_value","pd_category_id");		
		   
		   
		   if($pos_neg==1){
		   		$total_vat+=$rs_vat[$j]["vat"];
		   }else{
		   		$total_vat-=$rs_vat[$j]["vat"];
		   }
	}
	

$total=0;
$rs_total = $obj->getcrs($rs[$i]["branch_id"],$begin_date,$end_date);
	for($j=0;$j<$rs_total["rows"];$j++){
		$total+=$rs_total[$j]["sr_total"];
	}

$total_sub=$total-$total_vat;
   ?>
   <tr height="20">
			<td><?=$rs[$i]["branch_name"]?></td>
			<td align="right"><?=number_format($total_sub,2,".",",")?></td>
			<td align="right"><?=number_format($total_vat,2,".",",")?></td>
			<td align="right"><?=number_format($total,2,".",",")?></td>
   </tr>

 <?}?>
 
 <tr height="20">
 	<td style="padding-left: 20px; white-space: nowrap;" align="right" bgcolor="#d3d3d3"><b>TOTAL</b></td>
 	<td align="right" bgcolor="#d3d3d3"><b><?=number_format($all_total_sub,2,".",",")?></b></td>
 	<td align="right" bgcolor="#d3d3d3"><b><?=number_format($all_total_vat,2,".",",")?></b></td>
 	<td align="right" bgcolor="#d3d3d3"><b><?=number_format($all_total,2,".",",")?></b></td>
 </tr>
 
 <tr height="20">
 	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center" colspan="4">
	<b>Printed: </b><?=$dateobj->timezonefilter(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s")?>
	</td>
 </tr>

</p>
 
</table>
</span>

 
<?if($export=="print"){?>

<script type="text/javascript">
	window.print();
</script>
<?}?>