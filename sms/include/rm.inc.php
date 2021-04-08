<?php
/*
 * File name : rm.inc.php
 * Description : Class file relationship management report for sms system
 * Author : natt
 * Create date : 24-Aug-2009
 * Modified : natt@tap10.com
 */   
require_once("report.inc.php");
class rm extends report {
	
	function rm(){}
	
/*
 * get membership information from membership table in membership relationship management report
 * @modified - add this function in 24-Aug-2009
 */	
	function getmembership($where=false,$categoryid=false,$inactive=false,$order="member_code",$sortby="Z > A",$debug=false){	
		$sort = "";
		if($sortby==="A > Z"){$sort = "desc";}
		
		$sql = "select m_membership.*,(select
    max(fill_date) as fill_date
from
(
(select a_bookinginfo.a_member_code,a_bookinginfo.b_appt_date AS fill_date from a_bookinginfo , c_salesreceipt , c_srdetail , cl_product where a_bookinginfo.book_id =c_salesreceipt.book_id  and c_salesreceipt.book_id =c_srdetail.book_id  and c_srdetail.salesreceipt_id =c_salesreceipt.salesreceipt_id  and c_srdetail.pd_id =cl_product.pd_id and cl_product.pd_category_id =11) union

(select c_saleproduct.a_member_code,c_saleproduct.pds_date AS fill_date from c_saleproduct , c_salesreceipt , c_srdetail , cl_product where c_saleproduct.pds_id =c_salesreceipt.pds_id  and c_salesreceipt.pds_id =c_srdetail.pds_id  and c_srdetail.salesreceipt_id =c_salesreceipt.salesreceipt_id  and c_srdetail.pd_id =cl_product.pd_id and cl_product.pd_category_id =11) 
)as date_query where a_member_code=m_membership.member_code) as fill_date,mb_category.category_name,dl_sex.sex_type,dl_nationality.nationality_name " .
				"from m_membership,mb_category,dl_sex,dl_nationality " .
				"where m_membership.category_id = mb_category.category_id " .
				"and m_membership.sex_id=dl_sex.sex_id " .
				"and m_membership.nationality_id=dl_nationality.nationality_id ";
		if($categoryid){$sql .= "and m_membership.category_id='$categoryid' ";}
		if(!$inactive){
			$sql .= "and m_membership.expired=1 ";
			$sql .= "and (m_membership.expireddate>curdate() ";
			$sql .= "or m_membership.expireddate=\"0000-00-00\") ";
		}
		if($where!=""){
			$sql .= "and (lower(member_code) like \"%".strtolower($where)."%\" " .
					"or lower(fname) like \"%".strtolower($where)."%\" " .
					"or lower(mname) like \"%".strtolower($where)."%\" " .
					"or lower(lname) like \"%".strtolower($where)."%\" " .
					"or phone like \"%".strtolower($where)."%\" " .
					"or mobile like \"%".strtolower($where)."%\" " .
					"or lower(email) like \"%".strtolower($where)."%\" ) ";
		}
		if($order=="birthdate"){
			$sql .= "order by DATE_FORMAT(birthdate,\"%m\") $sort,DATE_FORMAT(birthdate,\"%d\") $sort";
		}else if($order=="sex_id"){
			$sql .= "order by dl_sex.sex_type $sort ";
		}else if($order=="nationality_id"){
			$sql .= "order by dl_nationality.nationality_name $sort ";
		}else if($order=="joindate"){
			if($sort=="desc"){
				$sort="asc";
			}else{
				$sort="desc";
			}
			$sql .= "order by m_membership.joindate $sort ";
		}else if($order=="fill_date"){
			if($sort=="desc"){
				$sort="asc";
			}else{
				$sort="desc";
			}
			$sql .= "order by fill_date $sort ";
		}else if($order=="expireddate"){
			$sql .= "order by m_membership.expireddate $sort ";
		}else{
			$sql .= "order by $order $sort ,member_code";
		}		
		
