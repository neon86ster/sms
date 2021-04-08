<?php
/*
 * File name : therapist.inc.php
 * Description : Class file checker report for cms system
 * Author : natt
 * Create date : 26-Feb-2009
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class therapist extends report {
	
	function therapist(){}
	
	function getcity($order=false,$sort=false) {
		$sql = "select * from al_city ";
		if($order=="Employee Code"||$order=="Employee Name"){
			$sql.="order by city_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		return $this->getResult($sql);
	}
	
	function gettherapist($order=false,$sort=false,$empid=false,$branchid=false,$cityid=false,$debug=false) {
		$sql = "select l_employee.*," .
				"bl_branchinfo.city_id from l_employee,bl_branchinfo where l_employee.emp_department_id=4 ";
		$sql .= //"and l_employee.emp_id != 1 " .
				"and bl_branchinfo.branch_id=l_employee.branch_id ";			// Therapist
		
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		if($branchid){$sql .= "and l_employee.branch_id=".$branchid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($order=="Employee Code"){
			$sql.="order by l_employee.emp_code + 0 ";
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
	
	function getmassage($order=false,$sort=false,$debug=false) {
		$sql = "select db_trm.* from db_trm where trm_category_id=3 ";	// Massage
		
		if($order=="Employee Code"||$order=="Employee Name"){
			$sql.="order by trm_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by trm_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getbranch($order=false,$sort=false,$branchid=false,$cityid=false,$debug=false) {
		$sql = "select * from bl_branchinfo where branch_active=1 ";
		$sql .= "and bl_branchinfo.branch_name!='All' ";
		
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($order=="Employee Code"||$order=="Employee Name"){
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
 * get therapist total hour from a_bookinginfo
 * @modified - add this function on 26-Feb-2009
 * 			 - modified sql query on 27-Feb-2009
 */	
	function getthhourinfo($branch_id=false,$start_date=false,$end_date=false,$empid=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthpl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,sum(l_hour.hour_calculate) as total," .
				//"CONCAT(l_employee.emp_code,' ',l_employee.emp_nickname) as therapist," .
				"l_employee.emp_id,l_employee.emp_code,l_employee.emp_nickname," .//sum(l_hour.hour_calculate) as total," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name,";
		$sql .= "al_city.city_id,al_city.city_name ";
		$sql .= "from a_bookinginfo,d_indivi_info,da_mult_th,l_hour,bl_branchinfo,al_city,l_employee ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and bl_branchinfo.branch_id=".$branch_id." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and da_mult_th.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and da_mult_th.hour_id=l_hour.hour_id ";
		$sql .= "and da_mult_th.therapist_id=l_employee.emp_id ";
		$sql .= "and l_employee.emp_department_id=4 ";	
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "group by a_bookinginfo.b_appt_date,da_mult_th.therapist_id ";
		$sql .= "order by l_employee.emp_code,a_bookinginfo.b_appt_date ";
		
		//echo "<br><br><br><br><br>".$sql."<br>";die();
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
	function getthhourdetail($start_date=false,$end_date=false,$empid=false,$branchid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthhourdetail(),Please insert Date for see this report!!");
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
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and l_employee.emp_department_id=4 ";	
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "order by l_employee.emp_code,a_bookinginfo.b_appt_date,a_bookinginfo.book_id ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
			
/*
 * get therapist total massage from a_bookinginfo
 * @modified - add this function on 26-Feb-2009
 * 			 - modified sql query on 27-Feb-2009
 */	
	function getthmsginfo($branch_id=false,$start_date=false,$end_date=false,$msgid=false,$empid=false,$group=false,$order=false,$sort=false,$city_id=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthmsginfo(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,count(1) as total," . //sum(l_hour.hour_calculate) as total," .
				"l_employee.emp_id,l_employee.emp_code,l_employee.emp_nickname," .//sum(l_hour.hour_calculate) as total," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name,";
		$sql .= "al_city.city_id,al_city.city_name,da_mult_msg.massage_id,db_trm.trm_name as massage_name ";
		$sql .= "from a_bookinginfo,d_indivi_info,da_mult_th,da_mult_msg,l_hour,bl_branchinfo,al_city,l_employee,db_trm ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and bl_branchinfo.branch_id=".$branch_id." ";}
		if($city_id){$sql .= "and bl_branchinfo.city_id=".$city_id." ";}
		if($msgid){$sql .= "and da_mult_msg.massage_id=".$msgid." ";}
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and da_mult_th.book_id=a_bookinginfo.book_id ";
		$sql .= "and da_mult_msg.book_id=a_bookinginfo.book_id ";
		$sql .= "and da_mult_msg.indivi_id=d_indivi_info.indivi_id ";
		$sql .= "and da_mult_msg.book_id=da_mult_th.book_id ";
		$sql .= "and da_mult_msg.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and db_trm.trm_id=da_mult_msg.massage_id ";
		//$sql .= "and l_employee.emp_active=1 ";
		//$sql .= "and db_trm.trm_id=1 ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		if($group=="massage"){$sql .= "group by a_bookinginfo.b_appt_date,da_mult_msg.massage_id ";}
		else{$sql .= "group by a_bookinginfo.b_appt_date,da_mult_th.multh_id ";}
		//$sql .= "group by a_bookinginfo.b_appt_date,da_mult_th.multh_id ";
		if($order=="Employee Code"||$order=="Employee Name"){
			$sql.="order by db_trm.trm_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Category"){
			$sql.="order by db_trm.trm_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by da_mult_msg.massage_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}else{
			$sql .= "order by l_employee.emp_code,a_bookinginfo.b_appt_date ";
		}
		
		//echo "<br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
		
/*
 * get therapist total massage detail from a_bookinginfo
 * @modified - add this function on 27-Feb-2009
 */	
	function getthmsgdetail($start_date=false,$end_date=false,$empid=false,$msgid=false,$branchid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthmsgdetail(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name," .
				"d_indivi_info.indivi_id,l_employee.emp_id,l_employee.emp_nickname,l_employee.emp_code,d_indivi_info.package_id,l_hour.hour_name,l_hour.hour_calculate,bl_room.room_name," .
				"a_bookinginfo.book_id,a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.b_qty_people " .
				"from a_bookinginfo,bl_branchinfo,al_city,l_employee,d_indivi_info,da_mult_th,da_mult_msg,l_hour,bl_room " .
				"where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($msgid){$sql .= "and da_mult_msg.massage_id=".$msgid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and da_mult_msg.book_id=a_bookinginfo.book_id ";
		$sql .= "and da_mult_msg.indivi_id=d_indivi_info.indivi_id ";
		$sql .= "and da_mult_msg.book_id=da_mult_th.book_id ";
		$sql .= "and da_mult_msg.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "group by d_indivi_info.indivi_id, da_mult_th.multh_id "; 
		$sql .= "order by l_employee.emp_code,a_bookinginfo.book_id,bl_branchinfo.branch_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
				
/*
 * get therapist total package from a_bookinginfo
 * @modified - add this function on 28-Feb-2009
 */	
	function getthpackageinfo($branch_id=false,$start_date=false,$end_date=false,$packageid=false,$empid=false,$group=false,$order=false,$sort=false,$cityid=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthpackageinfo(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,count(1) as total," . //sum(l_hour.hour_calculate) as total," .
				"l_employee.emp_id,l_employee.emp_code,l_employee.emp_nickname," .//sum(l_hour.hour_calculate) as total," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name,";
		$sql .= "al_city.city_id,al_city.city_name,d_indivi_info.package_id,db_package.package_name ";
		$sql .= "from a_bookinginfo,d_indivi_info,da_mult_th,bl_branchinfo,al_city,l_hour,l_employee,db_package ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($branch_id){$sql .= "and bl_branchinfo.branch_id=".$branch_id." ";}
		if($packageid){$sql .= "and d_indivi_info.package_id=".$packageid." ";}
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and da_mult_th.book_id=a_bookinginfo.book_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and d_indivi_info.package_id=db_package.package_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		if($group=="package"){$sql .= "group by a_bookinginfo.b_appt_date,d_indivi_info.package_id ";}
		else{$sql .= "group by a_bookinginfo.b_appt_date,da_mult_th.multh_id ";}
		if($order=="Employee Code"||$order=="Employee Name"){
			$sql.="order by db_package.package_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Category"){
			$sql.="order by db_package.package_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by d_indivi_info.package_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}else{
			$sql .= "order by l_employee.emp_code,a_bookinginfo.b_appt_date ";
		}
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
			
/*
 * get therapist total package detail from a_bookinginfo
 * @modified - add this function on 28-Feb-2009
 */	
	function getthpackagedetail($start_date=false,$end_date=false,$empid=false,$packageid=false,$branchid=false,$cityid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthmsgdetail(),Please insert Date for see this report!!");
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
		if($packageid){$sql .= "and d_indivi_info.package_id=".$packageid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.room_id=bl_room.room_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and l_hour.hour_id=da_mult_th.hour_id ";
		$sql .= "and l_employee.emp_id=da_mult_th.therapist_id ";
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "order by l_employee.emp_code,bl_branchinfo.branch_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
				
/*
 * summary total result set by emp_id
 * $fieldname - fieldname of summary value for example: customer per location report sunary "qty"
 */	
	function sumeachempfield($rs,$fieldname,$emp_id=false,$begin=false,$end=false){
		//print_r($rs);
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			if($rs[$i]["emp_id"]==$emp_id){
				$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
				if($appt_date>=$begin&&$appt_date<=$end){
						$sum += $rs[$i]["$fieldname"];
				}
				if($appt_date>$end&&isset($rs[$i+1]["emp_id"])&&$rs[$i]["emp_id"]!=$rs[$i+1]["emp_id"]){
					break;
				}
			}
		}
		return $sum;
	}
				
/*
 * summary total result set ecplain for all data
 * $fieldname - fieldname of summary value for example: customer per location report summary "qty"
 */	
	function sumeachfield($rs,$fieldname,$emp_id=false,$msgid=false,$packageid=false,$begin=false,$end=false){
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			if($emp_id==false){$rs[$i]["emp_id"]=false;}
			if($msgid==false){$rs[$i]["massage_id"]=false;}
			if($packageid==false){$rs[$i]["package_id"]=false;}
			if($appt_date>=$begin&&$appt_date<=$end){
				if($rs[$i]["emp_id"]==$emp_id&&$rs[$i]["massage_id"]==$msgid&&$rs[$i]["package_id"]==$packageid){
					$sum += $rs[$i]["$fieldname"];
				}
			}
		}
		return $sum;
	}
	
/*
 * auto generate selectbox of Therapist
 * @param $sname - selectbox name
 * @param $selected - selected id
 * @param $chkautosubmit - check form auto submit
 * @param $order - sql query language "order by $order"
 * @param $andparam - sql query language "where emp_active=1 and emp_department_id=4 and $andparam "
 * @modified - copy from appt.inc.php this function on 27 Feb 2009
 */
	function makeTherapistlist($sname=false,$selected=false,$chkautosubmit=false,$order=false,$andparam=false) {
		$sql = "select * from l_employee left join bl_branchinfo on l_employee.branch_id=bl_branchinfo.branch_id where l_employee.emp_active=1 and l_employee.emp_department_id=4 or l_employee.emp_id=1 ";
		
		if($andparam) {
			$sql .= "and ".$andparam." ";
		}

		if($order)
			$sql .= "order by $order ";
		//echo $sql;
		$row = $this->getResult($sql);
		
		//echo $selected;
		//echo $sql;
		echo "<select id=\"".$sname."\" name=\"".$sname."\" ";
		if($chkautosubmit)
			echo " onChange=\"this.form.submit();\">";
		else
			echo ">";	
		for($i=0; $i<$row["rows"]; $i++){
				
		$b_code = $this->getIdToText($row[$i]["branch_id"],"bl_branchinfo","branch_code","branch_id");
				
		echo "<option value=".$row[$i]["emp_id"];
					
					
		if ($row[$i]["emp_id"] == $selected) {
				echo " selected=\"selected\"";
		}			
					
		echo ">";
					
		if($row[$i]["branch_id"]&&$row[$i]["emp_id"]!=1)
			echo $b_code." ".$row[$i]["emp_code"]." ".$row[$i]["emp_nickname"]."</option>";
		else if($row[$i]["emp_id"]==1)
			echo " All </option>";
		else
			echo $b_code." ".$row[$i]["emp_nickname"]."</option>";
		}
		echo "</select>";
		
	}

/*
 * function forget appointment information of all therapist in queue 
 * @modifeid - add this function on 22-Dec-2009
 */
	function getThQueue($branchid=false,$apptdate=false,$debug=false){
		if(!$apptdate) {
			$this->setErrorMsg("therapist.getThQueue(),Please insert Date for see this report!!");
			return false;
		}
		if(!$branchid) {
			$this->setErrorMsg("therapist.getThQueue(),Please insert Branch for see this report!!");
			return false;
		}
		
		$sql = "select da_mult_th.indivi_id,bl_th_list.th_list_id,bl_th_list.th_id,bl_th_list.leave,l_employee.emp_code," .
				"da_mult_th.therapist_id, da_mult_th.hour_id as therapist_hour," .
				//"a_bookinginfo.b_appt_date,a_bookinginfo.b_appt_time_id,a_bookinginfo.b_branch_id," .
				"a_bookinginfo.b_appt_date,da_mult_th.start_id as b_appt_time_id,a_bookinginfo.b_branch_id," .
				"d_indivi_info.room_id,d_indivi_info.b_set_atspa,d_indivi_info.b_set_inroom," .
				"d_indivi_info.b_set_finish,l_employee.emp_nickname " .
				",da_mult_th.start_id,da_mult_th.end_id " .
				"from bl_th_list,l_employee,da_mult_th,a_bookinginfo,d_indivi_info " .
				"where bl_th_list.th_id=l_employee.emp_id " .
				"and da_mult_th.therapist_id=bl_th_list.th_id " .
				"and da_mult_th.indivi_id=d_indivi_info.indivi_id " .
				"and a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and da_mult_th.book_id=d_indivi_info.book_id " .
				"and da_mult_th.book_id=a_bookinginfo.book_id " .
				"and a_bookinginfo.b_branch_id=$branchid " .
				"and a_bookinginfo.b_appt_date=\"".$apptdate."\" " .
				"and DATE_FORMAT(bl_th_list.l_lu_date,\"%Y%m%d\")=\"".$apptdate."\" ".
				"and bl_th_list.branch_id=$branchid " .
				"and bl_th_list.l_lu_date>=\"".$apptdate."\" " .
				"and a_bookinginfo.b_set_cancel = 0 " .
				//"order by a_bookinginfo.b_appt_time_id,da_mult_th.multh_id ";
				"order by da_mult_th.start_id,da_mult_th.multh_id ";
				//echo "<br><br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
		
/*
 * function for generate data in table timeline-room in booking view page
 * @modifeid - add this function on 22-Dec-2009
 */
	function chkThAvailable($appointment = false, $therapistid = false, $appttime=false, $endtime=false, $timeperiod=false){
		for ($i = 0; $i <$appointment["rows"]; $i++) {
			if($appointment[$i]["therapist_id"] == $therapistid &&
				$timeperiod[$appointment[$i]["b_appt_time_id"]] <= $appttime &&
				$timeperiod[$appointment[$i]["time_end"]] > $appttime 
				){
					return "<b style='color:#ff0000;'>n/a</b>";
			}
			if($appointment[$i]["therapist_id"] == $therapistid &&
				$timeperiod[$appointment[$i]["b_appt_time_id"]] < $endtime &&
				$timeperiod[$appointment[$i]["time_end"]] >= $endtime 
				){
					return "<b style='color:#ff0000;'>n/a</b>";
			}
		}
		return "<b style='color:#008000;'>Available!!</b>";
	}
	
/*
 * function for generate data in table timeline-room in booking view page
 * @modifeid - add this function on 22-Dec-2009
 */
	function chkThTime($appointment = false, $therapistid = false, $timeperiod=false){
		$appointmenttime = "";
		$appointment_chktime = array();
		for ($i = 0; $i <$appointment["rows"]; $i++) {
			if($appointment[$i]["therapist_id"] == $therapistid){
					$appointment_chktime[$i]=substr($timeperiod[$appointment[$i]["b_appt_time_id"]],0,5)."-".substr($timeperiod[$appointment[$i]["time_end"]],0,5);
					if(!isset($appointment_chktime[$i-1])){$appointment_chktime[$i-1]="";}
					if($appointment_chktime[$i]!=$appointment_chktime[$i-1]){
						if($appointmenttime!=""){$appointmenttime.=",&nbsp;&nbsp;";}
						$appointmenttime.= substr($timeperiod[$appointment[$i]["b_appt_time_id"]],0,5)."-".substr($timeperiod[$appointment[$i]["time_end"]],0,5);
					}
			}
		}
		if($appointmenttime==""){$appointmenttime="-";}
		return $appointmenttime;
	}
}
?>
