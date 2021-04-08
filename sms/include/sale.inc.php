<?php
/*
 * File name : sale.inc.php
 * Description : Class file sale report for cms system
 * Author : natt
 * Create date : 23-Jan-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class sale extends report {
	
	function sale(){}
	
	function getbranchtype($order=false,$sort=false) {
		$sql = "select * from bl_branch_category where branch_category_active=1 ";
		
		if($order=="Category"){
			$sql.="order by branch_category_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}
			
		return $this->getResult($sql);
	}
	
	function getcity($order=false,$sort=false) {
		$sql = "select * from al_city ";
		if($order=="Category"){
			$sql.="order by city_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		return $this->getResult($sql);
	}
	
	function getbranch($order=false,$sort=false,$branchid=false,$branchcategoryid=false,$cityid=false,$debug=false) {
		$sql = "select * from bl_branchinfo where branch_active=1 ";
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($branchcategoryid){$sql .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and bl_branchinfo.branch_name!='All' ";
		
		if($order==="Category"){
			$sql.="order by branch_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by branch_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}else{
			$sql.="order by branch_name ";
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
 * get total sale from booking table for Sale Report
 */
	function getsaleinfo($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcpl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		$sql1 = "select a_bookinginfo.b_appt_date,bl_branchinfo.branch_name as branch_name,bl_branchinfo.branch_id,\"a_bookinginfo\" as tb_name,";
		$sql1 .= "bl_branch_category.branch_category_name,bl_branch_category.branch_category_id,";
		$sql1 .= "al_city.city_id,al_city.city_name,a_bookinginfo.book_id,";
		$sql1 .= "sum(c_salesreceipt.sr_total) as total ";
		
		$sql1 .= "from a_bookinginfo,bl_branchinfo,c_salesreceipt,bl_branch_category,al_city ";
		$sql1 .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql1 .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($book_id){$sql1 .= "and a_bookinginfo.book_id=".$book_id." ";}
		$sql1 .= "and a_bookinginfo.book_id=c_salesreceipt.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql1 .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql1 .= "group by a_bookinginfo.book_id ";
		
		
		//table c_saleproduct
		$sql2 = "select c_saleproduct.pds_date as b_appt_date,bl_branchinfo.branch_name as branch_name,bl_branchinfo.branch_id,\"c_saleproduct\" as tb_name,";
		$sql2 .= "bl_branch_category.branch_category_name,bl_branch_category.branch_category_id,";
		$sql2 .= "al_city.city_id,al_city.city_name,c_saleproduct.pds_id,";
		$sql2 .= "sum(c_salesreceipt.sr_total) as total ";
		
		
		$sql2 .= "from c_saleproduct,bl_branchinfo,c_salesreceipt,bl_branch_category,al_city ";
		$sql2 .= "where c_saleproduct.set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql2 .= "and c_saleproduct.pds_date='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		if($book_id){$sql2 .= "and c_saleproduct.pds_id=".$book_id." ";}
		$sql2 .= "and c_saleproduct.pds_id=c_salesreceipt.pds_id ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql2 .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql2 .= "group by c_saleproduct.pds_id ";
		
		$sql = "($sql1) union ($sql2) order by b_appt_date,branch_name ";
		//echo $sql."<br>";return false;
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getsrdetail($start_date=false,$end_date=false,$branch_id=false,$branchcategoryid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getsrdetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		$sql1 = "select a_appointment.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.c_set_cms as cms,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql1 .= "l_employee.emp_nickname as reception_name,";
		$sql1 .= "l_employee.emp_code as reception_code, ";	
		$sql1 .= "c_salesreceipt.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "l_paytype.pay_name as pay_name ";
		
		
		$sql1 .= "from a_bookinginfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_paytype,l_employee,a_appointment,l_tax,bl_branchinfo ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.book_id = a_appointment.book_id ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($branchcategoryid){$sql1 .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";
		$sql1 .= "and l_employee.emp_id = a_bookinginfo.b_receive_id ";
		$sql1 .= "order by a_bookinginfo.book_id,a_bookinginfo.b_branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "\"0\" as cms,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql2 .= "\"-\" as reception_name,";
		$sql2 .= "\"\" as reception_code, ";	
		$sql2 .= "c_salesreceipt.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "l_paytype.pay_name as pay_name ";
		
		
		$sql2 .= "from c_saleproduct,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_paytype,c_bpds_link,l_tax,bl_branchinfo ";
		$sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql2 .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		if($branchcategoryid){$sql2 .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1 ";
		$sql2 .= "order by c_bpds_link.tb_id,c_saleproduct.branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		$sql = "($sql1) union ($sql2) order by bpds_id,branch_id,salesreceipt_id,srdetail_id ";
		
		//echo "<br><br><br><br>$sql<br><br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
		
	}
	
	/*
	 * function for get product category imformation
	 * @modified - add this function on 24-Feb-2009
	 */
	function getpdcategory($acc_func=false,$order=false,$sort=false) {
		$sql = "select * from cl_product_category where pd_category_active=1 ";
		if($acc_func){
			if($acc_func==1){
				$sql.="and cl_product_category.pos_neg_value=0 ";
			}else if($acc_func==2){
				$sql.="and cl_product_category.pos_neg_value=1 ";
			}
		}
		
		if($order=="Category"){
			$sql.="order by pd_category_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		//echo $sql."<br>";
		return $this->getResult($sql);
	}
		
/*
 * get total item sale from booking table for Item Sale Report
 */
	function getitemsale($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$cityid=false,$order=false,$sort=false,$debug=false){

		if(!$start_date) {
			$this->setErrorMsg("checker.getcpl(),Please insert Date for see this report!!");
			return false;
		}
		if($branch_id==""){$branch_id=false;}
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		//table a_bookinginfo
		$sql1="select c_srdetail.pd_id,cl_product.pd_name,sum(c_srdetail.qty) as qty," .
				"cl_product_category.pd_category_name,cl_product_category.pd_category_id,cl_product_category.pos_neg_value, ";
				
		$sql1 .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total, ";
		$sql1 .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.set_sc*(c_srdetail.unit_price*c_srdetail.qty)*(a_bookinginfo.servicescharge/100)) " .
				"else  ( c_srdetail.set_sc * ( c_srdetail.unit_price * c_srdetail.qty ) * ( a_bookinginfo.servicescharge /100 ) )  end) as totalsc, ";
		$sql1 .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -c_srdetail.set_tax*(((c_srdetail.set_sc*(c_srdetail.unit_price*c_srdetail.qty)*(a_bookinginfo.servicescharge/100))+(c_srdetail.unit_price*c_srdetail.qty))*(l_tax.tax_percent/100)) " .
				"else c_srdetail.set_tax*(((c_srdetail.set_sc*(c_srdetail.unit_price*c_srdetail.qty)*(a_bookinginfo.servicescharge/100))+(c_srdetail.unit_price*c_srdetail.qty))*(l_tax.tax_percent/100)) end) as totalvat ";
				
				//"c_srdetail.salesreceipt_id " .
		$sql1 .= "from c_srdetail,cl_product,cl_product_category,a_bookinginfo,c_salesreceipt,l_tax " .
				"where c_srdetail.pd_id=cl_product.pd_id " .
				"and cl_product_category.pd_category_id=cl_product.pd_category_id " .
				"and c_srdetail.book_id=a_bookinginfo.book_id " .
				"and c_srdetail.salesreceipt_id=c_salesreceipt.salesreceipt_id " .
				"and a_bookinginfo.tax_id=l_tax.tax_id ".
				"and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		if($end_date==$start_date){$sql1 .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' " .
				"and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($cityid){$sql1 .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$cityid) ";}
		$sql1 .= "group by cl_product.pd_id ";
		$sql1 .= "order by cl_product_category.pd_category_name";
		
		//table c_saleproduct
		$sql2="select c_srdetail.pd_id,cl_product.pd_name,sum(c_srdetail.qty) as qty," .
				"cl_product_category.pd_category_name,cl_product_category.pd_category_id,cl_product_category.pos_neg_value, ";
		$sql2 .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total, ";
		$sql2 .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.set_sc*(c_srdetail.unit_price*c_srdetail.qty)*(c_saleproduct.servicescharge/100)) " .
				"else  ( c_srdetail.set_sc * ( c_srdetail.unit_price * c_srdetail.qty ) * ( c_saleproduct.servicescharge /100 ) )  end) as totalsc, ";
		$sql2 .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -c_srdetail.set_tax*(((c_srdetail.set_sc*(c_srdetail.unit_price*c_srdetail.qty)*(c_saleproduct.servicescharge/100))+(c_srdetail.unit_price*c_srdetail.qty))*(l_tax.tax_percent/100)) " .
				"else c_srdetail.set_tax*(((c_srdetail.set_sc*(c_srdetail.unit_price*c_srdetail.qty)*(c_saleproduct.servicescharge/100))+(c_srdetail.unit_price*c_srdetail.qty))*(l_tax.tax_percent/100)) end) as totalvat ";
		
				//"c_srdetail.salesreceipt_id " .
		$sql2 .= "from c_srdetail,cl_product,cl_product_category,c_saleproduct,c_salesreceipt,l_tax " .
				"where c_srdetail.pd_id=cl_product.pd_id " .
				"and cl_product_category.pd_category_id=cl_product.pd_category_id " .
				"and c_srdetail.pds_id=c_saleproduct.pds_id " .
				"and c_srdetail.salesreceipt_id=c_salesreceipt.salesreceipt_id " .
				"and c_saleproduct.tax_id=l_tax.tax_id ".
				"and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		if($end_date==$start_date){$sql2 .= "and c_saleproduct.pds_date='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		if($cityid){$sql2 .= "and c_saleproduct.branch_id in (select branch_id from bl_branchinfo where city_id=$cityid) ";}
		$sql2 .= "group by cl_product.pd_id";
	
	
		$sql = "select pd_id,pd_name,sum(qty) as qty,sum(total) as total,sum(totalvat) as totalvat,sum(totalsc) as totalsc," .
			"pd_category_name,pd_category_id,pos_neg_value from (" .
				"($sql1) union all ($sql2) order by pd_category_name ) as allbook group
    	by pd_name ";
    	
    	if($order){
    		
    		if($order=="Amount"){
    			$sql .= "order by pd_category_name,sum(total) ";	
    			if($sort=="A > Z"){$sql.="desc";}
    		}else if($order=="Qty"){
    			$sql .= "order by pd_category_name,sum(qty) ";	
    			if($sort=="A > Z"){$sql.="desc";}
    		}else{
    			$sql .= "order by pd_category_name,pd_name ";
    			if($sort=="A > Z"){$sql.="desc";}
    		}
    	}
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
	}
		
