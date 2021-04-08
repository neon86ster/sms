<?
ini_set("memory_limit","-1");
?>
<?
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");
require_once("account.inc.php");
$obj = new account();

$export = $obj->getParameter("export",false);

$date = $obj->getParameter("date");
$begin_date = $obj->getParameter("begin");
$end_date= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
$payid = $obj->getParameter("payid");
if(is_numeric($branch_id)&&$branch_id>0){$branch_id=$branch_id;}else{$branch_id=5;}
if($branch_id==""){$branch_id=0;}
$accfunc = $obj->getParameter("acc_func",1);
if(is_numeric($accfunc)&&$accfunc>0){$accfunc=$accfunc;}else{$accfunc=1;}
if($accfunc==2){$chkselect = true;}else{$chkselect = false;}

$rs = $obj->getforselectacc($branch_id,$begin_date,$end_date,$chkselect,$payid);
$reportname="Accounting Select Booking ";
$reportname.="<font style=\"color:#ff0000;\">";
$reportname.=($branch_id==0)?"All Branches":"at ".$obj->getIdToText($branch_id,"bl_branchinfo","branch_name","branch_id");
$reportname.="</font>";
$begindate = $dateobj->convertdate(substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2),"Y-m-d",$sdateformat);
$enddate = $dateobj->convertdate(substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2),"Y-m-d",$sdateformat);
?>
<script type="text/javascript" src="../scripts/ajax.js"></script>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<span class="pdffirstpage"/>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
    	<td valign="top" style="padding:10 20 50 20;" width="100%" align="center">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
    	<td class="reporth" width="100%" align="center" colspan="<?=($accfunc==1)?"8":"7"?>">
    		<b><p>Spa Management System</p>
    		<?=$reportname?></b><br>
    		<p><b style='color:#ff0000'><?=$dateobj->convertdate($begindate,$sdateformat,$ldateformat)?><?=($enddate==""||$begindate==$enddate)?"":" - ".$dateobj->convertdate($enddate,$sdateformat,$ldateformat)?><b><br><br></p>
    	</td>
	</tr>
	<tr height="32">
<? if($accfunc==1){?>	<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Select</b><input title="Check/Uncheck All"type="checkbox" id="checkall" name="checkall" class="checkbox" onclick="checkAll(this.checked)"></td>  <? } ?>
<? if($accfunc==2){?>	<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>New SR Number</b></td>  <? } ?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Book ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Payment Type</b></td>
<? if($accfunc==1){?>	<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Book Company</b></td>  <? } ?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sub Total</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat+Sc</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
	</tr>
