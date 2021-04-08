<?php

require_once ("cms.inc.php");
class search extends cms {
	protected $branchid;
	function customer(){}
	function search() {
		$this->branchid = 0;
	}
	
	function getBranchid() {
		return $this->branchid;
	}

	function getCustomer($cssearch = false, $limit = false, $records_per_page = false, $debug = false) {
		
		$cssearch = $this->convert_char($cssearch);
		
		$sql = "select a_bookinginfo.book_id,a_bookinginfo.b_qty_people, a_bookinginfo.b_appt_date, " .
		"bl_branchinfo.branch_name,a_bookinginfo.c_set_cms as set_cms,a_bookinginfo.a_member_code as member_code, " .
		"\"d_indivi_info\" as tbname,d_indivi_info.indivi_id as indivi_id, " .
		"d_indivi_info.cs_name,d_indivi_info.cs_phone,d_indivi_info.cs_email, " .
		"d_indivi_info.cs_age,d_indivi_info.cs_birthday,d_indivi_info.sex_id, " .
		"d_indivi_info.nationality_id,d_indivi_info.resident, d_indivi_info.visitor " .
		", d_indivi_info.package_id " .
		"from a_bookinginfo,bl_branchinfo,d_indivi_info ";

		$sql .= "where a_bookinginfo.b_branch_id=bl_branchinfo.branch_id " .
		"and a_bookinginfo.book_id=d_indivi_info.book_id  " .
		"and d_indivi_info.cs_name!=\"\" ";
		
		if ($cssearch) {
			$sql .= "and (d_indivi_info.cs_phone like \"%$cssearch%\" ";
			$sql .= "or LOWER(d_indivi_info.cs_name) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(d_indivi_info.cs_email) like \"%".strtolower($cssearch)."%\") ";
		}

		$sql .= "order by b_appt_date desc,book_id,cs_name ";
		
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}
		//echo $sql . "<br>";
		$rs = $this->getResult($sql);
		for($i=0;$i<$rs["rows"];$i++){

			$sql = "select member_code, fname, lname " .
					"from m_membership where member_code=".$rs[$i]["member_code"];
			$check_mem = $this->getResult($sql);
			$mem_name = $check_mem[0]["fname"].$check_mem[0]["lname"];
			$mem_name = str_replace(" ", "", $mem_name); 
			$cs_name = str_replace(" ", "", $rs[$i]["cs_name"]);

			if($mem_name!=$cs_name){
				$rs[$i]["member_code"]="";
			}

		}
		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $rs;
	}

	function getAgent($cssearch = false, $limit = false, $records_per_page = false, $debug = false) {
		
		$cssearch = $this->convert_char($cssearch);
		
		$sql="select a_bookinginfo.*,al_bookparty.bp_name,al_accomodations.acc_name from a_bookinginfo, al_bookparty,al_accomodations " .
			 "where a_bookinginfo.c_bp_id<>1 " .
			 "and a_bookinginfo.c_bp_id=al_bookparty.bp_id " .
			 "and a_bookinginfo.b_accomodations_id=al_accomodations.acc_id ";
		if ($cssearch) {
			$sql .= "and (a_bookinginfo.c_bp_phone like \"%$cssearch%\" ";
			$sql .= "or LOWER(a_bookinginfo.c_bp_person) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(al_bookparty.bp_name) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(al_accomodations.acc_name) like \"%".strtolower($cssearch)."%\") ";
		}

		$sql .= "order by a_bookinginfo.b_appt_date desc, a_bookinginfo.book_id ";
		
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}
		
		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getAgentCus($name = false, $debug = false) {
		
		$sql = "select a_bookinginfo.c_bp_person, sum(a_bookinginfo.b_qty_people) as total_cus from a_bookinginfo,al_bankacc_cms where a_bookinginfo.c_bp_person='$name' " .
				"and a_bookinginfo.c_bp_person=al_bankacc_cms.c_bp_person group by a_bookinginfo.c_bp_person";
		$rs = $this->getResult($sql);
		
		$total_cus = $rs[0]["total_cus"];

		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $total_cus;
	}
	
	function getMember($cssearch = false, $limit = false, $records_per_page = false, $debug = false) {
		
		$cssearch = $this->convert_char($cssearch);
		
		$sql = "select * from m_membership where `expired`=1 ";

		if ($cssearch) {
			$sql .= "and (`phone` like \"%$cssearch%\" ";
			$sql .= "or mobile like \"%".$cssearch."%\" ";
			$sql .= "or member_code like \"%".$cssearch."%\" ";
			$sql .= "or LOWER(fname) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(lname) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(email) like \"%".strtolower($cssearch)."%\") ";
		}
				
		$sql .= "order by member_code ";
		
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}
		
		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getMarketing($cssearch = false, $limit = false, $records_per_page = false, $debug = false) {
		
		$cssearch = $this->convert_char($cssearch);
		
		$sql = "select * from l_marketingcode where active=1 ";
		
		if ($cssearch) {
			$sql .= "and (`phone` like \"%$cssearch%\" ";
			$sql .= "or LOWER(sign) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(contactperson) like \"%".strtolower($cssearch)."%\") ";
		}
		
		$sql .= "order by issue desc,sign ";
		
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}	
		
		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getGift($cssearch = false, $limit = false, $records_per_page = false, $debug = false) {
		
		$cssearch = $this->convert_char($cssearch);
		
		$sql = "select * from g_gift where available=1 ";
		
		if ($cssearch) {
			$sql .= "and (`gift_number` like \"%$cssearch%\" ";
			$sql .= "or LOWER(give_to) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(receive_from) like \"%".strtolower($cssearch)."%\") ";
		}
		
		$sql .= "order by issue desc,gift_number ";
		
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}	
		
		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function getHotel($cssearch = false, $limit = false, $records_per_page = false, $debug = false) {
		
		$cssearch = $this->convert_char($cssearch);
		
		$sql = "select * from al_accomodations where acc_active=1 ";
		
		if ($cssearch) {
			$sql .= "and (LOWER(acc_name) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or LOWER(acc_person) like \"%".strtolower($cssearch)."%\" ";
			$sql .= "or acc_phone like \"%".$cssearch."%\" ";
			$sql .= "or acc_fax like \"%".$cssearch."%\" ";
			$sql .= "or LOWER(acc_email) like \"%".strtolower($cssearch)."%\") ";
		}
		
		$sql .= "order by acc_id ";
		
		if ($records_per_page) {
			$sql .= "limit $limit,$records_per_page";
		}	
		
		//echo $sql . "<br>";
		if ($debug) {
			echo $sql . "<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	function convert_char($content){
		$content = str_replace("&","&amp;",$content);
		$content = str_replace(">","&gt;",$content);
		$content = str_replace("<","&lt;",$content);
		$content = str_replace('"',"&quot;",$content);
		return $content;
	}
	
	
	
	
	
	
}


?>