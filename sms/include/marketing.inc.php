<?php
/*
 * File name : marketing.inc.php
 * Description : Class file marketing report for sms system
 * Author : natt
 * Create date : 28-Apr-2009
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class marketing extends report {
	
/*
 * get gift type information from gl_gifttype table
 * @modified - add this function on 06 May 2009
 */
	function getgifttype($order=false,$sort=false,$debug=false){
		$sql = "select * from gl_gifttype ";
		$sql .= "order by gifttype_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get gift issue qty/amount
 * separate function for marketingissue,marketingused because can't detect booking appt. date in the same query language 
 * @modified - add this function on 28 April 2009
 */
	function getgiftissue($start_date=false,$end_date=false,$branchid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getmarketingissue(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		/*$sql1 = "select 0 as type_id,\"All Gift Certificate\" as type_name," .
				"g_gift.gifttype_id as code_id,gl_gifttype.gifttype_name as code_name," .
				"\"g_gift\" as tb_name,";
		$sql1 .= "sum(case g_gift.tb_name " .
				"when '' then 0 else g_gift.value end) as issue_amount,";
		$sql1 .= "g_gift.id_sold,sum(case g_gift.tb_name " .
				"when '' then 0 else 1 end) as issue_qty,g_gift.gift_id ";
		
		
		$sql1 .= "from a_bookinginfo,g_gift,gl_gifttype ";
		$sql1 .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql1 .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql1 .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		$sql1 .= "and g_gift.tb_name != \"\" ";
		$sql1 .= "and g_gift.gifttype_id=gl_gifttype.gifttype_id ";
		$sql1 .= "and g_gift.id_sold=a_bookinginfo.book_id ";
		$sql1 .= "group by g_gift.gifttype_id ";
		$sql1 .= "order by g_gift.id_sold ";*/
		
		//table g_gift
		$sql = "select 0 as type_id,\"All Gift Certificate\" as type_name," .
				"g_gift.gifttype_id as code_id,gl_gifttype.gifttype_name as code_name," .
				"\"g_gift\" as tb_name,";
		$sql .= "sum(g_gift.value) as issue_amount,";
		$sql .= "g_gift.id_sold,count(g_gift.gift_number) as issue_qty,g_gift.gift_id ";
		
		
		$sql .= "from g_gift,gl_gifttype ";
		$sql .= "where g_gift.available=1 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and g_gift.issue='".$startdate."' ";}
		else{$sql .= "and g_gift.issue>='".$startdate."' and g_gift.issue<='".$enddate."' ";}
		$sql .= "and g_gift.gifttype_id=gl_gifttype.gifttype_id ";
		//$sql2 .= "and g_gift.gifttype_id!=11 ";	// except gift sold
		$sql .= "group by g_gift.gifttype_id ";
		$sql .= "order by gl_gifttype.gifttype_name ";
		
		//$sql = "($sql1) union ($sql2) order by code_name"; 
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}

/*
 * get gift used qty/amount for marketing report 
 * @modified - add this function on 5 Mar 2009
 */
	function getgiftused($start_date=false,$end_date=false,$branchid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getgiftused(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table g_gift
		$sql = "select \"0\" as type_id,\"All Gift Certificate\" as type_name," .
				"g_gift.gifttype_id as code_id,gl_gifttype.gifttype_name as code_name," .
				"\"g_gift\" as tb_name," .
				"g_gift.book_id,c_bpds_link.bpds_id,count(g_gift.gift_number) as used_qty," .
				"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.book_id = g_gift.book_id) as used_amount ";
								
		
		$sql .= "from a_bookinginfo,g_gift,gl_gifttype,c_bpds_link ";
		if($cityid){$sql .= ",bl_branchinfo ";}
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($cityid){
			$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
			$sql .= "and bl_branchinfo.city_id=".$cityid." ";
		}
		$sql .= "and a_bookinginfo.book_id=g_gift.book_id ";
		$sql .= "and g_gift.gifttype_id=gl_gifttype.gifttype_id ";
		$sql .= "and g_gift.available=1 ";
		$sql .= "and c_bpds_link.tb_id=g_gift.book_id ";
		$sql .= "and c_bpds_link.tb_name like \"a_bookinginfo\" ";
		
		$sql .= "group by gl_gifttype.gifttype_id,g_gift.book_id ";
		$sql .= "order by gl_gifttype.gifttype_name ";	
				

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get marketing code qty/amount for marketing report 
 * @modified - add this function on 5 Mar 2009
 */
	function getmarketingused($start_date=false,$end_date=false,$branchid=false,$cityid=false,$status=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getmarketingusedqty(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table l_marketingcode
		$sql1 = "select l_marketingcode.category_id as type_id,l_mkcode_category.category_name as type_name," .
				"l_marketingcode.mkcode_id as code_id,l_marketingcode.sign as code_name," .
				"\"l_marketingcode\" as tb_name," .
				"a_bookinginfo.book_id,c_bpds_link.bpds_id,count(a_bookinginfo.book_id) as used_qty,sum(a_bookinginfo.b_qty_people) as used_person," .
				"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.book_id = a_bookinginfo.book_id and c_salesreceipt.paid_confirm=1) as used_amount ";
				//"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.book_id = a_bookinginfo.book_id) as used_amount ,(select count(g_gift.book_id) from g_gift where g_gift.book_id = a_bookinginfo.book_id) as count_gift ";
		
		
		$sql1 .= "from a_bookinginfo,l_marketingcode,l_mkcode_category,c_bpds_link ";
		if($cityid){$sql1 .= ",bl_branchinfo ";}
		$sql1 .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql1 .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql1 .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($cityid){
			$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
			$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";
		}
		$sql1 .= "and a_bookinginfo.mkcode_id=l_marketingcode.mkcode_id ";
		$sql1 .= "and a_bookinginfo.mkcode_id!=1 ";
		$sql1 .= "and l_mkcode_category.category_id=l_marketingcode.category_id ";
		$sql1 .= "and c_bpds_link.tb_id=a_bookinginfo.book_id ";
		$sql1 .= "and c_bpds_link.tb_name like \"a_bookinginfo\" ";
		$sql1 .= ($status!=2)?"and (select count(g_gift.book_id) from g_gift where g_gift.book_id = a_bookinginfo.book_id) = 0 ":"";
		
		$sql1 .= "group by a_bookinginfo.book_id ";
		$sql1 .= "order by l_mkcode_category.category_name,l_marketingcode.sign ";
		
		//table l_marketingcode-product
		$sql2 = "select l_marketingcode.category_id as type_id,l_mkcode_category.category_name as type_name," .
				"l_marketingcode.mkcode_id as code_id,l_marketingcode.sign as code_name," .
				"\"l_marketingcode\" as tb_name," .
				"c_saleproduct.pds_id,c_bpds_link.bpds_id,count(c_saleproduct.pds_id) as used_qty,count(c_saleproduct.pds_id) as used_person," .
				"(select sum(c_salesreceipt.sr_total) from c_salesreceipt where c_salesreceipt.pds_id = c_saleproduct.pds_id and c_salesreceipt.paid_confirm=1) as used_amount ";
				
		
		
		$sql2 .= "from c_saleproduct,l_marketingcode,l_mkcode_category,c_bpds_link ";
		if($cityid){$sql2 .= ",bl_branchinfo ";}
		$sql2 .= "where c_saleproduct.set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql2 .= "and c_saleproduct.pds_date='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branchid){$sql2 .= "and c_saleproduct.branch_id=".$branchid." ";}
		if($cityid){
			$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
			$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";
		}
		$sql2 .= "and c_saleproduct.mkcode_id=l_marketingcode.mkcode_id ";
		$sql2 .= "and c_saleproduct.mkcode_id!=1 ";
		$sql2 .= "and l_mkcode_category.category_id=l_marketingcode.category_id ";
		$sql2 .= "and c_bpds_link.tb_id=c_saleproduct.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name like \"c_saleproduct\" ";
		$sql2 .= ($status!=2)?"and (select count(g_gift.id_sold) from g_gift where g_gift.id_sold = c_saleproduct.pds_id) = 0 ":"";
		
		$sql2 .= "group by c_saleproduct.pds_id ";
		
		$sql = "($sql1) union ($sql2) order by type_name,code_name";
		//echo "<br><br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
	function getsaledetail($start_date=false,$end_date=false,$branch=false,$city=false,$mkcode=false,$mktypeid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getmkdetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table c_saleproduct
		$sql = "select c_bpds_link.bpds_id as bpds_id,";
		
		$sql .= "c_saleproduct.pds_id as pds_id,";
		$sql .= "c_saleproduct.servicescharge as servicescharge,";
		$sql .= "l_tax.tax_percent as taxpercent,";
		$sql .= "cl_product.pd_name as pd_name,";
		$sql .= "c_srdetail.unit_price as unit_price,";
		$sql .= "c_srdetail.qty as quantity,";
		$sql .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql .= "c_srdetail.set_tax as plus_vat,";
		$sql .= "c_srdetail.set_sc as plus_servicecharge,";	
		$sql .= "c_salesreceipt.pay_id as pay_id,";
		$sql .= "c_saleproduct.branch_id as branch_id,";
		$sql .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql .= "l_paytype.pay_name as pay_name ";
		
		
		$sql .= "from c_saleproduct,c_salesreceipt,c_srdetail,cl_product,cl_product_category," .
				"l_paytype,c_bpds_link,l_tax,l_marketingcode ";
		if($city){$sql .= ",bl_branchinfo ";}
		$sql .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql .= "and c_bpds_link.tb_name like \"c_saleproduct\" ";
		$sql .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($end_date==false){$sql .= "and c_saleproduct.pds_date=".$startdate." ";}
		else{$sql .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branch){$sql .= "and c_saleproduct.branch_id=".$branch." ";}
		if($city){
			$sql .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
			$sql .= "and bl_branchinfo.city_id=".$city." ";
		}
		if($mkcode){$sql .= "and c_saleproduct.mkcode_id=".$mkcode." ";}
		$sql .= "and c_saleproduct.set_cancel<>1 ";
		$sql .= "and c_srdetail.pd_id<>1 ";
		$sql .= "and c_saleproduct.mkcode_id=l_marketingcode.mkcode_id ";

		if($mktypeid){$sql .= "and l_marketingcode.category_id=".$mktypeid." ";}
		$sql .= "order by c_saleproduct.pds_id,c_saleproduct.branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		//echo $sql."<br>";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
	function getsaleqtydetail($begin_date=false,$end_date=false,$branchid=false,$cityid=false,$mkcode=false,$mktypeid=false,$debug=false){
		if(!$begin_date) {
			$this->setErrorMsg("marketing.getmarketingusedqty(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($begin_date,0,4)."-".substr($begin_date,4,2)."-".substr($begin_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		$sql = "select c_bpds_link.bpds_id as bpds_id,";
		$sql .= "c_saleproduct.pds_id as pds_id,";
		$sql .= "bl_branchinfo.branch_name,";
		$sql .= "c_saleproduct.pds_date ";
			
		$sql .= "from c_saleproduct,c_bpds_link,bl_branchinfo,l_marketingcode ";
		$sql .= "where c_saleproduct.set_cancel<>1 ";
		$sql .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql .= "and c_bpds_link.tb_name like \"c_saleproduct\" ";
		if($end_date==false){$sql .= "and c_saleproduct.pds_date=".$startdate." ";}
		else{$sql .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branchid){$sql .= "and c_saleproduct.branch_id=".$branchid." ";}
		if($cityid){
			$sql .= "and bl_branchinfo.city_id=".$cityid." ";
		}
		if($mkcode){$sql .= "and c_saleproduct.mkcode_id=".$mkcode." ";}
		$sql .= "and c_saleproduct.mkcode_id=l_marketingcode.mkcode_id ";
		//$sql .= "and  l_mkcode_category.category_id=l_marketingcode.category_id ";
		if($mktypeid){$sql .= "and l_marketingcode.category_id=".$mktypeid." ";}
		$sql .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql .= "order by c_saleproduct.pds_id ";
		
		//echo "<br><br><br><br><br>".$sql."<br>".$mkcode."-".$mktypeid;
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
	}
/*
 * get booking detail for marketing amount report detail
 * @modified - add this function on 6 Mar 2009
 */
	function getmkdetail($start_date=false,$end_date=false,$branch=false,$city=false,$mkcode=false,$mktypeid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getmkdetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		$sql = "select c_bpds_link.bpds_id as bpds_id,";
		$sql .= "a_bookinginfo.book_id as book_id,";
		$sql .= "a_bookinginfo.c_set_cms as cms,";
		$sql .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql .= "l_tax.tax_percent as taxpercent,";
		$sql .= "cl_product.pd_name as pd_name,";
		$sql .= "c_srdetail.unit_price as unit_price,";
		$sql .= "c_srdetail.qty as quantity,";
		$sql .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql .= "c_srdetail.set_tax as plus_vat,";
		$sql .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql .= "l_employee.emp_nickname as reception_name,";
		$sql .= "l_employee.emp_code as reception_code, ";	
		$sql .= "c_salesreceipt.pay_id as pay_id,";
		$sql .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql .= "a_bookinginfo.b_qty_people as qty_person,";
		$sql .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql .= "l_paytype.pay_name as pay_name ";
		
		
		$sql .= "from a_bookinginfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category," .
				"l_paytype,l_employee,c_bpds_link,l_tax,l_marketingcode ";
		if($city){$sql .= ",bl_branchinfo ";}
		$sql .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql .= "and c_bpds_link.tb_name like \"a_bookinginfo\" ";
		$sql .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch){$sql .= "and a_bookinginfo.b_branch_id=".$branch." ";}
		if($city){
			$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
			$sql .= "and bl_branchinfo.city_id=".$city." ";
		}
		if($mkcode){$sql .= "and a_bookinginfo.mkcode_id=".$mkcode." ";}
		$sql .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql .= "and c_srdetail.pd_id<>1 ";
		$sql .= "and l_employee.emp_id = a_bookinginfo.b_receive_id ";
		$sql .= "and a_bookinginfo.mkcode_id=l_marketingcode.mkcode_id ";
	
		if($mktypeid){$sql .= "and l_marketingcode.category_id=".$mktypeid." ";}
		$sql .= "order by a_bookinginfo.book_id,a_bookinginfo.b_branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		//echo $sql;
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get booking detail for marketing qty report detail
 * @modified - add this function on 6 Mar 2009
 */
	function getmkqtydetail($start_date=false,$end_date=false,$branch=false,$city=false,$mkcode=false,$mktypeid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getmkqtydetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		$sql = "select c_bpds_link.bpds_id as bpds_id,";
		$sql .= "a_bookinginfo.book_id as book_id,";
		$sql .= "bl_branchinfo.branch_name,";
		$sql .= "a_bookinginfo.b_appt_date,";
		$sql .= "al_bookparty.bp_name as cms_company,";
		$sql .= "a_bookinginfo.c_bp_person as cms_name,";
		$sql .= "a_bookinginfo.b_qty_people as qty_person,";
		$sql .= "a_bookinginfo.c_set_cms as cms ";
		
		
		$sql .= "from a_bookinginfo,c_bpds_link,bl_branchinfo,l_marketingcode,al_bookparty ";
		$sql .= "where a_bookinginfo.b_set_cancel<>1 ";
		$sql .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql .= "and c_bpds_link.tb_name like \"a_bookinginfo\" ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch){$sql .= "and a_bookinginfo.b_branch_id=".$branch." ";}
		if($city){
			$sql .= "and bl_branchinfo.city_id=".$city." ";
		}
		if($mkcode){$sql .= "and a_bookinginfo.mkcode_id=".$mkcode." ";}
		$sql .= "and a_bookinginfo.mkcode_id=l_marketingcode.mkcode_id ";
		//$sql .= "and  l_mkcode_category.category_id=l_marketingcode.category_id ";
		if($mktypeid){$sql .= "and l_marketingcode.category_id=".$mktypeid." ";}
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "order by a_bookinginfo.book_id ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get booking detail for marketing report detail
 * @modified - add this function on 6 Mar 2009
 */
	function getgiftdetail($start_date=false,$end_date=false,$branch=false,$city=false,$gifttypeid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getgiftdetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table g_gift
		$sql = "select g_gift.* from g_gift,a_bookinginfo ";
		if($city){$sql .= ",bl_branchinfo ";}
		$sql .= "where g_gift.available = 1 ";
		$sql .= "and a_bookinginfo.book_id=g_gift.book_id ";	
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch){$sql .= "and a_bookinginfo.b_branch_id=".$branch." ";}
		if($city){
			$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
			$sql .= "and bl_branchinfo.city_id=".$city." ";
		}
		if($gifttypeid){$sql .= "and g_gift.gifttype_id=$gifttypeid ";}
		$sql .= "order by g_gift.gift_number ";	
				

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
	
/*
 * get gift issue detail for marketing report detail
 * @modified - add this function on 6 Mar 2009
 */
	function getgiftissuedetail($start_date=false,$end_date=false,$gifttypeid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("marketing.getgiftissuedetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table g_gift
		$sql = "select g_gift.* from g_gift where g_gift.available = 1 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and g_gift.issue='".$startdate."' ";}
		else{$sql .= "and g_gift.issue>='".$startdate."' and g_gift.issue<='".$enddate."' ";}
		if($gifttypeid){$sql .= "and g_gift.gifttype_id=$gifttypeid ";}
		$sql .= "order by g_gift.gift_number ";	
				

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
}
?>
