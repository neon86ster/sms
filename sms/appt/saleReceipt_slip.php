<?
include("../include.php");
$u_id = $obj->getUserIdLogin();

$nodata = "-- no have --";

$book_id = $obj->getParameter("book_id");
$sr_id = $obj->getParameter("sr_id");
$pds_id = $obj->getParameter("pds_id");

// #######################  Find data in table a_bookinginfo by book_id #######################
if($book_id){
$sql = "select b_appt_date,tax_id,servicescharge,b_branch_id from a_bookinginfo where book_id=$book_id";
$rs = $obj->getResult($sql);
$apptdate = $rs[0]["b_appt_date"];
$servicescharge = $rs[0]["servicescharge"];
$taxpercent = $obj->getIdToText($rs[0]["tax_id"],"l_tax","tax_percent","tax_id");
$sql = "select * from bl_branchinfo where branch_id =".$rs[0]["b_branch_id"];
$rsBranch = $obj->getResult($sql);
$sql = "select paid_confirm from c_salesreceipt where book_id =".$book_id;
$rspaid = $obj->getResult($sql);
$sql = "select c_srdetail.unit_price,c_srdetail.qty,c_srdetail.set_tax,c_srdetail.set_sc from c_srdetail " .
		"where c_srdetail.book_id =".$book_id." and c_srdetail.pd_id=80";
$rsmtake = $obj->getResult($sql);
$mtotal = 0;	
	for($i=0;$i<$rsmtake["rows"];$i++){
		 $total = $rsmtake[$i]["unit_price"]*$rsmtake[$i]["qty"]; 
		 				if($rsmtake[$i]["set_sc"]==1){
     				    	$sc = ($total*7)/100;	
     				    }else{
     				    	$sc=0;
     				    }
     				    if($rsmtake[$i]["set_tax"]==1){
     				    	$tax = (($total+$sc)*10)/100; 
     				    }else
     				    {
     				    	$tax=0;
     				    }
		$total = $total+$sc+$tax;
		$mtotal = $mtotal+$total;
	}
}
if($pds_id){
$sql = "select pds_date,tax_id,servicescharge,branch_id from c_saleproduct where pds_id=$pds_id";
$rs = $obj->getResult($sql);
$apptdate = $rs[0]["pds_date"];
$servicescharge = $rs[0]["servicescharge"];
$taxpercent = $obj->getIdToText($rs[0]["tax_id"],"l_tax","tax_percent","tax_id");
$sql = "select * from bl_branchinfo where branch_id =".$rs[0]["branch_id"];
$rsBranch = $obj->getResult($sql);
$sql = "select paid_confirm from c_salesreceipt where pds_id =".$pds_id;
$rspaid = $obj->getResult($sql);
$sql = "select c_srdetail.unit_price,c_srdetail.qty,c_srdetail.set_tax,c_srdetail.set_sc from c_srdetail " .
		"where c_srdetail.pds_id =".$pds_id." and c_srdetail.pd_id=80";
$rsmtake = $obj->getResult($sql);
$mtotal = 0;	
	for($i=0;$i<$rsmtake["rows"];$i++){
		 $total = $rsmtake[$i]["unit_price"]*$rsmtake[$i]["qty"]; 
		 				if($rsmtake[$i]["set_sc"]==1){
     				    	$sc = ($total*7)/100;	
     				    }else{
     				    	$sc=0;
     				    }
     				    if($rsmtake[$i]["set_tax"]==1){
     				    	$tax = (($total+$sc)*10)/100; 
     				    }else
     				    {
     				    	$tax=0;
     				    }
		$total = $total+$sc+$tax;
		$mtotal = $mtotal+$total;
	}
}
// #######################  End find data in table   ###############################
$today = date("Y-m-d");
////////////// update salereceipt number //////////////////
$sr_number = $obj->getIdToText($sr_id,"c_salesreceipt","salesreceipt_number","salesreceipt_id");
$chkid = false;

