<?php
/*
 * File name : account.inc.php
 * Description : Class file accountting report for cms system
 * Author : natt
 * Create date : 16-Feb-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class transport extends report {
	function gettrans($branch_id=false,$city_id=false,$start_date=false,$end_date=false,$book_id=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql="select a_bookinginfo.book_id as book_id,bl_branchinfo.branch_name," .
				"ab_transfer.driver_pu_id,ab_transfer.pu_time,ab_transfer.pu_place," .
				"ab_transfer.driver_tb_id,ab_transfer.tb_time,ab_transfer.tb_place," .
				"al_accomodations.acc_name,a_bookinginfo.b_hotel_room as b_hotel_room," .
				"l_employee.emp_nickname as rsvn_name,a_bookinginfo.c_bp_person,".
				"a_bookinginfo.c_bp_phone " .
				"from ab_transfer,a_bookinginfo,bl_branchinfo,al_accomodations,l_employee,al_city " .
				"where ab_transfer.book_id=a_bookinginfo.book_id ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($city_id){$sql .= "and bl_branchinfo.city_id=".$city_id." ";}
		if($book_id){$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
				"and a_bookinginfo.b_reservation_id=l_employee.emp_id " .
				"and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id " .
				"and ab_transfer.book_id=a_bookinginfo.book_id " .
				"and a_bookinginfo.b_set_cancel = '0' " .
				"order by al_city.city_name,ab_transfer.pu_time,ab_transfer.tb_time,a_bookinginfo.book_id asc";	
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
}
?>
