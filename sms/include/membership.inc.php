<?php
/*
 * File name : membership.inc.php
 * Description : Class file membership information for sms system
 * Author : natt
 * Create date : 11-Sep-2009
 * Modified : natt@tap10.com
 */   
require_once("report.inc.php");
class membership extends report {
		
/*
 * get membership general information for membership history report 
 */	
	function getmemberinfo($membercode=false,$debug=false){
		if(!$membercode) {
			$this->setErrorMsg("membership.getmemberinfo(),Please insert membership code for see this report!!");
			return false;
		}
		
		$sql = "select * from m_membership where member_code=$membercode";	
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}	
		
/*
 * get membership comment for membership history report 
 */	
	function getmembercomment($membercode=false,$debug=false){
		if(!$membercode) {
			$this->setErrorMsg("membership.getmemberinfo(),Please insert membership code for see this report!!");
			return false;
		}
		
		$sql = "select comments,l_lu_user,l_lu_date from ma_comment " .
				"where member_id=$membercode " .
				"order by l_lu_date desc";	
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}	
	
/*
 * insert membership comment
 */
	function setmembercomment($comment="",$memberid=false,$rscomment=false){
		if(!$memberid) {
			$this->setErrorMsg("membership.setmembercomment(),Please insert membership code for see this report!!");
			return false;
		}
		// check membership comment it should not add comment twice for each member
		$chkcomment=true;
		for($i=0;$i<$rscomment["rows"];$i++){
			if($rscomment[$i]["comments"]==$comment){
				$chkcomment=false;
			}
		}
		
		if($comment!="" && $chkcomment){
				$comment = str_replace("'","''",$comment);
				$comment = htmlspecialchars($comment);
				$userid = $_SESSION["__user_id"];
				$thisip = $_SERVER["REMOTE_ADDR"];
				$sql = "insert into ma_comment(member_id,comments,l_lu_user,l_lu_date,l_lu_ip) " .
					"values('$memberid','$comment','$userid',now(),'$thisip')";
		 		$id = $this->setResult($sql);

		}else{
				$this->setErrorMsg("The comment is duplicate. Please try again.");
				return false;
		}
		
		return $id;
	}
		
/*
 * get membership treatment information for membership history report 
 */	
	function getmembertrm($membercode=false,$debug=false){
		
		$sql = "select bl_branchinfo.branch_id,bl_branchinfo.branch_name," .
				"d_indivi_info.indivi_id,d_indivi_info.package_id,l_hour.hour_name," .
				"l_hour.hour_calculate,bl_room.room_name,a_bookinginfo.book_id," .
				"a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.b_qty_people," .
				"d_indivi_info.strength_id,d_indivi_info.scrub_id,d_indivi_info.wrap_id," .
				"d_indivi_info.bath_id,d_indivi_info.facial_id " .
				"from a_bookinginfo,bl_branchinfo,al_city,d_indivi_info,da_mult_th,l_hour,bl_room " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($membercode){$sql .= "and a_bookinginfo.a_member_code = \"$membercode\" ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and d_indivi_info.member_use=1 ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "order by a_bookinginfo.book_id,a_bookinginfo.b_branch_id,bl_room.room_name ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get membership sales receipt information for membership history report 
 */	
	function getmembersr($membercode=false,$debug=false){
		
		
	//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		//$sql1 .= "c_srpayment.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		//$sql1 .= "c_srpayment.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "bl_branchinfo.branch_name,";
		//$sql1 .= "a_bookinginfo.b_appt_date as appt_date,";
		
		$sql1 .= "cl_product.member_takeout as member_takeout,";
		$sql1 .= "c_srdetail.pd_id as pd_id,";
		$sql1 .= "c_srdetail.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_salesreceipt.sr_total, ";
		
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date ";
		//$sql1 .= "c_srpayment.pay_total ";
		
		
		//$sql1 .= "from c_bpds_link,a_bookinginfo,c_salesreceipt,c_srdetail,c_srpayment,cl_product,cl_product_category,l_tax,bl_branchinfo ";
		$sql1 .= "from c_bpds_link,a_bookinginfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_tax,bl_branchinfo ";
		//$sql1 .= "where a_bookinginfo.book_id = c_srpayment.book_id ";
		//$sql1 .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "where a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql1 .= "and a_bookinginfo.a_member_code = \"$membercode\" ";}
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";
	
	//echo $sql1."<br><br>"; 		


	//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		//$sql2 .= "c_srpayment.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		//$sql2 .= "c_srpayment.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "bl_branchinfo.branch_name,";
		//$sql2 .= "c_saleproduct.pds_date as appt_date,";
		
		$sql2 .= "cl_product.member_takeout as member_takeout,";
		$sql2 .= "c_srdetail.pd_id as pd_id,";
		$sql2 .= "c_srdetail.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_salesreceipt.sr_total, ";
		
		$sql2 .= "c_saleproduct.pds_date as appt_date ";
		//$sql2 .= "c_srpayment.pay_total ";
		
		
		//$sql2 .= "from c_saleproduct,c_salesreceipt,c_srdetail,c_srpayment,cl_product,cl_product_category,c_bpds_link,l_tax,bl_branchinfo ";
		$sql2 .= "from c_saleproduct,c_salesreceipt,c_srdetail,cl_product,cl_product_category,c_bpds_link,l_tax,bl_branchinfo ";
		//$sql2 .= "where c_saleproduct.pds_id = c_srpayment.pds_id ";
		//$sql2 .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "where c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		//$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srpayment.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql2 .= "and c_saleproduct.a_member_code = \"$membercode\" ";}
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1";
		
		
		//echo $sql."<br><br>"; 
		
		//$sql = "($sql1) union ($sql2) order by appt_date,bpds_id,branch_id,srdetail_id,pos_neg_value ";
		$sql = "($sql1) union ($sql2) order by appt_date,pos_neg_value desc,bpds_id,branch_id,srdetail_id";
		
		//echo $sql."11111<br>"; 
	
	
	/*			
		$Sql2chk = "select c_saleproduct.*, c_srpayment.* from c_saleproduct, c_srpayment ".
					"where c_saleproduct.a_member_code = \"$membercode\" ".
					"and c_saleproduct.pds_id=c_srpayment.pds_id";
		$chksql2 = $this->getResult($Sql2chk);
		
	if($chksql2){	
				//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql1 .= "c_srpayment.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "c_srpayment.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "bl_branchinfo.branch_name,";
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date,";
		$sql1 .= "c_srpayment.pay_total ";
		
		
		$sql1 .= "from c_bpds_link,a_bookinginfo,c_salesreceipt,c_srdetail,c_srpayment,cl_product,cl_product_category,l_tax,bl_branchinfo ";
		$sql1 .= "where a_bookinginfo.book_id = c_srpayment.book_id ";
		$sql1 .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql1 .= "and a_bookinginfo.a_member_code = \"$membercode\" ";}
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";
		

			//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql2 .= "c_srpayment.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "c_srpayment.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "bl_branchinfo.branch_name,";
		$sql2 .= "c_saleproduct.pds_date as appt_date,";
		$sql2 .= "c_srpayment.pay_total ";
		
		$sql2 .= "from c_saleproduct,c_salesreceipt,c_srdetail,c_srpayment,cl_product,cl_product_category,c_bpds_link,l_tax,bl_branchinfo ";
		$sql2 .= "where c_saleproduct.pds_id = c_srpayment.pds_id ";
		$sql2 .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srpayment.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql2 .= "and c_saleproduct.a_member_code = \"$membercode\" ";}
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1";
		
		$sql = "($sql1) union ($sql2) order by bpds_id,branch_id,salesreceipt_id,srdetail_id ";
	}else{
		
		//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql1 .= "c_salesreceipt.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "bl_branchinfo.branch_name,";
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date,";
		$sql1 .= "c_salesreceipt.sr_total ";
		
		
		$sql1 .= "from c_bpds_link,a_bookinginfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_tax,bl_branchinfo ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql1 .= "and a_bookinginfo.a_member_code = \"$membercode\" ";}
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";
		
		//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.plus_minus_value as plus_minus_value,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql2 .= "c_salesreceipt.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "bl_branchinfo.branch_name,";
		$sql2 .= "c_saleproduct.pds_date as appt_date,";
		$sql2 .= "c_salesreceipt.sr_total ";
		
		
		$sql2 .= "from c_saleproduct,c_salesreceipt,c_srdetail,cl_product,cl_product_category,c_bpds_link,l_tax,bl_branchinfo ";
		$sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql2 .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($membercode){$sql2 .= "and c_saleproduct.a_member_code = \"$membercode\" ";}
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1 ";
		
		$sql = "($sql1) union ($sql2) order by bpds_id,branch_id,salesreceipt_id,srdetail_id ";
	
			
		}*/
		
		if($debug) {
			echo "$sql1<br><br><br><br>$sql2<br><br><br><br>";
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	
	}
		
/*
 * get membership sales receipt total amount for membership history report 
 */	
	function getsramount($rssr=false,$startdate="20090101"){
		$checksr = array(); $cnt = 0;
		$srtotal = 0;
		for($i=0;$i<$rssr["rows"];$i++){
			$apptdate = str_replace("-","",$rssr[$i]["appt_date"]);
			if(!in_array($rssr[$i]["salesreceipt_id"],$checksr)){
				$checksr[$cnt] = $rssr[$i]["salesreceipt_id"];
				$cnt++;
				if($startdate&&$apptdate>=$startdate){
						$srtotal +=$rssr[$i]["sr_total"];					
				}
			}
		}
		if($srtotal<0){
			$srtotal=0;
		}
		return $srtotal;
	}
}
?>