if($sr_number==0&&$apptdate==$today){
	$sr_number = $obj->getIdToText($rsBranch[0]["branch_id"],"bl_branchinfo","sr_next_number","branch_id");
	$chksql = "update c_salesreceipt set salesreceipt_number=$sr_number where salesreceipt_id=$sr_id ";
	$chkid = $obj->setResult($chksql);
	$next_sr_number = $sr_number+1;
	if($chkid){
	$chksql = "update bl_branchinfo set sr_next_number=$next_sr_number where branch_id=".$rsBranch[0]["branch_id"];
	$id = $obj->setResult($chksql);
	$logid = $obj->updatelog_sr($sr_id,1);
	if($logid){
		if($book_id){
		?>
		<script>
			window.opener.document.appt.submit();
		</script>
		<?}else if($pds_id){
		?>
		<script>
			window.opener.document.pdforsale.submit();
		</script>
		<?
		}
	}
	}
}
if($sr_number==0){$sr_number = $obj->getIdToText($rsBranch[0]["branch_id"],"bl_branchinfo","sr_next_number","branch_id");}
/////////// End update salereceipt number /////////////////

// ####################### update salereceipt printed log #######################
$chksql = "select * from log_c_srprint where salesreceipt_id=$sr_id order by reprint_times desc";
$chkrs = $obj->getResult($chksql);
if($chkrs["rows"]>0){
	$reprint_times = $chkrs[0]["reprint_times"]+1;
}else{
	$reprint_times = 0;
}
$thisuser = $_SESSION["__user_id"];
$thisip = $_SERVER["REMOTE_ADDR"];
if($sr_id){
$sql = "insert into log_c_srprint(salesreceipt_id,l_lu_user,l_lu_ip,l_lu_date,reprint_times) " .
		"value($sr_id,\"$thisuser\",\"$thisip\",now(),$reprint_times) ";
$id = $obj->setResult($sql);
}
// ###################  End update salereceipt printed log  #####################

///////////////////////////// Find data in d_indivi_info by book_id /////////////////////////
$sqlIdv = "select * from d_indivi_info where book_id =$book_id" ;
$rsIdv = $obj->getResult($sqlIdv);

////////////// Sort Data in array $roomName /////////////////////////////////////////////
$roomName=array();
for($i=0;$i<$rsIdv["rows"];$i++){
	$sqlRoom = "select room_name from bl_room where room_id =".$rsIdv[$i]["room_id"] ;
	$rsRoom = $obj->getResult($sqlRoom);
	$roomName[$i]=$rsRoom[0]["room_name"];
}
asort($roomName);

////////////////////////////// Find srdetail data in table c_srdetail /////////////////////////
$sqlSrd = "select * from c_srdetail where salesreceipt_id =$sr_id order by srdetail_id" ;
$rsSrd = $obj->getResult($sqlSrd);
///////////////////////////// End find data by book_id /////////////////////////
$amount = array();
//======  Start init first value  ==============================================//
	$amount["amount"]=0;
	$amount["svc"]=0;
	$amount["tax"]=0;
	$amount["payment"]=0;
