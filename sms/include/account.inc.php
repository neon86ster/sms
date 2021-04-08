<?php
/*
 * File name : account.inc.php
 * Description : Class file accountting report for cms system
 * Author : natt
 * Create date : 30-Jan-2008
 * Modified : natt@chiangmaioasis.com
 */   
require_once("report.inc.php");
class account extends report {
	
	function account(){}

	function getforselectacc($branch_id=false,$start_date=false,$end_date=false,$ck_select=false,$payid=false,$debug=false) {
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		//table a_bookinginfo
		$sql1 = "select c_salesreceipt.a_accounting,c_salesreceipt.salesreceipt_id,c_salesreceipt.a_pagenumber,l_paytype.pay_name," .
				"a_appointment.bpds_id,\"a_bookinginfo\" as tb_name,c_salesreceipt.book_id," .
				"a_appointment.appt_date as b_appt_date,bl_branchinfo.branch_name,a_appointment.bp_name,min(log_c_sr.sr_datets) as sr_datets,";
		
		//total
		$sql1 .= "c_salesreceipt.sr_total as total ";
				
		$sql1 .= "from c_salesreceipt, bl_branchinfo, a_appointment, l_paytype, log_c_sr ";
		
		$sql1 .= "where a_appointment.appt_date >=\"$startdate\" " .
				"and a_appointment.appt_date <=\"$enddate\" ";
		if($branch_id){$sql1 .= "and a_appointment.branch_id=".$branch_id." ";}
		$sql1 .= "and a_appointment.b_set_cancel<>1 ";
		$sql1 .= "and a_appointment.book_id=c_salesreceipt.book_id ";
		$sql1 .= "and c_salesreceipt.paid_confirm<>0 ";
		$sql1 .= "and log_c_sr.salesreceipt_id=c_salesreceipt.salesreceipt_id ";
		$sql1 .= "and log_c_sr.paid_confirm<>0 ";
		$sql1 .= "and l_paytype.pay_id=c_salesreceipt.pay_id ";
		if($payid){
			$sql1 .= "and c_salesreceipt.pay_id = \"$payid\" ";
		}
		$sql1 .= "and a_appointment.branch_id=bl_branchinfo.branch_id ";
		if($ck_select){$sql1 .= "and c_salesreceipt.a_accounting=1 ";}
		$sql1 .= "group by c_salesreceipt.salesreceipt_id ";
		$sql1 .= "order by bl_branchinfo.branch_name,a_appointment.appt_date,c_salesreceipt.book_id ";
		
		
		//table c_saleproduct
		$sql2 = "select c_salesreceipt.a_accounting,c_salesreceipt.salesreceipt_id,c_salesreceipt.a_pagenumber,l_paytype.pay_name," .
				"c_bpds_link.bpds_id,c_bpds_link.tb_name,c_salesreceipt.pds_id as book_id," .
				"c_saleproduct.pds_date as b_appt_date,bl_branchinfo.branch_name," .
				"\"-\" as bp_name,min(log_c_sr.sr_datets) as sr_datets,";
		//$sql .= "cl_product_category.pos_neg_value,c_srdetail.unit_price,c_srdetail.qty,cl_product_category.set_payment,";
		
		//total
		$sql2 .= "c_salesreceipt.sr_total as total ";
				
		$sql2 .= "from c_saleproduct, ";
		$sql2 .= "c_salesreceipt, bl_branchinfo, c_bpds_link, l_paytype, log_c_sr ";
		
		$sql2 .= "where c_saleproduct.pds_date >=\"$startdate\" " .
				"and c_saleproduct.pds_date <=\"$enddate\" ";
		$sql2 .= "and c_saleproduct.pds_id=c_salesreceipt.pds_id ";
		if($branch_id){$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";}
		$sql2 .= "and c_saleproduct.set_cancel<>1 ";
		$sql2 .= "and c_bpds_link.tb_id=c_saleproduct.pds_id ";
		$sql2 .= "and c_bpds_link.tb_name=\"c_saleproduct\" ";
		$sql2 .= "and c_salesreceipt.paid_confirm<>0 ";
		$sql2 .= "and log_c_sr.salesreceipt_id=c_salesreceipt.salesreceipt_id ";
		$sql2 .= "and log_c_sr.paid_confirm<>0 ";
		$sql2 .= "and l_paytype.pay_id=c_salesreceipt.pay_id ";
		if($payid){
			$sql2 .= "and c_salesreceipt.pay_id = \"$payid\" ";
		}
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		if($ck_select){$sql2 .= "and c_salesreceipt.a_accounting=1 ";}
		$sql2 .= "group by c_salesreceipt.salesreceipt_id ";
		$sql2 .= "order by bl_branchinfo.branch_name,c_saleproduct.pds_date,c_salesreceipt.pds_id ";
		$sql = "($sql1) union ($sql2) order by branch_name,b_appt_date,sr_datets,bpds_id,salesreceipt_id ";
		
		if($debug) {
			echo $sql2."<br>";
			return false;
		}
		//echo $sql."<br>";
		return $this->getResult($sql);
	}
	
	//reordering salesreceipt using paid tick last date ordering
	function reorderSr($srs=false){
		$allsrs = implode(",",$srs);
		$chksql = "select salesreceipt_id,min(sr_datets) from log_c_sr " .
				"where salesreceipt_id in ($allsrs)" .
				"and log_c_sr.paid_confirm<>0 " .
				"group by salesreceipt_id " .
				"order by sr_datets";
		$chkrs = $this->getResult($chksql);
		for($i=0;$i<$chkrs["rows"];$i++){
			$srs[$i] = $chkrs[$i]["salesreceipt_id"];
		}
		return $srs;
	}
	
	function resetPagenum($srs=false,$newsrs=false,$pagenum=false,$branchid=false,$debug=false){
		//check if pagenumber already have in database
		$allsrs = implode(",",$srs);
		$chksql = "select c_salesreceipt.a_pagenumber from c_salesreceipt,a_bookinginfo " .
				"where salesreceipt_id in ($allsrs) " .
				"and c_salesreceipt.book_id=a_bookinginfo.book_id ".
				"and a_bookinginfo.b_branch_id=$branchid ";
		$chkrs = $this->getResult($chksql);
		$allnewsrs = "";
		for($i=0;$i<$chkrs["rows"];$i++){
			if($i){$allnewsrs .= ",";}
			$allnewsrs .= $chkrs[$i]["a_pagenumber"];
		}
		$lastpagenum = $pagenum+count($newsrs)-1;
		$chksql = "select c_salesreceipt.salesreceipt_id,count(c_salesreceipt.salesreceipt_id) as chkpagenum,a_bookinginfo.b_branch_id " .
				"from c_salesreceipt,a_bookinginfo " .
				"where c_salesreceipt.book_id=a_bookinginfo.book_id " .
				"and a_bookinginfo.b_branch_id=$branchid " .
				"and c_salesreceipt.a_pagenumber>=$pagenum " .
				"and c_salesreceipt.a_pagenumber<=$lastpagenum " .
				"and c_salesreceipt.a_pagenumber not in (".$allnewsrs.") " .
				"group by a_bookinginfo.b_branch_id";
		$chkrs = $this->getResult($chksql);
		//echo $lastpagenum."-$pagenum,$branchid";
		if($chkrs[0]["chkpagenum"]>0){
			$this->setErrorMsg("Can't running the same of new sale receipt number twice for each branch!!");
			return false;
		}
		$chksql = "";
		$chkid =1;
		for($i=0;$i<count($srs);$i++){
			if($chkid>0){
				$chksql .= "update c_salesreceipt set a_accounting=0, a_pagenumber=0 where salesreceipt_id=".$srs[$i].";";
				$sql = "update c_salesreceipt set a_accounting=0, a_pagenumber=0 where salesreceipt_id=".$srs[$i].";";
				$id = $this->setResult($sql);
				if($id){$chkid=$id*$chkid;}else{$chkid=0;}
			}
		}
		
		if($debug){echo $chksql."<br/>";return false;}
		
		if($chkid){return true;}
		else{$this->setErrorMsg("Can't reset c_salesreceipt(a_accounting,a_pagenumber) in resetPagenum()!!");}
	}
	
	function updatePagenum($srs=false,$pagenum=false,$debug=false){		
		if($pagenum==0){return false;}
		if(count($srs)==1&&$srs[0]==""){return false;}
		$chksql = "";
		$chkid =1;
		for($i=0;$i<count($srs);$i++){
			if($chkid>0){
				$chksql .= "update c_salesreceipt set a_accounting=1, a_pagenumber=$pagenum where salesreceipt_id=".$srs[$i].";";
				$sql = "update c_salesreceipt set a_accounting=1, a_pagenumber=$pagenum where salesreceipt_id=".$srs[$i].";";
				$id = $this->setResult($sql);
				$pagenum++;
				if($id){$chkid=$id*$chkid;}else{$chkid=0;}
			}
		}
		
		if($debug){echo $chksql."<br/>";return false;}
		
		if($chkid){return true;}
		else{$this->setErrorMsg("Can't reset c_salesreceipt(a_accounting,a_pagenumber) in resetPagenum()!!");return false;}
	}
	
	function getsr($pagenum=false,$branch_id=false,$start_date=false,$end_date=false,$debug=false){
		$startdate = substr($start_date,0,4)."-".substr($start_date,4,2)."-".substr($start_date,6,2);
		$enddate = substr($end_date,0,4)."-".substr($end_date,4,2)."-".substr($end_date,6,2);
		
		
		//table a_bookinginfo
		$sql1 = "select a_company_info.company_name,a_company_info.company_phone,a_company_info.tax_num,a_company_info.website," .
				"bl_branchinfo.branch_name,bl_branchinfo.branch_address,bl_branchinfo.branch_phone," .
				"c_salesreceipt.salesreceipt_id,c_salesreceipt.sr_datets,min(log_c_sr.sr_datets) as sr_date,c_salesreceipt.book_id," .
				"a_bookinginfo.b_appt_date,c_salesreceipt.a_pagenumber,\"a_bookinginfo\" as tb_name ";
		
		
		$sql1 .= "from a_bookinginfo, c_salesreceipt," .
				"bl_branchinfo,a_company_info,log_c_sr ";
		$sql1 .= "where a_bookinginfo.b_set_cancel<>1 ";
		if(!$pagenum){
			$sql1 .= "and a_bookinginfo.b_appt_date >= \"$startdate\" ";
			$sql1 .= "and a_bookinginfo.b_appt_date <= \"$enddate\" ";
		}
		$sql1 .= "and a_bookinginfo.book_id=c_salesreceipt.book_id ";
		$sql1 .= "and log_c_sr.salesreceipt_id=c_salesreceipt.salesreceipt_id ";
		$sql1 .= "and log_c_sr.paid_confirm=1 ";
		if($branch_id)
			$sql1 .= "and a_bookinginfo.b_branch_id=".$branch_id." ";
		if($pagenum)
			$sql1 .= "and c_salesreceipt.a_pagenumber=".$pagenum." ";
		$sql1 .= "and a_bookinginfo.b_branch_id=bl_branchinfo.branch_id ";
		$sql1 .= "and c_salesreceipt.a_accounting=1 ";
		$sql1 .= "group by c_salesreceipt.salesreceipt_id ";
		$sql1 .= "order by c_salesreceipt.sr_datets,a_bookinginfo.book_id,c_salesreceipt.salesreceipt_id ";
			
		
		//table c_saleproduct
		$sql2 = "select a_company_info.company_name,a_company_info.company_phone,a_company_info.tax_num,a_company_info.website," .
				"bl_branchinfo.branch_name,bl_branchinfo.branch_address,bl_branchinfo.branch_phone," .
				"c_salesreceipt.salesreceipt_id,c_salesreceipt.sr_datets,min(log_c_sr.sr_datets) as sr_date,c_salesreceipt.pds_id as book_id," .
				"c_saleproduct.pds_date as b_appt_date,c_salesreceipt.a_pagenumber,\"c_saleproduct\" as tb_name ";
		
						
		$sql2 .= "from c_saleproduct, c_salesreceipt," .
				"bl_branchinfo,a_company_info,log_c_sr ";
		$sql2 .= "where c_saleproduct.set_cancel<>1 ";
		if(!$pagenum){
			$sql2 .= "and c_saleproduct.pds_date >= \"$startdate\" ";
			$sql2 .= "and c_saleproduct.pds_date <= \"$enddate\" ";
		}
		$sql2 .= "and c_saleproduct.pds_id=c_salesreceipt.pds_id ";
		$sql2 .= "and log_c_sr.salesreceipt_id=c_salesreceipt.salesreceipt_id ";
		$sql2 .= "and log_c_sr.paid_confirm=1 ";
		if($branch_id)
			$sql2 .= "and c_saleproduct.branch_id=".$branch_id." ";
		if($pagenum)
			$sql2 .= "and c_salesreceipt.a_pagenumber=".$pagenum." ";
		$sql2 .= "and c_saleproduct.branch_id=bl_branchinfo.branch_id ";
		$sql2 .= "and c_salesreceipt.a_accounting=1 ";
		$sql2 .= "group by c_salesreceipt.salesreceipt_id ";
		$sql2 .= "order by c_salesreceipt.sr_datets,c_saleproduct.pds_id,c_salesreceipt.salesreceipt_id ";	
		
		$sql = "($sql1) union ($sql2) order by a_pagenumber,sr_datets,b_appt_date,book_id,salesreceipt_id ";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		//echo $sql."<br>";
		return $this->getResult($sql);
	}
	
	function getsrd($sr_id=false,$tb_name="a_bookinginfo",$acc=false,$debug=false){
		if($tb_name=="a_bookinginfo"){
			//table a_bookinginfo
			$sql = "select c_srdetail.pd_id,cl_product.pd_name,c_srdetail.unit_price,c_srdetail.qty,c_srdetail.set_tax,c_srdetail.set_sc," .
					"c_salesreceipt.book_id,c_salesreceipt.a_pagenumber,c_salesreceipt.sr_datets," .
					"a_bookinginfo.b_appt_date,a_bookinginfo.b_branch_id,a_bookinginfo.servicescharge,l_tax.tax_percent," .
					"cl_product_category.pos_neg_value,cl_product_category.set_payment,cl_product_category.pd_category_id ";
			$sql .= "from a_bookinginfo ";
			$sql .= "left join c_srdetail on a_bookinginfo.book_id = c_srdetail.book_id,c_salesreceipt,l_tax,cl_product_category,cl_product ";
			$sql .= "where a_bookinginfo.b_set_cancel <> 1 ";	
			$sql .= "and c_salesreceipt.salesreceipt_id = c_srdetail.salesreceipt_id ";
			$sql .= "and cl_product.pd_id = c_srdetail.pd_id ";
			$sql .= "and cl_product_category.pd_category_id = cl_product.pd_category_id ";
			$sql .= "and a_bookinginfo.tax_id = l_tax.tax_id ";
			if($acc){
			$sql .= "and c_salesreceipt.a_accounting = 1 ";
			}
			$sql .= "and c_srdetail.pd_id <> 1 ";
			$sql .= "and c_salesreceipt.salesreceipt_id=".$sr_id." ";
			$sql .= "order by c_srdetail.srdetail_id";
		}else{
			//table c_saleproduct
			$sql = "select c_srdetail.pd_id,cl_product.pd_name,c_srdetail.unit_price,c_srdetail.qty,c_srdetail.set_tax,c_srdetail.set_sc," .
					"c_salesreceipt.book_id,c_salesreceipt.a_pagenumber,c_salesreceipt.sr_datets," .
					"c_saleproduct.pds_date as b_appt_date,c_saleproduct.branch_id as b_branch_id,c_saleproduct.servicescharge,l_tax.tax_percent," .
					"cl_product_category.pos_neg_value,cl_product_category.set_payment,cl_product_category.pd_category_id ";
			$sql .= "from c_saleproduct ";
			$sql .= "left join c_srdetail on c_saleproduct.pds_id = c_saleproduct.pds_id,c_salesreceipt,l_tax,cl_product_category,cl_product ";
			$sql .= "where c_saleproduct.set_cancel <> 1 ";	
			$sql .= "and c_salesreceipt.salesreceipt_id = c_srdetail.salesreceipt_id ";
			$sql .= "and cl_product.pd_id = c_srdetail.pd_id ";
			$sql .= "and cl_product_category.pd_category_id = cl_product.pd_category_id ";
			$sql .= "and c_saleproduct.tax_id = l_tax.tax_id ";
			if($acc){
			$sql .= "and c_salesreceipt.a_accounting = 1 ";
			}
			$sql .= "and c_srdetail.pd_id <> 1 ";
			$sql .= "and c_salesreceipt.salesreceipt_id=".$sr_id." ";
			$sql .= "group by c_srdetail.srdetail_id ";
			$sql .= "order by c_srdetail.srdetail_id";
		}
		//echo $sql."<br>";
		if($debug) {
			echo $sql."<br>";
			return false;
		}
		
		return $this->getResult($sql);
		
	}
	
	function getroomname($book_id=false){
		if(!$book_id){
			$this->setErrorMsg("account.getroomname(),Please insert Book id for see this report!!");
			return false;
		}
		
		$sql = "select DISTINCT bl_room.room_name from d_indivi_info,bl_room " .
				"where d_indivi_info.room_id= bl_room.room_id " .
				"and d_indivi_info.book_id=".$book_id;
		
		$rs = $this->getResult($sql);
		
		$room = "";
		for($i=0;$i<$rs["rows"];$i++){
			if($i){$room.=",";}
			$room .= " ".$rs[$i]["room_name"];
		}
		//echo "rows: ".$rs["rows"];
		return $room;
	}
}
?>
