<?
/*
 * File name : csi.inc.php
 * Description : Class file csi report for cms system
 * Author : natt
 * Create date : 08-Ari-2009
 * Modified : natt@tap10.com
 */   
require_once("report.inc.php");
class csi extends report {
	
	function csi(){}
	
/*
 * get csi info for customer information report
 */
 	function getcsinfo($start_date=false,$end_date=false,$branch_id=false,$columnname=false,$cityid=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date," .
				"da_mult_th.therapist_id,l_employee.emp_code,l_employee.emp_nickname as therapist_name," .
				"max(da_mult_th.hour_id) as max_hour,f_csi.* ";
		$sql .= "from f_csi,a_bookinginfo,l_employee,da_mult_th,d_indivi_info ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and a_bookinginfo.book_id = f_csi.book_id ";
		$sql .= "and a_bookinginfo.book_id = d_indivi_info.book_id ";
		$sql .= "and a_bookinginfo.book_id = da_mult_th.book_id ";
		$sql .= "and d_indivi_info.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = d_indivi_info.book_id ";
		$sql .= "and d_indivi_info.indivi_id = da_mult_th.indivi_id ";
		$sql .= "and d_indivi_info.indivi_id = f_csi.indivi_id ";
		$sql .= "and l_employee.emp_id = da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_appt_date >=\"$startdate\" " .
				"and a_bookinginfo.b_appt_date <=\"$enddate\" ";
		//$sql .= "and a_bookinginfo.b_colormark =4 ";
		$sql .= "and l_employee.emp_id <> 1 ";

		if($columnname) {
			$sql .= "and f_csi.$columnname!=1 ";
		}
		
		if($branch_id) {
			$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";
		}
		if($cityid){$sql .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$cityid) ";}
				
		$sql .= "group by f_csi.indivi_id ";	// only for 1 st therapist'll used to count in csi msg. value
		$sql .= "order by l_employee.emp_nickname,a_bookinginfo.b_appt_date ";
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
 	
/*
 * get csi header value for customer information report
 */
	function getcsivalue($debug=false) 
	{
		$sql = "select * from fl_csi_value order by csiv_id ";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get csi index for customer information report
 */
	function getcsiindex($debug=false) 
	{
		$sql = "select * from fl_csi_index where csii_active=1 order by csii_priority desc";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get csi info for customer comments report
 */
	function getComment($start_date=false,$end_date=false,$branch_id=false,$cityid=false,$debug=false) {
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
				
		
		$sql = "select c_bpds_link.bpds_id,a_bookinginfo.book_id,bl_branchinfo.branch_name,a_bookinginfo.b_appt_date as appt_date,f_csi.* from ";
			
		$sql .= "f_csi,a_bookinginfo,bl_branchinfo,c_bpds_link ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and f_csi.book_id=a_bookinginfo.book_id ";
		$sql .= "and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id ";
		$sql .= "and a_bookinginfo.b_appt_date >=\"$startdate\" " .
				"and a_bookinginfo.b_appt_date <=\"$enddate\" ";
		
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id =".$branch_id." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id =".$cityid." ";}
		
		$sql .= "and c_bpds_link.tb_id=a_bookinginfo.book_id ";
		$sql .= "and c_bpds_link.tb_name=\"a_bookinginfo\" ";
		$sql .= "and f_csi.csi_comment <> '' and f_csi.csi_comment <> 'n/a' ";		// filter comment: '' and 'n/a'
		
		$sql .= "order by a_bookinginfo.b_appt_date desc,a_bookinginfo.book_id desc";
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get branch name from bl_branchinfo table for Recommendation Report, Therapist Detail Report
 */
	function getbranch($order=false,$sort=false,$branchid=false,$branchcategoryid=false,$cityid=false,$debug=false) {
		$sql = "select * from bl_branchinfo where branch_active=1 ";
		$sql .= "and bl_branchinfo.branch_name!='All' ";
		
		if($branchid){$sql .= "and bl_branchinfo.branch_id=".$branchid." ";}
		if($branchcategoryid){$sql .= "and bl_branchinfo.branch_category_id=".$branchcategoryid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($order==="Category" || $order==="Employee Code" || $order==="Employee Name" ){
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
 * function get recommendation rows header from fl_csi_recommend
 */
	function getrec($order=false,$sort=false,$debug=false){
		$sql = "select * from fl_csi_recommend ";
		
		if($order==="Category"){
			$sql.="order by rec_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by rec_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * get therapist name from l_employee table for Therapist Detail Report
 */
	function gettherapist($order=false,$sort=false,$branchid=false,$empid=false,$debug=false) {
		$sql = "select * from l_employee where emp_active=1 ";
		$sql .= "and l_employee.emp_department_id=4 ";
		
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		if($branchid){$sql .= "and l_employee.branch_id=".$branchid." ";}
		if($order==="Category" || $order==="Employee Code"){
			$sql.="order by emp_code ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Employee Name" ){
			$sql.="order by emp_nickname ";
			if($sort=="A > Z"){$sql.="desc";}
		}else if($order==="Default"){
			$sql.="order by emp_id ";
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
 * function get recommendation total for Recommendation Report
 */
	function getrecreport($start_date=false,$end_date=false,$branchid=false,$recid=false,$cityid=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select c_bpds_link.bpds_id,a_bookinginfo.book_id," .
				"d_indivi_info.indivi_id,fl_csi_recommend.rec_id,fl_csi_recommend.rec_name,count(f_csi.rec_id) as total," .
				"a_bookinginfo.b_appt_date,p_timer.time_start," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name,d_indivi_info.cs_name " .
				"from f_csi,fl_csi_recommend,bl_branchinfo,a_bookinginfo,p_timer,d_indivi_info,c_bpds_link " .
				"where a_bookinginfo.b_set_cancel=0 " .
				"and a_bookinginfo.book_id=f_csi.book_id " .
				"and a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and f_csi.indivi_id=d_indivi_info.indivi_id " .
				"and fl_csi_recommend.rec_id=f_csi.rec_id " .
				"and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id " .
				"and (d_indivi_info.b_set_inroom!=0 or d_indivi_info.b_set_finish!=0) ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($recid){$sql .= "and f_csi.rec_id=".$recid." ";}
		if($branchid){$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		$sql .= "and c_bpds_link.tb_id=a_bookinginfo.book_id ";
		$sql .= "and c_bpds_link.tb_name=\"a_bookinginfo\" ";
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		
		$sql .= "group by a_bookinginfo.b_appt_date,a_bookinginfo.book_id,d_indivi_info.indivi_id ";
		
		//$sql .= "group by a_bookinginfo.b_appt_date,a_bookinginfo.book_id,d_indivi_info.cs_name ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * summary total result set by branch or recommendation id
 * $fieldname - fieldname of summary value for example: recommendation report summary "total"
 */	
	function sumeachfield($rs,$fieldname,$branchid=false,$recid=false,$begin=false,$end=false){
		//print_r($rs);
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			if(!$recid){$rs[$i]["rec_id"]=0;}			// case show branch total
			if(!$branchid){$rs[$i]["branch_id"]=0;}			// case show rec total
			if($appt_date>=$begin&&$appt_date<=$end){
				if($rs[$i]["branch_id"]==$branchid&&$rs[$i]["rec_id"]==$recid){
					$sum += $rs[$i]["$fieldname"];
				}
			}
		}
		return $sum;
	}
		
/*
 * get get therapist-massage csi total for Therapist Massage Customer Popularity Report
 */
 	function getthcsi($start_date=false,$end_date=false,$branch_id=false,$empcode=false,$cityid=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date," .
				"da_mult_th.therapist_id,l_employee.emp_code,l_employee.emp_nickname as therapist_name," .
				"da_mult_th.hour_id as max_hour,f_csi.* ";
		$sql .= "from f_csi,a_bookinginfo,l_employee,da_mult_th,d_indivi_info ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and a_bookinginfo.book_id = f_csi.book_id ";
		$sql .= "and a_bookinginfo.book_id = d_indivi_info.book_id ";
		$sql .= "and a_bookinginfo.book_id = da_mult_th.book_id ";
		$sql .= "and d_indivi_info.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = d_indivi_info.book_id ";
		$sql .= "and d_indivi_info.indivi_id = da_mult_th.indivi_id ";
		$sql .= "and d_indivi_info.indivi_id = f_csi.indivi_id ";
		$sql .= "and l_employee.emp_id = da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_appt_date >=\"$startdate\" " .
				"and a_bookinginfo.b_appt_date <=\"$enddate\" ";
		//$sql .= "and a_bookinginfo.b_colormark =4 ";
		$sql .= "and l_employee.emp_id <> 1 ";

		//$sql .= "and f_csi.q_mg!=1 ";	cut off no reccommand
		
		if($branch_id) {
			$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";
		}
		if($cityid){$sql .= "and a_bookinginfo.b_branch_id in (select branch_id from bl_branchinfo where city_id=$cityid) ";}
				
		$sql .= "order by l_employee.emp_nickname,a_bookinginfo.b_appt_date ";
		//echo "<br>"."<br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * function get therapist-massage csi total for Therapist CSI Detail Report
 */	
	function getthmsgcsi($start_date=false,$end_date=false,$branchid=false,$empcode=false,$cityid=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
				
		$sql = "select a_bookinginfo.b_appt_date,a_bookinginfo.book_id," .
				"da_mult_th.therapist_id as emp_id,l_employee.emp_code,l_employee.emp_nickname as therapist_name," .
				"da_mult_th.hour_id as max_hour,";
		$sql .= "case f_csi.q_mg when 5 then fl_csi_value.csiv_value when 4 then fl_csi_value.csiv_value when 3 then fl_csi_value.csiv_value else 0 end as totalcsi,";
		$sql .= "case f_csi.q_mg when 1 then 0 else 1 end as total,";
		$sql .= "bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name ";
		$sql .= "from f_csi,a_bookinginfo,l_employee,da_mult_th,d_indivi_info,bl_branchinfo,fl_csi_value ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		$sql .= "and a_bookinginfo.book_id = f_csi.book_id ";
		$sql .= "and fl_csi_value.csiv_id = f_csi.q_mg ";
		$sql .= "and a_bookinginfo.book_id = d_indivi_info.book_id ";
		$sql .= "and a_bookinginfo.book_id = da_mult_th.book_id ";
		$sql .= "and d_indivi_info.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = da_mult_th.book_id ";
		$sql .= "and f_csi.book_id = d_indivi_info.book_id ";
		$sql .= "and d_indivi_info.indivi_id = da_mult_th.indivi_id ";
		$sql .= "and d_indivi_info.indivi_id = f_csi.indivi_id ";
		$sql .= "and l_employee.emp_id = da_mult_th.therapist_id ";
		$sql .= "and a_bookinginfo.b_appt_date >=\"$startdate\" " .
				"and a_bookinginfo.b_appt_date <=\"$enddate\" ";
		//$sql .= "and a_bookinginfo.b_colormark =4 ";
		$sql .= "and l_employee.emp_id <> 1 ";
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and f_csi.q_mg > 1 ";		// remove no recommended
		
		if($empcode) {
			$sql .= "and l_employee.emp_code=".$empcode." ";
		}
		
		if($branchid) {
			$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";
		}
		
		if($cityid){
			$sql .= "and bl_branchinfo.city_id=".$cityid." ";
		}
				
		//$sql .= "group by f_csi.indivi_id ";	// only for 1 st therapist'll used to count in csi msg. value
		$sql .= "order by l_employee.emp_nickname,l_employee.emp_code,a_bookinginfo.b_appt_date ";
		//echo $sql."<br><br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * function get therapist-massage csi total for Therapist CSI Report Detail
 */
	function getthcsidetail($start_date=false,$end_date=false,$branchid=false,$empid=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select c_bpds_link.bpds_id,a_bookinginfo.book_id,d_indivi_info.indivi_id," .
				"case f_csi.q_mg when 5 then fl_csi_value.csiv_value when 4 then fl_csi_value.csiv_value when 3 then fl_csi_value.csiv_value else 0 end as totalcsi,".
				"fl_csi_value.csiv_name,f_csi.csi_id,a_bookinginfo.b_appt_date," .
				"l_employee.emp_id,l_employee.emp_code,l_employee.emp_nickname as therapist_name," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name,da_mult_msg.multmsg_id as massage_id,db_trm.trm_name " .
				"from f_csi,fl_csi_value,db_trm,l_employee,da_mult_msg,da_mult_th,bl_branchinfo,a_bookinginfo,d_indivi_info,c_bpds_link " .
				"where a_bookinginfo.b_set_cancel=0 " .
				"and a_bookinginfo.book_id=f_csi.book_id " .
				"and a_bookinginfo.book_id=da_mult_th.book_id " .
				"and a_bookinginfo.book_id=da_mult_msg.book_id " .
				"and a_bookinginfo.book_id=d_indivi_info.book_id " .
				"and da_mult_th.book_id=da_mult_msg.book_id " .
				"and da_mult_th.therapist_id=l_employee.emp_id " .
				"and d_indivi_info.indivi_id=f_csi.indivi_id " .
				"and d_indivi_info.indivi_id=da_mult_msg.indivi_id " .
				"and d_indivi_info.indivi_id=da_mult_th.indivi_id " .
				"and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id " .
				"and f_csi.q_mg=fl_csi_value.csiv_id " .
				"and db_trm.trm_id=da_mult_msg.massage_id " .
				"and l_employee.emp_department_id=4 " .
				"and (d_indivi_info.b_set_inroom!=0 or d_indivi_info.b_set_finish!=0) ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($empid){$sql .= "and l_employee.emp_id=".$empid." ";}
		if($branchid){
			$sql .= "and l_employee.branch_id=".$branchid." ";
			//$sql .= "and a_bookinginfo.b_branch_id=".$branchid." ";
		}
		$sql .= "and c_bpds_link.tb_id=a_bookinginfo.book_id ";
		$sql .= "and c_bpds_link.tb_name=\"a_bookinginfo\" ";
		$sql .= "and f_csi.q_mg>1 ";	// no recommended
		
		$sql .= "group by l_employee.emp_code,a_bookinginfo.book_id ";
		$sql .= "order by l_employee.emp_nickname,a_bookinginfo.b_appt_date,a_bookinginfo.book_id ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * summary total result set by employee id
 * $fieldname - fieldname of summary value for example: Therapist Detail report summary "total"
 */	
	function sumeachempfield($rs,$fieldname,$empid=false,$begin=false,$end=false){
		$sum = 0;
		for($i=0;$i<$rs["rows"];$i++){
			$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
			if($appt_date>=$begin&&$appt_date<=$end){
				if($rs[$i]["emp_id"]==$empid){
					$sum += $rs[$i]["$fieldname"];
				}
			}
		}
		return $sum;
	}
	
		
/*
 * function get each therapist-customer total for Therapist CSI Rating Graph

	function getthcstotal($start_date=false,$end_date=false,$branch_id=false,$empcode=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("therapist.getthpl(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select a_bookinginfo.b_appt_date,count(a_bookinginfo.book_id) as total," .
				"l_employee.emp_id,l_employee.emp_code,l_employee.emp_nickname," .
				"bl_branchinfo.branch_id,bl_branchinfo.branch_name as branch_name,";
		$sql .= "al_city.city_id,al_city.city_name ";
		$sql .= "from a_bookinginfo,d_indivi_info,da_mult_th,l_hour,bl_branchinfo,al_city,l_employee ";
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and bl_branchinfo.branch_id=".$branch_id." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($empcode){$sql .= "and l_employee.emp_code=".$empcode." ";}
		$sql .= "and d_indivi_info.book_id=a_bookinginfo.book_id ";
		$sql .= "and da_mult_th.book_id=a_bookinginfo.book_id ";
		$sql .= "and d_indivi_info.indivi_id=da_mult_th.indivi_id ";
		$sql .= "and da_mult_th.hour_id=l_hour.hour_id ";
		$sql .= "and da_mult_th.therapist_id=l_employee.emp_id ";
		$sql .= "and l_employee.emp_department_id=4 ";	
		$sql .= "and l_employee.branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and (d_indivi_info.b_set_finish=1 or d_indivi_info.b_set_inroom=1) "; // already done or in room
		$sql .= "group by l_employee.emp_id ";
		$sql .= "order by a_bookinginfo.b_appt_date,l_employee.emp_code ";
		
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
 */	
}
?>
