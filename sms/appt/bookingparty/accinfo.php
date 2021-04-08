<?php
// Created on May 16, 2009
// show accommodation information
$root = $_SERVER["DOCUMENT_ROOT"];
include("$root/include.php");

$accid = $obj->getParameter("accid");
if($accid){
$sql = "select al_accomodations.*,al_city.city_name " .
		"from al_city,al_accomodations " .
		"where al_accomodations.city_id = al_city.city_id " .
		"and al_accomodations.acc_id = $accid";
$rs = $obj->getResult($sql);
}else{
	$rs[0]["acc_name"] = "-";
	$rs[0]["acc_person"] = "-";
	$rs[0]["acc_phone"] = "-";
	$rs[0]["acc_fax"] = "-";
	$rs[0]["acc_email"] = "-";
	$rs[0]["city_name"] = "-";
	$rs[0]["acc_address"] = "-";
	$rs[0]["acc_detail"] = "-";
}
?>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#eaeaea;border: <?=$fontcolor?> solid 4px;">
<tr>
	<td>Accommodation Name</td>
	<td><?=$obj->checkParameter($rs[0]["acc_name"],"-")?></td>
</tr>
<tr>
	<td>Contact person</td>
	<td><?=$obj->checkParameter($rs[0]["acc_person"],"-")?></td>
</tr>
<tr>
	<td>Phone</td>
	<td><?=$obj->checkParameter($rs[0]["acc_phone"],"-")?></td>
</tr>
<tr>
	<td>Fax</td>
	<td><?=$obj->checkParameter($rs[0]["acc_fax"],"-")?></td>
</tr>
<tr>
	<td>Email</td>
	<td><?=$obj->checkParameter($rs[0]["acc_email"],"-")?></td>
</tr>
<tr>
	<td>City</td>
	<td><?=$obj->checkParameter($rs[0]["city_name"],"-")?></td>
</tr>
<tr>
	<td>Address</td>
	<td><?=$obj->checkParameter($rs[0]["acc_address"],"-")?></td>
</tr>
<tr>
	<td>Specific Details</td>
	<td><?=$obj->checkParameter($rs[0]["acc_detail"],"-")?></td>
</tr>
</table>