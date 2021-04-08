<?
session_start();
include("../../include.php");
require_once("account.inc.php");
require_once("date.inc.php");
$obj = new account();
// system date format	 					
$chksql = "select long_date,short_date from a_company_info";
$chkrs =$obj->getResult($chksql);
$sdateformat = $obj->getIdToText($chkrs[0]["short_date"],"l_date","date_format","date_id");
$ldateformat = $obj->getIdToText($chkrs[0]["long_date"],"l_date","date_format","date_id");
$dateobj = new convertdate();
$begindate = $obj->getParameter("begin");
$enddate= $obj->getParameter("end");
$branch_id = $obj->getParameter("branchid");
//if(is_numeric($branch)&&$branch>0){$branch=$branch;}else{$branch=0;}
if(is_numeric($branch_id)&&$branch_id>0){$branch_id=$branch_id;}else{$branch_id=0;}
$accfunc = $obj->getParameter("acc_func",1);
$today = date("Ymd");
if($branch_id==""){$branch_id=6;}
if(!isset($_REQUEST["pagenum"])){$_REQUEST["pagenum"]="";}
if($_REQUEST["pagenum"]){
	$rs=$obj->getsr($_REQUEST["pagenum"]);
}else{
	$rs=$obj->getsr(0,$branch_id,$begindate,$enddate);
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sale Receipt</title>
<link href='report.css' type='text/css' rel='stylesheet'>
</head>
<body width="280px">
<table width="280px">
	<tr>
		<td align="center">
<?
for($j=0; $j<$rs["rows"]; $j++)
{
	if($rs[$j]["tb_name"]=="a_bookinginfo"){
		$rs_detail = $obj->getsrd($rs[$j]["salesreceipt_id"],"a_bookinginfo",1);
	}else{
		$rs_detail = $obj->getsrd($rs[$j]["salesreceipt_id"],"c_saleproduct",1);}

	switch(strlen($rs[$j]["a_pagenumber"])) {
		case 1:$new_rcnum="000".$rs[$j]["a_pagenumber"];break;
		case 2:$new_rcnum="00".$rs[$j]["a_pagenumber"];break;
		case 3:$new_rcnum="0".$rs[$j]["a_pagenumber"];break;
		default: $new_rcnum= $rs[$j]["a_pagenumber"];break;
	}


?>
		<table width="240px" height="659px" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td colspan="2" align="center" height="25">*************************</td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="30">ใบเสร็จรับเงิน/ใบกำกับภาษี (อย่างย่อ)<br>
			Receipt/Tax Invoice (ABB.) </td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="25">*************************</td>
		</tr>	
		<tr>
			<td colspan="2" align="center" height="30"><b><font color="red"><?=$rs[$j]["branch_name"]?> Oasis Spa</font></b></td>
		</tr>

		<tr>
			<td colspan="2" align="left">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="35" valign="top">ผู้ขาย</td>
					<td class="small">Destiny Enterprises Co,Ltd.<br><?=$rs[$j]["branch_address"]?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="small">Tel: <?=($rs[$j]["branch_phone"])?$rs[$j]["branch_phone"]:$rs[$j]["company_phone"]?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td class="small">Tax ID Number: <?=$rs[$j]["tax_num"]?></td>
				</tr>
				<tr>
					<td width="35" valign="top">ผู้ซื้อ</td>
					<td>สด</td>
				</tr>
			</table>
			</td>
		</tr>				
		<?  list($date,$time) =  explode(' ', $rs[$j]["sr_date"]);
			$data = $dateobj->timezonefilter($date,$time,"d/m/y H:i:s");?>
		<tr>
			<td colspan="2" align="center" valign="top" height="20" class="small">Date: <?=$data?></td>
		</tr>
		<tr bordercolor="#999999">			
		    <td align="left" valign="top" height="20" class="small">Receipt No.: <b><?=$new_rcnum?></b></td>
			<td align="right" valign="top" height="20" class="small"><b><?=$obj->getroomname($rs[$j]["book_id"])?></b></td>
		</tr>
		
		<tr>
			<td colspan="2" valign="top" class="small">
			<table width="240" cellpadding="3" cellspacing="0" border="1" bordercolor="#000000">
		
			<tr>
				<td align="center" class="small_2" width="81">Product</td>
				<td align="center" class="small_2" width="73">Price</td>
				<td align="center" class="small_2" width="19">Qty</td>
				<td align="center" class="small_2" width="57">Total</td>
			</tr>
<?
		//======  Start init first value  ==============================================//\
			$all_amount = 0;
			$all_sc = 0;
			$all_vat = 0;
			$all_total = 0;
		//======  End init first value  ================================================//
		$i=0;
		for($i=0; $i<$rs_detail["rows"]; $i++) {
		if(!isset($rs_detail[$i]["product_id"])){$rs_detail[$i]["product_id"]=0;}
		if(!isset($product_id[$i])){$product_id[$i]=0;}

			$taxpercent = $rs_detail[$i]["tax_percent"];
			$servicescharge = $rs_detail[$i]["servicescharge"];
			///////// Discount tax or servicecharge /////////////////
			if(!$rs_detail[$i]["set_sc"]&&!$rs_detail[$i]["set_tax"]){
				//echo "<br> dis tax sc";
				$rs_detail[$i]["unit_price"]=(100*$rs_detail[$i]["unit_price"])/(100+$taxpercent+$servicescharge+($taxpercent*$servicescharge)/100);
			}else if(!$rs_detail[$i]["set_tax"]){
				//echo "<br>dis tax : $taxpercent %";
				$rs_detail[$i]["unit_price"]=(100*$rs_detail[$i]["unit_price"])/(100+$taxpercent);
			}else if(!$rs_detail[$i]["set_sc"]){
				//echo "<br>dis sc : $servicescharge";
				$rs_detail[$i]["unit_price"]=(100*$rs_detail[$i]["unit_price"])/(100+$servicescharge);
			}
			
			
			$product["product_id"] = $rs_detail[$i]["pd_id"];
			$product["category_id"] = $rs_detail[$i]["pd_category_id"];
			$product["qty"] = $rs_detail[$i]["qty"];
			$product["unit_price"] = $rs_detail[$i]["unit_price"];
			$product["total"] = $product["qty"]*$product["unit_price"];
			$product["set_sc"] = 1;//$rs_detail[$i]["set_sc"];
			$product["set_tax"] = 1;//$rs_detail[$i]["set_tax"];
			$product["taxpercent"] = $taxpercent;
			$product["servicescharge"] = $servicescharge;
			
			/*$catagory_id = $rs_detail[$i]["pd_category_id"]; // keep catagory_id to check.
			$price = $rs_detail[$i]["unit_price"];
			$amount = $rs_detail[$i]["unit_price"]*$rs_detail[$i]["qty"];	
			$percent_svc = $rs_detail[$i]["servicescharge"];
			$sc = $object->get_invoicesvc($rs_detail[$i]["product_id"],$catagory_id,$amount,$sr_svc);
			$plus_vat = $obj->getIdToText($rs_detail[$i]["product_id"],"l_product","plus_vat","product_id");
			$vat = $object->get_vat($rs_detail[$i]["product_id"],$plus_vat,$amount,$sc,$catagory_id);*/
			
			//======  Start Calculate Totalprice,service charge and vat ====================//
			if($rs_detail[$i]["set_payment"]==0){
				if($rs_detail[$i]["pos_neg_value"]==0) {
					$all_amount -= $product["total"];
					$all_sc -= $obj->getsSvc($product);
					$all_vat -= $obj->getsTax($product,$obj->getsSvc($product,$j));
				} else {
					$all_amount += $product["total"];
					$all_sc += $obj->getsSvc($product);
					$all_vat += $obj->getsTax($product,$obj->getsSvc($product,$j));
				}
			}else{
				if($rs_detail[$i]["pos_neg_value"]==1) {
					$all_amount += $product["total"];
					$all_sc += $obj->getsSvc($product);
					$all_vat += $obj->getsTax($product,$obj->getsSvc($product,$j));
				} else {
					$all_amount -= $product["total"];
					$all_sc -= $obj->getsSvc($product);
					$all_vat -= $obj->getsTax($product,$obj->getsSvc($product,$j));
				}
			}
			
	
			$productname = $obj->getIdToText($rs_detail[$i]["product_id"],"l_product","product_name","product_id");
		//=================================  End Calculate =============================//
			if($product_id[$i]!=1) { // Start check blank items.
			
				echo "<tr height=\"20\">";
				echo "<td align=\"left\" width=\"115\" class=\"small_2\">".$rs_detail[$i]["pd_name"]."</td>";
				
				
				echo "<td align=\"right\" width=\"50\" class=\"small_2\">".number_format($product["unit_price"],0,".",",")."</td>";
				echo "<td align=\"center\" width=\"15\" class=\"small_2\">".$product["qty"]."</td>";
				echo "<td align=\"right\" width=\"60\" class=\"small_2\">".number_format($product["total"],0,".",",")."</td>";
				echo "</tr>";
			
			} // End check blank items.
		}
		
		$all_total = $all_amount+$all_sc+$all_vat;
?>		
		
			</table>
			<table width="240" cellpadding="3" cellspacing="0" border="0">
			<tr height="20">
				<td align="right" class="small">Sub Total:</td>
				<td align="right" class="small"><?=number_format($all_amount,2,".",",")?>฿</td>
			</tr>
			<tr height="20">
				<td align="right" width="145" class="small">Service Charge <?=number_format($product["servicescharge"],2,".",",")?>%:</td>
				<td align="right" width="95" class="small"><?=number_format($all_sc,2,".",",")?>฿</td>
			</tr>
			<tr height="20">
				<td align="right" class="small">Vat <?=number_format($product["taxpercent"],2,".",",")?>%:</td>
				<td align="right" class="small"><?=number_format($all_vat,2,".",",")?>฿</td>
			</tr>
			<tr height="20">
				<td align="right" class="small">Total Baht:</td>
				<td align="right" class="small"><?=number_format($all_total,2,".",",")?>฿</td>
			</tr>
			<tr>
				<td height="25" colspan="2" valign="bottom" align="center"><b>&nbsp;</b></td>
			</tr>
			<tr>
				<td height="70" colspan="2" align="center" valign="middle">Thank You
				<br> 
				We look forward to seeing you again.<br>
				<?=$rs[$j]["website"]?></td>
			</tr>
			<tr>
				<td align="center" valign="middle" height="40" colspan="4">
				<table width="100%" cellpadding="0" cellspacing="0">
					<tr bgcolor="#FFFFFF">
						<td colspan="1" align="right">*************</td>
						<td colspan="3" align="left">*************</td>
					</tr>
				</table>
			</td>
		</tr>
								
		</table>
		
	</td>
</tr>
</table>	</td>
	</tr>
</table>

<hr style="page-break-before:always;border:0;color:#ffffff;" />	
<table width="280px">
	<tr>
		<td align="center">
<? } ?></td>
	</tr>
</table>
</body>