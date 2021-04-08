<?php
/*
 * Created on Sep 23, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include("include.php");

  //$sql = "select * from s_pagename order by page_id";
  //$rs = $object->getResult($sql);
  //$sql = "select u_id from s_user order by u_id";
  //$userRs = $object->getResult($sql);
  $sql = "select group_id from s_group order by group_id";
  $groupRs = $object->getResult($sql);

  for($i=0;$i<$groupRs["rows"];$i++){
  	
  	$sql = "insert into s_grouptemplate (gpage_id, group_id, page_id, parent_id, menu_level," .
  			"page_name, url, edit_permission, view_permission, page_priority) " .
  			"values ('','".$groupRs[$i]["group_id"]."','108','2','1','VAT Report','checker/vat/index.php','0','0','88')";
  	//echo $sql."<br>";
  	//$object->setResult($sql);
  }
  
  $sql = "select s_userpermission.user_id, s_userpermission.group_id from s_userpermission order by user_id";
  $userRs = $object->getResult($sql);
  
  for($i=0;$i<$userRs["rows"];$i++){
  	//echo $userRs[$i]["user_id"]."-";
  	//echo $userRs[$i]["group_id"]."<br>";
  	$sql = "insert into s_upage (u_pageid, user_id, group_id, page_id, parent_id, menu_level, page_name" .
  			", url, edit_permission, view_permission, page_priority) " .
  			"values ('','".$userRs[$i]["user_id"]."','".$userRs[$i]["group_id"]."'," .
  					"'108','2','1','VAT Report','checker/vat/index.php','0','0','88')";
  	//echo $sql."<br>";
  	//$object->setResult($sql);
  }
 
?>