//======  End init first value  ================================================//

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Sales Receipt</title>
<link href='css/report.css' type='text/css' rel='stylesheet'>
<body>
		<table width="240" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td align="center" valign="middle" colspan="4">
			<img src="<?=$customize_part?>/images/branch/<?=$rsBranch[0]["sr_logo"]?>" width="100px" height="91px">
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" align="center" width="234">*************************</td>
		</tr>
		<tr>
			<td colspan="2" align="center" width="234"><b>ใบเสร็จรับเงิน/ใบกำกับภาษี<br/>Receipt/Tax Invoice</b></td>
		</tr>
		<tr>
			<td colspan="2" align="center" height="30" width="234">*************************</td>
		</tr>		
		<tr>
			<td colspan="2" align="center" width="234"><b><font color="red"><?=str_replace("[br]","<br>",$rsBranch[0]["branch_name2"]);?></font></b></td>
		</tr>
		<tr>
			<td colspan="2" align="left" valign="top" class="small" width="234"><?=$obj->getIdToText(1,"a_company_info","company_name","company_id")?><br/><?=str_replace("[br]","<br>",$rsBranch[0]["branch_address"])?><br/>
			Tel : <?=$rsBranch[0]["branch_phone"]?><br/>
			Tax Id Number :  <?=$rsBranch[0]["tax_number"]?> <br/>
			Printed by : <?=$_SESSION["__user"]?> <? if($reprint_times>0){ ?>&nbsp;reprint <?=$reprint_times?><? } ?><br/>
			</td>
		</tr>		
		<tr>
			<td colspan="2" align="center" valign="top" class="small" width="234">Date : 
			<? echo $dateobj->timezone_depend_branch(date("Y-m-d"),date("H:i:s"),"$sdateformat H:i:s",$rsBranch[0]["branch_id"]); ?></td>
		</tr>
		<tr bordercolor="#999999">			
		    <td align="left" class="small" valign="top" width="120">
		    Booking ID.: <b><?=($book_id)?$obj->getIdToText($book_id,"c_bpds_link","bpds_id","tb_id","tb_name=\"a_bookinginfo\""):$obj->getIdToText($pds_id,"c_bpds_link","bpds_id","tb_id","tb_name=\"c_saleproduct\"")?></b><br>
		    Receipt No.: <b><?=$sr_number?>
		    </td>
			<td align="right" class="small" valign="top" width="114"><b>
			<?
			sort($roomName);
			$roomcnt = (count($roomName)>3)?3:count($roomName);
			
			for($i=0;$i<$roomcnt;$i++){
				if($i==0){
					echo $roomName[$i];
				}else{
					echo ", ".$roomName[$i];
				}
			}
			?>
			</b></td>
		</tr>
		</table>
		
		
		<table width="240" cellpadding="3" cellspacing="0" border="1" bordercolor="#000000">
		
			<tr class="small">
				<td align="center" class="small_2" width="115">Product</td>
				<td align="center" class="small_2" width="50">Price</td>
				<td align="center" class="small_2" width="15">Qty</td>
				<td align="center" class="small_2" width="60">Total</td>
			</tr>
			<?
			for($i=0;$i<$rsSrd["rows"];$i++){
				$sqlPd = "select * from cl_product where pd_id =".$rsSrd[$i]["pd_id"] ;
				$rsPd = $obj->getResult($sqlPd);
				//echo $sqlPd."<br/>";
				
				///////// Discount tax or servicecharge /////////////////
				if(!$rsSrd[$i]["set_sc"]&&!$rsSrd[$i]["set_tax"]){
					//echo "<br/> dis tax sc";
					//$rsSrd[$i]["unit_price"]=(100*$rsSrd[$i]["unit_price"])/(100+$taxpercent+$servicescharge+($taxpercent*$servicescharge)/100);
				}else if(!$rsSrd[$i]["set_tax"]){
					//echo "<br/>dis tax : $taxpercent %";
					//$rsSrd[$i]["unit_price"]=(100*$rsSrd[$i]["unit_price"])/(100+$taxpercent);
				}else if(!$rsSrd[$i]["set_sc"]){
					//echo "<br/>dis sc : $servicescharge";
					//$rsSrd[$i]["unit_price"]=(100*$rsSrd[$i]["unit_price"])/(100+$servicescharge);
				}
				
				$product["product_id"][0] = $rsPd[0]["pd_id"];
				$product["category_id"][0] = $rsPd[0]["pd_category_id"];
				$product["qty"][0] = $rsSrd[$i]["qty"];
				$product["total"][0] = $rsSrd[$i]["qty"]*$rsSrd[$i]["unit_price"];
				//$product["set_sc"][0] = ($srd[$i]["srd_id"]=="")?$obj->getIdToText($product["product_id"][0],"cl_product","set_sc","pd_id"):$obj->getIdToText($srd[$i]["srd_id"],"c_srdetail","set_sc","srdetail_id");
				//$product["set_tax"][0] = ($srd[$i]["srd_id"]=="")?$obj->getIdToText($product["product_id"][0],"cl_product","set_tax","pd_id"):$obj->getIdToText($srd[$i]["srd_id"],"c_srdetail","set_tax","srdetail_id");
				//$product["set_sc"][0] = $rsSrd[$i]["set_sc"]; 
				//$product["set_tax"][0] = $rsSrd[$i]["set_tax"];
				$product["set_sc"][0] =  1;
				$product["set_tax"][0] = 1;
				$product["taxpercent"][0] = $taxpercent;
				$product["servicescharge"][0] = $servicescharge ;
				
				//For sc or tac = 0;
				$var_sc=($rsSrd[$i]["set_sc"])?$obj->getsSvc($product,0):0;
				$var_tax=($rsSrd[$i]["set_tax"])?$obj->getsTax($product,0,$var_sc):0;
				//
				
				if($obj->getIdToText($product["category_id"][0],"cl_product_category","set_payment","pd_category_id")==0) {
					if($obj->getIdToText($product["category_id"][0],"cl_product_category","pos_neg_value","pd_category_id")==0) {
						//$amount["amount"] -= $product["total"][0];
						//$amount["svc"] -= $obj->getsSvc($product,0);
						//$amount["tax"] -= $obj->getsTax($product,0,$obj->getsSvc($product,0));
						$amount["amount"] -= $product["total"][0];
						$amount["svc"] -= $var_sc;
						$amount["tax"] -= $var_tax;
					} else {
						//$amount["amount"] += $product["total"][0];
						//$amount["svc"] += $obj->getsSvc($product,0);
						//$amount["tax"] += $obj->getsTax($product,0,$obj->getsSvc($product,0));
						$amount["amount"] += $product["total"][0];
						$amount["svc"] += $var_sc;
						$amount["tax"] += $var_tax;
					}
				} else {
					if($obj->getIdToText($product["category_id"][0],"cl_product_category","pos_neg_value","pd_category_id")==0) {
						//$amount["payment"] += $product["total"][0];
						//$amount["payment"] += $obj->getsSvc($product,0);
						//$amount["payment"] += $obj->getsTax($product,0,$obj->getsSvc($product,0));
						$amount["payment"] += $product["total"][0];
						$amount["payment"] += $var_sc;
						$amount["payment"] += $var_tax;
					} else {
						//$amount["payment"] -= $product["total"][0];
						//$amount["payment"] -= $obj->getsSvc($product,0);
						//$amount["payment"] -= $obj->getsTax($product,0,$obj->getsSvc($product,0));
						$amount["payment"] -= $product["total"][0];
						$amount["payment"] -= $var_sc;
						$amount["payment"] -= $var_tax;
						
					}
				}
				if($obj->getIdToText($product["category_id"][0],"cl_product_category","set_payment","pd_category_id")==0){
				echo "<tr class=\"small\">
					<td align=left class=\"small_2\" width=115>".$rsPd[0]["pd_name"]."</td>
					<td align=right class=\"small_2\" width=50>".number_format($rsSrd[$i]["unit_price"],2,".",",")."</td>
					<td align=center class=\"small_2\" width=15>".$rsSrd[$i]["qty"]."</td>
					<td align=right class=\"small_2\" width=60>".number_format((($rsSrd[$i]["unit_price"])*($rsSrd[$i]["qty"])),2,".",",")."</td>
					</tr>";
				}
			}
			
			$r_amount = $amount["amount"];
			$r_svc = $amount["svc"];
			$r_tax = $amount["tax"];
			$r_payment = $amount["payment"];
			$r_total = $r_amount+$r_svc+$r_tax-$r_payment;
			?>
		</table>
		<br/>
		<table width="240" cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td align="right" class="small" width="145">Sub Total : </td>
			<td align="right" class="small" width="95"><?=number_format($r_amount,2,".",",")?> ฿</td>
		</tr>
		<? if($servicescharge>0){ ?>
		<tr>
			<td align="right" class="small">Service Charges <?=number_format($servicescharge,0,".",",")?>% : </td>
			<td align="right" class="small"><?=number_format($r_svc,2,".",",")?> ฿</td>
		</tr>
		<?}?>
		<? if($taxpercent>0){ ?>
		<tr>
			<td align="right" class="small">Tax <?=number_format($taxpercent,0,".",",")?>% : </td>
			<td align="right" class="small"><?=number_format($r_tax,2,".",",")?> ฿</td>
		</tr>
		<?}?>
		<tr>
			<td align="right" class="small">Payment : </td>
			<td align="right" class="small"><?=number_format($r_payment,2,".",",")?> ฿</td>
		</tr>
		<tr>
			<td align="right" class="small">Total :</td>
			<td align="right" class="small"><font color="ff0000"><b><?=number_format($r_total,2,".",",")?></b></font> <!--฿--></td>
		</tr>
		<?
