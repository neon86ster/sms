<?php
// Created on May 16, 2009
// show accommodation information
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$bpid = $obj->getParameter("bpid");
if($bpid){
$sql = "select al_bookparty.*,al_bookparty_category.bp_category_name " .
		"from al_bookparty,al_bookparty_category " .
		"where al_bookparty.bp_category_id = al_bookparty_category.bp_category_id " .
		"and al_bookparty.bp_id = $bpid";
$rs = $obj->getResult($sql);
}
if(!$bpid&&!$rs){
	$rs[0]["bp_name"] = "-";
	$rs[0]["bp_category_name"] = "-";
	$rs[0]["bp_person"] = "-";
	$rs[0]["bp_phone"] = "-";
	$rs[0]["bp_fax"] = "-";
	$rs[0]["bp_email"] = "-";
	$rs[0]["city_name"] = "-";
	$rs[0]["bp_address"] = "-";
	$rs[0]["bp_pcms"] = "-";
	$rs[0]["bp_detail"] = "-";
}
?>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eaeaea;border: <?=$fontcolor?> solid 4px;">
<tr>
	<td>Booking Party Name</td>
	<td><?=$obj->checkParameter($rs[0]["bp_name"],"-")?></td>
</tr>
<tr>
	<td>Category</td>
	<td><?=$obj->checkParameter($rs[0]["bp_category_name"],"-")?></td>
</tr>
<tr>
	<td>Contact person</td>
	<td><?=$obj->checkParameter($rs[0]["bp_person"],"-")?></td>
</tr>
<tr>
	<td>Phone</td>
	<td><?=$obj->checkParameter($rs[0]["bp_phone"],"-")?></td>
</tr>
<tr>
	<td>Fax</td>
	<td><?=$obj->checkParameter($rs[0]["bp_fax"],"-")?></td>
</tr>
<tr>
	<td>Email</td>
	<td><?=$obj->checkParameter($rs[0]["bp_email"],"-")?></td>
</tr>
<tr>
	<td>City</td>
	<td><?=($rs[0]["city_id"]=="-")?$rs[0]["city_id"]:$obj->getIdToText($rs[0]["city_id"],"al_city","city_name","city_id")?></td>
</tr>
<tr>
	<td>Address</td>
	<td><?=$obj->checkParameter($rs[0]["bp_address"],"-")?></td>
</tr>
<tr>
	<td>% Info. CMS</td>
	<td><?=($rs[0]["bp_pcms"]=="-")?$rs[0]["bp_pcms"]:$obj->getIdToText($rs[0]["bp_pcms"],"al_percent_cms","pcms_percent","pcms_id")?></td>
</tr>
<tr>
	<td>Specific Details</td>
	<td><?=$obj->checkParameter($rs[0]["bp_detail"],"-")?></td>
</tr>
</table>