<?	
$ck_date = $rs[0]["b_appt_date"];
$ck_branch = $rs[0]["branch_name"];
$sum_date = 0; $sum_branch = 0; $sum_all = 0;
$total_price=0;
$total_vatsc=0;
for($i=0; $i<$rs["rows"]; $i++) {

$sub_vatsc = 0;

	if($rs[$i]["tb_name"]=="a_bookinginfo"){
		$rs_detail = $obj->getsrd($rs[$i]["salesreceipt_id"],"a_bookinginfo");
	}else{
		$rs_detail = $obj->getsrd($rs[$i]["salesreceipt_id"],"c_saleproduct");
	}
	/////
	//======  Start init first value  ==============================================//\
			$all_amount = 0;
			$all_sc = 0;
			$all_vat = 0;
			$all_total = 0;
		//======  End init first value  ================================================//
		$j=0;
		for($j=0; $j<$rs_detail["rows"]; $j++) {
		if(!isset($rs_detail[$j]["product_id"])){$rs_detail[$j]["product_id"]=0;}
		if(!isset($product_id[$j])){$product_id[$j]=0;}

			$taxpercent = $rs_detail[$j]["tax_percent"];
			$servicescharge = $rs_detail[$j]["servicescharge"];
			///////// Discount tax or servicecharge /////////////////
			if(!$rs_detail[$j]["set_sc"]&&!$rs_detail[$j]["set_tax"]){
				//echo "<br> dis tax sc";
				$rs_detail[$j]["unit_price"]=(100*$rs_detail[$j]["unit_price"])/(100+$taxpercent+$servicescharge+($taxpercent*$servicescharge)/100);
			}else if(!$rs_detail[$j]["set_tax"]){
				//echo "<br>dis tax : $taxpercent %";
				$rs_detail[$j]["unit_price"]=(100*$rs_detail[$j]["unit_price"])/(100+$taxpercent);
			}else if(!$rs_detail[$j]["set_sc"]){
				//echo "<br>dis sc : $servicescharge";
				$rs_detail[$j]["unit_price"]=(100*$rs_detail[$j]["unit_price"])/(100+$servicescharge);
			}
			
			
			$product["product_id"] = $rs_detail[$j]["pd_id"];
			$product["category_id"] = $rs_detail[$j]["pd_category_id"];
			$product["qty"] = $rs_detail[$j]["qty"];
			$product["unit_price"] = $rs_detail[$j]["unit_price"];
			$product["total"] = $product["qty"]*$product["unit_price"];
			$product["set_sc"] = 1;
			$product["set_tax"] = 1;
			$product["taxpercent"] = $taxpercent;
			$product["servicescharge"] = $servicescharge;
			
			//======  Start Calculate Totalprice,service charge and vat ====================//
			if($rs_detail[$j]["set_payment"]==0){
				if($rs_detail[$j]["pos_neg_value"]==0) {
					$all_amount -= $product["total"];
					$all_sc -= $obj->getsSvc($product);
					$all_vat -= $obj->getsTax($product,$obj->getsSvc($product,$j));
				} else {
					$all_amount += $product["total"];
					$all_sc += $obj->getsSvc($product);
					$all_vat += $obj->getsTax($product,$obj->getsSvc($product,$j));
				}
			}else{
				if($rs_detail[$j]["pos_neg_value"]==1) {
					$all_amount += $product["total"];
					$all_sc += $obj->getsSvc($product);
					$all_vat += $obj->getsTax($product,$obj->getsSvc($product,$j));
				} else {
					$all_amount -= $product["total"];
					$all_sc -= $obj->getsSvc($product);
					$all_vat -= $obj->getsTax($product,$obj->getsSvc($product,$j));
				}
			}
		}
	$sub_vatsc += ($all_sc+$all_vat);
	/////
	$total_price+=$all_amount;
	$total_vatsc+=$sub_vatsc;
	
	$total_pricebranch+=$all_amount;
	$totoal_vatscbranch+=$sub_vatsc;
				
	if(!isset($rs[$i+1]["b_appt_date"])){$rs[$i+1]["b_appt_date"]="";}
	
	$acc[$i]["acc"] = "";
	if($rs[$i]["a_accounting"]) {
		$acc[$i]["acc"] = "checked";
	}
	
	$sum_date += $rs[$i]["total"];
	$sum_branch += $rs[$i]["total"];
	$sum_all += $rs[$i]["total"];
	$rs[$i]["pd_id"] =  $obj->getIdToText($rs[$i]["salesreceipt_id"],"c_srdetail","pd_id","salesreceipt_id");
	$rs[$i]["pd_name"] = $obj->getIdToText($rs[$i]["pd_id"],"cl_product","pd_name","pd_id");

	if($i%2==1){
		echo "<tr class=\"odd\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#d3d3d3'\" >\n";
	}else{
		echo "<tr class=\"even\" height=\"20\" onmouseover=\"this.style.backgroundColor='#b0dfde'\"  onmouseout=\"this.style.backgroundColor='#eaeaea'\" >\n";
	}
?>
<? if($accfunc==1){?>
					<td align="center"><input type="checkbox" id="acc[<?=$i?>]" name="acc[<?=$i?>]" value="checked" <?=$acc[$i]["acc"]?> class="checkbox"></td>
					<input type="hidden" name="sr_id[<?=$i?>]" id="sr_id[<?=$i?>]" value="<?=$rs[$i]["salesreceipt_id"]?>">
<? }else{?>
					<td align="center"><?=$rs[$i]["a_pagenumber"]?></td>
<?} ?>
<? 
$url = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."":"manage_pdforsale.php?pdsid=".$rs[$i]["book_id"]."";
$pagename = ($rs[$i]["tb_name"]=="a_bookinginfo")?"manageBooking":"managePds";
?>
					<td align="center"><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename.$rs[$i]["book_id"]?>')" class="menu"><?=$rs[$i]["bpds_id"]?></a></td>
					<td align="left"><?=$rs[$i]["pd_name"]?></td>
					<td align="left"><?=$rs[$i]["pay_name"]?></td>
<? if($accfunc==1){?>	
					<td align="left"><?=$rs[$i]["bp_name"]?></td>
<? } ?>
					<td align="right"><?=number_format($all_amount,2,".",",")?>
					<td align="right"><?=number_format($sub_vatsc,2,".",",")?>
					<td align="right"><?=number_format($rs[$i]["total"],2,".",",")?>
				</tr>
				
	
<?
	
	if($rs[$i]["b_appt_date"]!=$rs[$i+1]["b_appt_date"] || $rs[$i]["branch_name"]!=$rs[$i+1]["branch_name"]) {
?>	
	<tr height="20" bgcolor="#e0c0bc">
		<td colspan="<?=($accfunc==1)?"8":"7"?>">
		<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="35%" align="right"><b><?=$rs[$i]["branch_name"]?></b></td>
			<td width="35%" align="right"><b><?=$dateobj->convertdate($ck_date,"Y-m-d",$sdateformat)?></b></td>
			<td width="10%" align="right"><b><?=number_format($total_price,2,".",",")?></b></td>	
			<td width="10%" align="right"><b><?=number_format($total_vatsc,2,".",",")?></b></td>	
			<td width="10%" align="right"><b><?=number_format($sum_date,2,".",",")?></b></td>			
		</tr>
		</table>
		</td>
	</tr>
<?


		if($i!=($rs["rows"]-1)) {
			if($ck_branch!=$rs[$i+1]["branch_name"]) {
?>
			

			<tr height="20">
				<td colspan="<?=($accfunc==1)?"8":"7"?>">&nbsp;</td>
			</tr>
			
			<tr bgcolor="#CCCCCC" height="30">
				<td colspan="<?=($accfunc==1)?"8":"7"?>">
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					
					<td width="70%" align="center"><b color="#000099"><?=$ck_branch?> Total</b></td>
					<td width="10%" align="right"><b color="#000099"><?=number_format($total_pricebranch,2,".",",")?></b></td>
					<td width="10%" align="right"><b color="#000099"><?=number_format($totoal_vatscbranch,2,".",",")?></b></td>
					<td width="10%" align="right"><b color="#000099"><?=number_format($sum_branch,2,".",",")?></b></td>
								
				</tr>
				</table>
				</td>
			</tr>	

<?
				$total_pricebranch=0;
				$totoal_vatscbranch=0;
				$ck_branch=$rs[$i+1]["branch_name"];
				$sum_branch=0;
			}
?>
			<tr height="20">
				<td colspan="<?=($accfunc==1)?"8":"7"?>">&nbsp;</td>
			</tr>
		
			<tr height="32">
<? if($accfunc==1){?>	<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Select</b></td>  <? } ?>
<? if($accfunc==2){?>	<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>New SR Number</b></td>  <? } ?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Book ID</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Product</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Payment Type</b></td>
<? if($accfunc==1){?>	<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Book Company</b></td>  <? } ?>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Sub Total</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Vat+Sc</b></td>
					<td align="center" style="border-top:2px #000000 solid;border-bottom:2px #ff0000 solid;"><b>Total</b></td>
			</tr>
<?
		}
	
		$sum_date=0;
		$total_price=0;
		$total_vatsc=0;
		$ck_date=$rs[$i+1]["b_appt_date"];				
	}
}
?>		
				<tr height="20">
					<td colspan="<?=($accfunc==1)?"8":"7"?>">&nbsp;</td>
				</tr>
						
				<tr bgcolor="#CCCCCC" height="30">
					<td colspan="<?=($accfunc==1)?"8":"7"?>">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="70%" align="center"><b color="#000099"><?=$ck_branch?> Total</b></td>
						<td width="10%" align="right"><b color="#000099"><?=number_format($total_pricebranch,2,".",",")?></b></td>	
						<td width="10%" align="right"><b color="#000099"><?=number_format($totoal_vatscbranch,2,".",",")?></b></td>	
						<td width="10%" align="right"><b color="#000099"><?=number_format($sum_branch,2,".",",")?></b></td>								
					</tr>
					</table>
					</td>
				</tr>
				
				
				
				
				<tr bgcolor="#FCA367" height="30">
					<td colspan="<?=($accfunc==1)?"8":"7"?>">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>			
						<td width="70%" align="center"><b>Grand Total</b></td>
						<td width="30%" align="right"><b><?=number_format($sum_all,2,".",",")?></b></td>			
					</tr>
					</table>
		</td>
    </tr>
				<tr height="20">
					<td colspan="<?=($accfunc==1)?"8":"7"?>">&nbsp;</td>
				</tr>
    <tr>
    	<td width="100%" align="center" colspan="<?=($accfunc==1)?"8":"7"?>">
    		<b>Printed: </b><?php echo $data = $dateobj->timezone_global(date("Y-m-d"),date("H:i:s"),"$ldateformat H:i:s");?><input type="hidden" id="rows" name="rows" value="<?=$rs["rows"]?>" />
    	</td>
	</tr>
</table>
<?if($export=="print"){?>
<script type="text/javascript">
	window.print();
</script>
<?}?>