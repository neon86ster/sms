<?php
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$bpphone=$obj->getParameter("bpphone");

$sql_log="select bankacc_cms_id from `al_bankacc_cms` where `bankacc_active`=1 ";
$sql_log.="and lower(`c_bp_phone`) like '%".strtolower($bpphone)."%'";
$rs_log=$obj->getResult($sql_log);
/*
$sql="select a_bookinginfo.book_id,a_bookinginfo.b_customer_name, a_bookinginfo.b_appt_date, bl_branchinfo.branch_name, a_bookinginfo.b_qty_people " .
		"from a_bookinginfo,bl_branchinfo " .
		"where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and a_bookinginfo.c_bp_phone=$bpphone " .
		"order by a_bookinginfo.book_id,a_bookinginfo.b_appt_date";
*/
$view_type=$obj->getParameter("view_type","Default");

if($view_type=="Default"){
	$this_year=date("Y");
	$set_sdate=$this_year."-01-01";
	$set_edate=date("Y-m-d");
	$text_show="This Year to Date.";
}else if($view_type=="View All"){
	$text_show="All";
}
	$sql="select a_bookinginfo.book_id,a_bookinginfo.b_customer_name, a_bookinginfo.b_appt_date, bl_branchinfo.branch_name, a_bookinginfo.b_qty_people " .
			", a_bookinginfo.c_set_cms, a_bookinginfo.b_set_cancel, ";
	$sql .= "a_bookinginfo.c_cms_value as c_cms_value, ";		
	
		$sql .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total ,";
		$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_percent_cms.pcms_percent/100) as cms ";
				
	
		$sql .= "from a_bookinginfo,c_srdetail,c_salesreceipt," .
				"cl_product,cl_product_category,al_bookparty,al_percent_cms,bl_branchinfo,".
				"al_accomodations ";
				
		$sql .= "where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
if($view_type!="View All"){	
	$sql.=	"and (a_bookinginfo.b_appt_date>='$set_sdate' and a_bookinginfo.b_appt_date<='$set_edate') ";
}	

		
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
				"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ".
				"and a_bookinginfo.book_id=c_srdetail.book_id ";
//		$sql .= "and a_bookinginfo.b_set_cancel=0 ";		
//		$sql .= "and a_bookinginfo.b_branch_id=1 ";
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		//$sql .= "and a_bookinginfo.c_set_cms=1 ";
		$sql .= "and cl_product_category.set_commission=1 ";
		
		$sql .= "and c_salesreceipt.pay_id!=13 ";		//specific paytype - voucher
		$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		$sql .= "and a_bookinginfo.c_pcms_id=al_percent_cms.pcms_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		
		
		if($book_id) {
			$sql .= "and a_bookinginfo.book_id=".$book_id." ";
		}
		
	$sql.=	"and a_bookinginfo.c_bp_phone=$bpphone ";
	
	$sql .= "group by a_bookinginfo.book_id ";
	
	$sql.= "order by a_bookinginfo.b_appt_date desc,a_bookinginfo.book_id";

$rs=$obj->getResult($sql);

?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking Agent History</title>
<link href="/css/style.css" rel="stylesheet" type="text/css">
<script src="../scripts/component.js" type="text/javascript"></script>
<script src="../scripts/ajax.js" type="text/javascript"></script>
<!-- Begin Code Data Chooser -->
<link rel="stylesheet" type="text/css" href="../scripts/datechooser/datechooser.css">
<body>
<form name="history_bp" id="history_bp" action='' method='post' style="padding:0;margin:0">
<br>

<div align="right" style="margin-right:12px">
<input type="submit" name="view_type" id="view_type" value="Default" />
<input type="submit" name="view_type" id="view_type" value="View All" />
</div>

<div style="margin-left:12px">
<br>
<b>Display : <span class="style1"><?=$text_show?></span><b>
</div>
<br>
<div class="group5" width="100%" >
<fieldset>
<legend><b>History of B.P.Number:<span class="style1"> <?=$bpphone?> <input name="view_log" id="view_log" type="button" value="view log" onClick="window.open('banklog.php?bankacc_cms_id=<?=$rs_log[0]['bankacc_cms_id']?>','Banklog<?=$rs_log[0]['bankacc_cms_id']?>','location=0,toolbar=0,directoris=0,status=0,menubar=0,scrollbars=1,resizable=0')" style="font-size:11px"></span></b></legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cusinfo">
  <tr>
    <td colspan="2">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="comment">
    	<tr>
          <td class="mainthead">Booking ID</td>
          <td class="mainthead">Customer name</td>
          <td class="mainthead">Appointment Date</td>
          <td class="mainthead">Branch</td>
          <td class="mainthead">Total Customers</td>
          <td class="mainthead">Commission Amount</td>
        </tr>
  		<?	$total_cus=0;$cms_amount=0;$total_amount=0;
			for($i=0;$i<$rs["rows"];$i++){
				$trclass = ($i%2==0)?"content_list":"content_list1";
				$url = "manage_booking.php?chkpage=1&bookid=".$rs[$i]["book_id"]."";
				$pagename = "manageBooking".$rs[$i]["book_id"];
				$bpdsid=$obj->getIdToText($rs[$i]["book_id"],"a_appointment","bpds_id","book_id");
				$total_cus+=$rs[$i]["b_qty_people"];
				
				if($rs[$i]["c_set_cms"]==1){
				$cms_amount = $rs[$i]["cms"]+$rs[$i]["c_cms_value"];
				}
				
		if($rs[$i]["b_set_cancel"]==1){
			$style="text-decoration:line-through;";
		}else{
			$style="";
			$total_amount+=$cms_amount;
		}
		?>
        <tr class='<?=$trclass?>' style="<?=$style?>">
          <td><a href='javascript:;;' onClick="newwindow('/appt/<?=$url?>','<?=$pagename?>')" class="menu"><?=$bpdsid?></a></td>
          <td><?=$rs[$i]["b_customer_name"]?></td>
          <td><?=$dateobj->convertdate($rs[$i]["b_appt_date"],"Y-m-d",$sdateformat)?></td>
          <td><?=$rs[$i]["branch_name"]?></td>
          <td style="text-align:right"><?=$rs[$i]["b_qty_people"]?></td>
          <td style="text-align:right"><?=number_format($cms_amount,2,".",",")?></td>
        </tr>
 		<? } ?>
 		
 		<tr bgcolor="cecece">
          <td colspan="4"><b>Total</b></td>
          <td style="text-align:right"><b><?=$total_cus?></b></td>
          <td style="text-align:right"><b><?=number_format($total_amount,2,".",",")?></b></td>
        </tr>
        
      </table>
    </td>
  </tr>
</table>
<br>
</fieldset>
</form>
</body>