/*
 * get item sale detail from booking table for Item Sale Report Deatil
 */
	function getitemsaledetail($branch_id=false,$start_date=false,$end_date=false,$pd_id=false,$cityid=false,$pdcategoryid=false,$total=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getitemsaledetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		//table a_bookinginfo
		$sql1 = "select a_appointment.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"\"a_bookinginfo\" as tb_name,";
		$sql1 .= "a_bookinginfo.book_id as book_id,";
		$sql1 .= "a_bookinginfo.c_set_cms as cms,";
		$sql1 .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql1 .= "l_tax.tax_percent as taxpercent,";
		$sql1 .= "cl_product.pd_name as pd_name,";
		$sql1 .= "cl_product.pd_category_id as pd_category_id,";/////category id
		$sql1 .= "c_srdetail.unit_price as unit_price,";
		$sql1 .= "c_srdetail.qty as quantity,";
		$sql1 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql1 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql1 .= "c_srdetail.set_tax as plus_vat,";
		$sql1 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql1 .= "l_employee.emp_nickname as reception_name,";
		$sql1 .= "l_employee.emp_code as reception_code, ";	
		$sql1 .= "c_salesreceipt.pay_id as pay_id,";
		$sql1 .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql1 .= "bl_branchinfo.branch_name as branch_name,";
		$sql1 .= "bl_branchinfo.city_id as city_id,";//city
		$sql1 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql1 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql1 .= "l_paytype.pay_id as pay_id,";
		$sql1 .= "l_paytype.pay_name as pay_name ";
		
		
		$sql1 .= "from a_bookinginfo,bl_branchinfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_paytype,l_employee,a_appointment,l_tax ";
		$sql1 .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql1 .= "and a_bookinginfo.b_branch_id = bl_branchinfo.branch_id ";
		$sql1 .= "and a_bookinginfo.book_id = a_appointment.book_id ";
		$sql1 .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
		$sql1 .= "and a_bookinginfo.book_id=c_srdetail.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql1 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql1 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql1 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql1 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($pd_id){$sql1 .= "and c_srdetail.pd_id=".$pd_id." ";}
		if($cityid){$sql1 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($pdcategoryid){$sql1 .= "and cl_product.pd_category_id=$pdcategoryid ";}
		if($total==="pos"){$sql1 .= "and cl_product_category.pos_neg_value=1 ";}
		if($total==="neg"){$sql1 .= "and cl_product_category.pos_neg_value=0 ";}
		$sql1 .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql1 .= "and c_srdetail.pd_id<>1 ";
		$sql1 .= "and l_employee.emp_id = a_bookinginfo.b_receive_id ";
		$sql1 .= "order by a_bookinginfo.book_id,a_bookinginfo.b_branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		//table c_saleproduct
		$sql2 = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm," .
				"c_bpds_link.tb_name as tb_name,";
		$sql2 .= "c_saleproduct.pds_id as book_id,";
		$sql2 .= "\"0\" as cms,";
		$sql2 .= "c_saleproduct.servicescharge as servicescharge,";
		$sql2 .= "l_tax.tax_percent as taxpercent,";
		$sql2 .= "cl_product.pd_name as pd_name,";
		$sql2 .= "cl_product.pd_category_id as pd_category_id,";/////
		$sql2 .= "c_srdetail.unit_price as unit_price,";
		$sql2 .= "c_srdetail.qty as quantity,";
		$sql2 .= "cl_product_category.pos_neg_value as pos_neg_value,";
		$sql2 .= "c_srdetail.unit_price*c_srdetail.qty as amount,";
		$sql2 .= "c_srdetail.set_tax as plus_vat,";
		$sql2 .= "c_srdetail.set_sc as plus_servicecharge,";
		$sql2 .= "\"-\" as reception_name,";
		$sql2 .= "\"\" as reception_code, ";	
		$sql2 .= "c_salesreceipt.pay_id as pay_id,";
		$sql2 .= "c_saleproduct.branch_id as branch_id,";
		$sql2 .= "bl_branchinfo.branch_name as branch_name,";//
		$sql2 .= "bl_branchinfo.city_id as city_id,";
		$sql2 .= "c_salesreceipt.salesreceipt_id as salesreceipt_id,";
		$sql2 .= "c_srdetail.srdetail_id as srdetail_id,";
		$sql2 .= "l_paytype.pay_id as pay_id,";
		$sql2 .= "l_paytype.pay_name as pay_name ";
		
		
		$sql2 .= "from c_saleproduct,bl_branchinfo,c_salesreceipt,c_srdetail,cl_product,cl_product_category,l_paytype,c_bpds_link,l_tax ";
		$sql2 .= "where c_saleproduct.pds_id = c_salesreceipt.pds_id ";
		$sql2 .= "and c_saleproduct.branch_id = bl_branchinfo.branch_id ";
		$sql2 .= "and c_saleproduct.tax_id = l_tax.tax_id ";
		$sql2 .= "and c_saleproduct.pds_id = c_bpds_link.tb_id ";
		$sql2 .= "and c_saleproduct.pds_id=c_srdetail.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name = \"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm=1 ";
		$sql2 .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql2 .= "and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ";
		$sql2 .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql2 .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		if($end_date==false){$sql2 .= "and c_saleproduct.pds_date='".$startdate."' ";}
		else{$sql2 .= "and c_saleproduct.pds_date>='".$startdate."' and c_saleproduct.pds_date<='".$enddate."' ";}
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		if($pd_id){$sql2 .= "and c_srdetail.pd_id=".$pd_id." ";}
		if($cityid){$sql2 .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($pdcategoryid){$sql2 .= "and cl_product.pd_category_id=$pdcategoryid ";}
		if($total==="pos"){$sql2 .= "and cl_product_category.pos_neg_value=1 ";}
		if($total==="neg"){$sql2 .= "and cl_product_category.pos_neg_value=0 ";}
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_srdetail.pd_id<>1 ";
		$sql2 .= "order by c_bpds_link.tb_id,c_saleproduct.branch_id,c_salesreceipt.salesreceipt_id,c_srdetail.srdetail_id ";
		
		$sql = "($sql1) union ($sql2) order by paid_confirm desc,bpds_id,branch_id,salesreceipt_id,srdetail_id ";
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
	}
	
////	

}
?>
