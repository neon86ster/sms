<?php
/*
 * File name : checker.inc.php
 * Description : Class file checker report for cms system
 * Author : natt
 * Create date : 16-Jan-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class checker extends report {
	
	function checker(){}
	
/*
 * get result set of crs report
 */

	function getcrs($branch_id=false,$start_date=false,$end_date=false,$payid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcrs(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		//table a_bookinginfo
		$sql1 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm,";
		$sql1 .= "c_bpds_link.tb_name as tb_name,";
		$sql1 .= "a_bookinginfo.b_appt_date as appt_date,bl_branchinfo.branch_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.b_customer_name as customer_name,";
		$sql1 .= "a_bookinginfo.a_member_code as a_member_code,";
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
				"a_bookinginfo,l_hour,bl_branchinfo,l_paytype,c_bpds_link ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql1 .= "and l_hour.hour_id=a_bookinginfo.b_book_hour ";
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		if($cityid){$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($payid){
		$sql1 .= "and c_salesreceipt.pay_id=$payid ";
		}
		$sql1 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		//$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "group by c_salesreceipt.salesreceipt_id ";
		
		//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_date as appt_date,bl_branchinfo.branch_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "\"-\" as customer_name,";
		$sql2 .= "c_saleproduct.a_member_code as a_member_code,";
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
				"c_saleproduct,bl_branchinfo,l_paytype,c_bpds_link ";
		$sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		if($cityid){$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($payid){
		$sql2 .= "and c_salesreceipt.pay_id=$payid ";
		}
		$sql2 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		//$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "group by c_salesreceipt.salesreceipt_id ";
		
		$sql = "($sql1) union ($sql2) order by paid_confirm desc,branch_id,salesreceipt_number,bpds_id ";
		
		//echo $sql1;
		if($debug) {
			echo "<br><br><br><br>$sql1<br><br><br><br>$sql2<br><br><br><br>$sql<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
	}
	
	function getCancelOrNotfinish($type=false,$branch_id=false,$start_date=false,$end_date=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcrs(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);

		$sql1="select c_bpds_link.*,a_bookinginfo.book_id as book_id " .
				"from a_bookinginfo,c_bpds_link " .
				"where a_bookinginfo.book_id=c_bpds_link.tb_id " .
				"and c_bpds_link.tb_name='a_bookinginfo' ";
		if($type=="cancel"){
			$sql1.="and a_bookinginfo.b_set_cancel=1 ";
		}else if($type=="not finish"){
			$sql1.="and a_bookinginfo.b_set_cancel<>1 ";
			$sql1.="and (a_bookinginfo.book_id not in ( select c_salesreceipt.book_id from c_salesreceipt where c_salesreceipt.book_id=a_bookinginfo.book_id) or a_bookinginfo.book_id in ( select c_salesreceipt.book_id from c_salesreceipt where c_salesreceipt.paid_confirm=0))";
		}
				//"and IFNULL((select count(c_salesreceipt.salesreceipt_id) from c_salesreceipt where c_salesreceipt.book_id=a_bookinginfo.book_id),0)=0 ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		
		$sql2="select c_bpds_link.*,c_saleproduct.pds_id as book_id " .
				"from c_saleproduct,c_bpds_link " .
				"where c_saleproduct.pds_id=c_bpds_link.tb_id " .
				"and c_bpds_link.tb_name='c_saleproduct' ";
		if($type=="cancel"){
			$sql2.="and c_saleproduct.set_cancel=1 ";
		}else if($type=="not finish"){
			$sql2.="and c_saleproduct.set_cancel<>1 ";
			$sql2.="and c_saleproduct.pds_id not in ( select c_salesreceipt.pds_id from c_salesreceipt where c_salesreceipt.pds_id=c_saleproduct.pds_id)";
		}
				//"and IFNULL((select count(c_salesreceipt.salesreceipt_id) from c_salesreceipt where c_salesreceipt.pds_id=c_saleproduct.pds_id),0)=0 ";
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date ='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date >='".$startdate."' and c_saleproduct.pds_date <='".$enddate."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id =".$branch_id." ";}
		
		$sql = "($sql1) union ($sql2) order by bpds_id ";	
		
		if($debug) {
			echo "<br><br><br><br>$sql1<br><br><br><br>$sql2<br><br><br><br>$sql<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get total customer from booking table
 */	
	function getttcs($branch_id=false,$start_date=false,$end_date=false,$payid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getttcs(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select bl_branchinfo.branch_name as branch_name,count(d_indivi_info.indivi_id) as qty ";
		$sql .= "from a_bookinginfo,bl_branchinfo,d_indivi_info ";
		
		$sql .= "where a_bookinginfo.b_branch_id = bl_branchinfo.branch_id ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or was set inroom
		
		if($payid){
			$sql .= "and a_bookinginfo.book_id in " .
					"(select a_bookinginfo.book_id from a_bookinginfo,c_salesreceipt where " .
					"a_bookinginfo.book_id=c_salesreceipt.book_id " .
					"and c_salesreceipt.pay_id=$payid)";
		}
		
		$sql .= "group by bl_branchinfo.branch_name ";
		//echo $sql."<br>";
		if($debug) {
			echo "<br><br><br><br>".$sql."<br>";
		}
		
		return $this->getResult($sql);

	}
	
/*
 * get total therapist hour from booking table
 */	
	function gettthour($branch_id=false,$start_date=false,$end_date=false,$payid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.gettthour(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
						
		$sql = "select sum(l_hour.hour_calculate) as total,bl_branchinfo.branch_name as branch_name ";
		$sql .= "from a_bookinginfo,da_mult_th,l_hour,bl_branchinfo,d_indivi_info ";
		$sql .= "where a_bookinginfo.book_id=da_mult_th.book_id ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		$sql .= "and a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and da_mult_th.hour_id=l_hour.hour_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or was set inroom
		
		if($payid){
			$sql .= "and a_bookinginfo.book_id in " .
					"(select a_bookinginfo.book_id from a_bookinginfo,c_salesreceipt where " .
					"a_bookinginfo.book_id=c_salesreceipt.book_id " .
					"and c_salesreceipt.pay_id=$payid)";
		}
		
		$sql .= "group by bl_branchinfo.branch_name";
		
		$rs = $this->getResult($sql);
		
		if($debug) {
			echo $sql."<br>";
		}
						
		return $rs;
		
	}
	
/*
 * get commission from booking table in commission report
 */		
	function getcms($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$order=false,$sort=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcms(),Please insert Date for see this report!!");
			return false;
		}
		
		if($sort=="A > Z"){
			$sortby="asc";
		}else{
			$sortby="desc";
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		$sql = "select a_bookinginfo.book_id as book_id,a_bookinginfo.b_branch_id as branch_id,";
		$sql .= "bl_branchinfo.branch_name,al_bookparty.bp_name as cms_company_name,";
		$sql .= "a_bookinginfo.b_customer_name as cs_name,al_accomodations.acc_name as hotel,";
		$sql .= "a_bookinginfo.b_qty_people as qty_pp,a_bookinginfo.c_bp_person as cms_name,";
		$sql .= "a_bookinginfo.c_bp_id as cms_company,a_bookinginfo.c_bp_phone as cms_phone, ";
		$sql .= "a_bookinginfo.c_cms_value as c_cms_value, ";
		//$sql .= "cl_product_category.pos_neg_value,c_srdetail.unit_price*c_srdetail.qty as total,al_percent_cms.pcms_percent";
		$sql .= "al_percent_cms.pcms_percent as pcms_percent, ";
		
		$sql .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total ,";
		$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_percent_cms.pcms_percent/100) as cms ";
				
		$sql .= "from a_bookinginfo,c_srdetail,c_salesreceipt," .
				"cl_product,cl_product_category,al_bookparty,al_percent_cms,bl_branchinfo,".
				"al_accomodations ";
		
		$sql .= "where a_bookinginfo.c_set_cms=1 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
				"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ".
				"and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql .= "and a_bookinginfo.b_set_cancel=0 ";		
//		$sql .= "and a_bookinginfo.b_branch_id=1 ";
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql .= "and cl_product_category.set_commission=1 ";
		
		$sql .= "and c_salesreceipt.pay_id!=13 ";		//specific paytype - voucher
		//$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		$sql .= "and (c_salesreceipt.sr_total<>0 or a_bookinginfo.c_cms_value>0) ";
		
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		$sql .= "and a_bookinginfo.c_pcms_id=al_percent_cms.pcms_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		
		
		if($book_id) {
			$sql .= "and a_bookinginfo.book_id=".$book_id." ";
		}
		$sql .= "group by a_bookinginfo.book_id ";
		
		if($order=="Default"){
		$sql .= "order by a_bookinginfo.book_id $sortby";
		}else if($order=="Phone Number"){
		$sql .= "order by a_bookinginfo.c_bp_phone $sortby";
		}else if($order=="Branch"){
		$sql .= "order by bl_branchinfo.branch_name $sortby";
		}
		
		//echo "<br><br><br><br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql,$debug);
	}
		
/*
 * get bankaccount commission from booking table in commission report
 */		
	function getbankcms($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$bank_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		$sql = "select a_bookinginfo.book_id as book_id,a_bookinginfo.b_branch_id as branch_id,";
		$sql .= "bl_branchinfo.branch_name,";
		$sql .= "a_bookinginfo.b_customer_name as cs_name,";
		$sql .= "a_bookinginfo.c_bp_id as cms_company,a_bookinginfo.c_bp_phone as cms_phone," .
				"al_bankacc_cms.bankacc_name,al_bankacc_cms.bankacc_number,al_bankacc_cms.bankacc_comment," .
				"al_bankacc_cms.bank_branch,l_bankname.bank_Ename, ";
		$sql .= "al_percent_cms.pcms_percent as pcms_percent," .
				"al_accomodations.acc_name as hotel," .
				"a_bookinginfo.c_bp_person as cms_name," .
				"al_bookparty.bp_name as cms_company_name, ";
		$sql .= "a_bookinginfo.c_cms_value as c_cms_value, ";
		
		$sql .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total ,";
		$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_percent_cms.pcms_percent/100) as cms ";
				
		$sql .= "from a_bookinginfo,c_srdetail,c_salesreceipt," .
				"cl_product,cl_product_category,al_bookparty,al_percent_cms,bl_branchinfo,".
				"al_accomodations,al_bankacc_cms,l_bankname ";
		
		$sql .= "where a_bookinginfo.c_set_cms=1 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
				"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ".
				"and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql .= "and a_bookinginfo.b_set_cancel=0 ";		
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql .= "and cl_product_category.set_commission=1 ";
		
		$sql .= "and c_salesreceipt.pay_id!=13 ";	//specific paytype - voucher
		//$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		$sql .= "and (c_salesreceipt.sr_total<>0 or a_bookinginfo.c_cms_value>0) ";
		
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		$sql .= "and a_bookinginfo.c_pcms_id=al_percent_cms.pcms_id ";
		$sql .= "and al_bankacc_cms.bank_id=l_bankname.bank_id ";
		$sql .= "and al_bankacc_cms.bankacc_active=1 ";
		$sql .= "and al_bankacc_cms.c_bp_phone=a_bookinginfo.c_bp_phone ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		
		if($bank_id){
			$sql .= "and al_bankacc_cms.bank_id=".$bank_id." ";
		}
		if($book_id) {
			$sql .= "and a_bookinginfo.book_id=".$book_id." ";
		}
		$sql .= "group by a_bookinginfo.book_id ";
		$sql .= "order by l_bankname.bank_Ename ";

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql,$debug);
	}
	
/*
 * get commission-bank account from phonenumber
 */
	function getbankacc($phone=false,$debug=false){
		if(!$phone) {
			$this->setErrorMsg("checker.getbankacc(),Please insert phone number for get information!!");
			return false;		
		}
		
		$sql = "select al_bankacc_cms.*,l_bankname.bank_Ename from al_bankacc_cms,l_bankname " .
				"where al_bankacc_cms.bank_id=l_bankname.bank_id " .
				"and bankacc_active=1 ";
		$sql .= "and c_bp_phone='".$phone."' limit 1";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	
	}
	
/*
 * get commission from booking table in Commission Envelope Number Report
 */	
 	function getenvl($bp_id=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
 		if(!$start_date) {
			$this->setErrorMsg("checker.getenvl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.book_id as book_id,";
		$sql .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql .= "al_percent_cms.pcms_percent as cms_id,";
		$sql .= "a_bookinginfo.b_customer_name as cs_name,";
		$sql .= "al_accomodations.acc_name as hotel,";
		$sql .= "a_bookinginfo.b_qty_people as qty_pp,";
		$sql .= "a_bookinginfo.b_appt_date as bookdate,";
		$sql .= "a_bookinginfo.c_bp_person as cms_name,";
		$sql .= "al_bookparty.bp_name as cms_company,";
		$sql .= "a_bookinginfo.c_bp_phone as cms_phone, ";
		$sql .= "aa_commission.cms_id as cms_id, ";
		$sql .= "a_bookinginfo.c_cms_value as c_cms_value, ";

		$sql .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total ,";
		$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_percent_cms.pcms_percent/100) as cms ";
				
		$sql .= "from a_bookinginfo,aa_commission,c_srdetail,c_salesreceipt," .
				"cl_product,cl_product_category,al_bookparty,al_percent_cms,".
				"al_accomodations ";
		
		$sql .= "where a_bookinginfo.c_set_cms=1 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($bp_id){$sql .= "and a_bookinginfo.c_bp_id=".$bp_id." ";}
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
				"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ".
				"and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql .= "and aa_commission.book_id=a_bookinginfo.book_id ";
		$sql .= "and a_bookinginfo.b_set_cancel=0 ";		
//		$sql .= "and a_bookinginfo.b_branch_id=1 ";
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql .= "and cl_product_category.set_commission=1 ";
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		$sql .= "and a_bookinginfo.c_pcms_id=al_percent_cms.pcms_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		$sql .= "and aa_commission.cmsEnvnumber=0 ";
		
		$sql .= "and c_salesreceipt.pay_id!=13 ";	//specific paytype - voucher
		//$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		$sql .= "and (c_salesreceipt.sr_total<>0 or a_bookinginfo.c_cms_value>0) ";
		
		if($book_id) {
			$sql .= "and a_bookinginfo.book_id=".$book_id." ";
		}
		$sql .= "group by a_bookinginfo.book_id ";
		$sql .= "order by a_bookinginfo.book_id ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
 	}
 	
/*
 * function for update envolope number form Commission Envelope Number Report
 */ 
 	function updateenvl($cmsEnvnumber=0,$cms_price=0,$cms_id=false,$debug=false){
 		$chkcmsEnvnumber= $this->getIdToText($cmsEnvnumber,"aa_commission","cms_id","cmsEnvnumber");
 		
 		if($chkcmsEnvnumber){
 			$this->setErrorMsg("Please check on envolope number : $cmsEnvnumber !!");
 			return false;
 		}
 		
		$sql = "update aa_commission set cms_amount=$cms_price,cmsEnvnumber=$cmsEnvnumber where cms_id=$cms_id";
		return $this->setResult($sql);
 	}
 
 	
/*
 * get commission from booking table in Commission Dispersed Report
 */	
 	function getcdcms($start_date=false,$end_date=false,$page=false,$orderby=false,$anotherpara=false,$debug=false){
	 	if(!$start_date) {
			$this->setErrorMsg("checker.getcdcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as bookdate, a_bookinginfo.c_bp_id, " .
		//		"a_bookinginfo.c_bp_person as cms_name, a_bookinginfo.b_customer_name as cs_name, aa_commission.cmsEnvnumber, " .
		//		"aa_commission.cmsEnvdatepu, aa_commission.cmsGofst_id, a_bookinginfo.c_bp_phone as cms_phone, " .
		//		"a_bookinginfo.b_qty_people as qty_pp,a_bookinginfo.b_set_pickup,aa_commission.cms_id " .
		//		"from aa_commission LEFT JOIN a_bookinginfo ON a_bookinginfo.book_id = aa_commission.book_id " .
		//		"where aa_commission.cmsEnvnumber!=0 ";
				
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as bookdate, a_bookinginfo.c_bp_id, " .
				"a_bookinginfo.c_bp_person as cms_name, a_bookinginfo.b_customer_name as cs_name, aa_commission.cmsEnvnumber, " .
				"aa_commission.cmsEnvdatepu, aa_commission.cmsGofst_id, a_bookinginfo.c_bp_phone as cms_phone, " .
				"a_bookinginfo.b_qty_people as qty_pp,a_bookinginfo.b_set_pickup,aa_commission.cms_id " .
				"from aa_commission, a_bookinginfo,c_salesreceipt " .
				"where a_bookinginfo.book_id = aa_commission.book_id " .
				"and a_bookinginfo.book_id = c_salesreceipt.book_id " .
				"and aa_commission.cmsEnvnumber!=0 " .
				"and c_salesreceipt.pay_id!=13 " ;		//specific paytype - voucher
				//"and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		//$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		$sql .= "and (c_salesreceipt.sr_total<>0 or a_bookinginfo.c_cms_value>0) ";
		
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		
		if($anotherpara) {
			$sql .= " $anotherpara";
		}
		
		///
		$sql .= "group by a_bookinginfo.book_id ";
		///
		
		if($orderby) {
			$sql .= " order by $orderby ASC";
		}
		$showpage = $this->getShowpage();
		$start = ($page-1)*$showpage;
		if($page) {
			$sql .= " limit $start,$showpage";
		}
		
		//echo "<br><br><br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
 	}
 	
/*
 * function for update "Pick Up Date" and "Staff Gave" form Commission Dispersed Report
 */
 	function updatedisp($cmsEnvdatepu=0,$cmsGofst_id=0,$cms_id=false,$debug=false){
		$sql = "update aa_commission set cmsEnvdatepu=$cmsEnvdatepu,cmsGofst_id=$cmsGofst_id where cms_id=$cms_id";
	
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->setResult($sql);
 	}
 
/*
 * get customer from booking table for Customers Per Location Report
 */
	function getcpl($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcpl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,bl_branchinfo.branch_name as branch_name,bl_branchinfo.branch_id,";
		$sql .= "bl_branch_category.branch_category_name,bl_branch_category.branch_category_id,";
		$sql .= "al_city.city_id,al_city.city_name,a_bookinginfo.book_id,";
		$sql .= "a_bookinginfo.b_qty_people as qty ";
		
		$sql .= "from a_bookinginfo,bl_branchinfo,d_indivi_info,bl_branch_category,al_city ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; 
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "group by a_bookinginfo.book_id ";
		$sql .= "order by a_bookinginfo.b_appt_date,bl_branchinfo.branch_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getcpldetail($start_date=false,$end_date=false,$branchid=false,$branchcategoryid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcpldetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_appointment.bpds_id,a_appointment.book_id," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"a_appointment.appt_date,p_timer.time_start," .
				"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email,d_indivi_info.cs_age " .
				"from a_bookinginfo,bl_branchinfo,d_indivi_info,a_appointment,bl_branch_category,al_city,p_timer " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($branchcategoryid){$sql .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; 
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and a_appointment.book_id=a_bookinginfo.book_id ";
		$sql .= "group by d_indivi_info.indivi_id ";
		$sql .= "order by bl_branchinfo.branch_name,a_appointment.appt_date,p_timer.time_start ";
		
		//echo "<br><br><br><br>$sql<br><br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}

	function getbranchtype($order=false,$sort=false) {
		$sql = "select * from bl_branch_category where branch_category_active=1 ";
		
		if($order=="Category"){
			$sql.="order by branch_category_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by branch_category_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
			
		return $this->getResult($sql);
	}
	
	function getcity($order=false,$sort=false) {
		$sql = "select * from al_city ";
		if($order=="Category"){
			$sql.="order by city_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by city_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		return $this->getResult($sql);
	}
	
	function getbranch($order=false,$sort=false,$branchid=false,$branchcategoryid=false,$cityid=false,$debug=false) {
		$sql = "select * from bl_branchinfo where branch_active=1 ";
		$sql .= "and bl_branchinfo.branch_name!='All' ";
		
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($branchcategoryid){$sql .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($order==="Category"){
			$sql.="order by branch_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by branch_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}

/*
 * get total hour from booking table for Therapist Hours Per Location Report
 */
	function getthpl($branchid=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getthpl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
				
		$sql = "select bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"d_indivi_info.indivi_id,l_employee.emp_id,l_employee.emp_nickname,l_employee.emp_code," .
				"d_indivi_info.package_id,l_hour.hour_name,l_hour.hour_calculate as total,bl_room.room_name," .
				"bl_branch_category.branch_category_name,bl_branch_category.branch_category_id," .
				"al_city.city_id,al_city.city_name," .
				"a_bookinginfo.book_id,a_bookinginfo.b_appt_date,a_bookinginfo.b_qty_people " .
				"from a_bookinginfo,bl_branchinfo,al_city,l_employee,d_indivi_info,da_mult_th,l_hour,bl_room,bl_branch_category " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		//if($categoryid){$sql .= "and bl_branchinfo.branch_category_id=".$categoryid." ";}
		//if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		//if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "order by l_employee.emp_code,a_bookinginfo.book_id,bl_branchinfo.branch_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get total hour from booking table for Therapist Hours Per Location Report
 */
	function getthplw($start_date,$end_date,$branchid,$categoryid,$city,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getthpl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
				
		$sql = "select bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"d_indivi_info.indivi_id,l_employee.emp_id,l_employee.emp_nickname,l_employee.emp_code," .
				"d_indivi_info.package_id,l_hour.hour_name,l_hour.hour_calculate,bl_room.room_name," .
				"bl_branch_category.branch_category_name,bl_branch_category.branch_category_id," .
				"al_city.city_id,al_city.city_name," .
				"a_bookinginfo.book_id,a_bookinginfo.b_appt_date,a_bookinginfo.b_qty_people " .
				"from a_bookinginfo,bl_branchinfo,al_city,l_employee,d_indivi_info,da_mult_th,l_hour,bl_room,bl_branch_category " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		//if($categoryid){$sql .= "and bl_branchinfo.branch_category_id=".$categoryid." ";}
		//if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		//if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "order by l_employee.emp_code,a_bookinginfo.book_id,bl_branchinfo.branch_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	function gettherapist($order=false,$sort=false,$empid=false,$branchid=false,$cityid=false,$debug=false) {
		$sql = "select l_employee.*," .
				"bl_branchinfo.city_id from l_employee,bl_branchinfo where l_employee.emp_active=1 ";
		$sql .= "and l_employee.emp_department_id = 4 " .
				"and l_employee.emp_id != 1 " .
				"and bl_branchinfo.branch_id=l_employee.branch_id ";			// Therapist
		
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		if($branchid){$sql .= "and l_employee.branch_id=".$branchid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($order=="Employee Code"){
			$sql.="order by l_employee.emp_code ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order=="Employee Name"){
			$sql.="order by l_employee.emp_nickname ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get therapist total hour detail from a_bookinginfo
 * @modified - add this function on 27-Feb-2009
 */	
	function getthhourdetail($start_date=false,$end_date=false,$branchid=false,$categoryid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getthhourdetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"d_indivi_info.indivi_id,l_employee.emp_id,l_employee.emp_nickname,l_employee.emp_code,d_indivi_info.package_id,l_hour.hour_name,l_hour.hour_calculate,bl_room.room_name," .
				"a_bookinginfo.book_id,a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.b_qty_people " .
				"from a_bookinginfo,bl_branchinfo,al_city,l_employee,d_indivi_info,da_mult_th,l_hour,bl_room " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($categoryid){$sql .= "and bl_branchinfo.branch_category_id=".$categoryid." ";}
		//if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "order by l_employee.emp_code,a_bookinginfo.book_id,bl_branchinfo.branch_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
		
	function makehotelcmslistbox($selected=false,$debug=false){
		$sql1="select acc_id,acc_name,\"al_accomodations\" as tablename from al_accomodations where cmspercent>0 and acc_active=1 ";
		$sql2="select bp_id as acc_id,bp_name as acc_name,\"al_bookparty\" as tablename from al_bookparty where bp_cmspercent>0 and bp_active=1 ";
		$sql = "($sql1) union ($sql2) order by acc_name";
		
		//echo $sql."<br>";
		$rs = $this->getResult($sql);
		$count = $rs["rows"];
		
		echo "<select id=\"hotelid\" name=\"hotelid\" onChange=\"this.form.submit();\">";
		for($i=0; $i < $count; $i++) {
			$data = $rs[$i]["tablename"].$rs[$i]["acc_id"];
			echo "<option title=\"".$rs[$i]["acc_name"]."\" value=".$data;
			if ($data == $selected) {
				echo " selected=\"selected\"";
			}
			echo ">";
			echo $rs[$i]["acc_name"]."</option>";
		}
		echo "</select>";	
	}
}

function getResult($SQL=false, $debug=false) {
		if(!$SQL){
			$this->getErrorMsg("Plese check your query language!!");
			return false;
		}
		$m = new mysql($GLOBALS["global_database"],$GLOBALS["global_user"],$GLOBALS["global_pass"]);
		$rs = $m->getdata($SQL, $debug);
		$this->setRecordcount($m->get_recordcount(false));
		
		if(!$rs && $this->getDebugStatus()) {
			$this->setErrorMsg($m->get_msg());
			$this->printDebug("checker.getResult()",false,$SQL,$m->get_msg(),$m->__error);
		}
		if($this->getDebugStatus()||$debug==true) {$this->printDebug("checker.getResult()","founds <b>".$rs["rows"]."</b> row(s)",$SQL,$m->get_msg(),$m->__error);}
		
		unset($m);
		return $rs;
}
?>