		//echo $sql;
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get customer information from booking table in customer relationship management report
 * @modified - add this function in 24-Aug-2009
 */	
	function getcustinfo($start_date=false,$end_date=false,$where=false,$branchid=false,$cityid=false,$order="member_code",$sortby="Z > A",$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("rm.getcustinfo(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.a_member_code," .
				"d_indivi_info.cs_name,dl_nationality.nationality_name as cs_nation,d_indivi_info.cs_birthday," .
				"d_indivi_info.cs_email,d_indivi_info.cs_phone,dl_sex.sex_type as cs_gender," .
				"d_indivi_info.resident,d_indivi_info.visitor,al_city.city_name,d_indivi_info.cs_age " .
				"from a_bookinginfo,d_indivi_info,dl_sex,dl_nationality,bl_branchinfo,al_city " .
				"where a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and a_bookinginfo.b_set_cancel=0 " .
				"and d_indivi_info.sex_id=dl_sex.sex_id " .
				"and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
				"and bl_branchinfo.city_id=al_city.city_id " .
				"and d_indivi_info.nationality_id=dl_nationality.nationality_id ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id='$branchid' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id='$cityid' ";}
		if($where==""){
			$sql .= "and (d_indivi_info.cs_phone not like \"\" " .
					"or (lower(d_indivi_info.cs_email) not like \"\" " .
					"and lower(d_indivi_info.cs_email) not like \"n/a\") ) ";	// cutoff "" and "n/a"
		}else{
			$sql .= "and (lower(d_indivi_info.cs_name) like \"%".strtolower($where)."%\" " .
					"or d_indivi_info.cs_phone like \"%$where%\" " .
					"or lower(d_indivi_info.cs_email) like \"%".strtolower($where)."%\" ) ";
		}
		
		$sort = "";
		if($sortby==="A > Z"){$sort = "desc";}
		
		if($order=="cs_birthday"){
			$sql .= "order by DATE_FORMAT(cs_birthday,\"%m\") $sort,DATE_FORMAT(cs_birthday,\"%d\") $sort";
		}else if($order=="b_appt_date"){
			$sql .= "order by a_bookinginfo.$order $sort ";
		}else if($order=="sex_id"){
			$sql .= "order by dl_sex.sex_type $sort ";
		}else if($order=="nationality_id"){
			$sql .= "order by dl_nationality.nationality_name $sort ";
		}else if($order=="member_code"){
			$sql .= "order by a_bookinginfo.a_member_code $sort ";
		}		
		$sql .= ",a_bookinginfo.book_id $sort";		
		
		$rs = $this->getResult($sql);
		for($i=0;$i<$rs["rows"];$i++){
			$sql = "select member_code, fname, lname " .
					"from m_membership where member_code=".$rs[$i]["a_member_code"];
			$check_mem = $this->getResult($sql);
			$mem_name = $check_mem[0]["fname"].$check_mem[0]["lname"];
			$mem_name = str_replace(" ", "", $mem_name); 
			$cs_name = str_replace(" ", "", $rs[$i]["cs_name"]);
			if($mem_name!=$cs_name){
				$rs[$i]["a_member_code"]="";
			}
		}
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $rs;
	}
	
/*
 * get commission from booking table in agents relationship management
 * @modified - add this function in 24-Aug-2009
 */		
	function getcms($start_date=false,$end_date=false,$where=false,$branchid=false,$cityid=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("rm.getcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select al_bookparty.bp_id,al_bookparty.bp_name as company_name," .
				"sum(a_bookinginfo.b_qty_people) as qty_pp,count(a_bookinginfo.book_id) as cntbook," .
				"al_bookparty.bp_address,al_bookparty.bp_phone,al_bookparty.bp_email,al_bookparty.bp_detail ";
		
		$sql .= " from a_bookinginfo,bl_branchinfo,al_city,al_bookparty ";
		
		
		$sql .= "where a_bookinginfo.c_bp_id=al_bookparty.bp_id " .
				"and a_bookinginfo.b_set_cancel=0 " .
				"and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
				"and bl_branchinfo.city_id=al_city.city_id ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id='$branchid' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id='$cityid' ";}
		if($where){
			$sql .= "and (lower(al_bookparty.bp_name) like \"%".strtolower($where)."%\" " .
					"or al_bookparty.bp_phone like \"%$where%\" " .
					"or lower(al_bookparty.bp_address) like \"%".strtolower($where)."%\" " .
					"or lower(al_bookparty.bp_email) like \"%".strtolower($where)."%\" " .
					"or lower(al_bookparty.bp_detail) like \"%".strtolower($where)."%\" ) ";
		}
		$sql .= "group by a_bookinginfo.c_bp_id ";
		$sql .= "order by al_bookparty.bp_name ";	

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get booking information from booking table in Individual booking relationship management report
 * @modified - add this function in 24-Aug-2009
 */	
	function getbookinfo($start_date=false,$end_date=false,$where=false,$branchid=false,$cityid=false,$bpid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("rm.getbookinfo(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date," .
				"a_bookinginfo.b_customer_name as cs_name,al_bookparty.bp_id,al_bookparty.bp_name as company_name," .
				"al_accomodations.acc_id,al_accomodations.acc_name," .
				"a_bookinginfo.c_bp_person as bp_person,a_bookinginfo.c_bp_phone,bl_branchinfo.branch_name " .
				"from a_bookinginfo,bl_branchinfo,al_city,al_bookparty,al_accomodations " .
				"where a_bookinginfo.c_bp_id=al_bookparty.bp_id " .
				"and a_bookinginfo.b_set_cancel=0 " .
				"and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id " .
				"and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
				"and bl_branchinfo.city_id=al_city.city_id ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id='$branchid' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id='$cityid' ";}
		if($bpid){$sql .= "and a_bookinginfo.c_bp_id='$bpid' ";}
		if($where){
			$sql .= "and (lower(al_accomodations.acc_name) like \"%".strtolower($where)."%\" " .
					"or a_bookinginfo.c_bp_phone like \"%$where%\" " .
					"or lower(a_bookinginfo.b_customer_name) like \"%".strtolower($where)."%\" " .
					"or lower(a_bookinginfo.c_bp_person) like \"%".strtolower($where)."%\" " .
					"or lower(al_bookparty.bp_name) like \"%".strtolower($where)."%\" ) ";
		}
		$sql .= "order by al_bookparty.bp_name,a_bookinginfo.c_bp_person ";		
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
}
?>
