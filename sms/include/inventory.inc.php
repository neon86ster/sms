<?php
/*
 * File name : inventory.inc.php
 * Description : Class file inventory report for cms system
 * Author : natt
 * Create date : 24-Feb-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class inventory extends report {
	
	function gettrmcategory($order=false,$sort=false,$categoryid=false) {
		//$sql = "select * from db_trm_category where trm_category_id!=6 ";
		$sql = "select * from db_trm_category ";
		if($categoryid){
			$sql .= "and trm_category_id=".$categoryid." ";
		}
		if($order=="Category"){
			$sql.="order by trm_category_name ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		
		if($order=="Default"){
			$sql.="order by trm_category_id ";
			if($sort=="A > Z"){$sql.="desc";}
		}
		return $this->getResult($sql);
	}
	
/*
 * get all treatment for treatment table
 * @modified - add this function on 3 April 2009
 */
	function gettrm($order=false,$sort=false,$categoryid=false,$debug=false) {
		$sql = "select * from db_trm where trm_active=1 ";
		
		if($categoryid){$sql .= "and db_trm.trm_category_id=".$categoryid." ";}
		if($order==="Category"){
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
	
	function getinventory($start_date=false,$end_date=false,$branchid=false,$categoryid=false,$trmid=false,$debug=false){
		if(!$start_date) {
			$this->setErrorMsg("inventory.getinventory(),Please insert Date for see this report!!");
			return false;
		}
		
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		// all trm in d_indivi_info
		$sql1 = "select db_trm.trm_id,db_trm.trm_name,count(db_trm.trm_id) as total,a_bookinginfo.b_appt_date," .
				"bl_branchinfo.branch_name,db_trm_category.trm_category_name,db_trm_category.trm_category_id,a_bookinginfo.b_branch_id,a_bookinginfo.book_id " .
				"from db_trm_category,db_trm,d_indivi_info,a_bookinginfo,bl_branchinfo " .
				"where a_bookinginfo.b_set_cancel=0 " .
				"and db_trm.trm_id!=1 " .
				"and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id " .
				"and d_indivi_info.book_id=a_bookinginfo.book_id " .
				"and (d_indivi_info.b_set_inroom!=0 or d_indivi_info.b_set_finish!=0) " .
				"and db_trm_category.trm_category_id=db_trm.trm_category_id ";
		if($end_date==false){$sql1 .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql1 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($categoryid){$sql1 .= "and db_trm_category.trm_category_id=".$categoryid." ";}
		if($branchid){$sql1 .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($trmid){$sql1 .= "and db_trm.trm_id=".$trmid." ";}
		$sql1 .= "and (d_indivi_info.bath_id=db_trm.trm_id " .
				"or d_indivi_info.facial_id=db_trm.trm_id " .
				"or d_indivi_info.scrub_id=db_trm.trm_id " .
				"or d_indivi_info.wrap_id=db_trm.trm_id) " .
				"group by a_bookinginfo.b_appt_date,db_trm.trm_id,a_bookinginfo.book_id,a_bookinginfo.b_branch_id ";
		
		// all trm in da_mult_msg
		$sql2 = "select db_trm.trm_id,db_trm.trm_name,count(db_trm.trm_id) as total,a_bookinginfo.b_appt_date," .
				"bl_branchinfo.branch_name,db_trm_category.trm_category_name,db_trm_category.trm_category_id,a_bookinginfo.b_branch_id,a_bookinginfo.book_id " .
				"from db_trm_category,db_trm,da_mult_msg,d_indivi_info,a_bookinginfo,bl_branchinfo " .
				"where a_bookinginfo.b_set_cancel=0 " .
				"and db_trm.trm_id!=1 " .
				"and bl_branchinfo.branch_id=a_bookinginfo.b_branch_id " .
				"and da_mult_msg.indivi_id=d_indivi_info.indivi_id " .
				"and d_indivi_info.book_id=a_bookinginfo.book_id " .
				"and (d_indivi_info.b_set_inroom!=0 or d_indivi_info.b_set_finish!=0) " .
				"and db_trm_category.trm_category_id=db_trm.trm_category_id ";
		if($end_date==false){$sql2 .= "and a_bookinginfo.b_appt_date=".$startdate." ";}
		else{$sql2 .= "and a_bookinginfo.b_appt_date>='".$startdate."' and a_bookinginfo.b_appt_date<='".$enddate."' ";}
		if($categoryid){$sql2 .= "and db_trm_category.trm_category_id=".$categoryid." ";}
		if($branchid){$sql2 .= "and a_bookinginfo.b_branch_id=".$branchid." ";}
		if($trmid){$sql2 .= "and db_trm.trm_id=".$trmid." ";}
		$sql2 .= "and da_mult_msg.massage_id=db_trm.trm_id " .
				"group by a_bookinginfo.b_appt_date,db_trm.trm_id,a_bookinginfo.book_id,a_bookinginfo.b_branch_id ";
		
		$sql = "($sql1) union ($sql2) order by trm_category_id,trm_name,branch_name,b_appt_date,book_id";
		//$sql = "($sql1) union ($sql2) order by branch_name,b_appt_date,book_id";
		//echo "<br><br><br><br><br><br>".$sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
	}
	
/*
 * summary total result set by trm_category_id
 * $fieldname - fieldname of summary value for example: customer per location report sunary "qty"
 */	
	function sumeachinventoryfield($rs,$fieldname,$trmid=false,$begin=false,$end=false,$branchid=0){
		$sum = 0;

		//echo "<br>&nbsp;&nbsp;<b>$begin $end:</b><br><br> ";
		for($i=0;$i<$rs["rows"];$i++){
		if(!isset($rs[$i+1]["trm_id"])){$rs[$i+1]["trm_id"]=0;}
			if($rs[$i]["trm_id"]==$trmid){
				$chk_begin =ctype_digit($begin);
				$chk_end = ctype_digit($end);
				if(!$chk_begin&&$chk_end){
					if($rs[$i]["b_branch_id"]==$end){
						$sum += $rs[$i]["$fieldname"];
					}
				}else if($branchid>0){
					if($rs[$i]["b_branch_id"]==$branchid){
						$sum += $rs[$i]["$fieldname"];
					}
				}else{
					$appt_date = str_replace("-","",$rs[$i]["b_appt_date"]);
					if($appt_date>=$begin&&$appt_date<=$end){
							$sum += $rs[$i]["$fieldname"];
					}
				}
			}
			if($rs[$i]["trm_id"]==$trmid&&$rs[$i]["trm_id"]!=$rs[$i+1]["trm_id"]){
				break;
			}
		}
		//echo "&nbsp;&nbsp; $trmid - $sum<br>";//echo ($chk_begin&&!$chk_end)?"true<br>":"false<br>";
		
		return $sum;
	}
}
?>
