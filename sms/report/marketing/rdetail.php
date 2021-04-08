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
