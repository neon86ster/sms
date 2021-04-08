<?php
/*
 * File name : checker.inc.php
 * Description : Class file checker report for cms system
 * Author : natt
 * Create date : 16-Jan-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class commission extends report {
	
	function commission(){}
	
/*
 * get commission from booking table in commission report
 * @modified - add this function in 21 Feb 2009
 */		
	function getcms($branch_id=false,$start_date=false,$end_date=false,$havecms=false,$collapse=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("commission.getcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		$sql = "select a_bookinginfo.book_id, a_bookinginfo.b_appt_date as appt_date," .
				"a_bookinginfo.c_bp_person as cms_name,a_bookinginfo.c_bp_phone as cms_phone," .
				" a_bookinginfo.c_bp_id as cms_company," .
				"a_appointment.bpds_id,al_bookparty.bp_name as cms_company_name";
		if($collapse=="Expand"){$sql .=",sum(a_bookinginfo.b_qty_people) as qty_pp,count(a_bookinginfo.book_id) as cntbook ";}
		else{$sql .=",a_bookinginfo.b_qty_people as qty_pp "; }
		
		////////for Expand
		if($collapse=="Expand"){
			$sql .= " from a_bookinginfo,a_appointment,al_bookparty ";
		}else{
			$sql .= " from a_bookinginfo,a_appointment,al_bookparty,c_salesreceipt ";
		}
		////////
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";	
		$sql .= "and a_bookinginfo.book_id=a_appointment.book_id ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		
		if($collapse=="Expand"){
			$sql .= "and a_bookinginfo.book_id not in (select a_bookinginfo.book_id " .
					"from a_bookinginfo,c_salesreceipt where a_bookinginfo.book_id=c_salesreceipt.book_id " .
					"and (c_salesreceipt.pay_id=13 or c_salesreceipt.sr_total=0)" .
					"and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' " .
					")";
		}else{
		///////////for Expand
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id ";
		$sql .= "and c_salesreceipt.pay_id!=13 ";		//specific paytype - voucher
		$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		///////////
		}
		
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
		if($book_id) {$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($collapse=="Expand"){$sql .= "group by a_bookinginfo.c_bp_id ";}
		else{$sql .= "group by a_bookinginfo.book_id ";}
		$sql .= "order by a_appointment.bp_name,a_bookinginfo.book_id ";

		//echo "<br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * get commission from booking table in Booking Company Commission report
 * @modified - add this function in 21 Feb 2009
 */		
	function getbccms($branch_id=false,$start_date=false,$end_date=false,$havecms=false,$collapse=false,$book_id=false,$anotherpara=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("commission.getbccms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql = "select bl_branchinfo.branch_name, a_bookinginfo.b_customer_name as customer_name, a_bookinginfo.b_appt_date as appt_date," .
				"p_timer.time_start,a_bookinginfo.b_hotel_room as hotel_room,al_bookparty.bp_name," .
				"a_bookinginfo.c_bp_person as cms_name,a_bookinginfo.c_bp_phone as cms_phone," .
				" a_bookinginfo.c_bp_id as cms_company,a_bookinginfo.book_id," .
				"a_appointment.bpds_id,a_appointment.bp_name as cms_company_name";
		if($collapse=="Expand"){$sql .=",sum(a_bookinginfo.b_qty_people) as qty_pp,count(a_bookinginfo.book_id) as cntbook ";}
		else{$sql .=",a_bookinginfo.b_qty_people as qty_pp,al_accomodations.acc_id,al_accomodations.acc_name "; }
		
		
		////////for Expand
		if($collapse=="Expand"){
			$sql .= " from a_bookinginfo,a_appointment,bl_branchinfo,p_timer,al_bookparty,al_accomodations ";
		}else{
			$sql .= " from a_bookinginfo,a_appointment,bl_branchinfo,p_timer,al_bookparty,al_accomodations,c_salesreceipt ";
		}
		////////
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";	
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and a_bookinginfo.book_id=a_appointment.book_id ";
		$sql .= "and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id ";
		$sql .= "and p_timer.time_id=a_bookinginfo.b_appt_time_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		
		
		if($collapse=="Expand"){
			$sql .= "and a_bookinginfo.book_id not in (select a_bookinginfo.book_id " .
					"from a_bookinginfo,c_salesreceipt where a_bookinginfo.book_id=c_salesreceipt.book_id " .
					"and (c_salesreceipt.pay_id=13 or c_salesreceipt.sr_total=0)" .
					"and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' " .
					")";
		}else{
		///////////for Expand
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id ";
		$sql .= "and c_salesreceipt.pay_id!=13 ";		//specific paytype - voucher
		$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		///////////
		}
		
		if($anotherpara){$sql .= " $anotherpara";}
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
		if($book_id) {$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($collapse=="Expand"){$sql .= "group by a_bookinginfo.c_bp_phone ";}
		else{$sql .= "group by a_bookinginfo.book_id ";}
		$sql .= "order by a_bookinginfo.c_bp_phone,bl_branchinfo.branch_name,a_bookinginfo.b_appt_date ";

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
/*
 * generate commission hotel select box for Hotel Commission report
 * @modified - add this function in 17 March 2009
 */		
	function makehotelcmslistbox($selected=false,$debug=false){
		$sql1="select acc_id,acc_name,\"al_accomodations\" as tablename from al_accomodations where cmspercent>0 and acc_active=1 ";
		$sql2="select bp_id as acc_id,bp_name as acc_name,\"al_bookparty\" as tablename from al_bookparty where bp_cmspercent>0 and bp_active=1 ";
		$sql = "($sql1) union ($sql2) order by acc_name";
		
		//echo $sql."<br>";
		$rs = $this->getResult($sql);
		$count = $rs["rows"];
		
		echo "<select id=\"hotelid\" name=\"hotelid\">";
		echo "<option title=\"---select---\" value=\"\">---select---</option>";
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
	
	
/*
 * get hotel commission from booking table in hotel commission report
 * @modified - add this function in 17 March 2009
 */		
	function gethotelcms($acc_id=false,$table="al_accomodations",$start_date=false,$end_date=false,$branch=false,$city=false,$book_id=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("commission.gethotelcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		$sql = "select a_bookinginfo.b_appt_date as appt_date,a_bookinginfo.book_id as book_id,a_bookinginfo.b_branch_id as branch_id,";
		$sql .= "bl_branchinfo.branch_name,p_timer.time_start as appt_time,";
		$sql .= "a_bookinginfo.b_customer_name as cs_name,a_bookinginfo.b_hotel_room as hotel_room,";
		$sql .= "a_bookinginfo.b_qty_people as qty_pp,a_bookinginfo.c_bp_person as cms_name,";
		$sql .= "a_bookinginfo.c_bp_id as cms_company,a_bookinginfo.c_bp_phone as cms_phone,a_bookinginfo.c_set_cms, ";
		if($table=="al_accomodations"){$sql .= "al_accomodations.cmspercent, ";}
		if($table=="al_bookparty"){$sql .= "al_bookparty.bp_cmspercent as cmspercent, ";}
		
		$sql .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total ,";
		if($table=="al_accomodations"){
			$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_accomodations.cmspercent/100) as cms ";
		}
		if($table=="al_bookparty"){
			$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_bookparty.bp_cmspercent/100) as cms ";
		}
		
		$sql .= "from a_bookinginfo,c_srdetail,c_salesreceipt," .
				"cl_product,cl_product_category,al_bookparty,al_percent_cms,bl_branchinfo,p_timer,".
				"al_accomodations ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($table=="al_accomodations"){$sql .= "and a_bookinginfo.b_accomodations_id=".$acc_id." ";}
		if($table=="al_bookparty"){$sql .= "and a_bookinginfo.c_bp_id=".$acc_id." ";}
		if($branch){$sql .= "and a_bookinginfo.b_branch_id=".$branch." ";}
		if($city){$sql .= "and bl_branchinfo.city_id=".$city." ";}
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
				"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ".
				"and a_bookinginfo.book_id=c_srdetail.book_id ";
//		$sql .= "and a_bookinginfo.c_set_cms=1 ";		
//		$sql .= "and a_bookinginfo.b_branch_id=1 ";
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		$sql .= "and cl_product_category.set_commission=1 ";
		$sql .= "and c_salesreceipt.pay_id!=3 ";
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		$sql .= "and a_bookinginfo.c_pcms_id=al_percent_cms.pcms_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		$sql .= "and a_bookinginfo.b_appt_time_id=p_timer.time_id ";
		
		$sql .= "and c_salesreceipt.pay_id!=13 ";		//specific paytype - voucher
		$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		
		if($book_id) {
			$sql .= "and a_bookinginfo.book_id=".$book_id." ";
		}
		$sql .= "group by a_bookinginfo.book_id ";
		$sql .= "order by a_bookinginfo.b_appt_date,a_bookinginfo.book_id ";

		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql,$debug);
	}
	
}
?>
