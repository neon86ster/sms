<?php
require_once("cms.inc.php");
/*
 * File name : cms.inc.php
 * Description : Class file for convert date string from mask
 * Author : Mariusz Stankiewicz http://prettymad.net
 * Create date : 08-Jan-2009
 * Modified : natt@chiangmaioasis.com
 */   
 class showCurrency extends cms {
 	
	 	function currency($branch = false){
	 			 				
	 		$sql = "SELECT l_currency.currency_id, l_currency.currency_name, l_currency.currency_detail, bl_branchinfo.currency FROM l_currency, bl_branchinfo " .
	 		$sql .= "WHERE l_currency.currency_id = bl_branchinfo.currency and bl_branchinfo.branch_id = $branch";
			$chkrs = $this->getResult($sql);	
					
	 		$currency = $chkrs[0]["currency_name"]?$chkrs[0]["currency_name"]:$chkrs[0]["currency_name"];			 			
			return $currency;
			
	 	}
	 	
	 	function exchange($branch = false){
	 			 				
	 		$sql = "SELECT bl_branchinfo.exchange FROM bl_branchinfo " .
	 		$sql .= "WHERE bl_branchinfo.branch_id = $branch";
			$chkrs = $this->getResult($sql);	
					
	 		$exchange = $chkrs[0]["exchange"]?$chkrs[0]["exchange"]:$chkrs[0]["exchange"];			 			
			return $exchange;
			
	 	}
	 	
	 	function currencyBranch($sum=false, $exchange=false){
	 		$currencyBranch = $sum * $exchange;
	 		return $currencyBranch;
	 	}
	 	
	//Get Currency Depend on user's branch 	
	/* 	function exchangeBranch($branch = false, $sum = false, $exchange = false){
	 		$sql = "SELECT bl_branchinfo.branch_id, bl_branchinfo.exchange FROM bl_branchinfo " .
	 		$sql .= "WHERE bl_branchinfo.branch_id = $branch";
			$chkb = $this->getResult($sql);	
					
	 		$exchange = $chkb[0]["exchange"]?$chkb[0]["exchange"]:$chkb[0]["exchange"];			 			
			
			if($branch = $chkb[0]["branch_id"]){
				$exchangeBranch = $sum;
			}else{
				$exchangeBranch = $sum * $exchange;
			}		
	 		return $exchangeBranch;		
	 	}*/
 }
 
?>