/////////////////////////////////////////////////////////////////////////////////////////////
		
		require_once ("membership.inc.php");
		
		if($book_id){
			$sql = "select a_member_code from a_bookinginfo where book_id=$book_id";
			//echo $sql."<br>";
			$brs = $obj->getResult($sql);
			$membercode = $brs[0]["a_member_code"];
		}
		if($pds_id){
			$sql = "select a_member_code from c_saleproduct where pds_id=$pds_id";
			//echo $sql."<br>";
			$prs = $obj->getResult($sql);
			$membercode =$prs[0]["a_member_code"];
		}
		if($membercode!=0){
			$sum = 0;
			if ($GLOBALS["global_oasisclient"] == true) {
				require_once ("destiny.inc.php");
				$objomh = new destiny();
				$rs = $objomh->get_memberhistory($membercode);
			} else {
				$rs["rows"] = 0;
			}
		for ($i = 0; $i < $rs["rows"]; $i++) {

			if ($rs[$i]["catagory_id"] == 11) {
				$sum += $rs[$i]["total"];
			} else {
				$sum -= $rs[$i]["total"];
			}
		
		}
		if($sum<0){
			$sum = 0;	
		}
		$obj = new membership();
		$rssr = $obj->getmembersr($membercode);
		
		$product = array ();
		$product["balance"] = $sum;
		$balanceindex = 0;
		
		for ($i = 0; $i < $rssr["rows"]; $i++) {			
			
			// check plus_minus_value before check membership payment type
			// because when customer buy membership they can paid by another payment
			if ($rssr[$i]["plus_minus_value"] == 1) {
				$product["total"] = $rssr[$i]["amount"];
				$product["balance"] += $product["total"];
				
			} else
				 if ($rssr[$i]["pd_id"]==80){ 
     				    $product["set_sc"] = $rssr[$i]["plus_servicecharge"];
						$product["set_tax"] = $rssr[$i]["plus_vat"];
     				    $product["total"] = $rssr[$i]["amount"];
     				    if($rssr[$i]["plus_servicecharge"]==1){
     				    	$product["set_sc"] = ($rssr[$i]["amount"]*7)/100;	
     				    }else{
     				    	$product["set_sc"]=0;
     				    }
     				    if($rssr[$i]["plus_vat"]==1){
     				    	$product["set_tax"] = (($rssr[$i]["amount"]+$product["set_sc"])*10)/100; 
     				    }else
     				    {
     				    	$product["set_tax"]=0;
     				    }
     				    $product["total"] = $rssr[$i]["amount"] + $product["set_sc"] + $product["set_tax"];
						$product["balance"] -= $product["total"];
						
     					if(number_format($product["balance"],2,".",",")==(0.00)){
 							$product["balance"]=abs(number_format($product["balance"],2,".",","));
 						}
     				}
					if ($product["balance"] < 0) {
					//$product["balance"] = 0;
				}
		
	}
		?>
		<tr>
			<td height="40" align="right" class="small">Member Balance :</td>
			<td align="right" class="small"><?if($rspaid[0]["paid_confirm"]==0){echo number_format($product["balance"]-$mtotal,2,".",",");}else{echo number_format($product["balance"],2,".",",");}?> ฿</td>
		</tr>
<?
}
/////////////////////////////////////////////////////////////////////////////////////////////
?>

		<tr>
			<td height="30" colspan="2" valign="bottom" align="center"><b>Signature:...............................</b></td>
		</tr>
		<tr>
			<td height="30" colspan="2" align="center" valign="bottom"><?=str_replace("[br]","<br>",$rsBranch[0]["mk_msg"]);?></td>
		</tr>
		<tr>
			<td align="center" valign="middle" height="40" colspan="4">*************************</td>
		</tr>
		
		</table>
</body>
<script type="text/javascript">
	//window.print();
</script>
