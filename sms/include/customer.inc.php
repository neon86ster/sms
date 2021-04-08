<?php
/*
 * File name : checker.inc.php
 * Description : Class file checker report for cms system
 * Author : natt
 * Create date : 16-Jan-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class customer extends report {
	
	function customer(){}
	
/*
 * get customer from booking table in customer accomodations report
 * @modified - add this function in 28 Feb 2009
 */		
	function getcusperacc($city_id=false,$start_date=false,$end_date=false,$havecms=false,$collapse=false,$book_id=false,$branchid=false,$anotherpara=false,$order=false,$sort=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcusperacc(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date," .
				"a_bookinginfo.c_bp_person as cms_name,a_bookinginfo.c_bp_phone as cms_phone," .
				"a_bookinginfo.b_accomodations_id as acc_id,bl_branchinfo.branch_name," .
				" al_bookparty.bp_name as cms_company,a_bookinginfo.c_set_cms as has_cms," .
				"a_bookinginfo.b_branch_id,a_bookinginfo.c_bp_person as cms_company_name," .
			 	"al_accomodations.acc_name ";
		if($collapse=="Expand"){$sql .=",sum(a_bookinginfo.b_qty_people) as qty_pp,count(a_bookinginfo.book_id) as cntbook ";}
		else{$sql .=",a_bookinginfo.b_qty_people as qty_pp "; }
		
		$sql .= " from a_bookinginfo,al_bookparty,al_accomodations,bl_branchinfo ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 " .
				"and al_bookparty.bp_id=a_bookinginfo.c_bp_id " .
				"and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id ".	
				"and al_accomodations.acc_id=a_bookinginfo.b_accomodations_id ";	
		
		if($anotherpara){$sql .= " $anotherpara";}
		
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($city_id){$sql .= "and bl_branchinfo.city_id=".$city_id." ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id='$branchid' ";}
		if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
		if($book_id) {$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($collapse=="Expand"){$sql .= "group by a_bookinginfo.b_accomodations_id ";}
		else{$sql .= "group by a_bookinginfo.book_id ";}
		
	 if($order){
		if($order=="Total Bookings" && $collapse=="Expand"){
		$sql .= "order by cntbook ";
		if($sort=="A > Z"){$sql.="desc";}
		}else if($order=="Total Customers" && $collapse=="Expand"){
		$sql .= "order by qty_pp ";
	    if($sort=="A > Z"){$sql.="desc";}
		}else{
		$sql .= "order by al_accomodations.acc_name ";
		if($sort=="A > Z"){$sql.="desc ";}
		$sql .=",bl_branchinfo.city_id,a_bookinginfo.book_id ";
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
 * get customer information from booking table in customer information report
 * @modified - add this function in 28 Feb 2009
 */	
	function getcustinfo($start_date=false,$end_date=false,$where=false,$branchid=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcusperacc(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date," .
				"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email " .
				"from a_bookinginfo,d_indivi_info " .
				"where a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id='$branchid' ";}
		if($where==""){
			$sql .= "and (d_indivi_info.cs_phone not like \"\" " .
					"or (lower(d_indivi_info.cs_email) not like \"\" " .
					"and lower(d_indivi_info.cs_email) not like \"n/a\") ) ";	// cutoff "" and "n/a"
		}else{
			$sql .= "and (lower(d_indivi_info.cs_name) like \"%".strtolower($where)."%\" " .
					"or d_indivi_info.cs_phone like \"%$where%\" " .
					"or lower(d_indivi_info.cs_email) like \"%".strtolower($where)."%\" ) ";
		}
		$sql .= "order by a_bookinginfo.b_appt_date,a_bookinginfo.book_id ";		
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get customer information from booking table in number of customer report
 * @modified - add this function in 2 March 2009
 */	
	function getcustnum($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcustnum(),Please insert Date for see this report!!");
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
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "group by a_bookinginfo.book_id ";
		$sql .= "order by bl_branchinfo.branch_name,a_bookinginfo.b_appt_date ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getcustnumdetail($start_date=false,$end_date=false,$branchid=false,$branchcategoryid=false,$cityid=false,$debug=false){
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
		$sql .= "order by bl_branchinfo.branch_name,a_appointment.bpds_id,a_appointment.appt_date,p_timer.time_start ";
		
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
	
	function getcity($order=false,$sort=false,$cityid=false) {
		$sql = "select * from al_city ";
		if($cityid){
		$sql.="where city_id=".$cityid." ";	
		}
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
 * make age range in age of customer report
 * @modified - add this function on 3 March 2009
 */
	function makeagerange($sort=false) {
		$agerange = array();
		
		if($sort==="Z > A"){
		$agerange[0]["start"] = 0;
		$agerange[0]["end"] = 20;
		for($i=1; $i<=12; $i++) {
			if($i==1) {
				$agerange[$i]["start"] = 21;
				$agerange[$i]["end"] = 25;
			}
			else {
				$agerange[$i]["start"] = $agerange[$i-1]["start"]+5;
				$agerange[$i]["end"] = $agerange[$i-1]["end"]+5;
			}
		}
		$agerange[$i]["start"] = $agerange[$i-1]["start"]+5;
		$agerange[$i]["end"] = 100;
		}else{
		$agerange[0]["start"] = 81;
		$agerange[0]["end"] = 100;
		for($i=1; $i<=12; $i++) {
			if($i==1) {
				$agerange[$i]["start"] = 76;
				$agerange[$i]["end"] = 80;
			}
			else {
				$agerange[$i]["start"] = $agerange[$i-1]["start"]-5;
				$agerange[$i]["end"] = $agerange[$i-1]["end"]-5;
			}
		}
		$agerange[$i]["start"] = 0;
		$agerange[$i]["end"] = 20;
		}
		$agerange["rows"] = $i+1;
		return $agerange;
	}
		
/*
 * get customer information from booking table in age of customer report
 * @modified - add this function on 3 March 2009
 */
	 function getcusperage($city_id=false,$start_date=false,$end_date=false,$book_id=false,$branch_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcusperacc(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,1 as total,d_indivi_info.cs_age,a_bookinginfo.book_id,a_bookinginfo.b_branch_id ";
		
		$sql .= "from a_bookinginfo,d_indivi_info,dl_sex ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($city_id){$sql .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$city_id) ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.sex_id=dl_sex.sex_id ";
		$sql .= "and d_indivi_info.cs_age!=0 ";
		//$sql .= "order by dl_sex.sex_type,d_indivi_info.cs_age ";
		
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get customer information detail from booking table in age of customer report detail
 * @modified - add this function on 4 March 2009
 */
	function getcusperagedetail($start_date=false,$end_date=false,$beginage=false,$endage=false,$branchid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcusperagedetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_appointment.bpds_id,a_appointment.book_id," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"a_appointment.appt_date,p_timer.time_start," .
				"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email,d_indivi_info.cs_age,dl_sex.sex_type " .
				"from a_bookinginfo,bl_branchinfo,d_indivi_info,a_appointment,bl_branch_category,al_city,p_timer,dl_sex " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($beginage){$sql .= "and d_indivi_info.cs_age>=".$beginage." ";}
		if($endage){$sql .= "and d_indivi_info.cs_age<=".$endage." ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; 
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.cs_age!=0 ";
		$sql .= "and d_indivi_info.sex_id=dl_sex.sex_id ";
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
	
/*
 * get average value of cs_age group by sex type from booking individual table in age of customer report
 * @modified - add this function on 4 March 2009
 */
	function getavgage($start_date=false,$end_date=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getavgage(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select dl_sex.sex_type,avg(d_indivi_info.cs_age) as age ";
		$sql .= "from a_bookinginfo,d_indivi_info,dl_sex ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.sex_id=dl_sex.sex_id ";
		$sql .= "and d_indivi_info.cs_age!=0 ";
		
		$sql .= "group by dl_sex.sex_type ";
		
		$sql .= "order by dl_sex.sex_type ";
		
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
	function sumagefield($rs,$fieldname,$bagerange=false,$eagerange=false,$begin=false,$end=false){
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			if($rs[$i]["cs_age"]>=$bagerange&&$rs[$i]["cs_age"]<=$eagerange){
					$chk_begin =ctype_digit($begin);
					$chk_end = ctype_digit($end);
					if(!$chk_begin&&$chk_end){
						if($rs[$i]["b_branch_id"]==$end){
							$sum += $rs[$i]["$fieldname"];
						}
					}else if($appt_date>=$begin&&$appt_date<=$end){
						$sum += $rs[$i]["$fieldname"];
					}
						//echo "$begin: ".$rs[$i]["b_branch_id"]."==$end <br>";
			}
		}
		return $sum;
	}
	
/*
 * get customer information from booking table in Customer Resident Or Visitor Report
 * @modified - add this function on 4 March 2009
 */
	 function getcustlocal($resident=false,$start_date=false,$end_date=false,$branch_id=false,$book_id=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcustlocal(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,bl_branchinfo.branch_name as branch_name,bl_branchinfo.branch_id,";
		$sql .= "bl_branch_category.branch_category_name,bl_branch_category.branch_category_id,";
		$sql .= "al_city.city_id,al_city.city_name,a_bookinginfo.book_id,";
		$sql .= "1 as total ";
		
		$sql .= "from a_bookinginfo,bl_branchinfo,d_indivi_info,bl_branch_category,al_city ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}else
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		if($resident==="Resident"){$sql .= "and d_indivi_info.resident=1 ";}
		if($resident==="Visitor"){$sql .= "and d_indivi_info.visitor=1 ";}
		if($resident==="Unknown"){$sql .= "and d_indivi_info.resident=0 and d_indivi_info.visitor=0 ";}
		$sql .= "group by d_indivi_info.indivi_id ";
		$sql .= "order by a_bookinginfo.b_appt_date,bl_branchinfo.branch_name ";
		
		//echo "<br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br><br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get customer information from booking table in Customer Resident Or Visitor Detail
 * @modified - add this function on 4 March 2009
 */
	 function getcustlocaldetail($start_date=false,$end_date=false,$branchid=false,$branchcategoryid=false,$cityid=false,$resident=false,$debug=false){
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_appointment.bpds_id,a_appointment.book_id," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"a_appointment.appt_date,p_timer.time_start," .
				"d_indivi_info.resident,d_indivi_info.visitor," .
				"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email,d_indivi_info.cs_age,dl_sex.sex_type " .
				"from a_bookinginfo,bl_branchinfo,d_indivi_info,a_appointment,bl_branch_category,al_city,p_timer,dl_sex " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($branchcategoryid){$sql .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($resident==="Resident"){$sql .= "and d_indivi_info.resident=1 ";}
		if($resident==="Visitor"){$sql .= "and d_indivi_info.visitor=1 ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; 
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.sex_id=dl_sex.sex_id ";
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
	

/*
 * get customer information from booking table in health of customer report
 * @modified - add this function on 6 March 2009
 */
	function getcusthealthinfo($branch_id=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("checker.getcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.b_appt_time_id as appt_time_id," .
				"p_timer.time_start,l_employee.emp_code,l_employee.emp_nickname," .
				"d_indivi_info.cs_name,dl_nationality.nationality_name," .
				"al_accomodations.acc_name,db_package.package_name," .
				"da_mult_th.hour_id,da_mult_th.therapist_id,a_appointment.bpds_id " .
				
				"from a_bookinginfo,d_indivi_info,dl_nationality,al_accomodations,db_package,da_mult_th,p_timer,l_employee,a_appointment " .
				"where a_bookinginfo.b_set_cancel=0 " .
				"and a_bookinginfo.b_appt_time_id=p_timer.time_id " .
				"and a_bookinginfo.book_id=a_appointment.book_id " .
				"and a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and al_accomodations.acc_id=a_bookinginfo.b_accomodations_id " .
				"and d_indivi_info.package_id=db_package.package_id " .
				"and dl_nationality.nationality_id=d_indivi_info.nationality_id " .
				"and a_bookinginfo.book_id=da_mult_th.book_id " .
				"and d_indivi_info.indivi_id=da_mult_th.indivi_id " .
				"and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		$sql .= "order by a_bookinginfo.book_id ";		
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql,$debug);
	}
	
/*
 * get all continent for continent table
 * @modified - add this function on 6 March 2009
 */
	function getcontinent($order=false,$sort=false,$continentid=false) {
		$sql = "select * from dl_continent ";
		if($continentid){$sql .= "where continent_id=$continent_id ";}
		if($order==="Category"){
			$sql.="order by continent_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by continent_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get all nationality for nationality table
 * @modified - add this function on 6 March 2009
 */
	function getnationality($order=false,$sort=false,$nationalityid=false,$continentid=false,$debug=false) {
		$sql = "select * from dl_nationality where nationality_active=1 ";
		
		if($nationalityid){$sql .= "and dl_nationality.nationality_id=".$nationalityid." ";}
		if($continentid){$sql .= "and dl_nationality.continent_id=".$continentid." ";}
		if($order==="Category"){
			$sql.="order by nationality_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by nationality_id ";
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
 * get customer information from booking table in nationality of customer report
 * @modified - add this function in 6 March 2009
 */	
	function getcustnation($start_date=false,$end_date=false,$nationality_id=false,$continent_id=false,$book_id=false,$branch_id=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcustnation(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,a_bookinginfo.book_id," .
				"dl_nationality.nationality_name,dl_nationality.nationality_id,";
		$sql .= "dl_continent.continent_name,dl_continent.continent_id,d_indivi_info.indivi_id, ";
		$sql .= "1 as qty ";
		
		$sql .= "from a_bookinginfo,dl_nationality,d_indivi_info,dl_continent ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($nationality_id){$sql .= "and dl_nationality.nationality_id=".$nationality_id." ";}
		if($continent_id){$sql .= "and dl_continent.continent_id=".$continent_id." ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($cityid){$sql .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$cityid) ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "and d_indivi_info.nationality_id=dl_nationality.nationality_id ";
		$sql .= "and dl_nationality.continent_id=dl_continent.continent_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "order by a_bookinginfo.b_appt_date,dl_nationality.nationality_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
		
/*
 * get customer information from booking table in Customer Nationality Detail
 * @modified - add this function on 4 March 2009
 */
	 function getcustnationdetail($start_date=false,$end_date=false,$nationality_id=false,$continent_id=false,$book_id=false,$debug=false){
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_appointment.bpds_id,a_appointment.book_id," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"a_appointment.appt_date,p_timer.time_start," .
				"dl_continent.continent_name,dl_nationality.nationality_name," .
				"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email,d_indivi_info.cs_age " .
				"from a_bookinginfo,dl_nationality,bl_branchinfo,dl_continent,d_indivi_info,a_appointment,p_timer " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		
		if($nationality_id){$sql .= "and d_indivi_info.nationality_id=".$nationality_id." ";}
		if($continent_id){$sql .= "and dl_continent.continent_id=".$continent_id." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; 
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.nationality_id=dl_nationality.nationality_id ";
		$sql .= "and dl_nationality.continent_id=dl_continent.continent_id ";
		$sql .= "and a_appointment.book_id=a_bookinginfo.book_id ";
		$sql .= "group by d_indivi_info.indivi_id ";
		$sql .= "order by bl_branchinfo.branch_name,a_appointment.appt_date,p_timer.time_start,dl_nationality.nationality_name ";
		
		//echo "<br><br><br><br>$sql<br><br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get customer information from booking table in sex of customer report
 * @modified - add this function in 9 March 2009
 */	
	function getcustpersex($start_date=false,$end_date=false,$branchid=false,$branchcategoryid=false,$cityid=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcustpersex(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,bl_branchinfo.branch_name as branch_name,bl_branchinfo.branch_id,";
		$sql .= "bl_branch_category.branch_category_name,bl_branch_category.branch_category_id,";
		$sql .= "al_city.city_id,al_city.city_name,a_bookinginfo.book_id,";
		$sql .= "1 as qty," .
				"(case d_indivi_info.sex_id when 1 then 1 else 0 end) as mqty,(case d_indivi_info.sex_id when 1 then 0 else 1 end) as fqty ";
		
		$sql .= "from a_bookinginfo,bl_branchinfo,d_indivi_info,bl_branch_category,al_city ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}else
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.sex_id>0 ";
		$sql .= "group by d_indivi_info.indivi_id ";
		$sql .= "order by a_bookinginfo.b_appt_date,bl_branchinfo.branch_name ";
		
		//echo "<br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get customer information detail from booking table in gender of customer report detail
 * @modified - add this function on 4 March 2009
 */
	function getcustpersexdetail($start_date=false,$end_date=false,$branchid=false,$branchcategoryid=false,$cityid=false,$book_id=false,$sexid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("customer.getcusperagedetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_appointment.bpds_id,a_appointment.book_id," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"a_appointment.appt_date,p_timer.time_start," .
				"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email,d_indivi_info.cs_age,dl_sex.sex_type " .
				"from a_bookinginfo,bl_branchinfo,d_indivi_info,a_appointment,bl_branch_category,al_city,p_timer,dl_sex " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($branchcategoryid){$sql .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($sexid){$sql .= "and dl_sex.sex_id=".$sexid." ";}
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; 
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.branch_category_id=bl_branch_category.branch_category_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		//$sql .= "and d_indivi_info.cs_age!=0 ";
		$sql .= "and d_indivi_info.sex_id=dl_sex.sex_id ";
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
	
/*
 * summary total result set by branch,category an location
 * $fieldname - fieldname of summary value for example: customer per location report sunary "qty"
 */	
	function sumqtynation($rs,$fieldname,$nationalityid=false,$begin=false,$end=false){
		//print_r($rs);
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			if($appt_date>=$begin&&$appt_date<=$end){
				if($rs[$i]["nationality_id"]==$nationalityid){
					$sum += $rs[$i]["$fieldname"];
				}
			}
		}
		return $sum;
	}
		
/*
 * get customer general information for appointment > customer history report 
 */	
	function getapptcustinfo($phone=false,$book_id=false,$debug=0){
		if(!$phone) {
			$this->setErrorMsg("customer.getapptcustinfo(),Please insert phone number for see this report!!");
			return false;
		}
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date," .
				"d_indivi_info.cs_name,d_indivi_info.cs_age,d_indivi_info.cs_phone," .
				"dl_nationality.nationality_name,a_bookinginfo.a_member_code,dl_sex.sex_type," .
				"d_indivi_info.resident,d_indivi_info.visitor," .
				"d_indivi_info.cs_birthday,d_indivi_info.cs_email " .
				"from a_bookinginfo,dl_nationality,d_indivi_info left join dl_sex on d_indivi_info.sex_id=dl_sex.sex_id " .
				"where a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and d_indivi_info.nationality_id=dl_nationality.nationality_id ";
		if($phone){$sql .= "and lower(d_indivi_info.cs_phone) = \"$phone\" ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		
		$sql .= "order by a_bookinginfo.b_appt_date desc,a_bookinginfo.book_id ";		
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}	
		
/*
 * get customer sales receipt information for appointment > customer history report 
 */	
	function getapptcustsr($phone=false,$book_id=false,$debug=0){
		if(!$phone) {
			$this->setErrorMsg("customer.getapptcustsr(),Please insert phone number for see this report!!");
			return false;
		}
		
		//table a_bookinginfo
		$sql = "select c_bpds_link.bpds_id as bpds_id,c_salesreceipt.paid_confirm,";
		$sql .= "a_bookinginfo.b_appt_date as appt_date,bl_branchinfo.branch_name,";
		$sql .= "a_bookinginfo.book_id as book_id,";
		$sql .= "a_bookinginfo.c_set_cms as cms,";
		$sql .= "a_bookinginfo.servicescharge as servicescharge,";
		$sql .= "c_salesreceipt.pay_id as pay_id,";
		$sql .= "a_bookinginfo.b_branch_id as branch_id,";
		$sql .= "c_salesreceipt.salesreceipt_id,";
		$sql .= "c_salesreceipt.salesreceipt_number,";
		$sql .= "c_salesreceipt.sr_total,";
		$sql .= "l_paytype.pay_name as pay_name ";
		
		
		$sql .= "from a_bookinginfo,c_salesreceipt,d_indivi_info," .
				"bl_branchinfo,l_paytype,c_bpds_link ";
		$sql .= "where a_bookinginfo.book_id = c_salesreceipt.book_id ";
		$sql .= "and a_bookinginfo.book_id = c_bpds_link.tb_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and c_bpds_link.tb_name = \"a_bookinginfo\" ";
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and c_salesreceipt.pay_id=l_paytype.pay_id ";
		$sql .= "and a_bookinginfo.book_id=d_indivi_info.book_id ";
		if($phone){$sql .= "and lower(d_indivi_info.cs_phone) = \"$phone\" ";}
		$sql .= "and a_bookinginfo.b_set_cancel<>1 ";
		$sql .= "order by c_bpds_link.bpds_id,a_bookinginfo.b_branch_id,c_salesreceipt.salesreceipt_id ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
		
/*
 * get customer treatment information for appointment > customer history report 
 */	
	function getapptcusttrm($phone=false,$book_id=false,$debug=false){
		if(!$phone) {
			$this->setErrorMsg("customer.getapptcusttrm(),Please insert phone number for see this report!!");
			return false;
		}
		$sql = "select bl_branchinfo.branch_id,bl_branchinfo.branch_name," .
				"d_indivi_info.indivi_id,d_indivi_info.package_id,l_hour.hour_name," .
				"l_hour.hour_calculate,bl_room.room_name,a_bookinginfo.book_id," .
				"a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.b_qty_people," .
				"d_indivi_info.strength_id,d_indivi_info.scrub_id,d_indivi_info.wrap_id," .
				"d_indivi_info.bath_id,d_indivi_info.facial_id " .
				"from a_bookinginfo,bl_branchinfo,al_city,d_indivi_info,da_mult_th,l_hour,bl_room " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($phone){$sql .= "and lower(d_indivi_info.cs_phone) = \"$phone\" ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "order by a_bookinginfo.book_id,a_bookinginfo.b_branch_id ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
}
?>
