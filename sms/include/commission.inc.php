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
	function getcms($branch_id=false,$start_date=false,$end_date=false,$havecms=false,$collapse=false,$book_id=false,$cityid=false,$anotherpara=false,$order=false,$sort=false,$category=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("commission.getcms(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		$sql_sr="select sum(c_salesreceipt.sr_total) as sr_total " .
				"from c_salesreceipt,a_bookinginfo,bl_branchinfo,al_city " .
				"where c_salesreceipt.book_id=a_bookinginfo.book_id and a_bookinginfo.c_bp_id=cms_company ";
		$sql_sr .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql_sr .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql_sr .= "and bl_branchinfo.city_id=al_city.city_id ";
		
		if($end_date==false||$start_date==$end_date){$sql_sr .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql_sr .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($cityid){$sql_sr .= "and bl_branchinfo.city_id=".$cityid." ";}else
		if($branch_id){$sql_sr .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($havecms){$sql_sr .= "and a_bookinginfo.c_set_cms=1 ";}
		
		//echo $sql_sr."<br>";
		
		$sql = "select a_bookinginfo.book_id,a_bookinginfo.b_branch_id, a_bookinginfo.c_set_cms, a_bookinginfo.b_appt_date as appt_date," .
				"a_bookinginfo.c_bp_person as cms_name,a_bookinginfo.c_bp_phone as cms_phone," .
				" a_bookinginfo.b_accomodations_id," .
				" a_bookinginfo.c_bp_id as cms_company," .
				"a_appointment.bpds_id,al_bookparty.bp_name as cms_company_name";
		if($collapse=="Expand"){
			$sql .=",sum(a_bookinginfo.b_qty_people) as qty_pp" .
					",count(a_bookinginfo.book_id) as cntbook " .
					",($sql_sr) as sr_total " .
					",(($sql_sr)/sum(a_bookinginfo.b_qty_people)) as avg_sPerc ";
			$sql .= " from a_bookinginfo,a_appointment,al_bookparty,bl_branchinfo,al_city ";
		}
		else{$sql .=",a_bookinginfo.b_qty_people as qty_pp "; 
			$sql .= ",a_bookinginfo.c_cms_value as c_cms_value, ";
			$sql .= "sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end) as total ,";
			$sql .= "(sum(case cl_product_category.pos_neg_value " .
				"when 0 then -(c_srdetail.unit_price*c_srdetail.qty) " .
				"else (c_srdetail.unit_price*c_srdetail.qty) end))*(al_percent_cms.pcms_percent/100) as cms ";
			$sql .= "from a_bookinginfo,a_appointment,c_srdetail,c_salesreceipt," .
				"cl_product,cl_product_category,al_bookparty,al_percent_cms,bl_branchinfo,".
				"al_city ";
		}
		
		
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";	
		$sql .= "and a_bookinginfo.book_id=a_appointment.book_id ";
		$sql .= "and a_bookinginfo.c_bp_id=al_bookparty.bp_id ";
		$sql .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql .= "and bl_branchinfo.city_id=al_city.city_id ";
		
		if($collapse!="Expand"){
		$sql .= "and a_bookinginfo.book_id=c_salesreceipt.book_id " .
				"and c_salesreceipt.salesreceipt_id=c_srdetail.salesreceipt_id ".
				"and a_bookinginfo.book_id=c_srdetail.book_id ";	
		$sql .= "and c_srdetail.pd_id=cl_product.pd_id ";
		//$sql .= "and cl_product_category.set_commission=1 ";
		//$sql .= "and c_salesreceipt.pay_id!=13 ";		//specific paytype - voucher
		//$sql .= "and c_salesreceipt.sr_total<>0 ";	//sr_total not 0
		$sql .= "and c_salesreceipt.paid_confirm=1 ";
		$sql .= "and cl_product.pd_category_id=cl_product_category.pd_category_id ";
		$sql .= "and a_bookinginfo.c_pcms_id=al_percent_cms.pcms_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		}
		
		if($anotherpara){$sql .= " $anotherpara";}
		
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}else
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
		if($book_id) {$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($collapse=="Expand"){$sql .= "group by a_bookinginfo.c_bp_id ";}
		else{$sql .= "group by a_bookinginfo.book_id ";}
		
	 if($category=="Category"){
	 	$sql .= "order by (select al_bookparty_category.bp_category_name " .
	 			"from al_bookparty_category,al_bookparty where al_bookparty.bp_id=cms_company " .
	 			"and al_bookparty.bp_category_id=al_bookparty_category.bp_category_id ), ";
	 }else{
	 	$sql .= "order by ";
	 }
	 
	 if($order){
		if($order=="Total Bookings" && $collapse=="Expand"){
		$sql .= "cntbook ";
		if($sort=="A > Z"){$sql.="desc";}
		}else if($order=="Alphabet" && $collapse=="Expand"){
		$sql .= "cms_company_name ";
	    if($sort=="A > Z"){$sql.="desc";}
		}else if($order=="Total Customers" && $collapse=="Expand"){
		$sql .= "qty_pp ";
	    if($sort=="A > Z"){$sql.="desc";}
	    }else if($order=="Total Sales" && $collapse=="Expand"){
		$sql .= "sr_total ";	
		if($sort=="A > Z"){$sql.="desc";}
		}else if($order="Avg Total Sales Per Cust" && $collapse=="Expand"){
		$sql .= "avg_sPerc ";	
		if($sort=="A > Z"){$sql.="desc";}
		}else{
		$sql .= "a_appointment.bp_name ";
		if($sort=="A > Z"){$sql.="desc ";}
		$sql .=",a_bookinginfo.b_appt_date desc,a_bookinginfo.book_id ";
		}
	 }else{
	 	$sql .= "a_appointment.bp_name,a_bookinginfo.b_appt_date desc,a_bookinginfo.book_id ";
	 }
		//echo $sql."<br>";
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
	function getbccms($branch_id=false,$start_date=false,$end_date=false,$havecms=false,$collapse=false,$book_id=false,$anotherpara=false,$cityid=false,$order=false,$sort=false,$debug=false){
		
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
		if($collapse=="Expand"){$sql .=",sum(a_bookinginfo.b_qty_people) as qty_pp,count(a_bookinginfo.book_id) as cntbook ";
				/*$sql .=",(select sum(c_salesreceipt.sr_total) " .
						"from c_salesreceipt,a_bookinginfo " .
						"where c_salesreceipt.book_id=a_bookinginfo.book_id " .
						"and a_bookinginfo.c_bp_phone=cms_phone ";
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
				$sql .=	") as total ";*/
				$sql .=",(select sum(c_salesreceipt.sr_total) " .
						"from c_salesreceipt,a_bookinginfo,bl_branchinfo " .
						"where c_salesreceipt.book_id=a_bookinginfo.book_id " .
						"and a_bookinginfo.c_bp_phone=cms_phone ";
						$sql .= "and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id ";
						if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
						if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
						if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
				$sql .=	") as total ";
		}
		else{$sql .=",a_bookinginfo.b_qty_people as qty_pp,al_accomodations.acc_id,al_accomodations.acc_name "; }
		
		$sql .= " from a_bookinginfo,a_appointment,bl_branchinfo,p_timer,al_bookparty,al_accomodations ";
		
		$sql .= "where a_bookinginfo.b_set_cancel=0 ";	
		$sql .= "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		$sql .= "and a_bookinginfo.book_id=a_appointment.book_id ";
		$sql .= "and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id ";
		$sql .= "and p_timer.time_id=a_bookinginfo.b_appt_time_id ";
		$sql .= "and al_bookparty.bp_id=a_bookinginfo.c_bp_id ";
		
		if($anotherpara){$sql .= " $anotherpara";}
		if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($cityid){$sql .= "and bl_branchinfo.city_id=".$cityid." ";}
		if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
		if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
		if($book_id) {$sql .= "and a_bookinginfo.book_id=".$book_id." ";}
		if($collapse=="Expand"){$sql .= "group by a_bookinginfo.c_bp_phone ";}
		else{$sql .= "group by a_bookinginfo.book_id ";}
		

	 if($order){
		if($order=="Total Bookings" && $collapse=="Expand"){
		$sql .= "order by cntbook ";
		if($sort=="A > Z"){$sql.="desc";}
		}else if($order=="Total Customers" && $collapse=="Expand"){
		$sql .= "order by qty_pp ";
	    if($sort=="A > Z"){$sql.="desc";}
	    }else if($order=="DDC" && $collapse=="Expand"){
		$sql .= "order by (select bankacc_cms_id from al_bankacc_cms where c_bp_phone=cms_phone and bankacc_active=1) ";	
		if($sort=="A > Z"){$sql.="desc";}
		}else if($order=="Total Sales" && $collapse=="Expand"){
		$sql .= "order by total ";
	    if($sort=="A > Z"){$sql.="desc";}
	    }else{
		$sql .= "order by a_bookinginfo.c_bp_phone ";
		if($sort=="A > Z"){$sql.="desc ";}
		$sql .=",bl_branchinfo.branch_name,a_bookinginfo.b_appt_date ";
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
		
		echo "<select id=\"hotelid\" name=\"hotelid\" class=\"ctrDropDown\" onBlur=this.className='ctrDropDown'; onMouseDown=this.className='ctrDropDownClick'; onChange=this.className='ctrDropDown';>";
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
	
	/*
 * get commission from booking table in resevation center report
 * @modified - add this function in 22 March 2011
 */		
	function getResAgent($branch_id=false,$start_date=false,$end_date=false,$havecms=false,$city_id=false,$collapse=false,$anotherpara=false,$order=false,$sort=false,$category=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("agent.getResAgent(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
	
		$sql="select ";
		
	 if($collapse=="Expand"){		
		$sql .= "count(a_bookinginfo.book_id) as total_book";
	 }else{
	 	$sql .= "a_bookinginfo.book_id,a_bookinginfo.c_set_cms,a_bookinginfo.b_qty_people as qty_pp," .
	 			"a_bookinginfo.c_bp_person as cms_name,a_bookinginfo.b_appt_date as appt_date," .
	 			"a_bookinginfo.b_branch_id,a_bookinginfo.c_bp_id";
	 }
	 
		$sql .=	",a_bookinginfo.b_reservation_id," .
				"l_employee.emp_code," .
				"l_employee.emp_nickname, " .
				"l_employee.emp_department_id as dep_id " .
				"from a_bookinginfo,l_employee,bl_branchinfo,al_city " .
				"where a_bookinginfo.b_reservation_id=l_employee.emp_id " .
				"and a_bookinginfo.b_reservation_id>1 " .
				"and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
				"and bl_branchinfo.city_id=al_city.city_id " .
				"and a_bookinginfo.b_set_cancel<>1 ";
	 
	 if($end_date==false||$start_date==$end_date){$sql .= "and a_bookinginfo.b_appt_date='".$startdate."' ";}
		else{$sql .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
	 if($city_id){$sql .= "and bl_branchinfo.city_id=".$city_id." ";}else
	 if($branch_id){$sql .= "and a_bookinginfo.b_branch_id=".$branch_id." ";}
	 if($havecms){$sql .= "and a_bookinginfo.c_set_cms=1 ";}
	 
	 if($anotherpara){$sql .= " $anotherpara";}
	 
	 if($collapse=="Expand"){
		$sql .=	"group by l_employee.emp_code ";
	 }
	 
	 if($category=="Department"){
	 	$sql .= "order by dep_id,";
	 }else{
	 	$sql .= "order by ";
	 }
	 
	 if($order){
		if($order=="Total Bookings" && $collapse=="Expand"){
		$sql .= "total_book ";
		if($sort=="A > Z"){$sql.="desc";}
		}else if($collapse=="Expand"){
		$sql .= "l_employee.emp_nickname ";
		if($sort=="A > Z"){$sql.="desc ";}
		}else{
			$sql .= "l_employee.emp_nickname,b_branch_id,appt_date,book_id ";
		}
	 }
		//echo "<br><br><br><br><br><br><br><br><br><br><br>".$sql."<br>";
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
	}
	
}
?>
