<?php
include("../include.php");
require_once("appt.inc.php");

$barcode=$obj->getParameter("barcode");
$branch=$obj->getParameter("branch");

//$sql = "SELECT `pd_id` FROM `cl_product` WHERE `barcode` = '".$barcode."'";
$sql = "SELECT * FROM `cl_product` WHERE `barcode` = '".$barcode."'";
$rs = $obj->getResult($sql);

$sqlts= "SELECT bl_branchinfo.servicescharge, l_tax.tax_percent FROM bl_branchinfo,l_tax 
	  WHERE bl_branchinfo.branch_id = '".$branch."' and bl_branchinfo.tax_id=l_tax.tax_id ";
$rsts = $obj->getResult($sqlts);
	//echo $rs[0]["pd_id"];
	echo '{ "pd_id" : "'.$rs[0]["pd_id"].'"
			,"standard_price" : "'.$rs[0]["standard_price"].'"
			,"set_tax" : "'.$rs[0]["set_tax"].'"
			,"set_sc" : "'.$rs[0]["set_sc"].'"
			,"tax_percent" : "'.$rsts[0]["tax_percent"].'"
			,"sc_percent" : "'.$rsts[0]["servicescharge"].'"
		  }';
?>