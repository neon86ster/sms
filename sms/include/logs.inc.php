<?php
/*
 * File name : logs.inc.php
 * Description : Class file booking log for sms system
 * Author : natt
 * Create date : 05-Sep-2009
 * Modified : natt@tap10.com
 */   
require_once("cms.inc.php");
class logs extends cms {
	
/*
 * function for get history of who preview salereceipt sheet
 *  @modified - add this function on 04-09-2009
 */
	 function getSrPrintHis($sr_id=false,$limit=0,$records_per_page=false,$debug=false){		
		$sql = "select s_user.u as user,log_c_srprint.log_id,log_c_srprint.l_lu_ip,log_c_srprint.l_lu_date," .
				"log_c_srprint.reprint_times " .
				"from log_c_srprint,s_user " .
				"where log_c_srprint.l_lu_user = s_user.u_id ";
		if($sr_id){$sql .= "and log_c_srprint.salesreceipt_id = $sr_id " ;}
		
		if($records_per_page){$sql .= "limit $limit,$records_per_page";}
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	 }
	 
/*
 * function for get history of booking's commission 
 *  @modified - add this function on 04-09-2009
 */
	function get_log_cms($bookid=false,$limit=0,$records_per_page=false,$debug=false){
		if(!$bookid){
			$this->setErrorMsg("Please insert booking id for get commission log !!");
			return false;
		}
		$sql = "select log_c_bp.*,al_bookparty.bp_name as company_name, " .
				"al_percent_cms.pcms_percent,log_c_bp.c_cms_value as cms_value,s_user.u as user " .
				"from log_c_bp,al_bookparty,al_percent_cms,s_user " .
				"where s_user.u_id = log_c_bp.l_lu_user " .
				"and al_percent_cms.pcms_id = log_c_bp.c_pcms_id " .
				"and al_bookparty.bp_id = log_c_bp.c_bp_id " .
				"and log_c_bp.book_id = $bookid ";
		if($records_per_page){$sql .= "limit $limit,$records_per_page";}
				
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * function for get history of booking's sales receipt 
 *  @modified - add this function on 04-09-2009
 */
	function get_log_sr($bookid=false,$pdsid=false,$srid=false,$limit=0,$records_per_page=false,$debug=false){
		if(!$bookid && !$pdsid){
			$this->setErrorMsg("Please insert booking id for get sales receipt log !!");
			return false;
		}
		$sql = "select log_c_sr.*,l_paytype.pay_name as payment, " .
				"s_user.u as user " .
				"from log_c_sr,l_paytype,s_user " .
				"where s_user.u_id = log_c_sr.sr_lu_user " .
				"and l_paytype.pay_id = log_c_sr.pay_id ";
		if($bookid){$sql .= "and log_c_sr.book_id = $bookid ";}
		if($pdsid){$sql .= "and log_c_sr.pds_id = $pdsid ";}
		if($srid){$sql .= "and log_c_sr.salesreceipt_id = $srid ";}
		$sql .= "order by log_c_sr.salesreceipt_id,log_c_sr.log_id ";
				
		if($records_per_page){$sql .= "limit $limit,$records_per_page";}
				
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * function for get history of booking's sales receipt detail 
 *  @modified - add this function on 04-09-2009
 */
	function get_log_srdetail($bookid=false,$pdsid=false,$srid=false,$limit=0,$records_per_page=false,$debug=false){
		if(!$bookid && !$pdsid){
			$this->setErrorMsg("Please insert booking id for get sales receipt detail log !!");
			return false;
		}
		$sql = "select log_c_srdetail.*,cl_product.pd_name,s_user.u as user " .
				"from log_c_srdetail,cl_product,s_user " .
				"where s_user.u_id = log_c_srdetail.l_lu_user " .
				"and cl_product.pd_id = log_c_srdetail.pd_id ";
		if($bookid){$sql .= "and log_c_srdetail.book_id = $bookid ";}
		if($pdsid){$sql .= "and log_c_srdetail.pds_id = $pdsid ";}
		if($srid){$sql .= "and log_c_srdetail.salesreceipt_id = $srid ";}
		$sql .= "order by log_c_srdetail.l_lu_date,log_c_srdetail.log_id ";
		
		if($records_per_page){$sql .= "limit $limit,$records_per_page";}
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
	
/*
 * function for highlight content if 2 contents is difference
 * @param - content1
 * @param - content2
 * @modified - add this function on 09-09-2009
 */
 	function checkDiffChar($content1="",$content2=""){
 		if($content1!=$content2){return "<span class=\"find-search\">$content2</span>";}
 		else{return $content2;}
 	}
 	
 	/*
 * function for get history of booking's muti payment detail 
 *  @modified - add this function on 04-03-2010
 */
	function get_log_mpdetail($bookid=false,$pdsid=false,$srid=false,$limit=0,$records_per_page=false,$debug=false){
		if(!$bookid && !$pdsid){
			$this->setErrorMsg("Please insert booking id for get muti payment detail log !!");
			return false;
		}
		$sql = "select log_c_srpayment.*,l_paytype.pay_name,s_user.u as user " .
				"from log_c_srpayment,l_paytype,s_user " .
				"where s_user.u_id = log_c_srpayment.l_lu_user " .
				"and l_paytype.pay_id = log_c_srpayment.pay_id " ;
		if($bookid){$sql .= "and log_c_srpayment.book_id = $bookid ";}
		if($pdsid){$sql .= "and log_c_srpayment.pds_id = $pdsid ";}
		if($srid){$sql .= "and log_c_srpayment.salesreceipt_id = $srid ";}
		$sql .= "order by log_c_srpayment.l_lu_date,log_c_srpayment.log_id ";
		
		if($records_per_page){$sql .= "limit $limit,$records_per_page";}
		
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
	
/*
 * function for get therapist log  
 *  @modified - add this function on 02-04-2010
 */
	function get_log_th($bookid=false,$limit=0,$records_per_page=false,$debug=false){
		if(!$bookid){
			$this->setErrorMsg("Please insert booking id for get therapist log !!");
			return false;
		}
		$sql = "select log_c_therapist.*,l_employee.emp_nickname as therapist,s_user.u as user,bl_room.room_name as t_room, " .
				"db_package.package_name as t_package,l_hour.hour_calculate as hour,log_c_therapist.cs_name as customer,l_employee.emp_code," .
				"log_c_therapist.room,log_c_therapist.package " .
				"from log_c_therapist,s_user,l_employee,bl_room,db_package,l_hour " .
				
				
				"where log_c_therapist.l_lu_user = s_user.u_id " .
				"and log_c_therapist.room = bl_room.room_id " .
				"and log_c_therapist.package = db_package.package_id " .
				"and log_c_therapist.th_hour = l_hour.hour_id " .
				"and l_employee.emp_id = log_c_therapist.th_id " ;
				
				
		if($bookid){$sql .= "and log_c_therapist.book_id = $bookid ";}
			
				
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		return $this->getResult($sql);
	}
}
?>
